<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Tarea Asignada</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .task-card {
            background-color: #f9fafb;
            border-left: 4px solid {{ $priorityColor }};
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .task-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 10px 0;
        }
        .task-description {
            color: #6b7280;
            margin: 10px 0;
            white-space: pre-wrap;
        }
        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #6b7280;
        }
        .meta-label {
            font-weight: 600;
            color: #374151;
        }
        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background-color: {{ $priorityColor }};
            color: #ffffff;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #667eea;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #5568d3;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .assigned-by {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Nueva Tarea Asignada</h1>
        </div>
        
        <div class="content">
            <p class="assigned-by">
                <strong>{{ $assignedBy }}</strong> te ha asignado una nueva tarea.
            </p>
            
            <div class="task-card">
                <h2 class="task-title">{{ $taskTitle }}</h2>
                
                @if($taskDescription)
                    <p class="task-description">{{ $taskDescription }}</p>
                @endif
                
                <div class="task-meta">
                    <div class="meta-item">
                        <span class="meta-label">Prioridad:</span>
                        <span class="priority-badge">{{ $priority }}</span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Fecha inicio:</span>
                        <span>{{ $startDate }}</span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Fecha fin:</span>
                        <span>{{ $endDate }}</span>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $taskUrl }}" class="button">Ver Tarea en OberTrack</a>
            </div>
        </div>
        
        <div class="footer">
            <p>Este es un correo automático de OberTrack. Por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} OberTrack. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
