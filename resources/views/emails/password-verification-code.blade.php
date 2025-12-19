@extends('emails.layout')

@section('title', 'Código de Verificación - Obertrack')

@section('content')
    <h1 class="title">Solicitud de cambio de contraseña</h1>
    
    <p class="text">
        Hola, hemos recibido una solicitud para cambiar tu contraseña de Obertrack. 
        Utiliza el siguiente código para verificar tu identidad:
    </p>
    
    <div style="text-align: center; margin: 40px 0;">
        <div style="display: inline-block; font-size: 36px; font-weight: 800; color: #22A9C8; letter-spacing: 8px; padding: 16px 32px; border: 2px dashed #cbd5e0; border-radius: 8px; background-color: #f7fafc;">
            {{ $code }}
        </div>
    </div>
    
    <p class="text" style="text-align: center; color: #e53e3e; font-size: 14px; font-weight: 600;">
        Este código expira en 15 minutos.
    </p>

    <p class="text" style="font-size: 14px; border-top: 1px solid #edf2f7; padding-top: 20px; margin-top: 20px;">
        Si no has solicitado este cambio, puedes ignorar este correo de forma segura. 
        Tu contraseña no será modificada a menos que utilices el código anterior.
    </p>
@endsection
