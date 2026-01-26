# Queue Worker Setup - Obertrack

Este documento explica cómo configurar el queue worker para que los mensajes masivos de WhatsApp funcionen correctamente.

## ¿Por qué es necesario?

Los mensajes masivos de WhatsApp se envían mediante **jobs en cola** para:
- Evitar timeouts en el navegador
- Controlar el ritmo de envío (1 mensaje por minuto)
- Proteger la cuenta de WhatsApp contra baneos
- Permitir envíos en segundo plano

**Sin un queue worker activo, los mensajes NO se enviarán.**

## Configuración por Entorno

### 🖥️ Desarrollo Local (Windows)

#### Opción 1: Manual (Temporal)
Abre una terminal separada y ejecuta:
```bash
php artisan queue:work
```
Mantén esta terminal abierta mientras desarrollas.

#### Opción 2: Batch Script
Ejecuta el archivo `start-queue-worker.bat` haciendo doble clic.

#### Opción 3: Servicio de Windows (Recomendado)
1. Instala NSSM: https://nssm.cc/download
2. Ajusta la ruta de PHP en `install-windows-service.ps1` (línea 6)
3. Ejecuta PowerShell como Administrador
4. Navega a la carpeta del proyecto
5. Ejecuta: `.\install-windows-service.ps1`

El servicio se iniciará automáticamente con Windows.

### 🐧 Producción (Linux con Supervisor)

1. Copia el archivo de configuración:
```bash
sudo cp supervisor-obertrack-worker.conf /etc/supervisor/conf.d/obertrack-worker.conf
```

2. Ajusta las rutas en el archivo según tu servidor:
   - `command`: Ruta completa a PHP y artisan
   - `stdout_logfile`: Ruta a los logs
   - `user`: Usuario del servidor web (www-data, nginx, apache, etc.)

3. Recarga Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start obertrack-worker:*
```

4. Verifica el estado:
```bash
sudo supervisorctl status obertrack-worker:*
```

## Verificar que Funciona

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

### Limpiar jobs fallidos:
```bash
php artisan queue:flush
```

## Logs

Los logs del worker se guardan en:
- **Windows Service**: `storage/logs/queue-worker.log`
- **Supervisor**: `storage/logs/worker.log`
- **Manual**: Salida en la terminal

## Troubleshooting

### Los mensajes no se envían
1. Verifica que el worker esté corriendo:
   ```bash
   # Windows
   nssm status ObertrackQueueWorker
   
   # Linux
   sudo supervisorctl status obertrack-worker:*
   ```

2. Revisa los logs en `storage/logs/laravel.log`

3. Verifica la tabla `jobs` en la base de datos:
   ```sql
   SELECT * FROM jobs;
   ```

### El worker se detiene constantemente
- Aumenta `max-time` en el comando
- Revisa errores en los logs
- Verifica que la sesión de WhatsApp esté activa

### Cambios en el código no se reflejan
Reinicia el worker después de cambios en el código:
```bash
# Windows
nssm restart ObertrackQueueWorker

# Linux
sudo supervisorctl restart obertrack-worker:*

# Manual
Ctrl+C y vuelve a ejecutar php artisan queue:work
```

## Comandos Útiles

```bash
# Ver trabajos en tiempo real
php artisan queue:work --verbose

# Procesar solo un job (para pruebas)
php artisan queue:work --once

# Detener el worker después de procesar el job actual
php artisan queue:restart
```
