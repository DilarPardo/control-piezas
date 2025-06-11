# Control de Piezas

Este es un proyecto web desarrollado con Laravel 10 y Vue.js para gestionar proyectos, bloques, piezas y registros de producción. Está orientado a un flujo básico CRUD y permite el control visual de los datos mediante componentes Vue.

## 🚀 Requisitos

- PHP >= 8.1
- Composer
- Node.js y npm
- SQLite (o base de datos de tu preferencia)
- Laravel 10

## ⚙️ Instalación

1. Clona el repositorio:

```bash
git clone https://github.com/tuusuario/control-piezas.git
cd control-piezas
```

2. Instala las dependencias PHP:

```bash
composer install
```

3. Instala las dependencias frontend (Vue.js):

```bash
npm install
```

4. Copia el archivo `.env` y configura:

```bash
cp .env.example .env
```

Edita `.env` y configura la conexión a SQLite:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/completa/a/database/database.sqlite
```

Luego crea el archivo:

```bash
touch database/database.sqlite
```

5. Genera la clave de la app:

```bash
php artisan key:generate
```

6. Ejecuta las migraciones:

```bash
php artisan migrate
```

7. Compila los assets:

```bash
npm run dev
```

8. Inicia el servidor:

```bash
php artisan serve
```

## 📁 Estructura

- `resources/js/components`: Contiene los componentes Vue como `ProyectoComponent.vue`.
- `resources/views`: Contiene las vistas Blade que cargan Vue.
- `app/Http/Controllers`: Controladores para Proyectos, Bloques, Piezas y Registros.
- `routes/web.php`: Archivo donde se definen las rutas web.
- `database/migrations`: Migraciones para crear las tablas.

## ✅ Funcionalidades

- CRUD de proyectos
- CRUD de bloques por proyecto
- CRUD de piezas por bloque
- CRUD de registros
- Interfaz moderna con Vue.js

## 🛠 Herramientas y Tecnologías

- Laravel 10
- Vue 3
- Vite
- SQLite

## ✍️ Autor

Desarrollado por Dilar Jose Pardo Burgos.
