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

# Ejecutar migraciones en segundo plano o con tolerancia a fallos para no bloquear el inicio de Nginx
echo "Ejecutando migraciones de base de datos..."
php artisan migrate --force || echo "Aviso: No se pudieron ejecutar las migraciones en el arranque inicial. Se continuará con el inicio del servidor."

echo "Arrancando Supervisor (Nginx + PHP-FPM)..."
exec "$@"
