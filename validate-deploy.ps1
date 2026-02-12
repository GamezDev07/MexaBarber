# Script de validación previa al deploy en Render (PowerShell)
# Asegura que todo está configurado correctamente antes de hacer push

Write-Host "🔍 Validando configuración para deploy en Render..." -ForegroundColor Cyan
Write-Host ""

$ERRORS = 0
$WARNINGS = 0

function Write-Error-Custom {
    param([string]$Message)
    Write-Host "❌ ERROR: $Message" -ForegroundColor Red
    $script:ERRORS++
}

function Write-Warning-Custom {
    param([string]$Message)
    Write-Host "⚠️  WARNING: $Message" -ForegroundColor Yellow
    $script:WARNINGS++
}

function Write-Success {
    param([string]$Message)
    Write-Host "✅ $Message" -ForegroundColor Green
}

Write-Host "📋 Verificando archivos necesarios..." -ForegroundColor Cyan

# Verificar Dockerfile
if (Test-Path "Dockerfile") {
    Write-Success "Dockerfile existe"
} else {
    Write-Error-Custom "Dockerfile no encontrado en raíz"
}

# Verificar .dockerignore
if (Test-Path ".dockerignore") {
    Write-Success ".dockerignore existe"
} else {
    Write-Warning-Custom ".dockerignore no encontrado"
}

# Verificar render.yaml
if (Test-Path "render.yaml") {
    Write-Success "render.yaml existe"
} else {
    Write-Error-Custom "render.yaml no encontrado en raíz"
}

# Verificar .env.example
if (Test-Path ".env.example") {
    Write-Success ".env.example existe"
} else {
    Write-Error-Custom ".env.example no encontrado"
}

Write-Host ""
Write-Host "📦 Verificando configuraciones..." -ForegroundColor Cyan

# Verificar package.json
if (Test-Path "package.json") {
    Write-Success "package.json existe"
    $content = Get-Content package.json | ConvertFrom-Json
    if ($content.scripts.dev) {
        Write-Success "Script 'dev' configurado en package.json"
    } else {
        Write-Warning-Custom "Script 'dev' no encontrado en package.json"
    }
} else {
    Write-Error-Custom "package.json no encontrado"
}

# Verificar composer.json
if (Test-Path "composer.json") {
    Write-Success "composer.json existe"
} else {
    Write-Error-Custom "composer.json no encontrado"
}

# Verificar .env NO esté commiteado
if (Test-Path ".env") {
    try {
        $gitCheck = & git ls-files --error-unmatch .env 2>$null
        if ($?) {
            Write-Error-Custom ".env está en git (debería estar en .gitignore)"
        }
    } catch {
        Write-Success ".env existe pero no está en git ✓"
    }
}

Write-Host ""
Write-Host "🔒 Verificando secretos..." -ForegroundColor Cyan

# Verificar que .env.example no tenga valores reales
if (Test-Path ".env.example") {
    $envContent = Get-Content .env.example -Raw
    if ($envContent -match '[a-zA-Z0-9]{8,}(?<!tu_|your_|example)' -and -not ($envContent -match '\[|\]')) {
        Write-Warning-Custom ".env.example podría contener valores sensibles reales"
    } else {
        Write-Success ".env.example no contiene secretos expuestos ✓"
    }
}

Write-Host ""
Write-Host "📂 Verificando estructura..." -ForegroundColor Cyan

$dirs = @("controllers", "models", "public", "views", "includes", "src")
foreach ($dir in $dirs) {
    if (Test-Path $dir) {
        Write-Success "Directorio $dir existe"
    } else {
        Write-Warning-Custom "Directorio $dir no encontrado"
    }
}

Write-Host ""
Write-Host "🐳 Verificando Docker..." -ForegroundColor Cyan

# Verificar si Docker está instalado
$docker = Get-Command docker -ErrorAction SilentlyContinue
if ($docker) {
    Write-Success "Docker está instalado"
} else {
    Write-Warning-Custom "Docker no está instalado (necesario para testing local)"
}

Write-Host ""
Write-Host "🌿 Verificando Git..." -ForegroundColor Cyan

# Verificar que esté en repositorio git
if (Test-Path ".git") {
    Write-Success "Repositorio Git inicializado"
    
    # Verificar rama
    $branch = & git rev-parse --abbrev-ref HEAD
    Write-Success "Rama actual: $branch"
    
    # Verificar si hay cambios sin commitar
    try {
        $status = & git status --porcelain 2>$null
        if ([string]::IsNullOrEmpty($status)) {
            Write-Success "No hay cambios sin commitar"
        } else {
            Write-Warning-Custom "Tienes cambios sin commitar. Considera hacer git add/commit"
        }
    } catch {
        Write-Warning-Custom "No se pudo verificar el status de git"
    }
} else {
    Write-Error-Custom "No estás en un repositorio Git. Ejecuta: git init"
}

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "📊 Resumen de Validación" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

if ($ERRORS -eq 0 -and $WARNINGS -eq 0) {
    Write-Host "✅ ¡TODO LISTO PARA DEPLOY!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Próximos pasos:" -ForegroundColor Green
    Write-Host "1. git push origin main"
    Write-Host "2. Ve a https://render.com/dashboard"
    Write-Host "3. Crea tu Web Service conectado a GitHub"
    Write-Host ""
} elseif ($ERRORS -eq 0) {
    Write-Host "⚠️  $WARNINGS advertencia(s) encontrada(s)" -ForegroundColor Yellow
    Write-Host "Puedes proceder al deploy, pero revisa las advertencias." -ForegroundColor Green
} else {
    Write-Host "❌ $ERRORS error(es) encontrado(s)" -ForegroundColor Red
    Write-Host "Debes resolver estos errores antes de hacer deploy." -ForegroundColor Red
}
