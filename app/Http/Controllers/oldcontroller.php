<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
 
public function store(Request $request)
{
    $user = auth()->user();
    $task = new Task([
        'title' => $request->input('title'),
        'description' => $request->input('description'),
        'created_by' => $user->id,
        'visible_para' => $user->empleador_id,
        'completed' => $request->input('completed') === '1',
        'duration' => $request->input('duration'),
    ]);
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'duration' => 'required|numeric|min:0',
        // ... otras validaciones ...
    ]);

    $task = new Task($validatedData);
    $task->save();

     // Si el usuario es un empleado, también haz visible la tarea para su empleador
     if (auth()->user()->tipo_usuario == 'empleado') {
        $empleadoPorId = auth()->user()->empleador_id;
        $task->update(['visible_para' => $empleadoPorId]);
    }


    return back()->with('success', 'Tarea creada exitosamente.');
}


public function update(Request $request, $taskId)
{
    // Validar la solicitud antes de actualizar
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:65535',
        'completed' => 'nullable|boolean',
        'duration' => ['nullable', 'string', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
    ]);

    // Encontrar la tarea o lanzar una excepción si no existe
    $task = Task::findOrFail($taskId);
    

    // Actualizar la tarea con los datos validados
    $task->update([
        'title' => $validatedData['title'],
        'description' => $validatedData['description'],
        'completed' => $validatedData['completed'] ?? false,
        'updated_by' => auth()->id(),
        'duration' => $validatedData['duration'], 
        // 'updated_by' => auth()->id(),
    ]);

    // Redirigir con un mensaje de éxito
    return back()->with('success', 'Tarea actualizada exitosamente.');
}


public function destroy($id)
{
    $task = Task::findOrFail($id);
    $task->comments()->delete(); // Elimina todos los comentarios asociados
    $task->delete(); // Ahora elimina la tarea
    return redirect()->back()->with('success', 'Tarea eliminada con éxito');
}


    //     public function index()
    // {
    //     $userId = auth()->id(); // Obtiene el ID del usuario autenticado
    //     $tareas = Task::where('created_by', $userId)->get(); // Filtra las tareas por el usuario autenticado
    //     return view('empleados.edit_tarea', compact('tareas'));
    // }

    public function index()
    {
        $user = auth()->user();
        $empleadorId = $user->empleador_id;
    
    
        $tareas = Task::where(function($query) use ($user, $empleadorId) {
                        $query->where('created_by', $user->id)
                              ->orWhere('visible_para', $empleadorId);
                    })
                    ->with('comments')
                    ->get();
    
    
        return view('empleados.edit_tarea', compact('tareas'));
    }

    public function toggleCompletion($taskId)
    {
        $task = Task::find($taskId);
        $task->update(['completed' => request()->input('completed')]);
        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }


    public function addComment(Request $request, $taskId)
    {
    

        $comment = new Comment([
            'content' => $request->input('content'),
            'task_id' => $request->input('task_id'),
            'user_id' => auth()->id(),
        ]);
        $comment->save();

        return back()->with('success', 'Comentario agregado exitosamente.');
    }



    public function showComments($taskId)
    {
        $task = Task::findOrFail($taskId);
        $comments = $task->comments;

        return view('comments.show', compact('task', 'comments'));
    }

    public function updateComment(Request $request, $taskId, $commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $validatedData = $request->validate([
            'content' => 'required|string|max:65535',
        ]);
    
        $comment->update([
            'content' => $validatedData['content'],
            'updated_by' => auth()->id(), // Opcional: Guardar el ID del usuario que realizó la última actualización
        ]);
    
        return back()->with('success', 'Comentario actualizado exitosamente.');
    }
    

    public function deleteComment($taskId, $commentId)
{
    $comment = Comment::where('task_id', $taskId)->where('id', $commentId)->first();
    if ($comment) {
        $comment->delete();
        return redirect()->route('empleadores.tareas-asignadas', $taskId)->with('success', 'Comentario eliminado exitosamente.');
    }
    return redirect()->back()->withErrors(['message' => 'No se encontró el comentario para eliminar.']);
}



}








 // public function verTareasEmpleados()
    // {
    //     if (Auth::check()) {
    //         $user = Auth::user();
            
    //         // Verifica si el usuario autenticado es un empleador
    //         if ($user->tipo_usuario!= 'empleador') {
    //             abort(403, 'Acceso denegado');
    //         }
    
    //         // Filtra las tareas que están asignadas al empleador actual
    //         $tareas = Task::where('visible_para', $user->id)->get();
    
    //         return view('empleadores.ver_tareas_empleados', compact('tareas'));
    //     } else {
    //         abort(401, 'No autorizado');
    //     }
    // }
    
    // public function verTareasEmpleados(Request $request)
    // {
    //     $user = auth()->user();

    //     // Obtener los IDs de los empleados asignados a este empleador
    //     $empleadosIds = User::where('empleador_id', $user->id)->pluck('id');

    //     // Obtener las tareas de estos empleados
    //     $tareas = Task::whereIn('created_by', $empleadosIds)->with('comments');

    //     // Filtrado por estado
    //     if ($request->has('status') && $request->status !== 'all') {
    //         // Filtrar por estado booleano
    //         $tareas = $tareas->where('completed', $request->status === 'completed' ? 1 : 0);
    //     }

    //     // Búsqueda por título
    //     if ($request->has('search') && $request->search) {
    //         $tareas = $tareas->where('title', 'like', '%' . $request->search . '%');
    //     }

    //     $tareas = $tareas->get();

    //     // Preparar datos para el gráfico
    //     $taskData = $tareas->groupBy(function($tarea) {
    //         return $tarea->created_at->format('Y-m');
    //     });

    //     // Contar tareas completadas y pendientes por mes
    //     $chartData = [];
    //     foreach ($taskData as $mes => $tareasDelMes) {
    //         $chartData[$mes] = [
    //             'total' => $tareasDelMes->count(),
    //             'completed' => $tareasDelMes->where('completed', 1)->count(), // Cambiado a 1
    //             'pending' => $tareasDelMes->where('completed', 0)->count(), // Cambiado a 0
    //         ];
    //     }

    //     return view('empleadores.ver_tareas_empleados', compact('tareas', 'chartData'));
    // }




 // private function getMonthlyReportData($empleados, $month)
    // {
    //     $reportData = [];

    //     foreach ($empleados as $empleado) {
    //         $workHours = WorkHours::where('user_id', $empleado->id)
    //             ->whereYear('work_date', $month->year)
    //             ->whereMonth('work_date', $month->month)
    //             ->get()
    //             ->groupBy(function ($date) {
    //                 return Carbon::parse($date->work_date)->startOfWeek()->format('Y-m-d');
    //             });

    //         foreach ($workHours as $weekStart => $hours) {
    //             $weekEnd = Carbon::parse($weekStart)->endOfWeek()->format('Y-m-d');
    //             $totalHours = $hours->sum('hours_worked');
    //             $isApproved = $hours->every('approved');

    //             $reportData[] = [
    //                 'empleado' => $empleado->name,
    //                 'semana' => "$weekStart - $weekEnd",
    //                 'horas_trabajadas' => $totalHours,
    //                 'estado' => $isApproved ? 'Aprobada' : 'Pendiente'
    //             ];
    //         }
    //     }

    //     return $reportData;
    // }


// private function notifyZapier($month, $csvContent)
// {
//     $zapierWebhookUrl = 'https://hooks.zapier.com/hooks/catch/12433184/24b9yg9/';

//     $data = [
//         'month' => $month->format('F Y'),
//         'employer' => auth()->user()->name,
//         'employer_email' => auth()->user()->email,
//         'csv_content' => $csvContent,
//         'download_time' => now()->toDateTimeString(),
//     ];

//     // Extraer información adicional del CSV
//     $lines = explode("\n", $csvContent);
//     $data['total_hours'] = 0;
//     $data['total_records'] = 0;

//     foreach ($lines as $line) {
//         if (strpos($line, 'Total de horas:') !== false) {
//             $parts = str_getcsv($line);
//             $data['total_hours'] = $parts[1];
//         } elseif (strpos($line, 'Total de registros:') !== false) {
//             $parts = str_getcsv($line);
//             $data['total_records'] = $parts[1];
//         }
//     }

//     // Extraer la firma del empleador
//     $employerSignature = '';
//     foreach (array_reverse($lines) as $line) {
//         if (strpos($line, 'Firma:') !== false) {
//             $parts = str_getcsv($line);
//             $employerSignature = $parts[1];
//             break;
//         }
//     }
//     $data['employer_signature'] = $employerSignature;

//     Http::post($zapierWebhookUrl, $data);
// }







// private function notifyZapier($month, $csvContent)
// {
//     $zapierWebhookUrl = 'https://hooks.zapier.com/hooks/catch/12433184/24b9yg9/';

//     // Extraer información del CSV
//     $lines = explode("\n", $csvContent);
//     $totalHours = 0;
//     $totalRecords = 0;
//     $tableRows = [];
//     $dataRows = []; // Para almacenar datos individuales

//     foreach ($lines as $line) {
//         if (strpos($line, 'Total de horas:') !== false) {
//             $parts = str_getcsv($line);
//             $totalHours = $parts[1];
//         } elseif (strpos($line, 'Total de registros:') !== false) {
//             $parts = str_getcsv($line);
//             $totalRecords = $parts[1];
//         } elseif (strpos($line, 'PROFESIONAL') !== false) {
//             // Skip header line
//             continue;
//         } elseif (!empty($line)) {
//             $row = str_getcsv($line);
//             if (count($row) == 5) {
//                 // Agregar fila a la tabla HTML
//                 $tableRows[] = '<tr>
//                     <td style="border: 1px solid #ddd; padding: 2px;">' . htmlspecialchars($row[1]) . '</td> 
//                     <td style="border: 1px solid #ddd; padding: 2px;">' . htmlspecialchars($row[2]) . '</td> 
//                     <td style="border: 1px solid #ddd; padding: 2px;">' . htmlspecialchars($row[3]) . '</td> 
//                     <td style="border: 1px solid #ddd; padding: 2px;">' . htmlspecialchars($row[4]) . '</td> 
//                 </tr>';
                
//                 // Agregar datos individuales
//                 $dataRows[] = [
//                     'profesional' => $row[1],
//                     'semana' => $row[2],
//                     'horas_trabajadas' => $row[3],
//                     'estado' => $row[4],
//                 ];
//             }
//         }
//     }

//     // Construir el cuerpo del mensaje HTML
//     $htmlBody = "
//     <html>
//     <head>
//         <style>
//             body { font-family: Arial, sans-serif; line-height: 1.2; margin: 0; padding: 0; }
//             h2, h3 { margin: 0; padding: 0; }
//             p { margin: 0; padding: 0; }
//             table { width: 100%; border-collapse: collapse; }
//             th, td { border: 1px solid #ddd; padding: 2px; }
//         </style>
//     </head>
//     <body>
//         <h2>Reporte Mensual de Horas Trabajadas</h2>
//         <p><strong>Mes:</strong> " . htmlspecialchars($month->format('F Y')) . "</p>
//         <p><strong>Generado el:</strong> " . htmlspecialchars(now()->format('d/m/Y H:i:s')) . "</p>
//         <p><strong>Empleador:</strong> " . htmlspecialchars(auth()->user()->name) . "</p>
//         <p><strong>Email del Empleador:</strong> " . htmlspecialchars(auth()->user()->email) . "</p>
        
//         <h3>Detalles del Reporte</h3>
//         <table>
//             <thead>
//                 <tr>
//                     <th>Profesional</th>
//                     <th>Semana</th>
//                     <th>Horas Trabajadas</th>
//                     <th>Estado</th>
//                 </tr>
//             </thead>
//             <tbody>
//                 " . implode('', $tableRows) . "
//             </tbody>
//         </table>
        
//         <h3>Resumen</h3>
//         <p><strong>Total de registros:</strong> $totalRecords</p>
//         <p><strong>Total de horas:</strong> $totalHours</p>
        
//         <h3>Firma</h3>
//         <p><strong>Empleador:</strong> " . htmlspecialchars(auth()->user()->name) . "</p>
//         <p><strong>Fecha:</strong> " . htmlspecialchars(now()->format('d/m/Y')) . "</p>
//     </body>
//     </html>
//     ";

//     // Enviar los datos a Zapier
//     $data = [
//         'html_body' => $htmlBody,
//         'download_time' => now()->toDateTimeString(),
//         'total_hours' => $totalHours,
//         'total_records' => $totalRecords,
//         'data_rows' => $dataRows, // Enviar datos individuales
//         'month' => $month->format('F Y'),
//         'employer_name' => auth()->user()->name,
//         'employer_email' => auth()->user()->email,
//     ];

//     Http::post($zapierWebhookUrl, $data);
// }


// public function downloadMonthlyReport($month)
// {
//     $month = Carbon::parse($month);
//     $empleados = User::where('empleador_id', auth()->id())->get();
    
//     $filename = 'reporte_mensual_' . $month->format('Y_m') . '.csv';
    
//     $headers = [
//         "Content-type" => "text/csv",
//         "Content-Disposition" => "attachment; filename=$filename",
//         "Pragma" => "no-cache",
//         "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
//         "Expires" => "0"
//     ];

//     $columns = ['Empleado', 'Semana', 'Horas Trabajadas', 'Estado'];

//     $callback = function() use ($empleados, $month, $columns) {
//         $file = fopen('php://output', 'w');
//         fputcsv($file, $columns);

//         foreach ($empleados as $empleado) {
//             $workHours = WorkHours::where('user_id', $empleado->id)
//                 ->whereYear('work_date', $month->year)
//                 ->whereMonth('work_date', $month->month)
//                 ->get()
//                 ->groupBy(function ($date) {
//                     return Carbon::parse($date->work_date)->startOfWeek()->format('d/m/Y');
//                 });

//             foreach ($workHours as $weekStart => $hours) {
//                 $totalHours = $hours->sum('hours_worked');
//                 $isApproved = $hours->every('approved');
                
//                 fputcsv($file, [
//                     $empleado->name,
//                     $weekStart,
//                     $totalHours,
//                     $isApproved ? 'Aprobada' : 'Pendiente'
//                 ]);
//             }
//         }

//         fclose($file);
//     };

//     return new StreamedResponse($callback, 200, $headers);
// }