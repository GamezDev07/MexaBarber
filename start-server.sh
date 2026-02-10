#!/bin/bash

# Script para iniciar el servidor PHP en macOS/Linux

echo ""
echo "========================================"
echo "   AppSalon - Servidor PHP Local"
echo "========================================"
echo ""

# Verificar si PHP está disponible
if ! command -v php &> /dev/null; then
    echo "[ERROR] PHP no está instalado"
    exit 1
fi

# Verificar si composer.lock existe
if [ ! -f composer.lock ]; then
    echo "[AVISO] composer.lock no encontrado"
    echo "Ejecutando: composer install"
    composer install
    if [ $? -ne 0 ]; then
        echo "[ERROR] Falló composer install"
        exit 1
    fi
fi

# Verificar si node_modules existe
if [ ! -d node_modules ]; then
    echo "[AVISO] node_modules no encontrado"
    echo "Ejecutando: npm install"
    npm install
    if [ $? -ne 0 ]; then
        echo "[ERROR] Falló npm install"
        exit 1
    fi
fi

# Verificar conectividad a MySQL
echo ""
echo "Verificando conexión a MySQL..."
php -r "mysqli_connect('localhost', 'root', '', 'appsalon_mvc_php') or die('[ERROR] No se pudo conectar a MySQL');" 2>/dev/null
if [ $? -ne 0 ]; then
    echo "[ERROR] No se puede conectar a MySQL"
    echo ""
    echo "Asegúrate de:"
    echo "1. Que MySQL esté ejecutándose"
    echo "2. Que la BD 'appsalon_mvc_php' exista"
    echo "3. Que las credenciales en .env sean correctas"
    exit 1
fi
echo "[OK] Conexión a MySQL exitosa"

# Iniciar servidor
echo ""
echo "========================================"
echo "Iniciando servidor en http://localhost:8000"
echo "Presiona CTRL+C para detener"
echo "========================================"
echo ""

php -S 127.0.0.1:8000 -t public
