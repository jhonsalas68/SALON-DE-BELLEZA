#!/bin/sh
set -e

# Esperar a que la base de datos esté lista si es necesario (opcional)
echo "Ejecutando configuraciones de inicio de Laravel..."

# Limpiar y cachear configuraciones, rutas y vistas
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones en el arranque del contenedor (runtime)
echo "Ejecutando migraciones de base de datos..."
php artisan migrate --force

# Ejecutar el comando principal (Supervisor)
echo "Arrancando Supervisor (Nginx + PHP-FPM)..."
exec "$@"
