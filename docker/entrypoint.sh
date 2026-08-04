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

read_env_value() {
    key="$1"
    file_path="$2"

    if [ ! -f "$file_path" ]; then
        return
    fi

    grep "^${key}=" "$file_path" | head -n 1 | cut -d '=' -f 2-
}

sync_env_value_in_file() {
    key="$1"
    value="$2"
    file_path="$3"

    if [ -z "$value" ] || [ ! -f "$file_path" ]; then
        return
    fi

    if grep -q "^${key}=" "$file_path"; then
        sed -i "s#^${key}=.*#${key}=${value}#" "$file_path"
    else
        printf '\n%s=%s\n' "$key" "$value" >> "$file_path"
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

# Keep mounted Node dependencies aligned with the current package manifests.
if [ -f package.json ]; then
    node_install_marker="node_modules/.package-lock.json"

    # A Vite dev-server marker can survive a stopped container and override
    # otherwise valid production assets with an unreachable hot-server URL.
    if [ -f public/hot ]; then
        rm -f public/hot
    fi

    if [ ! -d node_modules ] \
        || [ ! -f "$node_install_marker" ] \
        || [ package.json -nt "$node_install_marker" ] \
        || { [ -f package-lock.json ] && [ package-lock.json -nt "$node_install_marker" ]; }; then
        npm install --ignore-scripts --no-audit --no-fund
    fi

    # Build Vite assets on first start and whenever frontend sources change.
    vite_manifest="public/build/manifest.json"
    frontend_needs_build=false

    if [ ! -f "$vite_manifest" ] \
        || [ package.json -nt "$vite_manifest" ] \
        || { [ -f package-lock.json ] && [ package-lock.json -nt "$vite_manifest" ]; } \
        || { [ -f vite.config.js ] && [ vite.config.js -nt "$vite_manifest" ]; } \
        || find resources -type f -newer "$vite_manifest" -print -quit | grep -q .; then
        frontend_needs_build=true
    fi

    if [ "$frontend_needs_build" = true ]; then
        npm run build
    fi
fi

# Generate the app key once, but never rotate an existing key on startup.
if ! grep -q '^APP_KEY=base64:' .env && ! grep -q '^APP_KEY=.+$' .env; then
    php artisan key:generate --force
fi

# Generate a dedicated testing app key when .env.testing has none.
if [ -f .env.testing ]; then
    testing_app_key="$(read_env_value "APP_KEY" ".env.testing")"

    if [ -z "$testing_app_key" ]; then
        php artisan key:generate --env=testing --force
    fi
fi

# Generate Passport keys once for API token auth when they are missing.
if [ ! -f storage/oauth-private.key ] || [ ! -f storage/oauth-public.key ]; then
    php artisan passport:keys --force
fi

# Clear cached config so container env values are picked up reliably.
php artisan config:clear

# Keep the dedicated testing database schema in sync for integration tests.
if [ -f .env.testing ]; then
    (
        set -a
        . ./.env.testing
        set +a
        php artisan migrate --force
    )
fi

exec "$@"
