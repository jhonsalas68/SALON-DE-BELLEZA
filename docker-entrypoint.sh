#!/bin/bash

# Railway asigna un puerto dinámico mediante la variable de entorno $PORT.
# Si no está definida (en local por ejemplo), usamos 80.
PORT=${PORT:-80}

# Modificar la configuración de Apache para escuchar en el puerto correcto
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Limpiar y cachear configuraciones de Laravel (Mejora de rendimiento en Producción)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones y seeders automáticamente en el despliegue
php artisan migrate --force --seed

# Iniciar Apache en primer plano
apache2-foreground
