FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction



FROM php:8.4-fpm

WORKDIR /app



# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    unzip \
    libzip-dev \
    gettext-base \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*



# Copy application source
COPY . /app

# Copy vendor dependencies from build stage
COPY --from=vendor /app/vendor /app/vendor



# Create required runtime directories
RUN mkdir -p \
    /run/php \
    /var/log/supervisor \
    /var/lib/nginx/body \
    /var/run/nginx



# Copy configuration files
COPY railway/nginx.conf /etc/nginx/nginx.conf
COPY railway/default.conf /etc/nginx/conf.d/default.conf
COPY railway/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY railway/start.sh /start.sh



# Set permissions
RUN chmod +x /start.sh \
    && chown -R www-data:www-data /app



# Container entrypoint
ENTRYPOINT ["/start.sh"]