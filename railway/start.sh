#!/bin/sh

set -e

echo "================================="
echo "Starting Container Initialization"
echo "================================="

#

# Configure PHP-FPM to listen on internal port

#

echo "Configuring PHP-FPM..."

sed -i 
's|^listen = .*|listen = 127.0.0.1:9001|' 
/usr/local/etc/php-fpm.d/[www.conf](http://www.conf)

echo "PHP-FPM configured to listen on 127.0.0.1:9001"

#

# Ensure Railway PORT exists

#

echo "Checking PORT environment variable..."

if [ -z "$PORT" ]; then
echo "PORT not provided by environment. Using default 8080"
export PORT=8080
fi

echo "Runtime PORT is: $PORT"

#

# Verify application files exist

#

echo "================================="
echo "FILE SYSTEM CHECK"
echo "================================="

if [ -f /app/public/index.php ]; then
echo "OK: /app/public/index.php exists"
else
echo "ERROR: /app/public/index.php NOT FOUND"
fi

echo "Listing /app/public:"
ls -la /app/public || true

echo "================================="

#

# Render Nginx configuration using dynamic PORT

#

echo "Rendering Nginx configuration..."

envsubst '${PORT}' 
< /etc/nginx/conf.d/default.conf \

> /etc/nginx/conf.d/default.conf.rendered

mv /etc/nginx/conf.d/default.conf.rendered 
/etc/nginx/conf.d/default.conf

#

# Validate Nginx configuration

#

echo "================================="
echo "Validating Nginx configuration..."
echo "================================="

nginx -t

echo "================================="
echo "Final Nginx Configuration:"
echo "================================="

cat /etc/nginx/conf.d/default.conf

echo "================================="

#

# Show listening ports BEFORE starting services

#

echo "================================="
echo "NETWORK PRECHECK"
echo "================================="

echo "Expected public PORT:"
echo "$PORT"

echo "================================="

#

# Start Supervisor (runs nginx + php-fpm)

#

echo "Starting Supervisor..."

exec /usr/bin/supervisord 
-c /etc/supervisor/conf.d/supervisord.conf
