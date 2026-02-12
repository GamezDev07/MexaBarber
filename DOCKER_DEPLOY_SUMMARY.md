# ✅ Dockerfile Deploy Ready - Resumen Técnico

## 📦 Archivos Creados/Actualizados

### 🐳 Docker & Deployment
- **`Dockerfile`** - Multi-stage build con Node para assets y PHP para la app
- **`.dockerignore`** - Optimiza la imagen excluyendo archivos innecesarios
- **`render.yaml`** - Configuración actualizada para Render con Docker

### 📚 Documentación
- **`RENDER_SETUP.md`** - Guía completa paso a paso (⭐ LEER PRIMERO)
- **`DOCKER_SETUP.md`** - Guía rápida de Docker y desarrollo

### ✔️ Validación Pre-Deploy
- **`validate-deploy.sh`** - Script Bash para validar (Linux/Mac)
- **`validate-deploy.ps1`** - Script PowerShell para validar (Windows)

### ⚙️ Configuración
- **`.env.example`** - Actualizado con variables Supabase
- **`.gitignore`** - Ya protege `.env` de commits accidentales

---

## 🚀 Pasos Rápidos para Deploy

### 1️⃣ Compilar Assets Local (una sola vez)
```bash
npm install
npm run dev
```

### 2️⃣ Validar Proyecto
```bash
# Windows
powershell -ExecutionPolicy Bypass -File validate-deploy.ps1

# Linux/Mac
bash validate-deploy.sh
```

### 3️⃣ Preparar Git
```bash
# Crear repositorio en GitHub
git init
git add .
git commit -m "chore: Docker deployment ready"
git remote add origin https://github.com/tu-usuario/appsalon.git
git push -u origin main
```

### 4️⃣ Deploy en Render
1. Ve a https://render.com/dashboard
2. Click **New +** → **Web Service**
3. Conecta tu repo GitHub `appsalon`
4. Render detecta el Dockerfile automáticamente
5. Agrega variables de entorno en Environment:
   ```
   DB_HOST=db.xxxxx.supabase.co
   DB_USER=postgres
   DB_PASSWORD=[Tu password Supabase]
   DB_NAME=postgres
   DB_PORT=5432
   DB_DRIVER=pgsql
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://appsalon.onrender.com
   ```
6. Click **Create Web Service**
7. ⏳ Espera ~3-5 minutos
8. ✅ Abre la URL que Render te da

---

## 🐳 Estructura del Dockerfile

### Stage 1: Build Assets (Node)
```dockerfile
FROM node:20-alpine AS assets
# Instala npm, compila SCSS → CSS, JS → JS minificado
```

### Stage 2: Runtime (PHP)
```dockerfile
FROM php:8.2-alpine
# Copia assets compilados del Stage 1
# Instala extensiones PHP (pdo_pgsql, etc)
# Instala Composer y dependencias PHP
# Corre como usuario no-root
# Expone puerto 8080
```

---

## 📝 Checklist Final

- [ ] npm install ✓
- [ ] npm run dev (compiló SCSS/JS) ✓
- [ ] composer install (sin errores) ✓
- [ ] ./validate-deploy.sh o .ps1 (todo ✓) ✓
- [ ] .env en .gitignore ✓
- [ ] Proyecto en GitHub ✓
- [ ] Conexión a Supabase lista ✓
- [ ] Render web service creado ✓
- [ ] Variables de entorno en Render ✓
- [ ] Application está **Live** en Render ✓

---

## 🔑 Variables Clave en Render

| Variable | Valor Ejemplo | Notas |
|----------|---------------|-------|
| DB_HOST | db.xxxxx.supabase.co | De Supabase Settings |
| DB_USER | postgres | Usuario por defecto Supabase |
| DB_PASSWORD | [TU_PASSWORD] | ⚠️ Fuerte y segura |
| DB_NAME | postgres | No cambiar |
| DB_DRIVER | pgsql | PostgreSQL |
| APP_ENV | production | desarrollo=development |
| APP_URL | https://appsalon.onrender.com | Tu URL de Render |

---

## ⚡ Comandos Docker Útiles

```bash
# Construir localmente
docker build -t appsalon .

# Ejecutar para testear
docker run -p 8080:8080 \
  -e DB_HOST=tu_host \
  -e DB_USER=postgres \
  -e DB_PASSWORD=tu_pass \
  appsalon

# Ver logs
docker logs -f appsalon

# Detener
docker stop appsalon
```

---

## 🔄 Flujo de Updates

Después de hacer cambios al código:

```bash
# 1. Compilar assets (si modificaste SCSS/JS)
npm run dev

# 2. Commitear
git add .
git commit -m "feat: descripción del cambio"

# 3. Push
git push origin main

# 4. Render se redeploya AUTOMÁTICAMENTE
# Monitorea en https://render.com/dashboard
```

---

## 🆘 Problemas Comunes

### ❌ "POST failed: Connection refused"
**Causa:** Render no puede conectar a la aplicación
**Solución:**
1. Revisa Logs en Render (click en el servicio)
2. Verifica puerto 8080 en Dockerfile
3. El health check tarda ~40 segundos, espera

### ❌ "SQLSTATE[08006]"
**Causa:** No puede conectar a Supabase
**Solución:**
1. Verifica credenciales en variables de entorno
2. Copia credenciales exactamente de Supabase Settings
3. Verifica que Supabase esté activo

### ❌ "table 'usuarios' doesn't exist"
**Causa:** No ejecutaste las migraciones en Supabase
**Solución:**
1. Ve a Supabase → SQL Editor
2. Pega `database/migrations/001_create_tables.sql`
3. Click Execute

### ❌ Assets CSS/JS no cargan
**Causa:** No compilaste localmente
**Solución:**
```bash
npm install
npm run dev
git add public/build/
git commit -m "rebuild: assets"
git push origin main
```

---

## 📊 Diagrama Arquitectura

```
┌─────────────────────┐
│    Tu Computadora   │
│  npm install        │
│  npm run dev        │
│  git push           │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│   GitHub (Repo)     │
│   appsalon/         │
└──────────┬──────────┘
           │ (webhook)
           ▼
┌─────────────────────┐
│    Render.com       │
│  Docker Build       │
│  Stage 1: Node      │
│  Stage 2: PHP       │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Contenedor Running │
│  php -S 0.0.0.0:80 │
└──────────┬──────────┘
           │ (HTTPS)
           ▼
┌─────────────────────┐
│     Supabase        │
│   PostgreSQL DB     │
└─────────────────────┘
```

---

## 🎯 Próximos Pasos

1. **Antes de hacer push:**
   ```bash
   powershell -ExecutionPolicy Bypass -File validate-deploy.ps1
   # O en Linux/Mac:
   bash validate-deploy.sh
   ```

2. **Lee la guía completa:**
   → [RENDER_SETUP.md](./RENDER_SETUP.md)

3. **Deploy:**
   - GitHub push
   - Render crea automáticamente
   - ✅ Live en ~5 minutos

4. **Monitorea:**
   - Logs en Render dashboard
   - Health check: `/health`

---

## ✨ Características Incluidas

- ✅ Multi-stage Docker build (optimizado)
- ✅ PHP 8.2 Alpine (ligero)
- ✅ Node 20 para compilar assets
- ✅ PostgreSQL/MySQL soporte
- ✅ PHPMailer para emails
- ✅ Health check automático
- ✅ Usuario no-root (seguridad)
- ✅ Environment variables listas
- ✅ `.dockerignore` optimizado
- ✅ Documentación completa

---

**¡Listo para producción! 🚀**

Cualquier duda, revisa [RENDER_SETUP.md](./RENDER_SETUP.md) o los comentarios en [Dockerfile](./Dockerfile)
