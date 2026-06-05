FROM php:8.2-fpm-alpine

# Instalar dependencias del sistema, herramientas de compilación para PostgreSQL y Node.js/npm
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    postgresql-dev \
    zip \
    unzip \
    nodejs \
    npm

# Instalar extensiones de PHP (incluyendo pgsql, pdo_pgsql, opcache, exif y pcntl)
RUN docker-php-ext-install pdo_mysql pdo_pgsql pgsql bcmath gd opcache exif pcntl

# Configurar OPcache para optimización de velocidad en producción
RUN echo -e "opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=10000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0\n\
opcache.save_comments=1" > /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www

# Copiar el proyecto al contenedor
COPY . .

# Instalar dependencias de Composer (optimizado para producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependencias de Node y compilar recursos estáticos de Vite, luego limpiar node_modules
RUN npm install && npm run build && rm -rf node_modules

# Crear directorios necesarios para Nginx y configurar permisos correctos para Laravel
RUN mkdir -p /run/nginx /var/log/nginx /var/lib/nginx \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/log/nginx /var/lib/nginx /run/nginx \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copiar archivos de configuración de Nginx y Supervisor
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Copiar script de entrada (entrypoint) y dar permisos de ejecución
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer el puerto
EXPOSE 80

# Usar el script de entrada y arrancar Supervisor
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
