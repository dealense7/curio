#!/bin/sh
set -e

cd /var/www/html

sync_env_value() {
    key="$1"
    value="$2"

    if [ -z "$value" ]; then
        return
    fi

    if grep -q "^${key}=" .env; then
        sed -i "s#^${key}=.*#${key}=${value}#" .env
    else
        printf '\n%s=%s\n' "$key" "$value" >> .env
    fi
}

# Bootstrap a local env file for first container start.
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Keep the mounted .env aligned with container service wiring.
sync_env_value "APP_ENV" "$APP_ENV"
sync_env_value "APP_DEBUG" "$APP_DEBUG"
sync_env_value "APP_URL" "$APP_URL"
sync_env_value "DB_CONNECTION" "$DB_CONNECTION"
sync_env_value "DB_HOST" "$DB_HOST"
sync_env_value "DB_PORT" "$DB_PORT"
sync_env_value "DB_DATABASE" "$DB_DATABASE"
sync_env_value "DB_USERNAME" "$DB_USERNAME"
sync_env_value "DB_PASSWORD" "$DB_PASSWORD"
sync_env_value "CACHE_STORE" "$CACHE_STORE"
sync_env_value "REDIS_CLIENT" "$REDIS_CLIENT"
sync_env_value "REDIS_HOST" "$REDIS_HOST"
sync_env_value "REDIS_PORT" "$REDIS_PORT"

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
