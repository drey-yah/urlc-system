# Stage 1: Build Frontend Assets
FROM node:18-alpine AS frontend
WORKDIR /app
COPY package*.json webpack.mix.js tailwind.config.js ./
RUN npm ci
COPY resources ./resources
COPY public ./public
RUN npm run prod

# Stage 2: Production PHP + Nginx Environment
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    curl \
    bash

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        gd \
        zip \
        intl \
        bcmath \
        opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy compiled assets from frontend stage
COPY --from=frontend /app/public/css ./public/css
COPY --from=frontend /app/public/js ./public/js
COPY --from=frontend /app/public/mix-manifest.json ./public/mix-manifest.json

# Install production PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy Docker configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /docker/entrypoint.sh
RUN chmod +x /docker/entrypoint.sh

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/docker/entrypoint.sh"]
