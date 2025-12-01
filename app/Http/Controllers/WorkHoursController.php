<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;

use App\Models\WorkHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkHoursController extends Controller
{
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'work_date' => 'required|date',
    //         'hours_worked' => 'required|numeric|min:0|max:24',
    //     ]);
    
    //     $workDate = Carbon::parse($request->work_date);
        
    //     if ($workDate->isWeekend()) {
    //         return back()->with('error', 'No se pueden registrar horas en fines de semana.');
    //     }
    
    //     $weekStart = $workDate->copy()->startOfWeek(Carbon::MONDAY);
    //     $weekEnd = $workDate->copy()->endOfWeek(Carbon::FRIDAY);
    
    //     $totalHoursThisWeek = WorkHours::where('user_id', auth()->id())
    //         ->whereBetween('work_date', [$weekStart, $weekEnd])
    //         ->sum('hours_worked');
    
    //     if ($totalHoursThisWeek + $request->hours_worked > 40) {
    //         return back()->with('error', 'No puedes exceder 40 horas por semana hábil.');
    //     }
    
    //     WorkHours::updateOrCreate(
    //         ['user_id' => auth()->id(), 'work_date' => $request->work_date],
    //         ['hours_worked' => $request->hours_worked]
    //     );
    
    //     return back()->with('success', 'Horas registradas correctamente.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'work_date' => 'required|date',
            'hours_worked' => 'required|numeric|min:0|max:24',
        ]);
    
        $workDate = Carbon::parse($request->work_date);
        
        if ($workDate->isWeekend()) {
            return back()->with('error', 'No se pueden registrar horas en fines de semana.');
        }
    
        $weekStart = $workDate->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $workDate->copy()->endOfWeek(Carbon::FRIDAY);
    
        $totalHoursThisWeek = WorkHours::where('user_id', auth()->id())
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->sum('hours_worked');
    
        if ($totalHoursThisWeek + $request->hours_worked > 40) {
            return back()->with('error', 'No puedes exceder 40 horas por semana hábil.');
        }
    
        // Registrar las horas trabajadas
        WorkHours::updateOrCreate(
            ['user_id' => auth()->id(), 'work_date' => $request->work_date],
            ['hours_worked' => $request->hours_worked]
        );
    
        // Calcular el nuevo total de horas para el mes
        $currentMonth = Carbon::parse($request->work_date)->startOfMonth();
        $totalHours = WorkHours::where('user_id', auth()->id())
            ->whereYear('work_date', $currentMonth->year)
            ->whereMonth('work_date', $currentMonth->month)
            ->sum('hours_worked');
    
        // Redirigir con el mensaje de éxito y el total de horas
        return redirect()->route('empleado.registrar-horas')->with([
            'success' => 'Horas registradas correctamente.',
            'totalHours' => $totalHours
        ]);
    }

//     public function getTotalHours()
// {
//     $currentMonth = now()->startOfMonth();
//     $totalHours = WorkHours::where('user_id', auth()->id())
//         ->whereYear('work_date', $currentMonth->year)
//         ->whereMonth('work_date', $currentMonth->month)
//         ->sum('hours_worked');

//     return response()->json(['totalHours' => $totalHours]);
// }


public function index()
{
    $currentMonth = now()->startOfMonth();
    $calendar = $this->generateCalendar($currentMonth);

    // Calcular el total de horas para el mes
    $totalHours = WorkHours::where('user_id', auth()->id())
        ->whereYear('work_date', $currentMonth->year)
        ->whereMonth('work_date', $currentMonth->month)
        ->sum('hours_worked');

    // Si hay un total de horas en la sesión, usarlo
    if (session('totalHours')) {
        $totalHours = session('totalHours');
    }

    return view('work_hours.index', compact('calendar', 'currentMonth', 'totalHours'));
}


    private function generateCalendar($month)
    {
        $calendar = [];
        $startDate = $month->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $endDate = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $workHours = WorkHours::where('user_id', auth()->id())
            ->whereBetween('work_date', [$startDate, $endDate])
            ->get()
            ->keyBy('work_date');

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $calendar[] = [
                'date' => $date->copy(),
                'inMonth' => $date->month === $month->month,
                'workHours' => $workHours->get($date->format('Y-m-d'))
            ];
        }

        return array_chunk($calendar, 7);
    }



    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'work_date' => 'required|date',
    //         'hours_worked' => 'required|numeric|min:0|max:24',
    //     ]);
    
    //     $workDate = Carbon::parse($request->work_date);
        
    //     if ($workDate->isWeekend()) {
    //         return $this->sendResponse(false, 'No se pueden registrar horas en fines de semana.');
    //     }
    
    //     $weekStart = $workDate->copy()->startOfWeek(Carbon::MONDAY);
    //     $weekEnd = $workDate->copy()->endOfWeek(Carbon::FRIDAY);
    
    //     $totalHoursThisWeek = WorkHours::where('user_id', auth()->id())
    //         ->whereBetween('work_date', [$weekStart, $weekEnd])
    //         ->sum('hours_worked');
    
    //     if ($totalHoursThisWeek + $request->hours_worked > 40) {
    //         return $this->sendResponse(false, 'No puedes exceder 40 horas por semana hábil.');
    //     }
    
    //     DB::beginTransaction();
    //     try {
    //         WorkHours::updateOrCreate(
    //             ['user_id' => auth()->id(), 'work_date' => $request->work_date],
    //             ['hours_worked' => $request->hours_worked]
    //         );
    //         DB::commit();
    
    //         // Calcular el nuevo total de horas para el mes
    //         $currentMonth = Carbon::parse($request->work_date)->startOfMonth();
    //         $totalHours = WorkHours::where('user_id', auth()->id())
    //             ->whereYear('work_date', $currentMonth->year)
    //             ->whereMonth('work_date', $currentMonth->month)
    //             ->sum('hours_worked');
    
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Horas registradas correctamente.',
    //             'totalHours' => $totalHours
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error al registrar las horas.'
    //         ]);
    //     }
    // }


    
   private function sendResponse($success, $message, $data = [])
    {
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        if (request()->ajax()) {
            return response()->json($response);
        }

        if ($success) {
            return back()->with('success', $message);
        } else {
            return back()->with('error', $message);
        }
    }



    // public function approve(WorkHours $workHours)
    // {
    //     $workHours->update(['approved' => true]);
        
    //     return back()->with('success', 'Horas aprobadas correctamente.');
    // }

    public function approveWeek(Request $request)
    {
        $request->validate([
            'week_start' => 'required|date',
            'employee_id' => 'required|exists:users,id'
        ]);

        $weekStart = Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

        WorkHours::where('user_id', $request->employee_id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->update(['approved' => true]);

        return back()->with('success', 'Semana aprobada correctamente.');
    }


    // public function downloadMonthlyReport($month)
    // {
    //     $month = Carbon::parse($month);
    //     $empleados = User::where('empleador_id', auth()->id())->get();
        
    //     // Obtener los datos del reporte
    //     $reportData = $this->getMonthlyReportData($empleados, $month);
        
    //     // Generar el contenido del CSV
    //     $csvContent = $this->generateCSV($reportData);
        
    //     // Notificar a Zapier
    //     $this->notifyZapier($month, $csvContent);
        
    //     // Nombre del archivo
    //     $fileName = 'reporte_mensual_' . $month->format('Y_m') . '.csv';
        
    //     // Configurar las cabeceras para la descarga
    //     $headers = [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => "attachment; filename=\"$fileName\"",
    //         'Pragma' => 'no-cache',
    //         'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
    //         'Expires' => '0'
    //     ];
    
    //     // Devolver la respuesta con el contenido del CSV
    //     return response($csvContent, 200, $headers);
    // }



    public function downloadMonthlyReport($month, Request $request)
    {
        // Validar el mes y el ID del empleado
        $request->validate([
            'employee_id' => 'required|exists:users,id',
        ]);

        $employeeId = $request->query('employee_id');
        $employee = User::findOrFail($employeeId);

        // Parsear el mes
        $month = Carbon::parse($month);
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        // Obtener las horas trabajadas para el mes especificado
        $workHours = WorkHours::where('user_id', $employeeId)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->where('approved', true)
            ->get();

        // Calcular el total de horas aprobadas
        $totalApprovedHours = $workHours->sum('hours_worked');

        // Verificar si hay suficientes horas aprobadas
        if ($totalApprovedHours < 160) {
            return back()->with('error', 'No se pueden descargar reportes hasta que se hayan aprobado al menos 160 horas.');
        }

        // Preparar los datos para el CSV
      
        $reportData = $this->prepareReportData($employee, $workHours, $month);

        // Generar el contenido del CSV
        $csvContent = $this->generateCSV($reportData, $employee, $month);
    
        // Notificar a Zapier
        $this->notifyZapier($month, $csvContent, $employee);

        // Nombre del archivo
        $fileName = "reporte_mensual_{$employee->name}_{$month->format('Y_m')}.csv";

        // Configurar las cabeceras para la descarga
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        // Devolver la respuesta con el contenido del CSV
        return response($csvContent, 200, $headers);
    }




    
private function prepareReportData($employee, $workHours, $month)
{
    $reportData = [];
    $weekStart = $month->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
    $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

    while ($weekStart->lte($month->endOfMonth())) {
        $weekHours = $workHours->filter(function ($workHour) use ($weekStart, $weekEnd) {
            return Carbon::parse($workHour->work_date)->between($weekStart, $weekEnd);
        });
        
        $totalHours = $weekHours->sum('hours_worked');



        $reportData[] = [
            'profesional' => $employee->name,
            'semana' => $weekStart->format('d/m/Y') . ' - ' . min($weekEnd, $month->copy()->endOfMonth())->format('d/m/Y'),
            'horas_trabajadas' => $totalHours,
            'estado' => $totalHours > 0 ? 'Aprobado' : 'Sin horas'
        ];

        $weekStart->addWeek();
        $weekEnd->addWeek();
    }

    return $reportData;
}




private function generateCSV($reportData, $employee, $month)
{
    $csv = fopen('php://temp', 'r+');
    
    // Añadir título y detalles del reporte
    fputcsv($csv, ['REPORTE MENSUAL DE HORAS TRABAJADAS']);
    fputcsv($csv, []);
    fputcsv($csv, ['Empresa:', auth()->user()->name]);
    fputcsv($csv, ['Profesional:', $employee->name]);
    fputcsv($csv, ['Email del Profesional:', $employee->email]);
    fputcsv($csv, ['Mes:', $month->format('F Y')]);
    fputcsv($csv, ['Generado el:', Carbon::now()->format('d/m/Y H:i:s')]);
    fputcsv($csv, []);
    
    // Añadir texto de certificación
    fputcsv($csv, ['CERTIFICACIÓN']);
    fputcsv($csv, ['Certifico que las horas aquí mostradas son correctas y autorizo el pago al Profesional.']);
    fputcsv($csv, []);
    
    // Añadir encabezados de la tabla
    fputcsv($csv, ['', 'PROFESIONAL', 'SEMANA', 'HORAS TRABAJADAS', 'ESTADO']);
    
    // Añadir datos y calcular el total de horas
    $rowNumber = 1;
    $totalHours = 0;
    foreach ($reportData as $row) {
        fputcsv($csv, [
            $rowNumber,
            $row['profesional'],
            $row['semana'],
            $row['horas_trabajadas'],
            $row['estado']
        ]);
        $totalHours += floatval($row['horas_trabajadas']);
        $rowNumber++;
    }
    
    // Añadir resumen
    fputcsv($csv, []);
    fputcsv($csv, ['RESUMEN']);
    fputcsv($csv, ['Total de registros:', count($reportData)]);
    fputcsv($csv, ['Total de horas:', number_format($totalHours, 2)]);
    
    // Añadir firma y fecha
    fputcsv($csv, []);
    fputcsv($csv, ['FIRMA']);
    fputcsv($csv, ['Empleador:', auth()->user()->name]);
    fputcsv($csv, ['Fecha:', Carbon::now()->format('d/m/Y')]);
    fputcsv($csv, ['Firma: ', auth()->user()->name]);
    
    rewind($csv);
    $content = stream_get_contents($csv);
    fclose($csv);
    
    return $content;
}


private function notifyZapier($month, $csvContent, $employee)
{
    $zapierWebhookUrl = 'https://hooks.zapier.com/hooks/catch/12433184/24b9yg9/';

    // Formatear el mes
    $formattedMonth = $month->format('F Y');

    // Obtener información del usuario autenticado (empleador)
    $employer = auth()->user()->name;
    $employer_email = auth()->user()->email;
    $download_time = now()->toDateTimeString();

    // Extraer información adicional del CSV
    $lines = explode("\n", $csvContent);
    $total_hours = 0;
    $total_records = 0;

    foreach ($lines as $line) {
        if (strpos($line, 'Total de horas:') !== false) {
            $parts = str_getcsv($line);
            $total_hours = isset($parts[1]) ? $parts[1] : 0;
        } elseif (strpos($line, 'Total de registros:') !== false) {
            $parts = str_getcsv($line);
            $total_records = isset($parts[1]) ? $parts[1] : 0;
        }
    }

    // Extraer la firma del empleador
    $employerSignature = '';
    foreach (array_reverse($lines) as $line) {
        if (strpos($line, 'Firma:') !== false) {
            $parts = str_getcsv($line);
            $employerSignature = isset($parts[1]) ? $parts[1] : '';
            break;
        }
    }

    // Crear contenido HTML con Tailwind CSS
    $formatted_content = "
        <html>
        <head>
            <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
        </head>
        <body class='bg-gray-100'>
            <div class='p-6 bg-white rounded-lg shadow-md max-w-2xl mx-auto mt-10'>
                <h1 class='text-2xl font-bold mb-4'>Reporte de Horas</h1>
                <p class='text-lg'><strong>Mes:</strong> {$formattedMonth}</p>
                <p class='text-lg'><strong>Profesional:</strong> {$employee->name}</p>
                <p class='text-lg'><strong>Empleador:</strong> {$employer}</p>
                <p class='text-lg'><strong>Email del empleador:</strong> {$employer_email}</p>
                <p class='text-lg'><strong>Hora de descarga:</strong> {$download_time}</p>
                <h2 class='text-xl font-semibold mt-6 mb-2'>Resumen</h2>
                <p class='text-lg'><strong>Total de horas:</strong> {$total_hours}</p>
                <p class='text-lg'><strong>Total de registros:</strong> {$total_records}</p>
                <h2 class='text-xl font-semibold mt-6 mb-2'>Firma</h2>
                <p class='text-lg'>{$employerSignature}</p>
            </div>
        </body>
        </html>
    ";

    // Preparar los datos para enviar a Zapier
    $data = [
        'month' => $formattedMonth,
        'professional_name' => $employee->name,
        'professional_email' => $employee->email,
        'employer' => $employer,
        'employer_email' => $employer_email,
        'csv_content' => base64_encode($csvContent),
        'formatted_content' => $formatted_content,
        'download_time' => $download_time,
        'total_hours' => $total_hours,
        'total_records' => $total_records,
        'employer_signature' => $employerSignature,
    ];

    // Enviar los datos a Zapier y capturar la respuesta
    $response = Http::post($zapierWebhookUrl, $data);

  
}



//     public function downloadMonthlyReport($empleadoId, $month)
// {
//     $month = Carbon::parse($month);
//     $empleado = User::findOrFail($empleadoId);
    
//     // Obtener los datos del reporte
//     $reportData = $this->getMonthlyReportData($empleado, $month);
    
//     // Generar el contenido del CSV
//     $csvContent = $this->generateCSV($reportData);
    
//     // Notificar a Zapier
//     $this->notifyZapier($month, $csvContent, $empleado);
    
//     // Nombre del archivo
//     $fileName = 'reporte_mensual_' . $empleado->name . '_' . $month->format('Y_m') . '.csv';
    
//     // Configurar las cabeceras para la descarga
//     $headers = [
//         'Content-Type' => 'text/csv',
//         'Content-Disposition' => "attachment; filename=\"$fileName\"",
//         'Pragma' => 'no-cache',
//         'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
//         'Expires' => '0'
//     ];

//     // Devolver la respuesta con el contenido del CSV
//     return response($csvContent, 200, $headers);
// }
   


    private function getMonthlyReportData($empleados, $month)
{
    $reportData = [];

    foreach ($empleados as $empleado) {
        $workHours = WorkHours::where('user_id', $empleado->id)
            ->whereYear('work_date', $month->year)
            ->whereMonth('work_date', $month->month)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->work_date)->startOfWeek()->format('Y-m-d');
            });

        foreach ($workHours as $weekStart => $hours) {
            $weekEnd = Carbon::parse($weekStart)->endOfWeek()->format('Y-m-d');
            $totalHours = $hours->sum('hours_worked');
            $isApproved = $hours->every('approved');

            $reportData[] = [
                'profesional' => $empleado->name,
                'semana' => "$weekStart - $weekEnd",
                'horas_trabajadas' => $totalHours,
                'estado' => $isApproved ? 'Aprobada' : 'Pendiente'
            ];
        }
    }

    return $reportData;
}




// private function generateCSV($reportData)
// {
//     $csv = fopen('php://temp', 'r+');
    
//     // Añadir título y detalles del reporte
//     fputcsv($csv, ['REPORTE MENSUAL DE HORAS TRABAJADAS']);
//     fputcsv($csv, []);
//     fputcsv($csv, ['Empresa:', auth()->user()->name]);
//     fputcsv($csv, ['Mes:', Carbon::now()->format('F Y')]);
//     fputcsv($csv, ['Generado el:', Carbon::now()->format('d/m/Y H:i:s')]);
//     fputcsv($csv, []);
    
//     // Añadir texto de certificación
//     fputcsv($csv, ['CERTIFICACIÓN']);
//     fputcsv($csv, ['Certifico que las horas aquí mostradas son correctas y autorizo el pago al Profesional.']);
//     fputcsv($csv, []);
    
//     // Añadir encabezados de la tabla
//     fputcsv($csv, ['', 'PROFESIONAL', 'SEMANA', 'HORAS TRABAJADAS', 'ESTADO']);
    
//     // Añadir datos
//     $rowNumber = 1;
//     foreach ($reportData as $row) {
//         fputcsv($csv, [
//             $rowNumber,
//             $row['profesional'],
//             $row['semana'],
//             $row['horas_trabajadas'],
//             $row['estado']
//         ]);
//         $rowNumber++;
//     }
    
//     // Añadir resumen
//     fputcsv($csv, []);
//     fputcsv($csv, ['RESUMEN']);
//     fputcsv($csv, ['Total de registros:', count($reportData)]);
//     fputcsv($csv, ['Total de horas:', array_sum(array_column($reportData, 'horas_trabajadas'))]);
    
//     // Añadir firma y fecha
//     fputcsv($csv, []);
//     fputcsv($csv, ['FIRMA']);
//     fputcsv($csv, ['Empleador:', auth()->user()->name]);
//     fputcsv($csv, ['Fecha:', Carbon::now()->format('d/m/Y')]);
//     fputcsv($csv, ['Firma: ', auth()->user()->name]);
    
//     rewind($csv);
//     $content = stream_get_contents($csv);
//     fclose($csv);
    
//     return $content;
// }





// //ULTIMO FUNCIONAL
// private function notifyZapier($month, $csvContent)
// {
//     $zapierWebhookUrl = 'https://hooks.zapier.com/hooks/catch/12433184/24b9yg9/';

//     // Formatear el mes
//     $formattedMonth = $month->format('F Y');

//     // Obtener información del usuario autenticado
//     $employer = auth()->user()->name;
//     $employer_email = auth()->user()->email;
//     $download_time = now()->toDateTimeString();

//     // Extraer información adicional del CSV
//     $lines = explode("\n", $csvContent);
//     $total_hours = 0;
//     $total_records = 0;

//     foreach ($lines as $line) {
//         if (strpos($line, 'Total de horas:') !== false) {
//             $parts = str_getcsv($line);
//             $total_hours = $parts[1];
//         } elseif (strpos($line, 'Total de registros:') !== false) {
//             $parts = str_getcsv($line);
//             $total_records = $parts[1];
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

//     // Crear contenido HTML con Tailwind CSS
//     $formatted_content = "
//         <html>
//         <head>
//             <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
//         </head>
//         <body class='bg-gray-100'>
//             <div class='p-6 bg-white rounded-lg shadow-md max-w-2xl mx-auto mt-10'>
//                 <h1 class='text-2xl font-bold mb-4'>Reporte de Horas</h1>
//                 <p class='text-lg'><strong>Mes:</strong> {$formattedMonth}</p>
//                 <p class='text-lg'><strong>Empleador:</strong> {$employer}</p>
//                 <p class='text-lg'><strong>Email:</strong> {$employer_email}</p>
//                 <p class='text-lg'><strong>Hora de descarga:</strong> {$download_time}</p>
//                 <h2 class='text-xl font-semibold mt-6 mb-2'>Resumen</h2>
//                 <p class='text-lg'><strong>Total de horas:</strong> {$total_hours}</p>
//                 <p class='text-lg'><strong>Total de registros:</strong> {$total_records}</p>
//                 <h2 class='text-xl font-semibold mt-6 mb-2'>Firma</h2>
//                 <p class='text-lg'>{$employerSignature}</p>
//             </div>
//         </body>
//         </html>
//     ";

//     // Preparar los datos para enviar a Zapier
//     $data = [
//         'month' => $formattedMonth,
//         'employer' => $employer,
//         'employer_email' => $employer_email,
//         'csv_content' => $csvContent,
//         'formatted_content' => $formatted_content, // Enviar contenido formateado
//         'download_time' => $download_time,
//         'total_hours' => $total_hours,
//         'total_records' => $total_records,
//         'employer_signature' => $employerSignature,
//     ];

//     // Enviar los datos a Zapier
//     Http::post($zapierWebhookUrl, $data);
// }






public function approveWeekWithComment(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:users,id',
        'week_start' => 'required|date',
        'comment' => 'nullable|string|max:255',
    ]);

    $weekStart = Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY);
    $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

    // Actualizar las horas trabajadas para el empleado en la semana especificada
    WorkHours::where('user_id', $request->employee_id)
        ->whereBetween('work_date', [$weekStart, $weekEnd])
        ->update([
            'approved' => true,
            'approval_comment' => $request->comment, // Guardar el comentario
        ]);

    return response()->json(['success' => true]);
}





public function update(Request $request, $taskId)
{
    $task = Task::findOrFail($taskId);
    
    $validatedData = $request->validate([
        'title' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'duration' => 'nullable|numeric|min:0',
        'completed' => 'boolean',
        // ... otras validaciones ...
    ]);

    $task->update($validatedData);

    if (isset($validatedData['duration'])) {
        WorkHours::updateOrCreate(
            [
                'user_id' => $task->created_by,
                'work_date' => $task->updated_at->toDateString(),
            ],
            [
                'hours_worked' => $validatedData['duration'],
                'approved' => false,
            ]
        );
    }

    return response()->json($task, 200);
}





public function approveMonth(Request $request)
{
    $month = $request->input('month');
    $user = Auth::user();

    $success = WorkHours::where('user_id', $user->id)
        ->whereRaw("DATE_FORMAT(work_date, '%Y-%m') = ?", [$month])
        ->update(['approved' => true]);

    return response()->json(['success' => $success]);
}

}