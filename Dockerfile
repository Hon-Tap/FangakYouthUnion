FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

FROM php:8.4-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY . /app
COPY --from=vendor /app/vendor /app/vendor

RUN printf '%s\n' \
'<?php' \
'error_reporting(E_ALL);' \
'ini_set("display_errors", "1");' \
'$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);' \
'$file = __DIR__ . "/public" . $path;' \
'if ($path !== "/" && is_file($file)) {' \
'    return false;' \
'}' \
'require __DIR__ . "/public/index.php";' \
> /app/router.php

EXPOSE 8080

CMD ["sh", "-c", "php -d display_errors=1 -d display_startup_errors=1 -d log_errors=1 -d error_reporting=E_ALL -S 0.0.0.0:${PORT:-8080} -t public"]