@extends('emails.layout')

@section('title', 'Registro de Ausencia - Obertrack')

@section('styles')
    <style>
        .absence-info {
            background-color: #fff7ed;
            border: 1px solid #ffedd5;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .info-title {
            font-size: 18px;
            color: #9a3412;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .deadline-box {
            background-color: #ffffff;
            border: 1px dashed #fdba74;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin-top: 16px;
        }
        .deadline-date {
            font-size: 20px;
            color: #ea580c;
            font-weight: 800;
        }
    </style>
@endsection

@section('content')
    <h1 class="title">📅 Registro de Ausencia</h1>
    
    <p class="text">
        Hola <strong>{{ $recipientName }}</strong>,
    </p>
    
    <p class="text">
        Hemos registrado tu ausencia para el día <strong>{{ $date }}</strong>. Recuerda que es importante mantener el equilibrio de tus horas mensuales.
    </p>
    
    <div class="absence-info">
        <div class="info-title">Plazo de Recuperación</div>
        <p class="text" style="font-size: 14px; margin-bottom: 0;">
            Tienes tiempo para recuperar estas horas hasta el final del mes en curso:
        </p>
        <div class="deadline-box">
            <div style="font-size: 12px; text-transform: uppercase; color: #718096; margin-bottom: 4px; font-weight: 600;">Fecha límite</div>
            <div class="deadline-date">{{ $endOfMonth }}</div>
        </div>
    </div>
    
    <p class="text">
        Si realizas horas adicionales para compensar esta ausencia, no olvides registrarlas mediante la opción de <strong>"Recuperar horas"</strong> en tu panel.
    </p>
    
    <div class="button-container">
        <a href="{{ route('profesional.registrar-horas') }}" class="button">Ir a mi Registro</a>
    </div>
    
    <p class="text" style="font-size: 14px; color: #718096; margin-top: 30px; border-top: 1px solid #edf2f7; padding-top: 20px;">
        💡 <strong>Obertrack Tip:</strong> Mantener tus horas al día facilita el proceso de facturación y seguimiento de tareas.
    </p>
@endsection
