# 🚀 Plan de Despliegue: Render + Supabase

## 📋 Cambios Necesarios (Resumen)

Actualmente tu app usa:
- **MySQL local** ❌ (no escalable)
- **PHP + MySQLi** (limitado)

Para Render + Supabase necesitamos:
- **PostgreSQL en Supabase** ✅ (escalable, SaaS)
- **PHP + PDO** ✅ (más flexible)
- **Variables de entorno dinámicas** ✅
- **Scripts de migración** ✅

---

## 🔄 FASES DE CAMBIO (Sin romper funcionalidad)

### FASE 1: Migración de BD (MySQL → PostgreSQL)
**Complejidad:** 🟡 MEDIA | **Tiempo:** 2-3 horas

#### 1.1 Cambios en estructura
```
MySQLi                          PDO + PostgreSQL
├── mysqli_connect()       ❌   PDO::connect() ✅
├── mysqli_query()         ❌   PDOStatement ✅
├── fetch_assoc()          ❌   fetch(PDO::FETCH_ASSOC) ✅
└── Real_escape_string()   ❌   Prepared Statements ✅
```

#### 1.2 Cambios en consultas SQL
```
MySQL                      PostgreSQL
├── AUTO_INCREMENT    ❌   SERIAL/BIGSERIAL ✅
├── varchar()         ✅   varchar() ✅
├── datetime()        ⚠️   timestamp ✅
└── ON DELETE...      ✅   ON DELETE... ✅
```

### FASE 2: Código PHP (MySQLi → PDO)
**Complejidad:** 🟡 MEDIA | **Tiempo:** 3-4 horas

#### 2.1 Actualizar ActiveRecord.php
```php
// ANTES (MySQLi)
$resultado = self::$db->query($query);
while($registro = $resultado->fetch_assoc()) { ... }

// DESPUÉS (PDO)
$stmt = self::$db->prepare($query);
$stmt->execute();
while($registro = $stmt->fetch(PDO::FETCH_ASSOC)) { ... }
```

#### 2.2 Actualizar database.php
```php
// ANTES (MySQLi)
$db = mysqli_connect('localhost', 'root', '', 'appsalon');

// DESPUÉS (PDO - Render + Supabase)
$dsn = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT;
$db = new PDO($dsn, DB_USER, DB_PASSWORD);
```

### FASE 3: Variables de Entorno
**Complejidad:** 🟢 BAJA | **Tiempo:** 30 minutos

```env
# ANTES (opcional)
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=root

# DESPUÉS (obligatorio para Render)
DB_HOST=db.supabase.co
DB_PORT=5432
DB_USER=postgres
DB_PASSWORD=xxxxx
DB_NAME=postgres
DB_SSL=require
```

### FASE 4: Configurar Supabase
**Complejidad:** 🟢 BAJA | **Tiempo:** 1 hora

- [ ] Crear cuenta en Supabase
- [ ] Crear proyecto
- [ ] Obtener credenciales
- [ ] Importar datos desde MySQL

### FASE 5: Configurar Render
**Complejidad:** 🟢 BAJA | **Tiempo:** 1.5 horas

- [ ] Conectar repositorio GitHub
- [ ] Configurar variables de entorno
- [ ] Configurar comando de inicio
- [ ] Deploy inicial

---

## 📁 Archivos a Modificar

```
CAMBIOS NECESARIOS:

1. includes/database.php         ❌ MySQLi → PDO
2. models/ActiveRecord.php       ❌ Reescribir métodos
3. classes/Email.php             ✅ Sin cambios
4. .env                          ✅ Solo agregar variables
5. composer.json                 ✅ Sin cambios (PDO es built-in)

CREAR NUEVOS:

6. database/migrations/001_init.sql  (para PostgreSQL)
7. deploy.sh / deploy.bat            (scripts de deploy)
```

---

## 🎯 ORDEN DE EJECUCIÓN (Sin parar la app)

### Hoy (Fase Local)
```
✅ COMPLETO: App funcionando con MySQL local
⏳ TODO: Crear cuenta de prueba
```

### Mañana (Fase de Preparación)
```
Paso 1: Crear archivo de migración SQL
Paso 2: Actualizar ActiveRecord.php (PDO)
Paso 3: Actualizar database.php (PDO)
Paso 4: Probar localmente con PostgreSQL (en Docker)
```

### Próxima semana (Fase Cloud)
```
Paso 5: Crear cuenta en Supabase
Paso 6: Importar datos a Supabase
Paso 7: Conectar app a Supabase
Paso 8: Crear cuenta en Render
Paso 9: Hacer push a GitHub
Paso 10: Deploy en Render
```

---

## ✅ CHECKLIST PENDIENTE

### Lo que ya completamos ✅
- [x] Servidor PHP activo (localhost:8000)
- [x] MySQL local funcional
- [x] BD importada (appsalon)
- [x] Variables de entorno (.env) configuradas
- [x] Assets compilados (SASS + JS)

### Lo que falta ⏳
- [ ] Crear cuenta de prueba (TÚ AHORA)
- [ ] Explorar la app (TÚ)
- [ ] Migrar código MySQLi → PDO (YO)
- [ ] Crear archivos de migración PG (YO)
- [ ] Registrar en Supabase (TÚ)
- [ ] Registrar en Render (TÚ)
- [ ] Deploy en ambas plataformas (YO + TÚ)

---

## 💰 COSTOS EN PRODUCTION (Render + Supabase)

| Servicio | Plan | Costo |
|----------|------|-------|
| Render | Web Service | $0-7/mes* |
| Supabase | Postgres | $0-25/mes** |
| Dominio | (tu dominio) | $0-15/año |
| **Total** | **Starter** | **$0-32/mes** |

*Render: Tier gratuito hasta $7/mes para aplicaciones pequeñas  
**Supabase: Gratuito hasta 1GB almacenamiento + 2GB transferencia/mes

---

## 📊 Diferencia Local vs Cloud

```
LOCAL (Ahora)              CLOUD (Render + Supabase)
├── MySQL (127.0.0.1)      ├── PostgreSQL (supabase.co)
├── PHP (localhost:8000)    ├── PHP (mi-app.render.com)
├── Datos en PC             ├── Datos replicados 3x (backup)
├── Sin SSL                 ├── SSL automático ✅
├── Sin uptime              ├── 99.9% uptime SLA
└── Acceso local            └── Acceso global
```

---

## 🔒 Seguridad Mejorada en Cloud

```
LOCAL                          CLOUD
├── ❌ Sin SSL                  ├── ✅ SSL/TLS
├── ❌ Credenciales en .env     ├── ✅ Env vars en plataforma
├── ❌ Sin backups              ├── ✅ Backups automáticos
├── ❌ Sin logs                 ├── ✅ Logs centralizados
└── ❌ Acceso desde internet    └── ✅ Firewall + DDoS protection
```

---

## 🚦 SIGUIENTE: ¿Qué Hago Ahora?

### RECOMENDACIÓN:
1. **Hoy:** Crea cuenta y explora la app (15-20 min)
2. **Cuando termines:** Avísame y empezamos con Paso 1

### YO MIENTRAS TANTO:
Preparo un script que:
- Genera la migración SQL para PostgreSQL
- Actualiza ActiveRecord.php automáticamente
- Crea archivo de deploy automático

---

## 📞 PRÓXIMO CHECKPOINT

Cuando completes la prueba de usuario, avísame y haremos:

```
✅ Code Review de cambios necesarios
✅ Validar compatibilidad PostgreSQL
✅ Crear script de migración BD
✅ Hacer commit y push inicial a GitHub
```

---

**¿Listo para explorar la app? Cuando termines, avisame y continuamos.** 🚀
