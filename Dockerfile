# AppSalon - PHP Runtime
# Assets (SCSS/JS) se compilaron localmente con: npm run dev
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

# Copiar proyecto completo (incluyendo public/build compilado)
COPY . .

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
