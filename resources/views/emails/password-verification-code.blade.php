<!DOCTYPE html>
<html>
<head>
    <title>Código de Verificación</title>
    <style>
        body { font-family: sans-serif; background-color: #f3f4f6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .code { font-size: 32px; font-weight: bold; color: #0976d6; text-align: center; letter-spacing: 5px; margin: 20px 0; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #0f172a;">Solicitud de cambio de contraseña</h2>
        </div>
        <p>Hola,</p>
        <p>Hemos recibido una solicitud para cambiar tu contraseña. Utiliza el siguiente código para verificar tu identidad:</p>
        
        <div class="code">{{ $code }}</div>
        
        <p>Este código expira en 15 minutos.</p>
        <p>Si no has solicitado este cambio, puedes ignorar este correo.</p>

        <div class="footer">
            &copy; {{ date('Y') }} Obertrack. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
