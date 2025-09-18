# ============================================================
# 1. Base PHP image (with required extensions)
# ============================================================
FROM php:8.3-fpm AS php-base

RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libicu-dev g++ zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath intl zip \
    && docker-php-ext-enable opcache \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app


# ============================================================
# 2. Composer dependencies
# ============================================================
FROM php-base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./ 

# Install PHP dependencies (skip scripts for now)
RUN --mount=type=cache,target=/root/.composer \
    composer install --no-dev --no-scripts --no-progress --prefer-dist

# Copy full application (artisan now exists)
COPY . .

# Run scripts + optimize autoload
RUN composer dump-autoload --optimize \
    && composer run-script post-autoload-dump


# ============================================================
# 3. Frontend build (Node.js + npm/pnpm)
# ============================================================
FROM node:20 AS frontend

WORKDIR /app
COPY package.json package-lock.json ./ 

# Configure registry + retry for stability
RUN npm config set registry https://registry.npmjs.org/

# Install dependencies with retry to avoid ECONNRESET
RUN --mount=type=cache,target=/root/.npm \
    bash -c "npm install || npm install || npm install"

COPY . .

RUN npm run build


# ============================================================
# 4. Production runtime (Laravel only, no Nginx)
# ============================================================
FROM php-base AS production

WORKDIR /app

# Copy built PHP vendor deps + app
COPY --from=vendor /app /app

# Copy built frontend assets
COPY --from=frontend /app/public/build /app/public/build

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 8000

# Run Laravel’s built-in server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
