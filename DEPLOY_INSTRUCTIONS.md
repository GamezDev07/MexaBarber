# 🚀 Instrucciones de Deploy a Render.com

## ⚠️ IMPORTANTE: Ejecuta ESTO PRIMERO en Supabase

**ANTES** de que la app en Render funcione, necesitas insertar los barberos en Supabase.

### Paso 1: Ir a Supabase SQL Editor

1. Abre tu dashboard de Supabase: https://supabase.com/dashboard
2. Selecciona tu proyecto
3. Ve a **SQL Editor** (en el menú lateral)

### Paso 2: Ejecutar insert_barberos_supabase.sql

Copia y pega el contenido completo de `insert_barberos_supabase.sql` en el SQL Editor y presiona **"Run"**.

Esto creará:
- 3 usuarios barberos (Raúl, Juan, Pedro)
- 3 registros en la tabla `barberos`

**Verifica que funcionó:**
```sql
SELECT b.id, CONCAT(u.nombre, ' ', u.apellido) as nombre, b.especialidad
FROM barberos b
JOIN usuarios u ON u.id = b.usuario_id
WHERE b.barbershop_id = 1;
```

Deberías ver los 3 barberos.

---

## 📦 Deploy a Render.com

### Variables de Entorno en Render

Asegúrate de que Render tiene estas variables configuradas:

```
DB_HOST=db.wutfzswiynhauqrhqngi.supabase.co
DB_PORT=5432
DB_DRIVER=pgsql
DB_USER=postgres
DB_PASSWORD=[tu_password_supabase]
DB_NAME=postgres
```

### Push del Código

```bash
git add .
git commit -m "Add barber insertion scripts and database setup"
git push origin main
```

Render detectará el push automáticamente y deployará.

---

## ✅ Verificación Post-Deploy

1. Ve a tu URL de Render: `https://appsalon.onrender.com`
2. Inicia sesión
3. Ve a "Nueva Cita"
4. **Paso 2: Deberías ver los 3 barberos**
5. **Paso 4: Deberías ver los 3 métodos de pago**

---

## 🐛 Troubleshooting

### No veo los barberos en Render

**Causa probable**: No ejecutaste `insert_barberos_supabase.sql`

**Solución**: 
1. Ve a Supabase SQL Editor
2. Ejecuta el script de inserción
3. Espera 1-2 minutos
4. Refresca la página en Render

### Error de conexión a BD

**Verifica** en Render Dashboard → Environment → Variables:
- `DB_DRIVER` debe ser `pgsql` (NO `mysql`)
- `DB_HOST` debe apuntar a Supabase
- `DB_PASSWORD` debe ser correcto

---

## 📝 Resumen

1. ✅ **PRIMERO**: Ejecutar `insert_barberos_supabase.sql` en Supabase
2. ✅ **SEGUNDO**: Verificar variables de entorno en Render
3. ✅ **TERCERO**: Push del código (ya lo haremos ahora)
4. ✅ **CUARTO**: Esperar deploy automático de Render
5. ✅ **QUINTO**: Probar en https://appsalon.onrender.com
