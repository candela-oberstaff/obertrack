<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $companyName }} - Reporte {{ $type }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #111827; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #1f2937; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; color: #6b7280; font-size: 12px; }
        
        .company-info { margin-bottom: 25px; padding: 10px; background-color: #f9fafb; border-radius: 4px; border: 1px solid #f3f4f6; }
        .company-info .label { font-weight: bold; color: #4b5563; }
        
        .professional-section { margin-bottom: 35px; page-break-inside: avoid; }
        .prof-header { 
            background-color: #f3f4f6; 
            padding: 8px 12px; 
            font-size: 14px; 
            font-weight: bold; 
            border-left: 4px solid #3b82f6; 
            color: #1f2937;
            margin-bottom: 15px;
        }
        
        .stats-row { margin-bottom: 15px; padding-left: 5px; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #f9fafb; font-weight: bold; color: #374151; }
        tr:nth-child(even) { background-color: #fdfdfd; }
        
        .text-red { color: #dc2626; }
        .text-green { color: #059669; }
        .text-orange { color: #d97706; }
        .font-bold { font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte {{ $type }} de Actividades</h1>
        <p>Período: {{ $startDate }} - {{ $endDate }}</p>
    </div>

    <div class="company-info">
        <span class="label">Cliente:</span> {{ $companyName }}<br>
        <span class="label">Generado el:</span> {{ date('d/m/Y H:i') }}
    </div>

    @foreach($professionalsData as $prof)
        <div class="professional-section">
            <div class="prof-header">
                {{ $prof['name'] }} <span style="font-weight:normal; color:#6b7280; font-size:12px;">({{ $prof['email'] }})</span>
            </div>
            
            <div class="stats-row">
                <strong>Resumen:</strong> 
                Total Registrado: {{ number_format($prof['total_hours'], 1) }}h | 
                <span class="text-green">Aprobadas: {{ number_format($prof['approved_hours'], 1) }}h</span> | 
                <span class="text-orange">Pendientes: {{ number_format($prof['pending_hours'], 1) }}h</span> | 
                <span class="text-green">Recuperadas: {{ number_format($prof['total_recovered'], 1) }}h</span> |
                <span class="text-red">Ausencias: {{ $prof['absences_count'] }}</span>
            </div>

            <div style="font-weight: bold; margin-bottom: 5px; color: #374151;">Jornadas Laborales</div>
            <table>
                <thead>
                    <tr>
                        <th width="15%">Fecha</th>
                        <th width="10%">Horas</th>
                        <th width="55%">Actividad / Detalles</th>
                        <th width="20%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prof['records'] as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record->work_date)->format('d/m/Y') }}<br><span style="color:#9ca3af; font-size:9px;">{{ \Carbon\Carbon::parse($record->work_date)->locale('es')->isoFormat('dddd') }}</span></td>
                            <td>
                                @if($record->absence_hours > 0)
                                    <span class="text-red font-bold">Ausencia</span><br>
                                    <span style="font-size:9px">({{ $record->absence_hours }}h)</span>
                                @else
                                    {{ $record->hours_worked }}
                                @endif
                            </td>
                            <td>
                                @if($record->absence_hours > 0)
                                    <div class="text-red"><strong>Motivo:</strong> {{ $record->absence_reason }}</div>
                                @else
                                    {{ $record->user_comment ?: 'Sin comentarios' }}
                                @endif
                            </td>
                            <td>
                                @if($record->approved)
                                    <span class="text-green font-bold">Aprobado</span>
                                @elseif($record->absence_hours > 0 && $record->approved)
                                    <span class="text-green font-bold">Validado</span>
                                @elseif($record->absence_hours > 0)
                                     <span class="text-red">Ausencia Reg.</span>
                                @else
                                    <span class="text-orange font-bold">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if(count($prof['records']) === 0)
                        <tr><td colspan="4" style="text-align:center; padding:20px; color:#9ca3af;">Sin jornada registrada.</td></tr>
                    @endif
                </tbody>
            </table>

            @if(count($prof['recovery_records']) > 0)
                <div style="font-weight: bold; margin: 15px 0 5px; color: #2563eb;">Recuperaciones de Horas</div>
                <table>
                    <thead style="background-color: #eff6ff;">
                        <tr>
                            <th width="15%">Fecha</th>
                            <th width="10%">Horas</th>
                            <th width="55%">Actividades de Recuperación</th>
                            <th width="20%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prof['recovery_records'] as $recovery)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($recovery->recovery_date)->format('d/m/Y') }}</td>
                                <td class="text-green font-bold">{{ $recovery->hours_recovered }}h</td>
                                <td>{{ $recovery->activities }}</td>
                                <td>
                                    @if($recovery->approved)
                                        <span class="text-green font-bold">Aprobado</span>
                                    @else
                                        <span class="text-orange font-bold">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
    
    <div class="footer">
        Este documento fue generado automáticamente por la plataforma Obertrack.
    </div>
</body>
</html>
