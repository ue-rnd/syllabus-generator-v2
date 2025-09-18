# Use PHP 8.3 FPM image
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    npm \
    libicu-dev

# Install PHP extensions (including intl)
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd intl

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Ensure cache and storage directories exist
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-interaction --prefer-dist

# Install Node dependencies and build assets
RUN npm install && npm run build

# Expose port 8000
EXPOSE 8000

# Set environment variables for Laravel

# Start Laravel using PHP-FPM
CMD ["php-fpm"]