# 🎯 Quick-Start: Deploy AppSalon en Render.com

## ¿Qué se creó?

```
✅ Dockerfile               - Multi-stage build para producción
✅ .dockerignore            - Optimización de imagen
✅ render.yaml              - Configuración Render automatizada
✅ RENDER_SETUP.md          - Guía completa detallada
✅ DOCKER_SETUP.md          - Guía rápida de Docker
✅ DOCKER_DEPLOY_SUMMARY.md - Resumen técnico y troubleshooting
✅ validate-deploy.sh       - Validador pre-push (Linux/Mac)
✅ validate-deploy.ps1      - Validador pre-push (Windows)
✅ .env.example             - Variables de entorno actualizadas
```

---

## 🚀 Plan de Acción (15 minutos)

### Paso 1: Compilar Assets (2 min)
```bash
npm install
npm run dev
```
✅ Esto crea: `public/build/css/app.css` y `public/build/js/app.js`

---

### Paso 2: Validar Proyecto (1 min)
**En Windows:**
```powershell
powershell -ExecutionPolicy Bypass -File validate-deploy.ps1
```

**En Linux/Mac:**
```bash
bash validate-deploy.sh
```

✅ Debe mostrar: `✅ ¡TODO LISTO PARA DEPLOY!`

---

### Paso 3: Crear Repo GitHub (3 min)
```bash
# Ir a https://github.com/new y crear repo 'appsalon'

git add .
git commit -m "feat: AppSalon listo para Render con Docker"
git remote add origin https://github.com/TU_USUARIO/appsalon.git
git branch -M main
git push -u origin main
```

---

### Paso 4: Crear Supabase DB (3 min)
1. Ve a https://supabase.com → Sign up/Login
2. **Create New Project:**
   - Name: `appsalon`
   - Password: `[Crea una fuerte]` ← GUARDA ESTO
   - Region: Tu zona horaria
3. Espera a que se cree (~5 min)

---

### Paso 5: Crear Tablas en Supabase (2 min)
1. En Supabase → **SQL Editor** → **New Query**
2. Pega todo el contenido de:
   ```
   database/migrations/001_create_tables.sql
   ```
3. Click **Execute** ✅

---

### Paso 6: Deploy en Render (3 min)
1. Ve a https://render.com/dashboard → **New +** → **Web Service**
2. **Connect GitHub:**
   - Selecciona tu repo `appsalon`
   - Click **Connect**
3. **Configurar:**
   - Name: `appsalon`
   - Region: La más cercana
   - Runtime: Docker (automático)
4. **Agregar variables:**

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
MAIL_USERNAME=[Opcional]
MAIL_PASSWORD=[Opcional]
MAIL_FROM=cuentas@appsalon.com
```

5. Click **Create Web Service**
6. ⏳ Espera ~3-5 minutos
7. ✅ Abre la URL que Render genera

---

## 📋 Variables Supabase (Dónde obtenerlas)

En Supabase Dashboard:
1. Click en tu proyecto
2. **Settings** → **Database**
3. Copia estos valores:

```
Connection String (Full URL):
postgresql://postgres:PASSWORD@db.XXXXX.supabase.co:5432/postgres

De aquí extrae:
- DB_HOST = db.XXXXX.supabase.co
- DB_PASSWORD = PASSWORD (entre : y @)
```

---

## ✔️ Checklist Rápido

- [ ] `npm run dev` compiló SCSS/JS
- [ ] `validate-deploy.ps1` o `.sh` pasó
- [ ] `.env` NO está en git ✓
- [ ] Proyecto en GitHub (rama main)
- [ ] Supabase cuenta creada
- [ ] Tablas creadas en Supabase SQL
- [ ] Variables copiadas correctamente
- [ ] Web Service creado en Render
- [ ] Status en Render es **Live**
- [ ] Puedo abrir la URL en navegador ✅

---

## 🎥 Resultado Esperado

```
https://appsalon.onrender.com
↓
✅ Carga la página de login
✅ Puedo navegar la app
✅ Se conecta a Supabase
✅ Todo funcionando en producción
```

---

## 🐛 Si Algo Falla...

### ❌ Render says "Build failed"
→ Click **Logs** en Render dashboard
→ Lee el error rojo
→ 95% es por: variables de entorno incompletas

### ❌ "Cannot connect to database"
→ Verifica `DB_PASSWORD` en Render
→ Copia exacto de Supabase sin espacios

### ❌ "Página en blanco o 502"
→ Espera 60 segundos
→ Recarga F5
→ Revisa **Logs** en Render

### ❌ "Table 'usuarios' doesn't exist"
→ Vuelve a Supabase SQL Editor
→ Ejecuta nuevamente: `001_create_tables.sql`

---

## 🔄 Después del Deploy

Cada vez que hagas cambios:

```bash
# 1. Si cambiaste SCSS/JS:
npm run dev

# 2. Commit
git add .
git commit -m "tú: descripción"
git push origin main

# 3. Render se redeploya automáticamente
# (toma ~2-3 minutos)
```

---

## 📚 Documentos de Referencia

| Documento | Para Qué |
|-----------|----------|
| [RENDER_SETUP.md](./RENDER_SETUP.md) | Guía paso-a-paso completa |
| [DOCKER_SETUP.md](./DOCKER_SETUP.md) | Desarrollo local con Docker |
| [Dockerfile](./Dockerfile) | Detalles técnicos del build |
| [render.yaml](./render.yaml) | Configuración Render |

---

## 💡 Pro Tips

1. **Antes de cada push, valida:**
   ```bash
   validate-deploy.ps1  # Windows
   validate-deploy.sh   # Linux/Mac
   ```

2. **Para testear localmente:**
   ```bash
   docker build -t appsalon .
   docker run -p 8080:8080 -e DB_HOST=... appsalon
   ```

3. **Ver logs sin dejar el terminal:**
   ```bash
   # Si lo corres en local
   docker logs -f nombre-contenedor
   ```

4. **Rebuild assets cuando sea necesario:**
   ```bash
   rm -rf public/build
   npm install
   npm run dev
   ```

---

## ⚠️ Seguridad

- ✅ **Never commit `.env`** (está en .gitignore)
- ✅ **Use strong passwords** en DB_PASSWORD
- ✅ **APP_DEBUG=false** en producción
- ✅ **Variables sensibles** solo en Render dashboard

---

## 🎉 ¡Listo!

Si completaste todos los pasos, tu app está:
- ✅ Deployada en producción
- ✅ Corriendo en HTTPS
- ✅ Conectada a PostgreSQL
- ✅ En Docker, escalable y segura

**URL:** https://appsalon.onrender.com

¿Preguntas? Lee [RENDER_SETUP.md](./RENDER_SETUP.md) para más detalles.
