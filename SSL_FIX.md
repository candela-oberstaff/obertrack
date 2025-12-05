# Solución al Error SSL de Brevo

## Problema
Error al enviar emails: `cURL error 77: error setting certificate file`

## Causa
Laragon/PHP no puede encontrar el archivo de certificados SSL (cacert.pem) en la ruta configurada.

## Solución Implementada

He configurado el cliente Guzzle en `BrevoEmailService.php` para:
- **Desarrollo (local)**: Deshabilitar la verificación SSL
- **Producción**: Usar verificación SSL normal

## Solución Alternativa (Producción)

Si necesitas habilitar SSL en producción, descarga el certificado:

1. Descarga `cacert.pem` desde: https://curl.se/ca/cacert.pem

2. Guárdalo en una ubicación accesible, por ejemplo:
   ```
   C:\laragon\etc\ssl\cacert.pem
   ```

3. Actualiza `php.ini`:
   ```ini
   curl.cainfo = "C:\laragon\etc\ssl\cacert.pem"
   openssl.cafile = "C:\laragon\etc\ssl\cacert.pem"
   ```

4. Reinicia el servidor

5. Modifica `BrevoEmailService.php` para usar verificación SSL:
   ```php
   $guzzleConfig['verify'] = 'C:\laragon\etc\ssl\cacert.pem';
   ```

## Verificar

Crea una tarea asignada a un empleado y verifica que se envíe el email correctamente.
