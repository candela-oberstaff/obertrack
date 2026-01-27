<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Comunicación de Obertrack' }}</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <!-- Header -->
        <div style="background-color: #ffffff; padding: 20px 40px; text-align: center; border-bottom: 3px solid #f0f0f0;">
             <!-- Logo -->
             <img src="{{ asset('images/logo.png') }}" alt="Obertrack" style="max-height: 50px;">
        </div>

        <!-- Content -->
        <div style="padding: 40px 40px; color: #333333; font-size: 16px;">
            {!! html_entity_decode($content) !!}
        </div>

        <!-- Footer -->
        <div style="background-color: #f8f8f8; padding: 20px; text-align: center; font-size: 12px; color: #888888; border-top: 1px solid #eeeeee;">
            <p style="margin: 0 0 10px;">&copy; {{ date('Y') }} Obertrack. Todos los derechos reservados.</p>
            <p style="margin: 0;">Has recibido este correo porque estás registrado en la plataforma Obertrack.</p>
        </div>
    </div>
</body>
</html>
