<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horas Pendientes por Aprobar</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .summary-card {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .summary-title {
            font-size: 18px;
            font-weight: 600;
            color: #92400e;
            margin: 0 0 15px 0;
        }
        .hours-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .hours-item {
            padding: 12px;
            margin: 8px 0;
            background-color: #ffffff;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #fde68a;
        }
        .hours-date {
            font-weight: 600;
            color: #374151;
        }
        .hours-amount {
            font-size: 18px;
            font-weight: 700;
            color: #f59e0b;
        }
        .total-hours {
            background-color: #f59e0b;
            color: #ffffff;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin: 20px 0;
        }
        .total-hours .label {
            font-size: 14px;
            opacity: 0.9;
        }
        .total-hours .amount {
            font-size: 32px;
            font-weight: 700;
            margin-top: 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #f59e0b;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #d97706;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .intro-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Horas Pendientes por Aprobar</h1>
        </div>
        
        <div class="content">
            <p class="intro-text">
                Tienes horas de trabajo pendientes de aprobación para tus empleados.
            </p>
            
            <div class="summary-card">
                <h2 class="summary-title">Resumen de Horas Pendientes</h2>
                
                @if(count($pendingHours) > 0)
                    <ul class="hours-list">
                        @foreach($pendingHours as $item)
                            <li class="hours-item">
                                <span class="hours-date">
                                    {{ $item['employee_name'] ?? 'Empleado' }}
                                    @if(isset($item['week']))
                                        - Semana {{ $item['week'] }}
                                    @endif
                                </span>
                                <span class="hours-amount">{{ $item['hours'] }} hs</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: #6b7280; margin: 0;">No hay detalles específicos disponibles.</p>
                @endif
            </div>
            
            <div class="total-hours">
                <div class="label">Total de Horas Pendientes</div>
                <div class="amount">{{ $totalHours }} hs</div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $approvalUrl }}" class="button">Ir a Aprobar Horas</a>
            </div>
            
            <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
                💡 <strong>Recordatorio:</strong> Es importante aprobar las horas de trabajo de tus empleados 
                de manera oportuna para mantener un registro preciso y actualizado.
            </p>
        </div>
        
        <div class="footer">
            <p>Este es un correo automático de OberTrack. Por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} OberTrack. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
