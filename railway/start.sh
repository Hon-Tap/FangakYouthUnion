#!/bin/sh
set -e

echo "================================="
echo "Starting Container Initialization"
echo "================================="

# 1. Configure PHP-FPM to listen on a TCP port (9000) instead of a socket
echo "Configuring PHP-FPM..."
sed -i 's|^listen = .*|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf

# 2. Ensure PORT exists for Nginx (Railway provides this)
if [ -z "$PORT" ]; then
    echo "PORT not provided by environment. Using default 8080"
    PORT=8080
fi
export PORT
echo "Runtime PORT is: $PORT"

# 3. Render Nginx configuration by injecting the PORT variable
echo "Rendering Nginx configuration..."
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.rendered
mv /etc/nginx/conf.d/default.conf.rendered /etc/nginx/conf.d/default.conf

echo "Validating Nginx configuration..."
nginx -t

echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf