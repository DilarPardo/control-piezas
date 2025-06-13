# Etapa 1: Build de frontend con Node
FROM node:20 AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . ./
RUN npm run build

# Crear base de datos SQLite si no existe
RUN mkdir -p /data && touch /data/database.sqlite && \
    chown -R www-data:www-data /data && chmod 664 /data/database.sqlite


# Etapa 2: Laravel con Apache
FROM php:8.2-apache

WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libzip-dev unzip sqlite3 libsqlite3-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite zip gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar código fuente
COPY . .

# Copiar build del frontend generado
COPY --from=frontend /app/public/build public/build

# Habilitar rewrite para Laravel
RUN a2enmod rewrite

# Instalar dependencias de PHP (Laravel)
RUN composer install --no-dev --optimize-autoloader

# Permisos y caches Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan cache:clear \
    && php artisan migrate --force || true

EXPOSE 80
