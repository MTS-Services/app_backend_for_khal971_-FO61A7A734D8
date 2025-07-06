FROM php:8.2-fpm

# ----------------------------------------
# 1. Install system dependencies
# ----------------------------------------
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# ----------------------------------------
# 2. Install Composer
# ----------------------------------------
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# ----------------------------------------
# 3. Set working directory
# ----------------------------------------
WORKDIR /var/www

# ----------------------------------------
# 4. Copy Laravel app source
# ----------------------------------------
COPY . .

# ----------------------------------------
# Prepare Laravel cache paths & permissions
RUN mkdir -p storage/framework/{views,sessions,cache} \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ----------------------------------------
# 6. Install PHP dependencies
# ----------------------------------------
RUN composer install --no-dev --optimize-autoloader

# ----------------------------------------
# 7. Run Artisan setup (config, routes, views, migrate)
# ----------------------------------------
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force || true

# ----------------------------------------
# 8. Configure Nginx and Supervisor
# ----------------------------------------
RUN rm -f /etc/nginx/sites-enabled/default
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ----------------------------------------
# 9. Expose HTTP port
# ----------------------------------------
EXPOSE 80

# ----------------------------------------
# 10. Start services with supervisor
# ----------------------------------------
CMD ["/usr/bin/supervisord", "-n"]
