#!/bin/sh
set -e

PORT="${PORT:-80}"
ROLE="${CONTAINER_ROLE:-web}"

echo "Starting container with role: $ROLE on port: $PORT"

if [ "$ROLE" = "queue" ]; then
    echo "Running queue worker..."
    exec php artisan queue:work --verbose --tries=3 --timeout=90
fi

# Configure Nginx port dynamically based on Railway/Render assigned $PORT
sed -i "s/PORT_PLACEHOLDER/$PORT/g" /etc/nginx/http.d/default.conf

# Ensure writable storage and cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage symlink if not already existing
php artisan storage:link || true

# Run database migrations if explicitly enabled
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force
fi

# Cache config, routes, and views for optimal production performance
if [ -n "$APP_KEY" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "Starting web server (Nginx + PHP-FPM)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
