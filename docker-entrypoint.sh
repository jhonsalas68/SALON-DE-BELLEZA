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

# Asegurarnos de que no haya otros módulos MPM activos que crashean Apache
rm -f /etc/apache2/mods-enabled/mpm_event.conf
rm -f /etc/apache2/mods-enabled/mpm_event.load
rm -f /etc/apache2/mods-enabled/mpm_worker.conf
rm -f /etc/apache2/mods-enabled/mpm_worker.load
a2enmod mpm_prefork

# Iniciar Apache en primer plano
exec apache2-foreground
