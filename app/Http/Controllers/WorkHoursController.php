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
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
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
    
        WorkHours::updateOrCreate(
            ['user_id' => auth()->id(), 'work_date' => $request->work_date],
            ['hours_worked' => $request->hours_worked]
        );
    
        $currentMonth = Carbon::parse($request->work_date)->startOfMonth();
        $totalHours = WorkHours::where('user_id', auth()->id())
            ->whereYear('work_date', $currentMonth->year)
            ->whereMonth('work_date', $currentMonth->month)
            ->sum('hours_worked');
    
        return redirect()->route('empleado.registrar-horas')->with([
            'success' => 'Horas registradas correctamente.',
            'totalHours' => $totalHours
        ]);
    }

    public function index()
    {
        $currentMonth = now()->startOfMonth();
        $calendar = $this->calendarService->generateCalendar($currentMonth, auth()->id());

        $totalHours = $this->calendarService->getTotalHoursForMonth($currentMonth, auth()->id());

        if (session('totalHours')) {
            $totalHours = session('totalHours');
        }

        return view('work_hours.index', compact('calendar', 'currentMonth', 'totalHours'));
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
        $request->validate([
            'employee_id' => 'required|exists:users,id',
        ]);

        $employeeId = $request->query('employee_id');
        $employee = User::findOrFail($employeeId);
        $monthDate = Carbon::parse($month);

        try {
            $result = $this->reportService->generateMonthlyReportOrchestration($employee, $monthDate);
            
            $this->zapierService->notifyReportDownload($monthDate, $result['csvContent'], $employee, $result['summary']);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$result['fileName']}\"",
            ];

            return response($result['csvContent'], 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show list of all professionals with weekly statistics
     */
    public function reportsIndex(Request $request)
    {
        $user = Auth::user();
        
        if ($user->tipo_usuario !== 'empleador') {
            abort(403, 'No autorizado');
        }

        $weekStart = $request->query('week') 
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $professionals = User::where('empleador_id', $user->id)
            ->orderBy('name')
            ->get()
            ->map(function ($professional, $index) use ($weekStart, $weekEnd) {
                $weekHours = WorkHours::where('user_id', $professional->id)
                    ->whereBetween('work_date', [$weekStart, $weekEnd])
                    ->get();

                $totalHours = $weekHours->sum('hours_worked');
                $pendingHours = $weekHours->where('approved', false)->sum('hours_worked');

                $weeklyAverage = $totalHours > 0 ? round($totalHours / 5, 1) : 0;

                $absences = 0;
                for ($i = 0; $i < 5; $i++) {
                    $date = $weekStart->copy()->addDays($i);
                    $dayHours = $weekHours->where('work_date', $date->format('Y-m-d'))->sum('hours_worked');
                    if ($dayHours == 0) {
                        $absences++;
                    }
                }

                $incompleteTasks = Task::where('visible_para', $professional->id)
                    ->where('completed', false)
                    ->count();

                $hasPendingWeeks = $pendingHours > 0;

                return [
                    'id' => $professional->id,
                    'name' => $professional->name,
                    'job_title' => $professional->job_title ?? 'Sin especificar',
                    'weekly_average' => $weeklyAverage,
                    'absences' => $absences,
                    'incomplete_tasks' => $incompleteTasks,
                    'has_pending_weeks' => $hasPendingWeeks,
                    'index' => $index + 1,
                ];
            });

        return view('reportes.index', [
            'professionals' => $professionals,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
        ]);
    }

    /**
     * Show individual professional report detail
     */
    public function professionalReport(User $user, Request $request)
    {
        $employer = Auth::user();
        
        if ($user->empleador_id !== $employer->id) {
            abort(403, 'No autorizado');
        }

        $weekStart = $request->query('week') 
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $weekHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get();

        $dailyHours = [];
        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        for ($i = 0; $i < 5; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $hours = $weekHours->where('work_date', $date->format('Y-m-d'))->first();
            $dailyHours[] = [
                'day' => $daysOfWeek[$i],
                'hours' => $hours ? $hours->hours_worked : 0,
                'status' => $hours ? ($hours->hours_worked > 0 ? 'Presente' : 'Ausente') : 'Ausente',
            ];
        }

        $totalHours = $weekHours->sum('hours_worked');
        $weeklyAverage = $totalHours > 0 ? round($totalHours / 5, 1) : 0;

        $absences = collect($dailyHours)->where('hours', 0)->count();

        $incompleteTasks = Task::where('visible_para', $user->id)
            ->where('completed', false)
            ->count();

        $comments = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->whereNotNull('approval_comment')
            ->pluck('approval_comment')
            ->filter()
            ->unique();

        return view('reportes.show', [
            'professional' => $user,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'weeklyAverage' => $weeklyAverage,
            'absences' => $absences,
            'incompleteTasks' => $incompleteTasks,
            'dailyHours' => $dailyHours,
            'comments' => $comments,
        ]);
    }
    /**
     * Download Weekly Report PDF
     */
    public function downloadWeeklyReport(User $user, Request $request)
    {
        $employer = Auth::user();
        if ($user->empleador_id !== $employer->id) abort(403);

        $weekStart = $request->query('week') 
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Data gathering (similar to report view)
        $weekHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get();

        $dailyHours = [];
        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        for ($i = 0; $i < 5; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $hours = $weekHours->where('work_date', $date->format('Y-m-d'))->first();
            $dailyHours[] = [
                'day' => $daysOfWeek[$i],
                'hours' => $hours ? $hours->hours_worked : 0,
            ];
        }

        $totalHours = $weekHours->sum('hours_worked');
        $weeklyAverage = $totalHours > 0 ? round($totalHours / 5, 1) : 0;
        
        $incompleteTasks = Task::where('visible_para', $user->id)
            ->where('completed', false)
            ->count();

        $comments = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->whereNotNull('approval_comment')
            ->pluck('approval_comment')
            ->filter();

        // Generate PDF
        $pdf = Pdf::loadView('reportes.pdf.weekly', [
            'professional' => $user,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'totalHours' => $totalHours,
            'weeklyAverage' => $weeklyAverage,
            'incompleteTasks' => $incompleteTasks,
            'dailyHours' => $dailyHours,
            'comments' => $comments
        ]);

        return $pdf->download("Reporte_Semanal_{$user->name}_{$weekStart->format('d-m-Y')}.pdf");
    }

    /**
     * Download Monthly Report PDF
     */
    public function downloadMonthlyReportPdf(User $user, Request $request)
    {
        $employer = Auth::user();
        if ($user->empleador_id !== $employer->id) abort(403);

        $date = $request->query('month') ? Carbon::parse($request->query('month')) : Carbon::now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Get all hours for the month
        $monthHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get();
            
        $totalApprovedHours = $monthHours->where('approved', true)->sum('hours_worked');

        // Calculate weekly breakdown
        $weeksData = [];
        $currentDate = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        
        while ($currentDate->lte($endOfMonth)) {
            $weekEnd = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);
            
            // Filter hours for this week
            $weekH = $monthHours->filter(function($h) use ($currentDate, $weekEnd) {
                return Carbon::parse($h->work_date)->between($currentDate, $weekEnd);
            });
            
            $wTotal = $weekH->sum('hours_worked');
            
            // Only add week if it falls within the month (at least partially)
            if ($currentDate->month == $startOfMonth->month || $weekEnd->month == $startOfMonth->month) {
                $weeksData[] = [
                    'period' => $currentDate->format('d/m') . ' - ' . $weekEnd->format('d/m'),
                    'hours' => $wTotal,
                    'approved' => $weekH->where('approved', true)->count() > 0 && $weekH->where('approved', false)->count() == 0
                ];
            }
            
            $currentDate->addWeek();
        }

        $pdf = Pdf::loadView('reportes.pdf.monthly', [
            'professional' => $user,
            'monthDate' => $startOfMonth,
            'totalApprovedHours' => $totalApprovedHours,
            'weeksData' => $weeksData
        ]);

        return $pdf->download("Reporte_Mensual_{$user->name}_{$startOfMonth->format('F_Y')}.pdf");
    }
}
