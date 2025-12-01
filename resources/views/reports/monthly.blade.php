<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual de Horas Trabajadas</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Reporte Mensual de Horas Trabajadas</h1>
    <p>Empleado: {{ $employee->name }}</p>
    <p>Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    <p>Total de horas trabajadas: {{ $totalHours }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Horas Trabajadas</th>
                <th>Comentarios</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workHours as $workHour)
                <tr>
                    <td>{{ $workHour->work_date->format('d/m/Y') }}</td>
                    <td>{{ $workHour->hours_worked }}</td>
                    <td>{{ $workHour->approval_comment }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Certifico que las horas aquí mostradas son correctas y autorizo el pago al Profesional.</p>
    <p>Firma del cliente: ________________________</p>
</body>
</html>