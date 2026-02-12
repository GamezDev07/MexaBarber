# Multi-stage build para AppSalon
# Stage 1: Compilar assets (SCSS, JS)
FROM node:20-alpine AS assets

WORKDIR /app

# Copiar package.json y package-lock.json
COPY package.json package-lock.json* ./

# Instalar dependencias de Node
RUN npm install --legacy-peer-deps

# Copiar archivos SCSS y JS
COPY src/ ./src/
COPY gulpfile.js ./

# Compilar SCSS y JS
RUN npm run dev 2>/dev/null || npx gulp

# ============================================
# Stage 2: Preparar aplicación PHP
# ============================================
FROM php:8.2-alpine

WORKDIR /app

# Instalar extensiones PHP necesarias
RUN apk add --no-cache \
    postgresql-dev \
    libpq-dev \
    linux-headers \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    && apk del postgresql-dev linux-headers

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos de la aplicación
COPY . .

# Copiar assets compilados desde stage 1
COPY --from=assets /app/public/build /app/public/build

# Instalar dependencias PHP
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Crear usuario no-root por seguridad
RUN addgroup -g 1000 www && adduser -D -u 1000 -G www www
USER www

# Exponemos el puerto
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php -r "if (file_exists('public/index.php')) { exit(0); } else { exit(1); }"

# Comando de inicio
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
