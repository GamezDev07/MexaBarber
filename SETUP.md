# 🚀 Setup del Proyecto AppSalón

## Requisitos
- PHP 8.0+
- Node.js 16+ (para Gulp)
- MySQL 8.0+
- Composer

## Pasos de Instalación

### 1. Clonar y configurar el proyecto
```bash
# Navegar a la carpeta del proyecto
cd AppSalon_PHP_MVC_JS_SASS_FIN

# Copiar archivo de configuración si no existe
# (ya viene como .env)

# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node
npm install
```

### 2. Configurar la Base de Datos

#### Opción A: MySQL Local
1. Abre tu cliente MySQL (MySQL Workbench, phpMyAdmin, etc.)
2. Importa el archivo `appsalon_mvc_php.sql`:
   ```sql
   source /ruta/al/archivo/appsalon_mvc_php.sql
   ```
3. Asegúrate que en `.env` tienes:
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=
   DB_NAME=appsalon_mvc_php
   ```

#### Opción B: Usar Supabase (Próximamente)
- Configuraremos en la próxima fase

### 3. Compilar archivos SASS y JS

```bash
# Compilar en modo desarrollo (watch)
npm run dev

# Compilar una sola vez (producción)
gulp build
```

Este comando:
- Compila SASS a CSS en `public/build/css/`
- Minifica JavaScript en `public/build/js/`
- Optimiza imágenes

### 4. Iniciar el servidor PHP

#### Opción A: Servidor PHP Built-in
```bash
# En Windows
php -S localhost:8000 -t public

# En macOS/Linux
php -S 127.0.0.1:8000 -t public
```

#### Opción B: Usar XAMPP/WAMP
- Coloca la carpeta en `htdocs` (XAMPP) o `www` (WAMP)
- Accede a `http://localhost/AppSalon_PHP_MVC_JS_SASS_FIN/public`

### 5. Acceder a la aplicación

- **URL**: `http://localhost:8000`
- **Admin**: Se configura desde login

## 🔐 Credenciales de Prueba

Después de importar la BD, usa las siguientes credenciales demo (si existen en la BD):
- Usuario: demo@appsalon.com
- Contraseña: password123

*Nota: Crea tu propia cuenta desde el formulario de registro*

## 📁 Estructura del Proyecto

```
├── public/              # Carpeta pública (punto de entrada)
│   ├── index.php       # Archivo principal
│   └── build/          # Archivos compilados (genera Gulp)
├── src/                # Archivos fuente
│   ├── js/             # JavaScript
│   └── scss/           # Estilos SASS
├── includes/           # Configuración y funciones
├── models/             # Modelos de datos
├── controllers/        # Controladores
├── views/              # Vistas HTML
├── classes/            # Clases auxiliares
└── .env                # Variables de entorno (crear)
```

## 🚨 Troubleshooting

### "Error: No se pudo conectar a MySQL"
- Verifica que MySQL esté corriendo
- Comprueba credenciales en `.env`
- Asegúrate de tener la BD `appsalon_mvc_php` creada

### "PHP command not found"
- En Windows, agrega PHP a variables de entorno
- O usa la ruta completa a php.exe

### "SASS compilation error"
- Ejecuta `npm install` primero
- Intenta `npm run dev` nuevamente

### Puerto 8000 ocupado
- Usa otro puerto: `php -S localhost:3000 -t public`
- O mata el proceso: `lsof -i :8000` (macOS/Linux)

## 📝 Variables de Entorno (.env)

Ver archivo `.env.example` para todas las variables disponibles.

## 🔄 Próximas Fases

- ✅ Fase 1: Setup local actual
- ⏳ Fase 2: Migración a Laravel (opcional)
- ⏳ Fase 3: PostgreSQL + Supabase
- ⏳ Fase 4: Integración Stripe
- ⏳ Fase 5: Deploy en Render + Vercel

## 📞 Soporte

Para problemas específicos durante el setup, revisa los logs en:
- Navegador: Abre DevTools (F12) → Console
- PHP: Revisa `php_error_log`

---
¡Listo para ver tu aplicación funcionando! 🎉
