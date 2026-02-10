# 🚀 Guía Rápida: Deploy en Render + Supabase

## Paso 1: Crear proyecto en Supabase (5 min)

1. Ve a: https://supabase.com
2. Haz clic en "Start your project"
3. Crea una cuenta (Gmail, GitHub, etc)
4. Nuevo proyecto:
   - Nombre: appsalon
   - Región: Tu zona horaria
   - Password: Crea una fuerte (guárdala)

5. Cuando se cree el proyecto, ve a **Settings > Database**
6. Copia la "Connection string" (URI):
   ```
   postgresql://postgres:[PASSWORD]@db.xxxxx.supabase.co:5432/postgres
   ```

7. En tu proyecto local, actualiza `.env`:
   ```env
   DB_HOST=db.xxxxx.supabase.co
   DB_PORT=5432
   DB_DRIVER=pgsql
   DB_USER=postgres
   DB_PASSWORD=[TU_PASSWORD]
   DB_NAME=postgres
   ```

## Paso 2: Crear tablas en Supabase

1. En Supabase, ve a **SQL Editor**
2. Nuevo query
3. Pega el contenido de `database/migrations/001_create_tables.sql`
4. Haz clic en "Execute"

## Paso 3: Migrar datos (MySQL → PostgreSQL)

### Opción A: Usar herramienta online (rápido)
- https://www.pgloader.io/
- https://www.migrate.guru/

### Opción B: Exportar/Importar manual
```bash
# Exportar MySQL
mysqldump -u root -p appsalon > backup.sql

# Importar en PostgreSQL (convert format primero)
# Nota: Necesitas convertir syntax MySQL → PostgreSQL
```

## Paso 4: Deploy en Render (10 min)

### 4.1 Preparar GitHub
```bash
cd tu-proyecto
git init
git add .
git commit -m "Initial commit - Ready for cloud deployment"
git remote add origin https://github.com/tu-usuario/appsalon.git
git push -u origin main
```

### 4.2 Conectar con Render
1. Ve a: https://render.com
2. Crea cuenta (GitHub)
3. Nuevo **Web Service**
4. Conectar repo: appsalon
5. Configurar:
   - **Name:** appsalon
   - **Environment:** Node (PHP) - Usa plan gratuito
   - **Build Command:** `composer install`
   - **Start Command:** `php -S 0.0.0.0:${PORT:-8080} -t public`

### 4.3 Variables de Entorno en Render
Agregar en **Environment**:
```
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_DRIVER=pgsql
DB_USER=postgres
DB_PASSWORD=[TU_PASSWORD]
DB_NAME=postgres
APP_URL=https://appsalon.onrender.com
APP_ENV=production
```

### 4.4 Deploy
Haz clic en "Create Web Service" y espera ~2-3 minutos

## Paso 5: Verificar

1. Render te da una URL: `https://appsalon.onrender.com`
2. Abre en navegador
3. Verifica que cargue sin errores

---

## ⚠️ Troubleshooting

| Problema | Solución |
|----------|----------|
| Error conexión PostgreSQL | Verifica credenciales Supabase |
| Tabla no existe | Ejecuta `001_create_tables.sql` en Supabase |
| Datos no aparecen | Migra datos desde MySQL |
| Error 502 en Render | Revisa logs: Panel de Render → Logs |

---

## 📊 Flujo completo

```
Local (MySQL)
    ↓ (Export)
PostgreSQL (Supabase)
    ↓ (Git Push)
Backend (Render)
    ↓ (HTTPS)
Cliente (Navegador)
```

---

**¿Necesitas ayuda en algún paso?** Avísame el número del paso. 🚀
