@echo off
REM Script: Preparar proyecto para Render + Supabase
REM Uso: prepare-deploy.bat

setlocal enabledelayedexpansion

echo Preparando proyecto para deploy...
echo.

REM Crear .env.production si no existe
if not exist ".env.production" (
    echo Creando .env.production...
    copy .env.example .env.production
    echo IMPORTANTE: Actualiza .env.production con credenciales de Supabase
)

echo.
echo Instalando dependencias PHP...
composer install --optimize-autoloader --no-dev

echo.
echo Proyecto listo para deploy!
echo.
echo Proximos pasos:
echo 1. git add .
echo 2. git commit -m "Prepare for cloud deployment"
echo 3. git push origin main
echo 4. Conectar con Render y seleccionar rama main
echo.

pause
