# Sitio web del Parque Brincaeste para compra de entradas y boletos

Esta hecha con React ,laravel ,Inertia y Tailwind

### Screenshot 🖥️️

![screenshot-1](/public/screenshot/img-1.png)

![screenshot-2](/public/screenshot/img-2.png)

![screenshot-3](/public/screenshot/img-3.jpg)

![screenshot-4](/public/screenshot/img-4.png)

![screenshot-5](/public/screenshot/img-5.png)

## Cómo Levantar el Entorno de Desarrollo con Docker
Este proyecto utiliza Docker para gestionar el entorno de desarrollo, lo que asegura consistencia y evita conflictos de versiones de PHP, Base de Datos, etc. en tu máquina local.

### Requisitos

Asegúrate de tener instalados en tu máquina:

* **Docker Desktop**: Necesario para ejecutar los contenedores Docker.
    * [Descargar Docker Desktop](https://www.docker.com/products/docker-desktop/)
* **Node.js y npm (o Yarn)**: Para gestionar las dependencias de frontend y compilar los assets.
    * [Descargar Node.js](https://nodejs.org/es/download/) (incluye npm)

### Configuración Inicial

1.  **Clona el Repositorio:**
2.  **Configura el Archivo `.env`:**
    Copia el archivo de ejemplo `.env.example` y renómbralo a `.env`. Luego, ábrelo y asegúrate de que las variables de conexión a la base de datos y Redis apunten a los servicios Docker.

    ```bash
    cp .env.example .env
    ```

    **Verifica estas líneas en tu `.env`:**
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=ticket # O el nombre de tu DB
    DB_USERNAME=laravel # O tu usuario de DB
    DB_PASSWORD=1234 # O tu contraseña de DB

    REDIS_HOST=redis
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    ```
    *(Asegúrate de que `DB_PASSWORD` coincida con `MARIADB_PASSWORD` en `docker-compose.yml`)*

3.  **Obtén tu UID y GID para Docker (Importante para Permisos):**
    Para evitar problemas de permisos con Docker y las carpetas `storage`/`bootstrap/cache`, tu contenedor PHP-FPM necesita ejecutarse con tu ID de usuario. Abre tu terminal (no dentro de Docker) y ejecuta:
    ```bash
    id -u  # Anota este número (ej. 1000)
    id -g  # Anota este número (ej. 1000)
    ```
    Luego, **edita el archivo `Dockerfile`** en la raíz de tu proyecto y **reemplaza `1000` con tus números obtenidos** en las siguientes líneas:
    ```dockerfile
    ARG PUID=1000 # <--- CAMBIA ESTE NÚMERO
    ARG PGID=1000 # <--- CAMBIA ESTE NÚMERO
    ```

### Levantando los Servicios Docker

En la raíz de tu proyecto, ejecuta los siguientes comandos en tu terminal:

1.  **Construye y levanta todos los contenedores:**
    La opción `--build` es crucial la primera vez o si cambias el `Dockerfile`. La opción `-d` los ejecuta en segundo plano.
    ```bash
    docker-compose up --build -d
    ```

2.  **Instala las dependencias de PHP con Composer (dentro del contenedor):**
    ```bash
    docker-compose exec app composer install
    ```

3.  **Genera la clave de la aplicación Laravel (dentro del contenedor):**
    ```bash
    docker-compose exec app php artisan key:generate
    ```

4.  **Ejecuta las migraciones de la base de datos (dentro del contenedor):**
    Esto creará las tablas de la base de datos.
    ```bash
    docker-compose exec app php artisan migrate
    ```

5.  **Instala y compila las dependencias de Frontend (en tu máquina host):**
    ```bash
    npm install
    npm run dev # O 'npm run build' para producción, o 'npm run watch'
    ```
    Mantén `npm run dev` corriendo mientras desarrollas para ver los cambios de frontend al instante.

### Accede a la Aplicación

Una vez que todos los pasos anteriores se completen, abre tu navegador y visita:

* `http://localhost`

---

### Comandos Útiles de Docker Compose

* `docker-compose ps`: Muestra el estado de tus servicios.
* `docker-compose stop`: Detiene los servicios sin eliminarlos.
* `docker-compose start`: Inicia los servicios detenidos.
* `docker-compose down`: Detiene y elimina los contenedores (los volúmenes de datos persisten por defecto).
* `docker-compose down -v`: **Detiene y elimina contenedores Y volúmenes de datos.** Usa esto con precaución, ya que borrará tus datos de base de datos y Redis. Útil para un reinicio limpio si hay problemas de datos o permisos.
* `docker-compose logs -f [nombre_servicio]`: Muestra los logs en tiempo real de un servicio (ej. `app`, `nginx`, `db`, `redis`).
* `docker-compose exec [nombre_servicio] bash`: Abre una terminal interactiva dentro de un contenedor (ej. `docker-compose exec app bash` para el contenedor PHP).
