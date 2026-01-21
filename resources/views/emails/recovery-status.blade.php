@extends('emails.layout')

@section('title', 'Estado de Recuperación - Obertrack')

@section('content')
    <h1 class="title">
        @if($approved)
            ✅ Recuperación Aprobada
        @else
            ❌ Recuperación Rechazada
        @endif
    </h1>
    
    <p class="text">
        Hola, tu solicitud de recuperación de horas ha sido procesada por <strong>{{ $approvedBy }}</strong>.
    </p>
    
    <div class="highlight-box">
        <table style="width: 100%;">
            <tr>
                <td style="padding-bottom: 8px; width: 40%; color: #718096; font-size: 14px; font-weight: 600;">Fecha</td>
                <td style="padding-bottom: 8px; color: #2d3748; font-size: 14px;">{{ $date }}</td>
            </tr>
            <tr>
                <td style="padding-bottom: 8px; color: #718096; font-size: 14px; font-weight: 600;">Horas</td>
                <td style="padding-bottom: 8px; color: #2d3748; font-size: 14px;">{{ $hours }}h</td>
            </tr>
            <tr>
                <td style="padding-bottom: 8px; color: #718096; font-size: 14px; font-weight: 600;">Estado</td>
                <td style="padding-bottom: 8px;">
                    @if($approved)
                        <span style="color: #059669; font-weight: 700;">Aprobada</span>
                    @else
                        <span style="color: #dc2626; font-weight: 700;">Rechazada</span>
                    @endif
                </td>
            </tr>
            @if($comment)
            <tr>
                <td style="padding-top: 12px; color: #718096; font-size: 14px; font-weight: 600;" colspan="2">Comentario:</td>
            </tr>
            <tr>
                <td style="padding-bottom: 8px; color: #4a5568; font-size: 14px; font-style: italic;" colspan="2">
                    "{{ $comment }}"
                </td>
            </tr>
            @endif
        </table>
    </div>

    <div class="button-container">
        <a href="{{ $dashboardUrl }}" class="button">Ver Mi Historial</a>
    </div>

    <p class="text" style="font-size: 14px; color: #718096; text-align: center;">
        Si tienes dudas, por favor contacta con tu supervisor.
    </p>
@endsection
