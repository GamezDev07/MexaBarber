# 🚀 Guía Completa: Deploy AppSalon en Render + Supabase + Docker

## 📋 Requisitos Previos

- Cuenta en [Render.com](https://render.com)
- Cuenta en [Supabase.com](https://supabase.com)
- GitHub para conectar el repositorio
- Cuenta en [Mailtrap.io](https://mailtrap.io) (para emails)

---

## ✅ Paso 1: Preparar Base de Datos en Supabase (5 min)

### 1.1 Crear Proyecto Supabase

1. Ve a https://supabase.com y crea cuenta
2. **Create New Project:**
   - **Name:** `appsalon`
   - **Database Password:** Genera una fuerte (GUÁRDALA)
   - **Region:** Tu zona horaria

3. Espera a que se cree (~5 min)

### 1.2 Obtener Connection String

1. Ve a **Settings → Database**
2. Copia la **Connection String (Full URL):**

```
postgresql://postgres:[PASSWORD]@db.xxxxx.supabase.co:5432/postgres
```

3. Anota: `[HOST]`, `[PASSWORD]` para después

### 1.3 Crear Tablas en Supabase

1. En Supabase, ve a **SQL Editor → New Query**
2. Copia todo el contenido de: `database/migrations/001_create_tables.sql`
3. Pega en el editor
4. Haz clic en **Execute** ✅

---

## 📦 Paso 2: Preparar Proyecto Local (10 min)

### 2.1 Instalar Dependencias

```bash
# Instalar dependencias Node (para SCSS/JS)
npm install

# Compilar SCSS y JS
npm run dev

# Instalar dependencias PHP
composer install
```

### 2.2 Configurar .env Local

Copia `.env.example` → `.env`:

```bash
cp .env.example .env
```

Edita `.env` con tu configuración Supabase:

```env
DB_DRIVER=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_USER=postgres
DB_PASSWORD=[tu_password_supabase]
DB_NAME=postgres

APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8080
```

### 2.3 Probar Localmente con Docker (Opcional)

```bash
# Construir imagen Docker
docker build -t appsalon .

# Ejecutar contenedor
docker run -it --rm \
  -e DB_HOST=db.xxxxx.supabase.co \
  -e DB_USER=postgres \
  -e DB_PASSWORD=tu_password \
  -e DB_NAME=postgres \
  -p 8080:8080 \
  appsalon
```

---

## 🔐 Paso 3: Preparar GitHub (5 min)

### 3.1 Subir a GitHub

```bash
# Inicializar git (si no está ya)
git init

# Agregar todos los archivos
git add .

# Commit inicial
git commit -m "feat: AppSalon - Listo para deploy en Render"

# Crear repositorio remoto en GitHub
# Ve a https://github.com/new y crea un repo llamado 'appsalon'

# Conectar y subir
git remote add origin https://github.com/[TU_USUARIO]/appsalon.git
git branch -M main
git push -u origin main
```

### 3.2 Asegurarse de que estos archivos estén commiteados:

- ✅ `Dockerfile`
- ✅ `.dockerignore`
- ✅ `render.yaml`
- ✅ `.env.example` (NUNCA commitar `.env` con secretos)
- ✅ `composer.json`
- ✅ `package.json`

---

## 🎯 Paso 4: Deploy en Render (15 min)

### 4.1 Conectar GitHub en Render

1. Ve a https://render.com/dashboard
2. Haz clic en **New +** → **Web Service**
3. **Connect a Repository:**
   - Selecciona tu cuenta GitHub
   - Busca y selecciona `appsalon`
   - Haz clic en **Connect**

### 4.2 Configurar Web Service

**Configuración Básica:**
- **Name:** `appsalon`
- **Region:** Su zona horaria más cercana
- **Plan:** Free (o superior si quieres mejor rendimiento)

**Construcción y Deploy:**
- **Runtime:** Docker
- **Docker Build:** Automático (detecta Dockerfile)
- **Docker Compose:** No necesario

### 4.3 Agregar Variables de Entorno

En **Environment**, agregar estas variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://appsalon.onrender.com

DB_DRIVER=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_USER=postgres
DB_PASSWORD=[Tu password Supabase]
DB_NAME=postgres

MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=[Tu usuario Mailtrap]
MAIL_PASSWORD=[Tu password Mailtrap]
MAIL_FROM=cuentas@appsalon.com
```

### 4.4 Crear el Web Service

1. Haz clic en **Create Web Service**
2. Espera el build (~3-5 minutos)
3. Verifica el log de construcción

---

## ✨ Paso 5: Verificar Deploy

### 5.1 Comprobar Status

1. En Render Dashboard, ve a tu servicio `appsalon`
2. Verifica que el status sea **Live** (verde)
3. Abre la URL: `https://appsalon.onrender.com`

### 5.2 Si hay errores

Haz clic en **Logs** para ver qué pasó:

```log
# Si ves este error:
ERROR: php: command not found

# El Dockerfile tiene un problema - revisa que tengas:
✅ docker:latest en producción
✅ Las dependencias PHP instaladas

# Si ves error de DB:
SQLSTATE[08006]

# Verifica:
✅ Credenciales Supabase correctas
✅ Las tablas se crearon en Supabase
✅ IP de Render está en whitelist Supabase (usualmente automático)
```

---

## 🚀 Paso 6: Migrar Datos (MySQL → PostgreSQL)

### Opción A: Usando herramientas online (Recomendado)

1. Ve a https://www.pgloader.io/
2. Sigue las instrucciones para convertir MySQL → PostgreSQL
3. Importa en Supabase SQL Editor

### Opción B: Exportar/Importar manual con conversión

```bash
# 1. Exportar desde MySQL local
mysqldump -u root -p --compatible=postgresql appsalon > backup.sql

# 2. Convertir archivos SQL (remove MySQL specifics)
# - Elimina: ENGINE=InnoDB, CHARSET=utf8mb4
# - Reemplaza: AUTO_INCREMENT → SERIAL

# 3. Importar en Supabase SQL Editor
# Copia y pega el contenido en SQL Editor → Execute
```

---

## 📊 Diagrama del Flujo

```
┌─────────────────────────┐
│   Repositorio GitHub    │
│   (appsalon)            │
└────────────┬────────────┘
             │ (webhook)
             ▼
┌─────────────────────────┐
│  Render.com             │
│  Docker Build & Deploy  │
└────────────┬────────────┘
             │ (HTTPS)
             ▼
┌─────────────────────────┐
│  Aplicación en Render   │
│  php -S 0.0.0.0:8080    │
└────────────┬────────────┘
             │ (conexión)
             ▼
┌─────────────────────────┐
│  Base de Datos          │
│  Supabase PostgreSQL    │
└─────────────────────────┘
```

---

## 🐛 Troubleshooting Completo

| Error | Causa | Solución |
|-------|-------|----------|
| `502 Bad Gateway` | Aplicación crashed | Ve a Logs en Render, busca el error |
| `SQLSTATE[08006]` | No puede conectar DB | Verifica credenciales DB en `.env` |
| `POST failed: Connection refused` | Puerto incorrecto | El Dockerfile expone puerto 8080 ✓ |
| `Composer timeout` | Build tarda mucho | Aumenta timeout en render.yaml |
| `npm: command not found` | Node no instalado | El Dockerfile Stage 1 usa Node ✓ |
| `tabla no existe` | Migraciones no corrieron | Ejecuta SQL en Supabase SQL Editor |

---

## 🔄 Actualizaciones Futuras

Cada vez que hagas cambios:

```bash
# Haz tus cambios, después:
git add .
git commit -m "descripción del cambio"
git push origin main

# Render se redeploya automáticamente (webhook)
# Monitorea en: https://render.com/dashboard
```

---

## 📝 Checklist Final

- [ ] ✅ Proyecto en GitHub
- [ ] ✅ Base de datos en Supabase con tablas
- [ ] ✅ Variables de entorno configuradas en Render
- [ ] ✅ Dockerfile presente en root
- [ ] ✅ render.yaml presente en root
- [ ] ✅ npm run dev compiló SCSS/JS
- [ ] ✅ composer install completó sin errores
- [ ] ✅ Deploy en Render completado
- [ ] ✅ App accesible en https://appsalon.onrender.com

---

## 🆘 Soporte

Si tienes problemas:

1. Revisa los **Logs** en el dashboard de Render
2. Verifica el `.env` tiene todas las variables
3. Comprueba que Supabase tiene las tablas creadas
4. Prueba localmente con `docker build` y `docker run`

¡Éxito en el deploy! 🎉
