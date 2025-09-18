FROM php:8.3-fpm

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev libpq-dev \
    libicu-dev libzip-dev npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && docker-php-ext-enable opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY ./ .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-interaction --prefer-dist
RUN npm install
RUN npm run build 

RUN php artisan migrate --seed --force
RUN php artisan key:generate
RUN php artisan storage:link

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

