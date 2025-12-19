@extends('emails.layout')

@section('title', 'Horas Pendientes por Aprobar - Obertrack')

@section('styles')
    <style>
        .hours-list {
            width: 100%;
            margin-bottom: 24px;
        }
        .hours-row {
            border-bottom: 1px solid #edf2f7;
        }
        .hours-row:last-child {
            border-bottom: none;
        }
        .employee-cell {
            padding: 12px 0;
            font-weight: 600;
            color: #2d3748;
        }
        .amount-cell {
            padding: 12px 0;
            text-align: right;
            font-weight: 700;
            color: #22A9C8;
            font-size: 18px;
        }
        .total-box {
            background-color: #22A9C8;
            color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
    </style>
@endsection

@section('content')
    <h1 class="title">⏰ Horas Pendientes por Aprobar</h1>
    
    <p class="text">
        Tienes horas de trabajo pendientes de aprobación para tus empleados. 
        Por favor, revísalas para mantener el control de tu equipo.
    </p>
    
    <div class="highlight-box">
        <h2 style="font-size: 18px; color: #2d3748; margin-top: 0; margin-bottom: 16px;">Resumen por empleado</h2>
        
        @if(count($pendingHours) > 0)
            <table class="hours-list">
                @foreach($pendingHours as $item)
                    <tr class="hours-row">
                        <td class="employee-cell">
                            {{ $item['employee_name'] ?? 'Empleado' }}
                            @if(isset($item['week']))
                                <br><span style="font-size: 12px; color: #718096; font-weight: normal;">Semana {{ $item['week'] }}</span>
                            @endif
                        </td>
                        <td class="amount-cell">{{ $item['hours'] }} hs</td>
                    </tr>
                @endforeach
            </table>
        @else
            <p style="color: #718096; margin: 0; font-style: italic;">No hay detalles específicos disponibles.</p>
        @endif
    </div>
    
    <div class="total-box">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 4px; font-weight: 600;">Total de Horas Pendientes</div>
        <div style="font-size: 36px; font-weight: 800;">{{ $totalHours }} hs</div>
    </div>
    
    <div class="button-container">
        <a href="{{ $approvalUrl }}" class="button">Ir a Aprobar Horas</a>
    </div>
    
    <p class="text" style="font-size: 14px; color: #718096; margin-top: 30px; border-top: 1px solid #edf2f7; padding-top: 20px;">
        💡 <strong>Recordatorio:</strong> Es importante aprobar las horas de trabajo de tus empleados 
        de manera oportuna para mantener un registro preciso y actualizado.
    </p>
@endsection
