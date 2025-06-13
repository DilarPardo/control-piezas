FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    npm \
    nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Crear directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Crear archivo .env si no existe
RUN cp .env.example .env

# Instalar dependencias PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Dar permisos necesarios antes de ejecutar artisan
RUN chmod -R 777 storage bootstrap/cache

# Generar clave de aplicación Laravel
RUN php artisan key:generate

# Instalar dependencias JS y compilar frontend (Vue, React, etc.)
RUN npm install && npm run build

# Crear base de datos SQLite si no existe
RUN mkdir -p database && touch database/database.sqlite && chmod -R 777 database

# Exponer puerto
EXPOSE 10000

# Comando para iniciar el servidor de desarrollo Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
