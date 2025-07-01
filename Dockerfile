FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libcurl4-openssl-dev \
    libsodium-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip sodium

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]

