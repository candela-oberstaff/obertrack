# Stage 1: Build dependencies
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    postgresql-dev \
    zip unzip git curl \
    nodejs npm \
    nginx

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql opcache

# Configure PHP for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini

# Stage 2: Install Composer dependencies
FROM base AS composer-deps
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Stage 3: Build frontend assets
FROM base AS node-build
WORKDIR /app
COPY package*.json ./
# Install ALL dependencies (including devDependencies) for build
RUN npm ci --legacy-peer-deps
COPY . .
COPY --from=composer-deps /app/vendor ./vendor
RUN npm run build

# Stage 4: Production image
FROM php:8.2-fpm-alpine

# Install runtime dependencies
RUN apk add --no-cache \
    postgresql-dev \
    nginx \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql opcache

# Configure PHP
COPY --from=base /usr/local/etc/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY --from=composer-deps /app/vendor ./vendor
COPY --from=node-build /app/public ./public
COPY . .

# Set permissions
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache && \
    chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s \
  CMD curl -f http://localhost:80/up || exit 1

# Start command (JSON array format to avoid shell issues)
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=80"]
