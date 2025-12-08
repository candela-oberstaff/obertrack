# Prueba de Notificaciones de Horas Pendientes

## Comando para Probar

Para probar las notificaciones de horas pendientes, ejecuta:

```bash
php artisan notify:pending-hours --days=0
```

> **Nota:** Usamos `--days=0` para que incluya TODAS las horas pendientes, incluso las creadas hoy.

## Requisitos para que funcione

1. **Debe haber horas de trabajo pendientes de aprobación**
   - Ve a la sección de registro de horas
   - Registra algunas horas como empleado
   - NO las apruebes todavía

2. **El empleador debe tener un email configurado**
   - Verifica que el usuario empleador tenga un email válido en la base de datos

3. **Debe haber una relación empleador-empleado**
   - El empleado debe estar asociado a un empleador (`empleador_id`)

## Qué esperar

El comando mostrará:
```
Checking for pending hours older than 0 days...
✓ Notification sent to [Nombre Empleador] ([email])

Total notifications sent: 1
```

## Verificar el Email

1. Revisa la bandeja de entrada del empleador
2. Deberías recibir un email con:
   - Resumen de horas pendientes por empleado
   - Total de horas pendientes
   - Botón para ir a aprobar

## Troubleshooting

Si no se envía el email, verifica:

1. **Logs:**
   ```bash
   Get-Content storage\logs\laravel.log -Tail 30
   ```

2. **Horas pendientes en la BD:**
   ```sql
   SELECT * FROM work_hours WHERE approved = false;
   ```

3. **Empleadores con email:**
   ```sql
   SELECT id, name, email FROM users WHERE tipo_usuario = 'empleador';
   ```

## Programación Automática

Una vez que funcione, el comando se ejecutará automáticamente:
- **Cuándo:** Cada lunes a las 9:00 AM
- **Configurado en:** `routes/console.php`
- **Qué hace:** Busca horas pendientes de más de 7 días y envía notificaciones
