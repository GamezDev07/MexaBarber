# Plan de Implementación - MexaBarber SaaS Multi-Tenant

## Decisiones de Arquitectura
- **Stack**: PHP MVC custom actual + Vanilla JS
- **Multi-tenant**: Shared DB con `barbershop_id` en todas las tablas
- **Notificaciones**: In-app + Email (PHPMailer)
- **Pagos**: MercadoPago

---

## FASE 1: Base Multi-Tenant + Modelo de Barberos

### 1.1 Migración de Base de Datos
**Archivo**: `database/migrations/002_multi_tenant.sql`

```sql
-- Tabla de barberías (multi-tenant)
CREATE TABLE IF NOT EXISTS barbershops (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(60) NOT NULL,
    telefono VARCHAR(15),
    direccion VARCHAR(255),
    logo VARCHAR(255),
    activo SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agregar barbershop_id a tablas existentes
ALTER TABLE usuarios ADD COLUMN barbershop_id INTEGER REFERENCES barbershops(id);
ALTER TABLE servicios ADD COLUMN barbershop_id INTEGER REFERENCES barbershops(id);
ALTER TABLE citas ADD COLUMN barbershop_id INTEGER REFERENCES barbershops(id);

-- Tabla de barberos/empleados
CREATE TABLE IF NOT EXISTS barberos (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
    barbershop_id INTEGER REFERENCES barbershops(id) ON DELETE CASCADE,
    especialidad VARCHAR(100),
    activo SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agregar barbero_id a citas
ALTER TABLE citas ADD COLUMN barbero_id INTEGER REFERENCES barberos(id);
-- Estado de la cita: pendiente, confirmada, completada, cancelada
ALTER TABLE citas ADD COLUMN estado VARCHAR(20) DEFAULT 'pendiente';
-- Método de pago: efectivo, tarjeta, online, transferencia
ALTER TABLE citas ADD COLUMN metodo_pago VARCHAR(20);
-- Estado de pago: pendiente, pagado, fallido, reembolsado
ALTER TABLE citas ADD COLUMN pago_estado VARCHAR(20) DEFAULT 'pendiente';
-- Referencia de pago (ID de MercadoPago o referencia de transferencia)
ALTER TABLE citas ADD COLUMN pago_referencia VARCHAR(255);
-- Número de turno del día
ALTER TABLE citas ADD COLUMN turno INTEGER;

-- Tabla de notificaciones in-app
CREATE TABLE IF NOT EXISTS notificaciones (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
    barbershop_id INTEGER REFERENCES barbershops(id) ON DELETE CASCADE,
    tipo VARCHAR(50) NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    leida SMALLINT DEFAULT 0,
    cita_id INTEGER REFERENCES citas(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de pagos (registro detallado)
CREATE TABLE IF NOT EXISTS pagos (
    id SERIAL PRIMARY KEY,
    cita_id INTEGER REFERENCES citas(id) ON DELETE SET NULL,
    barbershop_id INTEGER REFERENCES barbershops(id) ON DELETE CASCADE,
    monto DECIMAL(8,2) NOT NULL,
    metodo VARCHAR(20) NOT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente',
    referencia_externa VARCHAR(255),
    datos_pago TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices
CREATE INDEX idx_barberos_barbershop ON barberos(barbershop_id);
CREATE INDEX idx_barberos_usuario ON barberos(usuario_id);
CREATE INDEX idx_citas_barbero ON citas(barbero_id);
CREATE INDEX idx_citas_estado ON citas(estado);
CREATE INDEX idx_citas_barbershop ON citas(barbershop_id);
CREATE INDEX idx_notificaciones_usuario ON notificaciones(usuario_id);
CREATE INDEX idx_notificaciones_leida ON notificaciones(leida);
CREATE INDEX idx_pagos_cita ON pagos(cita_id);
CREATE INDEX idx_pagos_barbershop ON pagos(barbershop_id);
CREATE INDEX idx_usuarios_barbershop ON usuarios(barbershop_id);
CREATE INDEX idx_servicios_barbershop ON servicios(barbershop_id);
```

### 1.2 Nuevos Modelos
Siguiendo el patrón ActiveRecord existente:

| Archivo | Clase | Tabla |
|---------|-------|-------|
| `models/Barbershop.php` | `Barbershop` | `barbershops` |
| `models/Barbero.php` | `Barbero` | `barberos` |
| `models/Notificacion.php` | `Notificacion` | `notificaciones` |
| `models/Pago.php` | `Pago` | `pagos` |

### 1.3 Modificar Modelos Existentes
- `models/Usuario.php` - Agregar `barbershop_id` a `$columnasDB`
- `models/Servicio.php` - Agregar `barbershop_id` a `$columnasDB`
- `models/Cita.php` - Agregar `barbero_id`, `estado`, `metodo_pago`, `pago_estado`, `pago_referencia`, `turno`, `barbershop_id` a `$columnasDB`

### 1.4 Middleware de Barbershop
**Archivo**: `includes/funciones.php` - Agregar función `getBarbershopId()` que:
- Detecta la barbería por subdomain o por sesión del usuario logueado
- Almacena `barbershop_id` en `$_SESSION`

---

## FASE 2: Panel de Administración - Dashboard

### 2.1 Nuevo Controller
**Archivo**: `controllers/AdminController.php` - Expandir con nuevos métodos:

| Método | Ruta | Descripción |
|--------|------|-------------|
| `index()` | GET `/admin` | Dashboard principal con KPIs |
| `empleados()` | GET `/admin/empleados` | Lista de empleados |
| `crearEmpleado()` | GET/POST `/admin/empleados/crear` | Formulario agregar barbero |
| `editarEmpleado()` | GET/POST `/admin/empleados/editar` | Editar barbero |
| `eliminarEmpleado()` | POST `/admin/empleados/eliminar` | Eliminar barbero |
| `toggleEmpleado()` | POST `/admin/empleados/toggle` | Activar/desactivar |
| `agenda()` | GET `/admin/agenda` | Vista calendario |
| `ingresos()` | GET `/admin/ingresos` | Panel de ingresos |

### 2.2 Nuevas Rutas en `public/index.php`
```php
// Dashboard Admin
$router->get('/admin', [AdminController::class, 'index']);

// Empleados
$router->get('/admin/empleados', [AdminController::class, 'empleados']);
$router->get('/admin/empleados/crear', [AdminController::class, 'crearEmpleado']);
$router->post('/admin/empleados/crear', [AdminController::class, 'crearEmpleado']);
$router->get('/admin/empleados/editar', [AdminController::class, 'editarEmpleado']);
$router->post('/admin/empleados/editar', [AdminController::class, 'editarEmpleado']);
$router->post('/admin/empleados/eliminar', [AdminController::class, 'eliminarEmpleado']);
$router->post('/admin/empleados/toggle', [AdminController::class, 'toggleEmpleado']);

// Agenda
$router->get('/admin/agenda', [AdminController::class, 'agenda']);

// Ingresos
$router->get('/admin/ingresos', [AdminController::class, 'ingresos']);
```

### 2.3 Nuevas Vistas
| Archivo | Contenido |
|---------|-----------|
| `views/admin/index.php` | **Dashboard**: KPIs (citas del día, ingresos, servicios top, barbero top) |
| `views/admin/empleados/index.php` | Lista de barberos con toggle activo/inactivo |
| `views/admin/empleados/crear.php` | Formulario para agregar barbero |
| `views/admin/empleados/editar.php` | Formulario para editar barbero |
| `views/admin/agenda.php` | Vista calendario con filtro por barbero |
| `views/admin/ingresos.php` | Tabla de ingresos con filtros |
| `views/templates/admin-sidebar.php` | Sidebar de navegación admin |

### 2.4 Dashboard - Consultas SQL para KPIs
```sql
-- Citas del día
SELECT COUNT(*) FROM citas WHERE fecha = CURRENT_DATE AND barbershop_id = ?

-- Ingresos del día (citas completadas)
SELECT COALESCE(SUM(s.precio), 0) as total
FROM citas c
JOIN citasservicios cs ON cs.citaId = c.id
JOIN servicios s ON s.id = cs.servicioId
WHERE c.fecha = CURRENT_DATE AND c.estado = 'completada' AND c.barbershop_id = ?

-- Servicios más vendidos (mes actual)
SELECT s.nombre, COUNT(*) as total
FROM citasservicios cs
JOIN servicios s ON s.id = cs.servicioId
JOIN citas c ON c.id = cs.citaId
WHERE c.barbershop_id = ? AND EXTRACT(MONTH FROM c.fecha) = EXTRACT(MONTH FROM CURRENT_DATE)
GROUP BY s.nombre ORDER BY total DESC LIMIT 5

-- Barbero con más servicios (mes actual)
SELECT CONCAT(u.nombre, ' ', u.apellido) as barbero, COUNT(*) as total
FROM citas c
JOIN barberos b ON b.id = c.barbero_id
JOIN usuarios u ON u.id = b.usuario_id
WHERE c.barbershop_id = ? AND EXTRACT(MONTH FROM c.fecha) = EXTRACT(MONTH FROM CURRENT_DATE)
GROUP BY barbero ORDER BY total DESC LIMIT 1
```

### 2.5 Nuevos archivos JS y CSS
| Archivo | Contenido |
|---------|-----------|
| `src/js/admin.js` | Lógica del dashboard (gráficos, filtros, toggles) |
| `src/js/agenda.js` | Calendario interactivo, drag & drop de reasignación |
| `src/sass/admin/_dashboard.scss` | Estilos del dashboard |
| `src/sass/admin/_empleados.scss` | Estilos de gestión de empleados |
| `src/sass/admin/_agenda.scss` | Estilos del calendario |
| `src/sass/admin/_sidebar.scss` | Estilos del sidebar |

---

## FASE 3: Sistema de Citas Mejorado (Cliente)

### 3.1 Modificar Flujo de Reserva
Cambiar de 3 pasos a **5 pasos**:

1. **Servicios** - Selección de servicios (existente)
2. **Barbero** - Selección de barbero o "Sin preferencia" (asignación automática por turno)
3. **Fecha y Hora** - Calendario + horarios disponibles del barbero seleccionado
4. **Método de Pago** - Efectivo / Tarjeta en establecimiento / Online (MercadoPago) / Transferencia bancaria
5. **Resumen y Confirmación** - Resumen + botón de reservar (o pagar si es online)

### 3.2 Nuevas APIs
| Ruta | Método | Descripción |
|------|--------|-------------|
| `GET /api/barberos` | GET | Lista de barberos activos de la barbería |
| `GET /api/disponibilidad` | GET | Horarios disponibles de un barbero en una fecha |
| `POST /api/citas` | POST | Crear cita (modificar existente para incluir barbero, pago, turno) |
| `GET /api/mis-citas` | GET | Obtener citas del usuario logueado (pendientes y confirmadas) |
| `POST /api/citas/cancelar` | POST | Cancelar una cita propia (cambia estado a 'cancelada') |
| `POST /api/citas/modificar` | POST | Modificar fecha, hora o barbero de una cita propia |
| `POST /api/pago/crear` | POST | Crear preferencia de pago en MercadoPago |
| `POST /api/pago/webhook` | POST | Webhook de MercadoPago para confirmar pagos |
| `GET /api/notificaciones` | GET | Obtener notificaciones del usuario |
| `POST /api/notificaciones/leer` | POST | Marcar notificación como leída |

### 3.3 Gestión de Citas del Cliente

**Vista**: `views/cita/mis-citas.php` - Panel donde el usuario ve sus citas activas
**Ruta**: `GET /mis-citas` → `CitaController::misCitas()`

**Funcionalidad**:
- Lista de citas pendientes/confirmadas del usuario con fecha, hora, barbero, servicios y estado
- Botón **"Cancelar"** en cada cita:
  - Confirmación con SweetAlert antes de cancelar
  - Solo se puede cancelar si la cita es futura (fecha > hoy, o fecha = hoy pero hora > ahora + 1h)
  - Al cancelar → notificación al barbero asignado + al admin
  - Si el pago fue online → se marca como `pago_estado = 'reembolsado'` (reembolso manual por admin)
- Botón **"Modificar"** en cada cita:
  - Abre modal/formulario para cambiar: fecha, hora y/o barbero
  - Mismas validaciones que al crear (disponibilidad del barbero, horarios válidos)
  - Al modificar → notificación al barbero anterior (si cambió) y al nuevo barbero
  - Restricción: solo se puede modificar hasta 2 horas antes de la cita

**Enlace en barra de navegación**: Agregar "Mis Citas" junto a "Crear Cita" en `views/templates/barra.php`

**JS**: `src/js/mis-citas.js` - Lógica de cancelar/modificar con fetch a las APIs

### 3.4 Sistema de Turnos
- Al crear una cita, se calcula el número de turno del día:
  ```sql
  SELECT COALESCE(MAX(turno), 0) + 1 FROM citas WHERE fecha = ? AND barbershop_id = ?
  ```
- El turno se muestra al cliente en la confirmación

### 3.4 Modificar `views/cita/index.php`
- Agregar tabs para barbero y método de pago
- Paso 2: Grid de barberos con foto/nombre + opción "Sin preferencia"
- Paso 4: Radio buttons para método de pago

### 3.5 Modificar `src/js/app.js`
- Ampliar objeto `cita` con: `barberoId`, `metodoPago`
- Agregar funciones: `consultarBarberos()`, `seleccionarBarbero()`, `seleccionarMetodoPago()`, `consultarDisponibilidad()`
- Integrar SDK de MercadoPago para pago online
- Actualizar `mostrarResumen()` con nueva información
- Cambiar `pasoFinal` de 3 a 5

---

## FASE 4: Notificaciones

### 4.1 Servicio de Notificaciones
**Archivo**: `classes/NotificacionService.php`

Métodos:
- `notificarNuevaCita($barberoId, $citaId)` - Email + in-app al barbero
- `notificarCancelacion($barberoId, $citaId)` - Email + in-app al barbero
- `notificarReasignacion($barberoAnteriorId, $barberoNuevoId, $citaId)` - Email + in-app a ambos
- `notificarPagoRecibido($citaId)` - In-app al admin

### 4.2 Plantillas de Email
**Archivo**: `classes/Email.php` - Agregar métodos:
- `enviarNotificacionBarbero($barberoEmail, $tipo, $datosCita)`

### 4.3 Centro de Notificaciones (Frontend)
- Icono de campana en la barra de navegación
- Dropdown con lista de notificaciones
- Badge con contador de no leídas
- Polling cada 30 segundos via `/api/notificaciones`

---

## FASE 5: Integración MercadoPago

### 5.1 Configuración
**Archivo**: `includes/config.php` - Agregar:
```php
define('MP_ACCESS_TOKEN', getenv('MP_ACCESS_TOKEN'));
define('MP_PUBLIC_KEY', getenv('MP_PUBLIC_KEY'));
```

**Archivo**: `.env.example` - Agregar:
```
MP_ACCESS_TOKEN=
MP_PUBLIC_KEY=
```

### 5.2 Clase de Pago
**Archivo**: `classes/MercadoPagoService.php`
- Método `crearPreferencia($cita, $servicios)` - Crea preferencia de pago
- Método `verificarPago($paymentId)` - Verifica estado del pago
- Dependencia: `mercadopago/dx-php` vía Composer

### 5.3 Flujo de Pago Online
1. Cliente selecciona "Online" como método de pago
2. Al confirmar, se crea preferencia en MercadoPago → redirección al checkout
3. MercadoPago envía webhook → se actualiza `pago_estado` a 'pagado'
4. Solo entonces la cita pasa a estado 'confirmada'
5. Se notifica al barbero

### 5.4 Flujo de Otros Métodos
- **Efectivo/Tarjeta en establecimiento**: Cita queda en estado 'confirmada' inmediatamente, pago se registra cuando el barbero marca como completada
- **Transferencia bancaria**: Cita queda 'pendiente' hasta que admin confirme la transferencia manualmente

---

## FASE 6: Agenda/Calendario Admin

### 6.1 Vista de Calendario
- Vista mensual/semanal/diaria
- Cada cita como bloque de color según estado
- Filtro dropdown por barbero
- Click en cita → modal con detalles + acciones

### 6.2 Acciones sobre Citas (Admin)
- Cambiar estado: pendiente → confirmada → completada / cancelada
- Reasignar a otro barbero (drag & drop o selector)
- Al reasignar → se envía notificación a ambos barberos

### 6.3 APIs para Agenda
| Ruta | Método | Descripción |
|------|--------|-------------|
| `GET /api/admin/citas` | GET | Citas por rango de fechas + filtro barbero |
| `POST /api/admin/citas/estado` | POST | Cambiar estado de cita |
| `POST /api/admin/citas/reasignar` | POST | Reasignar barbero |

---

## Resumen de Archivos a Crear/Modificar

### Archivos NUEVOS:
```
database/migrations/002_multi_barbershop.sql
models/Barbershop.php
models/Barbero.php
models/Notificacion.php
models/Pago.php
classes/NotificacionService.php
classes/MercadoPagoService.php
views/admin/dashboard.php
views/admin/empleados/index.php
views/admin/empleados/crear.php
views/admin/empleados/editar.php
views/admin/agenda.php
views/admin/ingresos.php
views/templates/admin-sidebar.php
views/cita/mis-citas.php
src/js/admin.js
src/js/agenda.js
src/js/mis-citas.js
src/sass/admin/_dashboard.scss
src/sass/admin/_empleados.scss
src/sass/admin/_agenda.scss
src/sass/admin/_sidebar.scss
```

### Archivos a MODIFICAR:
```
public/index.php              → Nuevas rutas
includes/config.php            → Constantes MercadoPago
includes/funciones.php         → getBarbershopId(), isBarbero()
models/Usuario.php             → barbershop_id
models/Servicio.php            → barbershop_id
models/Cita.php                → campos nuevos
models/AdminCita.php           → queries actualizadas
controllers/AdminController.php → Métodos nuevos del dashboard
controllers/APIController.php   → Endpoints nuevos (crear, cancelar, modificar, mis-citas)
controllers/CitaController.php  → Pasar barberos a la vista + misCitas()
classes/Email.php              → Métodos de notificación
views/cita/index.php           → Pasos 2 (barbero) y 4 (pago)
views/templates/barra.php      → Notificaciones + nav admin + "Mis Citas"
views/layout.php               → SDK MercadoPago condicional
src/js/app.js                  → Flujo de 5 pasos
src/sass/app.scss              → Imports nuevos
composer.json                  → mercadopago/dx-php
.env.example                   → Variables MercadoPago
gulpfile.js                    → Nuevos JS sources
```

---

## Orden de Implementación
1. **Fase 1** - Base de datos + modelos (fundamento de todo)
2. **Fase 2** - Panel admin dashboard + gestión empleados
3. **Fase 3** - Flujo de citas mejorado (barbero + pago + turno)
4. **Fase 4** - Notificaciones in-app + email
5. **Fase 5** - MercadoPago
6. **Fase 6** - Agenda/calendario
