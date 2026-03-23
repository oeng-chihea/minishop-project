#!/bin/sh
set -e

APP_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"
cd "$APP_DIR"

PORT="${PORT:-10000}"
HOST="${HOST:-0.0.0.0}"

echo "Starting Laravel service..."
echo "Binding web server to ${HOST}:${PORT}"

if php artisan migrate --force --no-interaction; then
  echo "Migrations completed."
else
  echo "Migration failed."

  if [ "${MIGRATE_FRESH_ON_FAIL:-false}" = "true" ]; then
    echo "MIGRATE_FRESH_ON_FAIL=true, running migrate:fresh..."
    php artisan migrate:fresh --force --no-interaction || true
  fi

  echo "Continuing startup so service stays live."
fi

echo "Starting web server..."
# Use Laravel's router script so all application routes resolve correctly.
exec php -S "${HOST}:${PORT}" server.php
