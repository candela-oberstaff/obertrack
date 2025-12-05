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
use App\Services\CalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkHoursController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private ZapierService $zapierService,
        private WorkHoursApprovalService $approvalService,
        private CalendarService $calendarService
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
        $calendar = $this->calendarService->generateCalendar($currentMonth, auth()->id());

        // Calcular el total de horas para el mes usando el servicio
        $totalHours = $this->calendarService->getTotalHoursForMonth($currentMonth, auth()->id());

        // Si hay un total de horas en la sesión, usarlo
        if (session('totalHours')) {
            $totalHours = session('totalHours');
        }

        return view('work_hours.index', compact('calendar', 'currentMonth', 'totalHours'));
    }

    // Calendar generation moved to CalendarService

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
        $monthDate = Carbon::parse($month);

        try {
            // Delegar la orquestación al servicio
            $result = $this->reportService->generateMonthlyReportOrchestration($employee, $monthDate);
            
            // Notificar a Zapier
            $this->zapierService->notifyReportDownload($monthDate, $result['csvContent'], $employee, $result['summary']);

            // Configurar las cabeceras para la descarga
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$result['fileName']}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            // Devolver la respuesta con el contenido del CSV
            return response($result['csvContent'], 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Removed misplaced update() method - task updates belong in TaskController
}