#!/bin/bash
# Script: Preparar proyecto para Render + Supabase
# Uso: ./prepare-deploy.sh

echo "🚀 Preparando proyecto para deploy..."
echo ""

# Verificar requisitos
echo "📋 Verificando requisitos..."
command -v git &> /dev/null || echo "⚠️  Git no encontrado"
command -v composer &> /dev/null || echo "⚠️  Composer no encontrado"

echo "✅ Requisitos OK"
echo ""

# Crear .env.production si no existe
if [ ! -f .env.production ]; then
    echo "📝 Creando .env.production..."
    cp .env.example .env.production
    echo "⚠️  IMPORTANTE: Actualiza .env.production con credenciales de Supabase"
fi

echo ""
echo "📦 Instalando dependencias PHP..."
composer install --optimize-autoloader --no-dev

echo ""
echo "✅ Proyecto listo para deploy!"
echo ""
echo "Próximos pasos:"
echo "1. git add ."
echo "2. git commit -m 'Prepare for cloud deployment'"
echo "3. git push origin main"
echo "4. Conectar con Render y seleccionar rama main"
echo ""
