#!/bin/sh
set -e

echo "Starting Laravel service..."

max_attempts=20
attempt=1

until php artisan migrate --force; do
  if [ "$attempt" -ge "$max_attempts" ]; then
    echo "Database migration failed after $max_attempts attempts."
    exit 1
  fi

  echo "Migration attempt $attempt/$max_attempts failed. Retrying in 5 seconds..."
  attempt=$((attempt + 1))
  sleep 5
done

echo "Migrations completed. Starting web server..."
exec php -S 0.0.0.0:${PORT:-10000} -t public
