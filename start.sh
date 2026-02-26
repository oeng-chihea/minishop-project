#!/bin/sh
set -e

echo "Starting Laravel service..."

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
exec php -S 0.0.0.0:${PORT:-10000} -t public
