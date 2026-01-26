#!/bin/bash
set -e

echo "Starting Obertrack services..."

# Inicia el queue worker en segundo plano
echo "Starting queue worker..."
php artisan queue:work --sleep=3 --tries=3 --timeout=90 &

# Guarda el PID
WORKER_PID=$!
echo "Queue worker started with PID: $WORKER_PID"

# Función para manejar señales de terminación
cleanup() {
    echo "Stopping queue worker..."
    kill -TERM $WORKER_PID 2>/dev/null || true
    wait $WORKER_PID 2>/dev/null || true
    echo "Queue worker stopped"
    exit 0
}

trap cleanup SIGTERM SIGINT

# Inicia PHP-FPM (ajusta según tu configuración)
echo "Starting PHP-FPM..."
php-fpm

# Espera a que termine (no debería terminar)
wait
