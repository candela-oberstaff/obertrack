<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkHours;
use App\Http\Requests\StoreWorkHoursRequest;
use App\Http\Requests\ApproveWorkHoursRequest;
use App\Services\ReportService;
use App\Services\ZapierService;
use App\Services\WorkHoursApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkHoursController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private ZapierService $zapierService,
        private WorkHoursApprovalService $approvalService
    ) {}

    public function store(StoreWorkHoursRequest $request)
    {
        // Validación ya manejada por StoreWorkHoursRequest
    
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

    public function approveWeek(ApproveWorkHoursRequest $request)
    {
        $this->approvalService->approveWeek($request->employee_id, $request->week_start);
        return back()->with('success', 'Semana aprobada correctamente.');
    }

    public function approveWeekWithComment(ApproveWorkHoursRequest $request)
    {
        $this->approvalService->approveWeekWithComment(
            $request->employee_id, 
            $request->week_start, 
            $request->comment
        );
        return response()->json(['success' => true]);
    }

    public function approveMonth(Request $request)
    {
        $month = $request->input('month');
        $user = Auth::user();

        $success = $this->approvalService->approveMonth($user->id, $month);

        return response()->json(['success' => $success]);
    }

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

        // Preparar los datos para el CSV usando ReportService
        $reportData = $this->reportService->prepareReportData($employee, $workHours, $month);

        // Generar el contenido del CSV usando ReportService
        $csvContent = $this->reportService->generateCSV($reportData, $employee, $month);
    
        // Extraer resumen del CSV
        $summary = $this->reportService->extractCSVSummary($csvContent);

        // Notificar a Zapier usando ZapierService
        $this->zapierService->notifyReportDownload($month, $csvContent, $employee, $summary);

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
}