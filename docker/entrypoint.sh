#!/bin/sh
set -e

echo "[Container Init] Preparing Laravel Application..."

# Ensure storage and bootstrap/cache permissions
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage symbolic link if not exists
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link || true
fi

# In production container startup
if [ "$APP_ENV" = "production" ]; then
    echo "[Container Init] Caching Laravel Configuration & Routes..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "[Container Init] Starting application processes..."
exec "$@"
