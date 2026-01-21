@extends('emails.layout')

@section('title', 'Estado de Tarea Actualizado - Obertrack')

@section('content')
    <h1 class="title">🔄 Estado de Tarea Actualizado</h1>
    
    <p class="text">
        Hola, te notificamos que <strong>{{ $updatedBy }}</strong> ha actualizado el estado de una tarea asignada.
    </p>
    
    <div class="highlight-box">
        <h2 style="font-size: 18px; color: #1a202c; margin-top: 0; margin-bottom: 12px;">{{ $taskTitle }}</h2>
        
        <table style="width: 100%;">
            <tr>
                <td style="padding-bottom: 8px; width: 40%; color: #718096; font-size: 14px; font-weight: 600;">Estado Anterior</td>
                <td style="padding-bottom: 8px; color: #e53e3e; font-size: 14px;">{{ $previousStatus }}</td>
            </tr>
            <tr>
                <td style="padding-bottom: 8px; color: #718096; font-size: 14px; font-weight: 600;">Nuevo Estado</td>
                <td style="padding-bottom: 8px;">
                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 700; background-color: {{ $completed ? '#10b981' : '#3182ce' }}; color: #ffffff;">
                        {{ $statusLabel }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    @if($completed)
        <p class="text" style="color: #10b981; font-weight: bold; text-align: center; margin-top: 20px;">
            ✅ ¡La tarea ha sido marcada como completada!
        </p>
    @endif

    <div class="button-container">
        <a href="{{ $taskUrl }}" class="button">Ver en Dashboard</a>
    </div>

    <p class="text" style="font-size: 14px; color: #718096; text-align: center;">
        Este es un mensaje automático de seguimiento de tareas de Obertrack.
    </p>
@endsection
