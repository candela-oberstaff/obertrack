# Instalación de Brevo SDK

## Paso 1: Instalar dependencias

Ejecuta el siguiente comando en tu terminal (donde ya ejecutaste `composer install`):

```bash
composer update sendinblue/api-v3-sdk
```

O si prefieres actualizar todas las dependencias:

```bash
composer update
```

## Paso 2: Verificar instalación

Después de que termine la instalación, verifica que el paquete se instaló correctamente:

```bash
composer show sendinblue/api-v3-sdk
```

## Paso 3: Reiniciar servidor

Si tienes el servidor de desarrollo corriendo, reinícialo:

```bash
# Detén el servidor actual (Ctrl+C)
# Luego inícialo de nuevo
php artisan serve
```

## Solución de problemas

Si obtienes errores durante la instalación, intenta:

```bash
composer clear-cache
composer update sendinblue/api-v3-sdk --with-all-dependencies
```

## Verificar que funciona

Una vez instalado, crea una tarea asignada a un empleado y deberías recibir un email de notificación.

Revisa los logs en `storage/logs/laravel.log` para ver si se envió correctamente.
