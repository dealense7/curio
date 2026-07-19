#!/bin/sh
set -e

cd /var/www/html

# Bootstrap a local env file for first container start.
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Install PHP dependencies only when the mounted workspace has none yet.
if [ ! -d vendor ]; then
    composer install --no-interaction --prefer-dist
fi

# Install Node dependencies for Vite-driven frontend assets when needed.
if [ -f package.json ] && [ ! -d node_modules ]; then
    npm install --ignore-scripts
fi

# Generate the app key once, but never rotate an existing key on startup.
if ! grep -q '^APP_KEY=base64:' .env && ! grep -q '^APP_KEY=.+$' .env; then
    php artisan key:generate --force
fi

# Clear cached config so container env values are picked up reliably.
php artisan config:clear

exec "$@"
