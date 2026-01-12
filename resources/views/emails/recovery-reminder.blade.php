@extends('emails.layout')

@section('title', 'Recordatorio de Recuperación de Horas - Obertrack')

@section('styles')
    <style>
        .recovery-info {
            background-color: #f0f9ff;
            border: 1px solid #e0f2fe;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .info-title {
            font-size: 18px;
            color: #0369a1;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .pending-box {
            background-color: #ffffff;
            border: 1px dashed #7dd3fc;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin-top: 16px;
        }
        .pending-hours {
            font-size: 24px;
            color: #0284c7;
            font-weight: 800;
        }
        .deadline-box {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e0f2fe;
            text-align: center;
        }
        .deadline-date {
            font-size: 16px;
            color: #0369a1;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <h1 class="title">⏰ Horas pendientes de recuperación</h1>
    
    <p class="text">
        Hola <strong>{{ $recipientName }}</strong>,
    </p>
    
    <p class="text">
        Este es un recordatorio de que actualmente tienes horas pendientes de recuperar debido a ausencias registradas.
    </p>
    
    <div class="recovery-info">
        <div class="info-title">Estado de Recuperación</div>
        <div class="pending-box">
            <div style="font-size: 12px; text-transform: uppercase; color: #718096; margin-bottom: 4px; font-weight: 600;">Total pendiente</div>
            <div class="pending-hours">{{ $pendingHours }} horas</div>
        </div>
        
        <div class="deadline-box">
            <p class="text" style="font-size: 14px; margin-bottom: 4px;">
                Tienes tiempo para compensar estas horas hasta:
            </p>
            <div class="deadline-date">{{ $deadline }}</div>
        </div>
    </div>
    
    <p class="text">
        Mantener tu balance de horas al día es fundamental para el seguimiento de tus tareas y el proceso de facturación mensual.
    </p>
    
    <div class="button-container">
        <a href="{{ route('empleado.registrar-horas') }}" class="button">Registrar recuperación</a>
    </div>
    
    <p class="text" style="font-size: 14px; color: #718096; margin-top: 30px; border-top: 1px solid #edf2f7; padding-top: 20px;">
        💡 <strong>Obertrack Tip:</strong> Recuerda usar la opción "Recuperar horas" al registrar tu actividad para que se descuenten de tu saldo pendiente.
    </p>
@endsection
