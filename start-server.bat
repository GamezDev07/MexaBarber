@echo off
REM Script para iniciar el servidor PHP en Windows

echo.
echo ========================================
echo    AppSalon - Servidor PHP Local
echo ========================================
echo.

REM Verificar si PHP está disponible
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] PHP no está instalado o no está en PATH
    echo.
    echo Para agregar PHP a PATH:
    echo 1. Ve a: Panel de Control > Sistema > Variables de Entorno
    echo 2. Busca PATH en variables del sistema
    echo 3. Haz clic en Editar
    echo 4. Agrega la ruta a tu instalación PHP (ej: C:\PHP\)
    echo.
    pause
    exit /b 1
)

REM Verificar si composer.lock existe
if not exist composer.lock (
    echo [AVISO] composer.lock no encontrado
    echo Ejecutando: composer install
    composer install
    if %errorlevel% neq 0 (
        echo [ERROR] Falló composer install
        pause
        exit /b 1
    )
)

REM Verificar si node_modules existe
if not exist node_modules (
    echo [AVISO] node_modules no encontrado
    echo Ejecutando: npm install
    call npm install
    if %errorlevel% neq 0 (
        echo [ERROR] Falló npm install
        pause
        exit /b 1
    )
)

REM Verificar conectividad a MySQL
echo.
echo Verificando conexión a MySQL...
php -r "mysqli_connect('localhost', 'root', '', 'appsalon_mvc_php') or die('[ERROR] No se peut conectar a MySQL');" >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] No se puede conectar a MySQL
    echo.
    echo Asegúrate de:
    echo 1. Que MySQL esté ejecutándose
    echo 2. Que la BD 'appsalon_mvc_php' exista
    echo 3. Que las credenciales en .env sean correctas
    echo.
    pause
    exit /b 1
)
echo [OK] Conexión a MySQL exitosa

REM Iniciar servidor
echo.
echo ========================================
echo Iniciando servidor en http://localhost:8000
echo Presiona CTRL+C para detener
echo ========================================
echo.

php -S localhost:8000 -t public

pause
