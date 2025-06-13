# Etapa 1: Build de frontend con Node
FROM node:20 AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . ./
RUN npm run build

# Crear base de datos si no existe
RUN mkdir -p /data && touch /data/database.sqlite && \
    chown -R www-data:www-data /data && chmod 664 /data/database.sqlite


# Etapa 2: Configuración final de Laravel + Apache
FROM php:8.2-apache

WORKDIR /var/www/html

# Requisitos del sistema
RUN apt-get update && apt-get install -y \
    git curl libzip-dev unzip sqlite3 libsqlite3-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar todo desde la build
COPY . .

# Copiar build del frontend
COPY --from=frontend /app/public/build public/build

# Configuración Apache
RUN a2enmod rewrite

# Permisos y optimización Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache \
    && composer install --no-dev --optimize-autoloader \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan cache:clear \
    && php artisan migrate --force || true

# Puerto por defecto de Apache
EXPOSE 80
