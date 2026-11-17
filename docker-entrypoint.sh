#!/bin/sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force --no-interaction
fi

php artisan config:clear
php artisan migrate --force --no-interaction
php artisan storage:link || true
php artisan config:cache
php artisan route:cache || true
php artisan view:cache

exec "$@"
