# Queue Worker en Coolify v4 (Beta)

## Método 1: Docker Compose Override (Recomendado)

Coolify v4 permite usar un `docker-compose.override.yml` o configurar comandos personalizados.

### Paso 1: Crear script de inicio

Crea `start.sh` en la raíz del proyecto:

```bash
#!/bin/bash

# Inicia el queue worker en segundo plano
php artisan queue:work --sleep=3 --tries=3 --timeout=90 &

# Guarda el PID del worker
echo $! > /tmp/queue-worker.pid

# Inicia PHP-FPM o tu servidor web principal
php-fpm
```

Dale permisos de ejecución:
```bash
chmod +x start.sh
```

### Paso 2: Modificar Dockerfile

Si tienes un `Dockerfile`, modifica el `CMD` o `ENTRYPOINT`:

```dockerfile
# Al final del Dockerfile
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
```

Si NO tienes Dockerfile, Coolify usa uno por defecto. En ese caso, usa el Método 2.

## Método 2: Supervisord (Más Robusto)

### Paso 1: Crear configuración de Supervisord

Crea `supervisord.conf` en la raíz:

```ini
[supervisord]
nodaemon=true
user=root

[program:php-fpm]
command=php-fpm
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:queue-worker]
command=php artisan queue:work --sleep=3 --tries=3 --timeout=90
directory=/var/www/html
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
```

### Paso 2: Modificar Dockerfile

```dockerfile
FROM php:8.2-fpm

# Instalar supervisor
RUN apt-get update && apt-get install -y supervisor

# Copiar configuración
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ... resto de tu Dockerfile ...

# Usar supervisord como comando principal
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

## Método 3: Configuración en Coolify UI (Sin archivos)

En Coolify v4 beta:

1. Ve a tu aplicación
2. Click en **"Configuration"** o **"Settings"**
3. Busca **"Pre Start Command"** o **"Post Deployment Command"**
4. Agrega:
   ```bash
   php artisan queue:work --sleep=3 --tries=3 --timeout=90 &
   ```

**Nota**: Este método puede no ser persistente entre reinicios.

## Método 4: Usar systemd dentro del contenedor

Crea `queue-worker.service`:

```ini
[Unit]
Description=Obertrack Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/html
ExecStart=/usr/local/bin/php artisan queue:work --sleep=3 --tries=3 --timeout=90
Restart=always

[Install]
WantedBy=multi-user.target
```

En el Dockerfile:
```dockerfile
COPY queue-worker.service /etc/systemd/system/
RUN systemctl enable queue-worker
```

## Verificación

### Ver logs del worker:
```bash
# SSH a tu contenedor en Coolify
docker exec -it <container-name> bash

# Ver procesos
ps aux | grep queue:work

# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

### Probar manualmente:
```bash
# Dentro del contenedor
php artisan queue:work --once
```

## Recomendación para Coolify v4

**Usa el Método 2 (Supervisord)** porque:
- ✅ Reinicia automáticamente el worker si falla
- ✅ Gestiona múltiples procesos de forma robusta
- ✅ Los logs aparecen en Coolify
- ✅ Compatible con la mayoría de setups de Laravel

## Troubleshooting

### El worker no inicia
1. Revisa los logs de deployment en Coolify
2. SSH al contenedor y ejecuta manualmente: `php artisan queue:work --once`
3. Verifica permisos: `chown -R www-data:www-data storage`

### Worker se detiene
- Usa Supervisord (Método 2) para auto-reinicio
- Aumenta el timeout: `--timeout=300`

### No ves logs
- Asegúrate de que stdout/stderr van a `/dev/stdout` y `/dev/stderr`
- Revisa `storage/logs/laravel.log` directamente
