<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Semanal</title>
</head>
<body>
    <h1>Reporte Semanal de Horas Trabajadas</h1>
    <p>Semana del {{ $weekStart->format('d/m/Y') }} al {{ $weekEnd->format('d/m/Y') }}</p>
    <p>Total de horas trabajadas: {{ $totalHours }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Horas Trabajadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workHours as $workHour)
                <tr>
                    <td>{{ $workHour->work_date->format('d/m/Y') }}</td>
                    <td>{{ $workHour->hours_worked }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>