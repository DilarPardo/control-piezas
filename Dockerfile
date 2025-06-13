# Etapa 1: Solo instalar dependencias Node
FROM node:20 AS frontend

WORKDIR /app
COPY package*.json ./
RUN npm install

# Etapa 2: Laravel + Apache
FROM php:8.2-apache

WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libzip-dev unzip sqlite3 libsqlite3-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite zip gd


# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . .

# Copiar node_modules de la etapa frontend
COPY --from=frontend /app/node_modules ./node_modules

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Build de assets con acceso completo a vendor y node_modules
RUN npm run build

# Configuración Apache y Laravel
RUN a2enmod rewrite && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 storage bootstrap/cache && \
    mkdir -p /data && touch /data/database.sqlite && \
    chown -R www-data:www-data /data && chmod 664 /data/database.sqlite && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan cache:clear && \
    php artisan migrate --force || true

EXPOSE 80
