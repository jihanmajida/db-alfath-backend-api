# Dockerfile for Laravel 12 + PHP 8.2 + Node.js (for Vite)

# Build stage for Node dependencies
FROM node:20-alpine AS node_modules
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install --legacy-peer-deps || npm install --force

# Main application image
FROM php:8.2-fpm-alpine

# Install system dependencies and Node.js
RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    zlib-dev \
    libxml2-dev \
    oniguruma-dev \
    curl \
    git \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install intl pdo pdo_mysql zip gd xml mbstring

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer


# Copy application code and node_modules from build stages
COPY . /var/www
COPY --from=node_modules /app/node_modules /var/www/node_modules


# Install PHP dependencies, build frontend assets, and set permissions
RUN cd /var/www && \
    composer install --no-interaction --prefer-dist --optimize-autoloader && \
    npm run build && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache


# Set working directory
WORKDIR /var/www

# Expose port 9000 for php-fpm
EXPOSE 9000

# Start php-fpm
CMD ["php-fpm"]
