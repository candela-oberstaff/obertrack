<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Semanal - {{ $professional->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #22A9C8; padding-bottom: 20px; }
        .brand { color: #22A9C8; font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; margin-top: 10px; }
        .report-title { font-size: 20px; margin-top: 5px; color: #1f2937; }
        .logo { width: 80px; height: auto; margin-bottom: 10px; }
        
        /* ... existing styles ... */
        .meta-info { margin-bottom: 30px; background: #f3f4f6; padding: 15px; border-radius: 8px; border-left: 4px solid #22A9C8; }
        
        .stat-box { text-align: center; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; }
        .stat-value { font-size: 24px; font-weight: bold; color: #111827; display: block; margin-top: 5px; }
        .stat-label { font-size: 12px; text-transform: uppercase; color: #6b7280; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; background-color: #f9fafb; padding: 10px; border-bottom: 2px solid #e5e7eb; font-size: 12px; text-transform: uppercase; color: #6b7280; }
        td { padding: 12px 10px; border-bottom: 1px solid #e5e7eb; }
        .status-present { color: #059669; font-weight: bold; }
        .status-absent { color: #dc2626; font-weight: bold; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Obertrack Logo">
        <div class="brand">Obertrack</div>
        <div class="report-title">Reporte Semanal de Actividad</div>
    </div>

    <div class="meta-info">
        <div class="meta-item"><span class="label">Profesional:</span> {{ $professional->name }}</div>
        <div class="meta-item"><span class="label">Cargo:</span> {{ $professional->job_title ?? 'No especificado' }}</div>
        <div class="meta-item"><span class="label">Periodo:</span> {{ $weekStart->format('d/m/Y') }} al {{ $weekEnd->format('d/m/Y') }}</div>
        <div class="meta-item"><span class="label">Empresa:</span> {{ Auth::user()->name }}</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td width="50%" style="border: none; padding: 0 5px 10px 0;">
                <div class="stat-box">
                    <span class="stat-label">Total Horas</span>
                    <span class="stat-value">{{ $totalHours }}</span>
                </div>
            </td>
            <td width="50%" style="border: none; padding: 0 0 10px 5px;">
                <div class="stat-box">
                    <span class="stat-label">Promedio Diario</span>
                    <span class="stat-value">{{ $weeklyAverage }}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td width="50%" style="border: none; padding: 10px 5px 0 0;">
                <div class="stat-box">
                    <span class="stat-label">Ausencias</span>
                    <span class="stat-value">{{ $absences }}</span>
                </div>
            </td>
            <td width="50%" style="border: none; padding: 10px 0 0 5px;">
                <div class="stat-box">
                    <span class="stat-label">Tareas Incompletas</span>
                    <span class="stat-value">{{ $incompleteTasks }}</span>
                </div>
            </td>
        </tr>
    </table>

    <h3>Detalle Diario</h3>
    <table>
        <thead>
            <tr>
                <th>Día</th>
                <th>Fecha</th>
                <th>Horas Registradas</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyHours as $day)
            <tr>
                <td>{{ $day['day'] }}</td>
                <td>{{ $weekStart->copy()->addDays(array_search($day['day'], ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']))->format('d/m/Y') }}</td>
                <td>{{ $day['hours'] }} horas</td>
                <td>
                    @if($day['hours'] > 0)
                        <span class="status-present">Presente</span>
                    @else
                        <span class="status-absent">Ausente</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <h3>Comentarios y Observaciones</h3>
        @php $hasComments = false; @endphp
        @foreach($comments as $record)
            @if($record->user_comment || $record->approval_comment)
                @php $hasComments = true; @endphp
                <div style="background: #f9fafb; padding: 12px; border-left: 4px solid #22A9C8; margin-bottom: 10px;">
                    <div style="font-size: 11px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 5px;">
                        {{ Carbon\Carbon::parse($record->work_date)->format('l d/m/Y') }}
                    </div>
                    @if($record->user_comment)
                        <div style="font-size: 13px; margin-bottom: 4px;">
                            <span style="font-weight: bold;">Profesional:</span> {{ $record->user_comment }}
                        </div>
                    @endif
                    @if($record->approval_comment)
                        <div style="font-size: 13px;">
                            <span style="font-weight: bold;">Empresa:</span> {{ $record->approval_comment }}
                        </div>
                    @endif
                </div>
            @endif
        @endforeach

        @if(!$hasComments)
            <p style="color: #6b7280; font-style: italic;">No hay comentarios registrados para este periodo.</p>
        @endif
    </div>

    <div class="footer">
        Generado automáticamente por Obertrack el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
