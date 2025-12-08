<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Mensual - {{ $professional->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #3b82f6; padding-bottom: 20px; }
        .brand { color: #3b82f6; font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .report-title { font-size: 20px; margin-top: 10px; color: #1f2937; }
        .meta-info { margin-bottom: 30px; background: #f3f4f6; padding: 15px; border-radius: 8px; }
        .meta-item { margin-bottom: 5px; }
        .label { font-weight: bold; color: #4b5563; }
        
        .stat-box { text-align: center; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; margin-bottom: 20px; }
        .stat-value { font-size: 32px; font-weight: bold; color: #111827; display: block; }
        .stat-label { font-size: 14px; text-transform: uppercase; color: #6b7280; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; background-color: #f9fafb; padding: 10px; border-bottom: 2px solid #e5e7eb; font-size: 12px; text-transform: uppercase; color: #6b7280; }
        td { padding: 12px 10px; border-bottom: 1px solid #e5e7eb; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Obertrack</div>
        <div class="report-title">Reporte Mensual de Rendimiento</div>
    </div>

    <div class="meta-info">
        <div class="meta-item"><span class="label">Profesional:</span> {{ $professional->name }}</div>
        <div class="meta-item"><span class="label">Cargo:</span> {{ $professional->job_title ?? 'No especificado' }}</div>
        <div class="meta-item"><span class="label">Mes:</span> {{ ucfirst($monthDate->locale('es')->monthName) }} {{ $monthDate->year }}</div>
        <div class="meta-item"><span class="label">Empresa:</span> {{ Auth::user()->name }}</div>
    </div>

    <div style="width: 100%; display: table; margin-bottom: 20px;">
        <div style="display: table-cell; padding-right: 10px;">
            <div class="stat-box">
                <span class="stat-label">Total Horas Aprobadas</span>
                <span class="stat-value">{{ $totalApprovedHours }}</span>
            </div>
        </div>
        <div style="display: table-cell; padding-left: 10px;">
            <div class="stat-box">
                <span class="stat-label">Total Semanas Trabajadas</span>
                <span class="stat-value">{{ count($weeksData) }}</span>
            </div>
        </div>
    </div>

    <h3>Resumen Semanal</h3>
    <table>
        <thead>
            <tr>
                <th>Semana</th>
                <th>Periodo</th>
                <th>Horas Totales</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($weeksData as $week)
            <tr>
                <td>Semana {{ $loop->iteration }}</td>
                <td>{{ $week['period'] }}</td>
                <td><strong>{{ $week['hours'] }} horas</strong></td>
                <td>
                    <span style="color: {{ $week['approved'] ? '#059669' : '#d97706' }}; font-weight: bold;">
                        {{ $week['approved'] ? 'Aprobada' : 'Pendiente' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado automáticamente por Obertrack el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
