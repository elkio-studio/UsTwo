FROM node:22-bookworm-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

FROM composer:2.11.1 AS composer

FROM php:8.5-apache-bookworm
ENV APP_ENV=production APP_DEBUG=false
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y --no-install-recommends ca-certificates libpq-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY . .
COPY --from=frontend /app/public/build ./public/build
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache
CMD ["sh", "deploy/start-render.sh"]
