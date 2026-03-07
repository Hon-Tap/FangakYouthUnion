#!/bin/sh
set -e

sed -i 's|listen = 9000|listen = 127.0.0.1:9000|g' /usr/local/etc/php-fpm.d/www.conf

export PORT="${PORT:-8080}"
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.rendered
mv /etc/nginx/conf.d/default.conf.rendered /etc/nginx/conf.d/default.conf

echo "=== NGINX CONFIG ==="
cat /etc/nginx/conf.d/default.conf
echo "=== END NGINX CONFIG ==="

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf