#!/bin/sh
set -e

# Configurar el puerto dinámico de Railway para Nginx
echo "Configurando Nginx para escuchar en el puerto ${PORT:-80}..."
sed -i "s/listen 80;/listen ${PORT:-80};/g" /etc/nginx/nginx.conf

echo "Optimizando configuraciones de Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones con tolerancia a fallos
echo "Ejecutando migraciones de base de datos..."
php artisan migrate --force || echo "Aviso: No se pudieron ejecutar las migraciones en el arranque inicial."

# Asegurar permisos correctos para www-data después de ejecutar artisan como root
echo "Ajustando permisos de storage y bootstrap/cache..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true
chmod -R 777 /var/www/storage /var/www/bootstrap/cache || true

echo "Arrancando Supervisor (Nginx + PHP-FPM)..."
exec "$@"
