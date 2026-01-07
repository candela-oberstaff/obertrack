@extends('emails.layout')

@section('title', 'Nueva Tarea Asignada - Obertrack')

@section('content')
    <h1 class="title">📋 Nueva Tarea Asignada</h1>
    
    <p class="text">
        Hola, <strong>{{ $assignedBy }}</strong> te ha asignado una nueva tarea en Obertrack. 
        Aquí tienes los detalles:
    </p>
    
    <div class="highlight-box">
        <h2 style="font-size: 20px; color: #1a202c; margin-top: 0; margin-bottom: 12px;">{{ $taskTitle }}</h2>
        
        @if($taskDescription)
            <div style="color: #4a5568; margin-bottom: 20px; white-space: pre-wrap; font-style: italic;">
                "{{ $taskDescription }}"
            </div>
        @endif
        
        <table style="width: 100%;">
            <tr>
                <td style="padding-bottom: 8px; width: 40%; color: #718096; font-size: 14px; font-weight: 600;">Prioridad</td>
                <td style="padding-bottom: 8px;">
                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 700; background-color: {{ $priorityColor }}; color: #ffffff;">
                        {{ $priority }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding-bottom: 8px; color: #718096; font-size: 14px; font-weight: 600;">Fecha de inicio</td>
                <td style="padding-bottom: 8px; color: #2d3748; font-size: 14px;">{{ $startDate }}</td>
            </tr>
            <tr>
                <td style="padding-bottom: 8px; color: #718096; font-size: 14px; font-weight: 600;">Fecha límite</td>
                <td style="padding-bottom: 8px; color: #2d3748; font-size: 14px;">{{ $endDate }}</td>
            </tr>
        </table>
    </div>

    <div class="button-container">
        <a href="{{ $taskUrl }}" class="button">Ver Tarea en Obertrack</a>
    </div>

    <p class="text" style="font-size: 14px; color: #718096; text-align: center;">
        💡 Recuerda mantener actualizados tus registros de horas asociados a esta tarea.
    </p>
@endsection
