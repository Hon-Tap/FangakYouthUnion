#!/bin/sh

set -e

echo "================================="
echo "Starting Container Initialization"
echo "================================="

echo "Configuring PHP-FPM..."

sed -i 's|^listen = .*|listen = 127.0.0.1:9001|' /usr/local/etc/php-fpm.d/[www.conf](http://www.conf)

echo "PHP-FPM configured"

#

# Ensure Railway PORT exists

#

if [ -z "$PORT" ]; then
echo "PORT not provided by environment. Using default 8080"
export PORT=8080
fi

echo "Runtime PORT is: $PORT"

#

# Render Nginx configuration

#

echo "Rendering Nginx configuration..."

envsubst '${PORT}' 
< /etc/nginx/conf.d/default.conf \

> /etc/nginx/conf.d/default.conf.rendered

mv /etc/nginx/conf.d/default.conf.rendered 
/etc/nginx/conf.d/default.conf

echo "Validating Nginx configuration..."

nginx -t

echo "Starting Supervisor..."

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
