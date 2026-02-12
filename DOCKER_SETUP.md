# AppSalon - PHP MVC + Docker Deploy Ready 🚀

## Descripción

AppSalon es una aplicación PHP MVC moderna para gestionar un salón de belleza. Incluye:

- ✅ **Backend:** PHP 8.2 + patrón MVC
- ✅ **Frontend:** SASS + JavaScript compilados
- ✅ **Base de datos:** PostgreSQL (Supabase)
- ✅ **Deployment:** Docker + Render (PaaS)
- ✅ **Email:** Integración con PHPMailer

---

## 📋 Requisitos

### Desarrollo Local

- PHP 8.2+
- Node.js 20+
- Composer
- MySQL (opcional, para desarrollo local)

### Producción

- Docker (automático en Render)
- PostgreSQL 15+ (Supabase)
- Acceso a Render.com

---

## 🚀 Inicio Rápido

### 1. Clonar proyecto

```bash
git clone https://github.com/tu-usuario/appsalon.git
cd appsalon
```

### 2. Instalar dependencias

```bash
# Node.js (frontend assets)
npm install

# Compilar SCSS/JS
npm run dev

# PHP (backend)
composer install
```

### 3. Configurar variables de entorno

```bash
cp .env.example .env

# Edita .env con tu configuración local
# Para desarrollo local, usa MySQL:
# DB_HOST=localhost
# DB_DRIVER=mysql
# DB_NAME=appsalon
```

### 4. Crear base de datos

```bash
# Si usas MySQL local:
mysql -u root -p < database/migrations/001_create_tables.sql

# Importa en la base de datos 'appsalon'
```

### 5. Ejecutar servidor local

```bash
# PHP Built-in Server (puerto 8000)
php -S localhost:8000 -t public

# O con Docker:
docker build -t appsalon .
docker run -p 8080:8080 appsalon
```

Abre tu navegador en: **http://localhost:8000**

---

## 📁 Estructura del Proyecto

```
├── controllers/          # Lógica de controladores
├── models/              # Modelos y ActiveRecord
├── classes/             # Clases auxiliares
├── views/               # Templates HTML
├── public/              # Asset compilados + punto de entrada
│   ├── index.php       # Entry point
│   └── build/          # CSS/JS compilado
├── src/                 # Archivos fuente
│   ├── scss/           # Estilos SASS
│   └── js/             # Scripts JavaScript
├── includes/            # Configuración
├── database/            # Migraciones SQL
├── vendor/              # Dependencias PHP (composer)
├── node_modules/        # Dependencias JS (no commitar)
├── Dockerfile           # Deploy en Render
├── render.yaml          # Config Render
└── .env.example         # Variables de entorno
```

---

## 🔧 Comandos Principales

### Frontend (SCSS/JS)

```bash
# Instalar dependencias
npm install

# Compilar assets (development)
npm run dev

# Compilar assets (one-time)
npx gulp
```

### Backend (PHP)

```bash
# Instalar dependencias
composer install

# Actualizar dependencias
composer update

# Autoload PSR-4
composer dumpautoload
```

### Git

```bash
# Validar antes de hacer push
./validate-deploy.sh              # Linux/Mac
powershell -ExecutionPolicy Bypass -File validate-deploy.ps1  # Windows

# Subir cambios
git add .
git commit -m "descripción del cambio"
git push origin main
```

---

## 🐳 Docker

### Construir imagen

```bash
docker build -t appsalon:latest .
```

### Ejecutar contenedor

```bash
docker run -d \
  --name appsalon \
  -p 8080:8080 \
  -e DB_HOST=db.supabase.co \
  -e DB_USER=postgres \
  -e DB_PASSWORD=your_password \
  -e DB_NAME=postgres \
  appsalon:latest
```

### Verificar logs

```bash
docker logs appsalon
```

### Detener contenedor

```bash
docker stop appsalon
docker rm appsalon
```

---

## 🚀 Deploy en Render

Para deploy automático en Render.com con Docker:

1. **Sigue la guía:** [RENDER_SETUP.md](./RENDER_SETUP.md)
2. **Valida el proyecto:**
   ```bash
   ./validate-deploy.sh  # Linux/Mac
   # o
   powershell -ExecutionPolicy Bypass -File validate-deploy.ps1  # Windows
   ```
3. **Git push y Render se redeploya automáticamente**

La URL automática será: `https://appsalon.onrender.com`

---

## 📊 Base de Datos

### PostgreSQL (Producción en Supabase)

```bash
# Connection string en .env:
DB_DRIVER=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_USER=postgres
DB_PASSWORD=your_password
DB_NAME=postgres
```

### MySQL (Desarrollo local)

```bash
# Connection string en .env:
DB_DRIVER=mysql
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=appsalon
```

### Ejecutar migraciones

```sql
-- En Supabase SQL Editor o MySQL:
SOURCE database/migrations/001_create_tables.sql;
```

---

## 🔐 Seguridad

- ✅ **`.env` NO está commiteado** - Contiene secretos
- ✅ **`.env.example` sí está** - Template para variables
- ✅ **Usa `DB_PASSWORD=` fuerte en producción**
- ✅ **APP_DEBUG=false en producción**
- ✅ **Docker corre como usuario no-root**

---

## ⚠️ Variables de Entorno Importantes

```env
# Aplicación
APP_ENV=production          # development | production
APP_DEBUG=false            # true | false
APP_URL=https://appsalon.onrender.com

# Base de datos
DB_DRIVER=pgsql            # pgsql | mysql
DB_HOST=db.supabase.co
DB_PORT=5432
DB_USER=postgres
DB_PASSWORD=tu_password
DB_NAME=postgres

# Email (opcional)
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_contraseña
MAIL_FROM=cuentas@appsalon.com
```

---

## 📞 Soporte & Troubleshooting

### Error: "php: command not found"
- Docker está usando PHP 8.2-alpine
- Verifica que la app esté en `/app`

### Error: "SQLSTATE[08006] - connection refused"
- Credenciales DB incorrectas
- Verifica variables de entorno en Render

### Error: "composer.lock" conflict
```bash
git pull --no-commit
git checkout --theirs composer.lock
git add composer.lock && git commit
```

### Assets no cargan
```bash
# Recompila localmente:
npm install
npm run dev
git add public/build/
git commit -m "rebuild: assets"
git push
```

---

## 📚 Recursos

- [Render Documentation](https://render.com/docs)
- [Supabase Docs](https://supabase.com/docs)
- [PHP Official](https://www.php.net/docs.php)
- [Docker Docs](https://docs.docker.com/)

---

## 📝 Licencia

Este proyecto es educativo. Usa libremente para aprender.

---

## 🙋 Preguntas?

Revisa:
1. [RENDER_SETUP.md](./RENDER_SETUP.md) - Guía completa de deploy
2. [Dockerfile](./Dockerfile) - Configuración Docker
3. Logs de Render en el dashboard
4. Logs Docker: `docker logs appsalon`

---

**¡Listo para producción!** 🚀 Sigue [RENDER_SETUP.md](./RENDER_SETUP.md) para deploying.
