#!/bin/sh
set -e

sed -i 's|listen = 9000|listen = 127.0.0.1:9000|g' /usr/local/etc/php-fpm.d/www.conf

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf