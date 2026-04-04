#!/bin/sh

set -e

echo "================================="
echo "Starting Container Initialization"
echo "================================="

#
# Ensure PHP-FPM listens on TCP
#

echo "Configuring PHP-FPM..."

sed -i \
's|^listen = .*|listen = 127.0.0.1:9000|' \
/usr/local/etc/php-fpm.d/www.conf

#
# Ensure Railway PORT exists
#

if [ -z "$PORT" ]; then
    echo "PORT not provided by environment. Using default 8080"
    export PORT=8080
fi

echo "PORT is: $PORT"

#
# Render Nginx configuration with dynamic PORT
#

echo "Rendering Nginx configuration..."

envsubst '${PORT}' \
< /etc/nginx/conf.d/default.conf \
> /etc/nginx/conf.d/default.conf.rendered

mv /etc/nginx/conf.d/default.conf.rendered \
/etc/nginx/conf.d/default.conf

#
# Show final configuration (for logs / debugging)
#

echo "================================="
echo "Final Nginx Configuration:"
echo "================================="

cat /etc/nginx/conf.d/default.conf

echo "================================="

#
# Start Supervisor (runs nginx + php-fpm)
#

echo "Starting Supervisor..."

exec /usr/bin/supervisord \
-c /etc/supervisor/conf.d/supervisord.conf