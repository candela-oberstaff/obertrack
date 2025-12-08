#!/bin/sh
set -e

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start Supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
