#!/bin/sh
set -e

PORT="${PORT:-80}"
ROLE="${CONTAINER_ROLE:-web}"

echo "Starting container with role: $ROLE on port: $PORT"

if [ "$ROLE" = "queue" ]; then
    echo "Running queue worker..."
    exec php artisan queue:work --verbose --tries=3 --timeout=90
fi

# Ensure nginx run and log directories exist in Alpine
mkdir -p /run/nginx /var/log/nginx /var/log/supervisor

# Configure Nginx port dynamically based on Railway/Render assigned $PORT
sed -i "s/PORT_PLACEHOLDER/$PORT/g" /etc/nginx/http.d/default.conf

# Test Nginx syntax
nginx -t

# Ensure writable storage and cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage symlink if not already existing
php artisan storage:link || true

# Clear cached routes and config to ensure dynamic routing without closures hanging
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run database migrations if explicitly enabled
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force || true
fi

echo "Starting web server (Nginx + PHP-FPM)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
