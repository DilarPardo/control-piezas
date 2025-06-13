# Imagen base con PHP, Composer y extensiones necesarias
FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    zip \
    npm \
    nodejs

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Crear directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto al contenedor
COPY . .

# Instalar dependencias PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instalar dependencias JS y compilar assets (para Vue)
RUN npm install && npm run build

# Generar key de Laravel
RUN php artisan key:generate

# Crear base de datos SQLite si no existe
RUN mkdir -p database && touch database/database.sqlite

# Dar permisos necesarios
RUN chmod -R 777 storage bootstrap/cache database

# Puerto que Laravel usará
EXPOSE 10000

# Comando que ejecutará Laravel al iniciar el contenedor
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
