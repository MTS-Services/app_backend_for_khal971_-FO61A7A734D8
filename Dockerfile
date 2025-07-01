FROM php:8.2-fpm

# Install required system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libcurl4-openssl-dev \
    libsodium-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip sodium

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel source code and php.ini
COPY . .
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Clear composer cache and install dependencies with full logging
RUN composer clear-cache && composer install --no-dev --optimize-autoloader -vvv || cat storage/logs/laravel.log

# Set file permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Start PHP-FPM
CMD ["php-fpm"]
