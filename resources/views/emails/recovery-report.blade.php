@extends('emails.layout')

@section('title', 'Reporte de Horas Recuperadas - ' . $recoveryData['employee_name'] . ' - Obertrack')

@section('styles')
    <style>
        .recovery-details {
            width: 100%;
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .detail-item {
            margin-bottom: 16px;
        }
        .detail-item:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-size: 13px;
            color: #718096;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .detail-value {
            font-size: 16px;
            color: #2d3748;
            font-weight: 700;
        }
        .hours-badge {
            display: inline-block;
            background-color: #22A9C8;
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 800;
        }
        .activities-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .activity-list {
            margin: 0;
            padding-left: 20px;
            color: #4a5568;
        }
        .activity-list li {
            margin-bottom: 8px;
        }
        .activity-list li:last-child {
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
    <h1 class="title">🔄 Reporte de Horas Recuperadas</h1>
    
    <p class="text">
        El profesional <strong>{{ $recoveryData['employee_name'] }}</strong> ha enviado un reporte detallado de las horas recuperadas.
    </p>
    
    <div class="recovery-details">
        <table width="100%">
            <tr>
                <td width="50%" class="detail-item">
                    <div class="detail-label">Fecha de recuperación</div>
                    <div class="detail-value">{{ $recoveryData['date'] }}</div>
                </td>
                <td width="50%" class="detail-item">
                    <div class="detail-label">Horas recuperadas</div>
                    <div class="hours-badge">{{ $recoveryData['hours'] }} hs</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="activities-box">
        <div class="detail-label" style="margin-bottom: 12px;">Actividades realizadas</div>
        @php
            $activities = explode("\n", $recoveryData['activities']);
            $activities = array_filter(array_map('trim', $activities));
        @endphp

        @if(count($activities) > 0)
            <ul class="activity-list">
                @foreach($activities as $activity)
                    <li>{{ $activity }}</li>
                @endforeach
            </ul>
        @else
            <p style="color: #718096; margin: 0; font-style: italic;">{{ $recoveryData['activities'] }}</p>
        @endif
    </div>
    
    <p class="text">
        Puedes revisar y aprobar este reporte desde tu panel administrativo.
    </p>
    
    <div class="button-container">
        <a href="{{ route('reportes.index') }}" class="button">Ver en el Dashboard</a>
    </div>
    
    <p class="text" style="font-size: 14px; color: #718096; margin-top: 30px; border-top: 1px solid #edf2f7; padding-top: 20px;">
        💡 <strong>Obertrack Tip:</strong> Las horas recuperadas ayudan a mantener equilibrada la jornada mensual de tus profesionales.
    </p>
@endsection
