# Queue Worker Setup para Coolify

## Configuración en Coolify

Coolify permite ejecutar múltiples procesos en un mismo proyecto usando un archivo `Procfile` o configurando servicios adicionales.

### Opción 1: Usando Procfile (Recomendado)

Crea un archivo `Procfile` en la raíz del proyecto:

```
web: php artisan serve --host=0.0.0.0 --port=8000
worker: php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

**Nota**: Si usas Nginx/Apache en lugar de `php artisan serve`, ajusta la línea `web` según tu configuración.

### Opción 2: Servicio Adicional en Coolify

1. Ve a tu proyecto en Coolify
2. Click en **"Add a new service"** o **"Storages & Services"**
3. Agrega un nuevo servicio con:
   - **Name**: `queue-worker`
   - **Command**: `php artisan queue:work --sleep=3 --tries=3 --timeout=90`
   - **Working Directory**: Misma que tu aplicación principal
   - **Environment**: Mismas variables de entorno

### Opción 3: Script de Inicio Personalizado

Si Coolify usa un script de inicio, modifícalo para incluir:

```bash
#!/bin/bash

# Inicia el queue worker en segundo plano
php artisan queue:work --sleep=3 --tries=3 --timeout=90 &

# Inicia tu aplicación web (ajusta según tu setup)
php artisan serve --host=0.0.0.0 --port=8000
```

## Verificación

### Logs en Coolify
Los logs del worker aparecerán en la sección de logs de Coolify. Busca líneas como:
```
[2026-01-26 12:00:00] Processing: App\Jobs\SendMassWhatsappJob
[2026-01-26 12:00:05] Processed:  App\Jobs\SendMassWhatsappJob
```

### Comandos útiles desde Coolify SSH

Conéctate por SSH a tu contenedor y ejecuta:

```bash
# Ver jobs pendientes
php artisan queue:monitor

# Ver jobs fallidos
php artisan queue:failed

# Reintentar jobs fallidos
php artisan queue:retry all
```

## Reiniciar el Worker

Después de hacer cambios en el código:

1. En Coolify, haz un **Redeploy** del proyecto
2. O ejecuta desde SSH: `php artisan queue:restart`

## Troubleshooting

### El worker no aparece en los logs
- Verifica que el `Procfile` esté en la raíz del proyecto
- Asegúrate de que Coolify detectó el `Procfile` (revisa los logs de deployment)
- Intenta la Opción 2 (servicio adicional) si el Procfile no funciona

### Jobs no se procesan
- Verifica que `QUEUE_CONNECTION=database` esté en las variables de entorno
- Revisa los logs del worker en Coolify
- Ejecuta `php artisan queue:work --once` manualmente para ver errores

### El worker se detiene
- Aumenta el timeout: `--timeout=300`
- Revisa los logs de errores en `storage/logs/laravel.log`
- Verifica que la base de datos esté accesible

## Variables de Entorno Necesarias

Asegúrate de tener configuradas en Coolify:

```env
QUEUE_CONNECTION=database
WAHA_BASE_URL=http://tu-waha-url:3000
WAHA_API_KEY=tu-api-key
```

## Monitoreo

Para monitorear el estado del queue worker en producción, puedes:

1. **Horizon** (opcional): Instala Laravel Horizon para una UI visual
   ```bash
   composer require laravel/horizon
   php artisan horizon:install
   ```

2. **Logs**: Revisa regularmente `storage/logs/laravel.log`

3. **Alertas**: Configura notificaciones en Coolify para cuando el worker falle
