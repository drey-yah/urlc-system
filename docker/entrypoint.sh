#!/bin/sh
set -e

PORT="${PORT:-80}"
ROLE="${CONTAINER_ROLE:-web}"

echo "Starting container with role: $ROLE on port: $PORT"

if [ "$ROLE" = "queue" ]; then
    echo "Running queue worker..."
    exec php artisan queue:work --verbose --tries=3 --timeout=90
fi

# Ensure nginx and supervisor run and log directories exist
mkdir -p /run/nginx /var/log/nginx /var/log/supervisor /var/run

# Dynamically set Nginx listen port: listen on $PORT and also listen on 80
if [ "$PORT" != "80" ]; then
    sed -i "s/listen PORT_PLACEHOLDER;/listen $PORT;\n    listen 80;/g" /etc/nginx/http.d/default.conf
else
    sed -i "s/listen PORT_PLACEHOLDER;/listen 80;/g" /etc/nginx/http.d/default.conf
fi

# Check nginx configuration
echo "Testing Nginx config on port $PORT and 80..."
/usr/sbin/nginx -t

# Test PHP-FPM configuration
echo "Testing PHP-FPM config..."
/usr/local/sbin/php-fpm -t

# Storage and cache permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Storage symlink
php artisan storage:link || true

# Clear any cached state
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run database migrations if explicitly enabled
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force || true
fi

echo "Starting web server via supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
