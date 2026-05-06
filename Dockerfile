FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Install Node.js & npm (for Vite build)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (Incluyendo OPcache para aceleración)
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application
COPY . /var/www/html

# Install Composer dependencies (optimizing for production)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Install NPM dependencies and build Vite assets
RUN npm cache clean --force && npm install --network-timeout=1000000
RUN npm run build

# Set directory permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Change Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# ==========================================
# SUPER OPTIMIZACIÓN DE RENDIMIENTO
# ==========================================

# 1. Configurar OPcache para precompilar Laravel (Hace la app 10x más rápida)
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=10000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0\n\
opcache.save_comments=1" > /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# 2. Optimizar Apache para la memoria RAM gratuita de Railway (evita bloqueos)
RUN echo "<IfModule mpm_prefork_module>\n\
    StartServers          1\n\
    MinSpareServers       1\n\
    MaxSpareServers       3\n\
    MaxRequestWorkers     10\n\
    MaxConnectionsPerChild 100\n\
</IfModule>" > /etc/apache2/mods-available/mpm_prefork.conf

# Ensure only one MPM is loaded (prefork is required for mod_php)
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork || true

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
