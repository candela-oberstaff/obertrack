<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Mensual - {{ $professional->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #22A9C8; padding-bottom: 20px; }
        .brand { color: #22A9C8; font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; margin-top: 10px; }
        .report-title { font-size: 20px; margin-top: 5px; color: #1f2937; }
        .logo { width: 80px; height: auto; margin-bottom: 10px; }
        
        /* ... existing styles ... */
        .meta-info { margin-bottom: 30px; background: #f3f4f6; padding: 15px; border-radius: 8px; border-left: 4px solid #22A9C8; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo" style="width: 250px;" alt="Obertrack Logo">
        <div class="report-title">Reporte Mensual de Rendimiento</div>
    </div>

    <div class="meta-info">
        <div class="meta-item"><span class="label">Profesional:</span> {{ $professional->name }}</div>
        <div class="meta-item"><span class="label">Cargo:</span> {{ $professional->job_title ?? 'No especificado' }}</div>
        <div class="meta-item"><span class="label">Mes:</span> {{ ucfirst($monthDate->locale('es')->monthName) }} {{ $monthDate->year }}</div>
        <div class="meta-item"><span class="label">Empresa:</span> {{ Auth::user()->name }}</div>
    </div>

    <table class="stats-grid" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td width="50%" style="border: none; padding: 0 5px 10px 0;">
                <div class="stat-box">
                    <span class="stat-label">Total Horas Aprobadas</span>
                    <span class="stat-value">{{ $totalApprovedHours }}</span>
                </div>
            </td>
            <td width="50%" style="border: none; padding: 0 0 10px 5px;">
                <div class="stat-box">
                    <span class="stat-label">Semanas Registradas</span>
                    <span class="stat-value">{{ count($weeksData) }}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td width="50%" style="border: none; padding: 10px 5px 0 0;">
                <div class="stat-box">
                    <span class="stat-label">Ausencias (Mes)</span>
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

    <h3>Resumen Semanal</h3>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
        <thead>
            <tr>
                <th style="text-align: left; background-color: #f9fafb; padding: 10px; border-bottom: 2px solid #e5e7eb; font-size: 12px; color: #6b7280;">Semana</th>
                <th style="text-align: left; background-color: #f9fafb; padding: 10px; border-bottom: 2px solid #e5e7eb; font-size: 12px; color: #6b7280;">Periodo</th>
                <th style="text-align: left; background-color: #f9fafb; padding: 10px; border-bottom: 2px solid #e5e7eb; font-size: 12px; color: #6b7280;">Horas Totales</th>
                <th style="text-align: left; background-color: #f9fafb; padding: 10px; border-bottom: 2px solid #e5e7eb; font-size: 12px; color: #6b7280;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($weeksData as $week)
            <tr>
                <td style="padding: 12px 10px; border-bottom: 1px solid #e5e7eb;">Semana {{ $loop->iteration }}</td>
                <td style="padding: 12px 10px; border-bottom: 1px solid #e5e7eb;">{{ $week['period'] }}</td>
                <td style="padding: 12px 10px; border-bottom: 1px solid #e5e7eb;"><strong>{{ $week['hours'] }} horas</strong></td>
                <td style="padding: 12px 10px; border-bottom: 1px solid #e5e7eb;">
                    <span style="color: {{ $week['approved'] ? '#059669' : '#d97706' }}; font-weight: bold;">
                        {{ $week['approved'] ? 'Aprobada' : 'Pendiente' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <h3>Comentarios y Observaciones del Mes</h3>
        @php $hasComments = false; @endphp
        @foreach($comments as $record)
            @if($record->user_comment || $record->approval_comment)
                @php $hasComments = true; @endphp
                <div style="background: #f9fafb; padding: 12px; border-left: 4px solid #22A9C8; margin-bottom: 15px;">
                    <div style="font-size: 11px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 8px;">
                        {{ Carbon\Carbon::parse($record->work_date)->translatedFormat('l d/m/Y') }}
                    </div>
                    
                    @if($record->user_comment)
                        @php
                            $pComment = $record->user_comment;
                            $pActivities = [];
                            $pSummary = '';
                            if (str_contains($pComment, 'Resumen adicional:')) {
                                $parts = explode('Resumen adicional:', $pComment);
                                $pActivities = array_filter(explode("\n", trim($parts[0])), fn($a) => !empty(trim($a)));
                                $pSummary = trim($parts[1]);
                            } elseif (str_contains($pComment, "\n")) {
                                $pActivities = array_filter(explode("\n", trim($pComment)), fn($a) => !empty(trim($a)));
                            } else {
                                $pSummary = $pComment;
                            }
                        @endphp
                        
                        <div style="font-size: 13px; margin-bottom: 4px;">
                            <span style="font-weight: bold; color: #1f2937;">Tareas realizadas:</span>
                            @if(count($pActivities) > 0)
                                <ul style="margin: 5px 0 5px 15px; padding: 0; list-style-type: disc;">
                                    @foreach($pActivities as $activity)
                                        <li style="margin-bottom: 2px;">{{ $activity }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if($pSummary)
                                <div style="margin-top: {{ count($pActivities) > 0 ? '5px' : '0' }}; font-style: italic; color: #4b5563;">
                                    {{ $pSummary }}
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($record->approval_comment)
                        <div style="font-size: 13px; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
                            <span style="font-weight: bold; color: #1f2937;">Feedback Empresa:</span>
                            <div style="margin-top: 3px; font-style: italic; color: #4b5563;">
                                "{{ $record->approval_comment }}"
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach

        @if(!$hasComments)
            <p style="color: #6b7280; font-style: italic;">No hay comentarios registrados para este mes.</p>
        @endif
    </div>

    <div class="footer">
        Generado automáticamente por Obertrack el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
