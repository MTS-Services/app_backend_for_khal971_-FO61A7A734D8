FROM php:8.2-fpm

# 1️⃣ Install PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libcurl4-openssl-dev \
    libsodium-dev git \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip sodium

# 2️⃣ Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3️⃣ Set working directory
WORKDIR /var/www

# 4️⃣ Copy app source and php.ini
COPY . .
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# 5️⃣ Ensure permissions before composer install
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# 6️⃣ Install dependencies (and show logs on error)
RUN composer install --no-dev --optimize-autoloader -vvv \
    || (echo "COMPOSER INSTALL FAILED" && ls -la && cat /var/www/storage/logs/laravel.log || true)

# 7️⃣ Final permissions (safety)
RUN chown -R www-data:www-data /var/www

# 8️⃣ Start PHP-FPM
CMD ["php-fpm"]
