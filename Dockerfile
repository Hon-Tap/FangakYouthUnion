FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Kill every MPM definition, then recreate ONLY prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && rm -f /etc/apache2/mods-available/mpm_*.load /etc/apache2/mods-available/mpm_*.conf \
    && printf "LoadModule mpm_prefork_module /usr/lib/apache2/modules/mod_mpm_prefork.so\n" > /etc/apache2/mods-available/mpm_prefork.load \
    && printf "<IfModule mpm_prefork_module>\n    StartServers             2\n    MinSpareServers          1\n    MaxSpareServers          5\n    MaxRequestWorkers      150\n    MaxConnectionsPerChild   0\n</IfModule>\n" > /etc/apache2/mods-available/mpm_prefork.conf \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf \
    && a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY . /var/www/html
WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html

# Fail the build immediately if Apache still has an MPM conflict
RUN apache2ctl -M && apache2ctl configtest

EXPOSE 80
CMD ["apache2-foreground"]