# Imagen base oficial de PHP con soporte para CLI
FROM php:8.2-cli

# Variables de entorno para evitar interacciones
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    DEBIAN_FRONTEND=noninteractive

# Instalar extensiones de sistema necesarias
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    sqlite3 \
    libsqlite3-dev \
    npm \
    nodejs \
    && docker-php-ext-install pdo pdo_sqlite gd zip

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Crear directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto al contenedor
COPY . .

# Instalar dependencias PHP (Laravel, Excel, etc.)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instalar dependencias JS y compilar frontend (Vue, React, etc.)
RUN npm install && npm run build

# Generar clave de aplicación Laravel
RUN php artisan key:generate

# Crear base de datos SQLite si no existe
RUN mkdir -p database && touch database/database.sqlite

# Dar permisos a las carpetas necesarias
RUN chmod -R 777 storage bootstrap/cache database

# Puerto expuesto (puedes cambiarlo si Laravel usa otro)
EXPOSE 10000

# Comando para ejecutar Laravel al iniciar el contenedor
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
