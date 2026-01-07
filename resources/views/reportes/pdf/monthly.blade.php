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
        <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Obertrack Logo">
        <div class="brand">Obertrack</div>
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
