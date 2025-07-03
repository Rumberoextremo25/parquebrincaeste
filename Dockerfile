# Usa una imagen de PHP 8.2 con Apache y Alpine Linux.
# Esta es una imagen ligera y optimizada para servidores web.
FROM php:8.2-apache-alpine

# Define las variables para el ID de usuario (PUID) y el ID de grupo (PGID) de tu máquina.
# ¡IMPORTANTE!: Reemplaza '1000' por los números que obtuviste al ejecutar 'id -u' y 'id -g' en tu terminal.
# Si ambos te dieron '1000', déjalos así.
ARG PUID=1000
ARG PGID=1000

# Instala las dependencias del sistema operativo (Alpine) necesarias para las extensiones de PHP.
# Se instalan tanto las librerías de desarrollo (.dev) para la compilación, como las de runtime.
# La opción '--virtual .build-deps' nos permite eliminar las dependencias de desarrollo después,
# para mantener la imagen más pequeña.
RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    g++ \
    make \
    libjpeg-turbo-dev \
    libpng-dev \
    freetype-dev \
    libwebp-dev \
    zlib-dev \
    icu-dev \
    libxml2-dev \
    oniguruma-dev \
    linux-headers \
    gmp-dev && \
    apk add --no-cache \
    libjpeg-turbo \
    libpng \
    freetype \
    libwebp \
    zlib \
    icu-libs \
    libxml2 \
    oniguruma \
    gmp

# Configura e instala la extensión GD con soporte explícito para diferentes formatos de imagen.
# '-j$(nproc)' usa todos los núcleos del procesador para acelerar la compilación.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd

# Instala el resto de las extensiones PHP comunes y necesarias para Laravel.
# La extensión 'json' no se incluye aquí porque ya viene preinstalada/habilitada en esta imagen base de PHP.
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    ctype \
    xml \
    opcache

# Limpia las dependencias de construcción para reducir el tamaño final de la imagen Docker.
RUN apk del .build-deps

# Habilita el módulo de reescritura de Apache (mod_rewrite) para las URLs amigables de Laravel.
RUN a2enmod rewrite

# Ajusta el usuario bajo el cual Apache se ejecutará dentro del contenedor.
# Esto es CRUCIAL para evitar problemas de permisos con las carpetas compartidas (bind mounts).
# Borramos el usuario 'www-data' por defecto y lo recreamos con tu PUID/PGID.
RUN deluser www-data && \
    addgroup -g ${PGID} www-data && \
    adduser -u ${PUID} -G www-data -s /bin/sh -D www-data

# Instala Composer (el gestor de dependencias de PHP) copiándolo de una imagen oficial.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo predeterminado dentro del contenedor.
WORKDIR /var/www/html

# Copia los archivos de tu proyecto al contenedor.
# Durante el desarrollo con 'volumes' en docker-compose.yml, esta copia es menos crítica,
# ya que tus archivos locales sobrescribirán o sincronizarán con los del contenedor.
COPY . .

# Ajusta los permisos de las carpetas 'storage' y 'bootstrap/cache'.
# Aunque los permisos del host son prioritarios con bind mounts, esto asegura una buena base.
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expone el puerto 80, que es el puerto por defecto de Apache.
EXPOSE 80

# El comando que se ejecuta cuando el contenedor se inicia.
# La imagen base de PHP-Apache ya tiene un CMD predefinido para iniciar Apache,
# por lo que no es estrictamente necesario definirlo aquí a menos que quieras sobrescribir.
# CMD ["apache2-foreground"] # Este es el comando por defecto en la imagen de Apache.