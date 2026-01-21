@extends('emails.layout')

@section('title', 'Horas Aprobadas - Obertrack')

@section('content')
    <h1 class="title">✅ Horas Aprobadas</h1>
    
    <p class="text">
        ¡Hola! Tus horas registradas han sido aprobadas por <strong>{{ $approvedBy }}</strong>.
    </p>
    
    <div class="highlight-box">
        <table style="width: 100%;">
            <tr>
                <td style="padding-bottom: 8px; width: 40%; color: #718096; font-size: 14px; font-weight: 600;">Tipo de aprobación</td>
                <td style="padding-bottom: 8px; color: #2d3748; font-size: 14px;">{{ $type }}</td>
            </tr>
            @if($period)
            <tr>
                <td style="padding-bottom: 8px; color: #718096; font-size: 14px; font-weight: 600;">Periodo</td>
                <td style="padding-bottom: 8px; color: #2d3748; font-size: 14px;">{{ $period }}</td>
            </tr>
            @endif
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
        <a href="{{ $dashboardUrl }}" class="button">Ver Panel de Control</a>
    </div>

    <p class="text" style="font-size: 14px; color: #718096; text-align: center;">
        Buen trabajo manteniendo tus registros al día.
    </p>
@endsection
