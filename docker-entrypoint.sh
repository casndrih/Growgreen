#!/bin/sh
set -eu

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Render injects environment variables directly; never rely on a local .env file.
: "${APP_KEY:?APP_KEY must be configured in the Render environment}"

php artisan config:clear
php artisan migrate --force --no-interaction
if [ "${GROWGREEN_DEMO_SEED:-false}" = "true" ]; then
    php artisan db:seed --class=Database\\Seeders\\DemoDataSeeder --force --no-interaction
fi
php artisan storage:link || true
php artisan config:cache
php artisan route:cache || true
php artisan view:cache

exec "$@"
