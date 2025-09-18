# Multi-stage Dockerfile for Laravel 12 (PHP 8.2) + Vite + Browsershot (Chromium)

# 1) Composer deps (no scripts to avoid running artisan during build)
FROM composer:2 AS composer_deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --no-scripts

# 2) Frontend build (Vite)
FROM node:20-bullseye AS frontend_build
WORKDIR /app
COPY package*.json ./
RUN npm ci --no-audit --no-fund
COPY . ./
RUN npm run build

# 3) Final runtime: PHP 8.2 + Apache + Chromium + Node (for Browsershot/Puppeteer)
FROM php:8.2-apache-bookworm AS runtime

ENV APP_ENV=production \
    APACHE_DOCUMENT_ROOT=/var/www/html/public \
    PUPPETEER_SKIP_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium

# Apache setup
RUN a2enmod rewrite \
 && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e 's!Directory /var/www/!Directory ${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf \
 && sed -ri -e 's!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# System packages, PHP extensions, Chromium, Node.js 20.x
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    ca-certificates \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    libsqlite3-0 sqlite3 libsqlite3-dev \
    chromium \
    fonts-dejavu \
 && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j"$(nproc)" \
    gd \
    bcmath \
    intl \
    pdo_mysql \
    pdo_sqlite \
    zip \
    exif \
    opcache

# Install Node.js (for Browsershot runtime via Puppeteer)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get update \
 && apt-get install -y --no-install-recommends nodejs \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy application source
COPY . .

# Copy vendor dependencies built without scripts
COPY --from=composer_deps /app/vendor ./vendor

# Install production Node dependencies needed at runtime (e.g., puppeteer)
# Keeping only prod deps keeps image smaller while enabling Browsershot.
COPY package*.json ./
RUN npm ci --omit=dev --no-audit --no-fund || true

# Copy built assets from frontend stage
COPY --from=frontend_build /app/public/build ./public/build

# Ensure correct permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80

# Default command
CMD ["apache2-foreground"]


