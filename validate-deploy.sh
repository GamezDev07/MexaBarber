#!/bin/bash

# Script de validación previa al deploy en Render
# Asegura que todo está configurado correctamente antes de hacer push

echo "🔍 Validando configuración para deploy en Render..."
echo ""

ERRORS=0
WARNINGS=0

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para errores
error() {
    echo -e "${RED}❌ ERROR: $1${NC}"
    ((ERRORS++))
}

# Función para advertencias
warning() {
    echo -e "${YELLOW}⚠️  WARNING: $1${NC}"
    ((WARNINGS++))
}

# Función para éxito
success() {
    echo -e "${GREEN}✅ $1${NC}"
}

echo "📋 Verificando archivos necesarios..."

# Verificar Dockerfile
if [ -f "Dockerfile" ]; then
    success "Dockerfile existe"
else
    error "Dockerfile no encontrado en raíz"
fi

# Verificar .dockerignore
if [ -f ".dockerignore" ]; then
    success ".dockerignore existe"
else
    warning ".dockerignore no encontrado"
fi

# Verificar render.yaml
if [ -f "render.yaml" ]; then
    success "render.yaml existe"
else
    error "render.yaml no encontrado en raíz"
fi

# Verificar .env.example
if [ -f ".env.example" ]; then
    success ".env.example existe"
else
    error ".env.example no encontrado"
fi

echo ""
echo "📦 Verificando configurationes..."

# Verificar package.json
if [ -f "package.json" ]; then
    success "package.json existe"
    if grep -q '"dev"' package.json; then
        success "Script 'dev' configurado en package.json"
    else
        warning "Script 'dev' no encontrado en package.json"
    fi
else
    error "package.json no encontrado"
fi

# Verificar composer.json
if [ -f "composer.json" ]; then
    success "composer.json existe"
else
    error "composer.json no encontrado"
fi

# Verificar .env NO esté commiteado
if [ -f ".env" ]; then
    if git ls-files --error-unmatch .env >/dev/null 2>&1; then
        error ".env está en git (debería estar en .gitignore)"
    else
        success ".env existe pero no está en git ✓"
    fi
fi

echo ""
echo "🔒 Verificando secretos..."

# Verificar que .env.example no tenga valores reales
if [ -f ".env.example" ]; then
    if grep -q "password\|senha\|secret" .env.example | grep -v "=\["; then
        warning ".env.example podría contener valores sensibles"
    else
        success ".env.example no contiene secretos expuestos ✓"
    fi
fi

echo ""
echo "📂 Verificando estructura..."

# Verificar directorios clave
DIRS=("controllers" "models" "public" "views" "includes" "src")
for dir in "${DIRS[@]}"; do
    if [ -d "$dir" ]; then
        success "Directorio $dir existe"
    else
        warning "Directorio $dir no encontrado"
    fi
done

echo ""
echo "🐳 Verificando Docker..."

# Verificar si Docker está instalado (para testing local)
if command -v docker &> /dev/null; then
    success "Docker está instalado"
    DOCKER_AVAILABLE=true
else
    warning "Docker no está instalado (necesario para testing local)"
    DOCKER_AVAILABLE=false
fi

echo ""
echo "🌿 Verificando Git..."

# Verificar que esté en repositorio git
if [ -d ".git" ]; then
    success "Repositorio Git inicializado"
    
    # Verificar rama
    BRANCH=$(git rev-parse --abbrev-ref HEAD)
    success "Rama actual: $BRANCH"
    
    # Verificar si hay cambios sin commitar
    if git diff-index --quiet HEAD --; then
        success "No hay cambios sin commitar"
    else
        warning "Tienes cambios sin commitar. Considera hacer git add/commit"
    fi
else
    error "No estás en un repositorio Git. Ejecuta: git init"
fi

echo ""
echo "=================================================="
echo "📊 Resumen de Validación"
echo "=================================================="

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✅ ¡TODO LISTO PARA DEPLOY!${NC}"
    echo ""
    echo "Próximos pasos:"
    echo "1. git push origin main"
    echo "2. Ve a https://render.com/dashboard"
    echo "3. Crea tu Web Service conectado a GitHub"
    echo ""
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️  $WARNINGS advertencia(s) encontrada(s)${NC}"
    echo -e "${GREEN}Puedes proceder al deploy, pero revisa las advertencias.${NC}"
    exit 0
else
    echo -e "${RED}❌ $ERRORS error(es) encontrado(s)${NC}"
    echo -e "${RED}Debes resolver estos errores antes de hacer deploy.${NC}"
    exit 1
fi
