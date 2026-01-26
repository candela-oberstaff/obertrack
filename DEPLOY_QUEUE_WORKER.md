# Despliegue del Queue Worker en Coolify

## ✅ Cambios Realizados

He agregado el **queue worker** a tu configuración existente de Supervisor en `docker/supervisord.conf`.

Ahora tu contenedor ejecutará automáticamente:
1. **PHP-FPM** - Tu aplicación Laravel
2. **Nginx** - Servidor web
3. **Queue Worker** - Procesador de mensajes de WhatsApp ✨ NUEVO

## 🚀 Pasos para Desplegar

### 1. Commitea los cambios

```bash
git add docker/supervisord.conf
git commit -m "Add queue worker for WhatsApp mass messaging"
git push
```

### 2. Redeploy en Coolify

- Ve a tu proyecto en Coolify
- Click en **"Redeploy"** o **"Deploy"**
- Espera a que termine el deployment

### 3. Verifica que funciona

Conéctate por SSH a tu contenedor en Coolify:

```bash
# Ver procesos corriendo
ps aux | grep "queue:work"

# Deberías ver algo como:
# www-data  123  0.0  1.2  php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

Ver logs del queue worker:

```bash
# Logs en tiempo real
tail -f /var/www/html/storage/logs/laravel.log

# Buscar actividad del worker
grep "Processing:" /var/www/html/storage/logs/laravel.log
```

## 🧪 Probar el Sistema

1. **Inicia sesión de WhatsApp** en la aplicación
2. **Envía un mensaje masivo** desde Admin o Manager
3. **Verifica en los logs** que se están procesando:

```bash
# Deberías ver líneas como:
[2026-01-26 13:45:00] Processing: App\Jobs\SendMassWhatsappJob
[2026-01-26 13:45:05] Processed:  App\Jobs\SendMassWhatsappJob
```

## 🔧 Comandos Útiles

### Ver jobs pendientes:
```bash
php artisan queue:monitor
```

### Ver jobs fallidos:
```bash
php artisan queue:failed
```

### Reintentar jobs fallidos:
```bash
php artisan queue:retry all
```

### Reiniciar el worker (después de cambios en código):
```bash
php artisan queue:restart
```

## 📊 Monitoreo

Los logs del queue worker aparecerán en:
- **Coolify UI**: En la sección de logs del contenedor
- **Archivo**: `/var/www/html/storage/logs/laravel.log`

## ⚠️ Importante

- **Reiniciar después de cambios**: Cada vez que hagas cambios en el código, haz redeploy en Coolify para que el worker use el código actualizado
- **Delay de seguridad**: Los mensajes se envían con 60 segundos de intervalo para proteger tu cuenta de WhatsApp
- **Auto-reinicio**: Si el worker falla, Supervisor lo reiniciará automáticamente

## 🆘 Troubleshooting

### El worker no aparece en los procesos
1. Revisa los logs de deployment en Coolify
2. Verifica que `docker/supervisord.conf` se copió correctamente
3. SSH al contenedor y ejecuta: `supervisorctl status`

### Jobs no se procesan
1. Verifica que `QUEUE_CONNECTION=database` en las variables de entorno
2. Revisa `storage/logs/laravel.log` para errores
3. Ejecuta manualmente: `php artisan queue:work --once`

### Worker se detiene
- Supervisor lo reiniciará automáticamente
- Revisa logs para identificar el error
- Aumenta el timeout si es necesario: `--timeout=300`
