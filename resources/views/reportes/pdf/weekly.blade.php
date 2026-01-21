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
        <img src="{{ public_path('images/logo.png') }}" class="logo" style="width: 250px;" alt="Obertrack Logo">
        <div class="report-title">Reporte Semanal de Actividad</div>
    </div>

    <div class="meta-info">
        <div class="meta-item"><span class="label">Profesional:</span> {{ $professional->name }}</div>
        <div class="meta-item"><span class="label">Cargo:</span> {{ $professional->job_title ?? 'No especificado' }}</div>
        <div class="meta-item"><span class="label">Periodo:</span> {{ $weekStart->format('d/m/Y') }} al {{ $weekEnd->format('d/m/Y') }}</div>
        <div class="meta-item"><span class="label">Empresa:</span> {{ Auth::user()->name }}</div>
    </div>

    <table class="stats-grid" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td width="25%" style="border: none; padding: 0 5px 10px 0;">
                <div class="stat-box">
                    <div class="stat-label">Total Horas</div>
                    <div class="stat-value">{{ $totalHours }}</div>
                </div>
            </td>
            <td width="25%" style="border: none; padding: 0 5px 10px 5px;">
                <div class="stat-box">
                    <div class="stat-label">Recuperadas</div>
                    <div class="stat-value">{{ $recoveredHours }}</div>
                </div>
            </td>
            <td width="25%" style="border: none; padding: 0 5px 10px 5px;">
                <div class="stat-box">
                    <div class="stat-label">Ausencias</div>
                    <div class="stat-value">{{ $absences }}</div>
                </div>
            </td>
            <td width="25%" style="border: none; padding: 0 0 10px 5px;">
                <div class="stat-box">
                    <div class="stat-label">Tareas Inc.</div>
                    <div class="stat-value">{{ $overdueTasks->count() }}</div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: none; padding: 10px 0 0 0;">
                <div class="stat-box" style="background-color: #fef2f2; border-color: #fecaca;">
                    <span class="stat-label" style="color: #991b1b;">Horas de recuperación pendientes (TOTAL)</span>
                    <span class="stat-value" style="color: #b91c1c;">{{ $pendingBalance }} horas</span>
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
                <th>Horas de Tareas</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <tbody>
            @foreach($dailyHours as $day)
            <tr>
                <td>{{ $day['day'] }}</td>
                <td>{{ $weekStart->copy()->addDays(array_search($day['day'], ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']))->format('d/m/Y') }}</td>
                <td>{{ $day['hours'] }} horas</td>
                <td>
                    <span style="color: {{ $day['status_color'] ?? '#dc2626' }}; font-weight: bold;">
                        {{ $day['status_label'] ?? 'Ausente' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top: 30px;">Estado de Tareas</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Tarea</th>
                <th style="width: 25%;">Vencimiento</th>
                <th style="width: 25%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($overdueTasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') : '-' }}</td>
                    <td><span style="color: #dc2626; font-weight: bold;">Incompleta/Vencida</span></td>
                </tr>
            @empty
            @endforelse
            @foreach($inProgressTasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') : '-' }}</td>
                    <td><span style="color: #d97706; font-weight: bold;">En Progreso</span></td>
                </tr>
            @endforeach
            @foreach($completedTasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') : '-' }}</td>
                    <td><span style="color: #059669; font-weight: bold;">Completada</span></td>
                </tr>
            @endforeach
            
            @if($overdueTasks->isEmpty() && $inProgressTasks->isEmpty() && $completedTasks->isEmpty())
                <tr>
                    <td colspan="3" style="text-align: center; color: #6b7280; font-style: italic;">No hay tareas asignadas para este periodo.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <h3>Comentarios y Observaciones</h3>
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
            <p style="color: #6b7280; font-style: italic;">No hay comentarios registrados para este periodo.</p>
        @endif
    </div>

    <div class="footer">
        Generado automáticamente el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
