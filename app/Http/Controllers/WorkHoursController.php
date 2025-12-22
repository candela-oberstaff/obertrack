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
        private CalendarService $calendarService,
        private \App\Services\BrevoEmailService $emailService
    ) {}

    public function store(StoreWorkHoursRequest $request)
    {
        $user = auth()->user();
        if (empty($user->phone_number) || empty($user->location)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Por favor, completa tus datos personales en tu perfil antes de registrar horas.']);
            }
            return redirect()->route('profile.edit')->with('error', 'Por favor, completa tus datos personales antes de registrar horas.');
        }

        $workDate = Carbon::parse($request->work_date);
        
        if ($workDate->isWeekend()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No se pueden registrar horas en fines de semana.']);
            }
            return back()->with('error', 'No se pueden registrar horas en fines de semana.');
        }
    
        $weekStart = $workDate->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $workDate->copy()->endOfWeek(Carbon::FRIDAY);
    
        $totalHoursThisWeek = WorkHours::where('user_id', auth()->id())
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->where('work_date', '!=', $request->work_date) // Exclude current day if updating
            ->sum('hours_worked');
    
        if ($request->hours_worked > 8) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No puedes registrar más de 8 horas por día.']);
            }
            return back()->with('error', 'No puedes registrar más de 8 horas por día.');
        }

        if ($totalHoursThisWeek + $request->hours_worked > 40) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No puedes exceder 40 horas por semana hábil.']);
            }
            return back()->with('error', 'No puedes exceder 40 horas por semana hábil.');
        }
    
        WorkHours::updateOrCreate(
            ['user_id' => auth()->id(), 'work_date' => $request->work_date],
            [
                'hours_worked' => $request->hours_worked, 
                'user_comment' => $request->user_comment,
                'absence_reason' => $request->absence_reason
            ]
        );
    
        $currentMonth = Carbon::parse($request->work_date)->startOfMonth();
        $totalHours = WorkHours::where('user_id', auth()->id())
            ->whereYear('work_date', $currentMonth->year)
            ->whereMonth('work_date', $currentMonth->month)
            ->sum('hours_worked');
    
        // Send notification to employer with cooldown
        try {
            $user = auth()->user();
            $employer = $user->empleador_id ? User::find($user->empleador_id) : null;

            if ($employer) {
                $cacheKey = "pending_hours_notification_{$employer->id}_{$user->id}";
                
                if (!cache()->has($cacheKey)) {
                    $pendingHoursCount = WorkHours::where('user_id', $user->id)
                        ->whereRaw('approved IS FALSE')
                        ->sum('hours_worked');

                    if ($pendingHoursCount > 0) {
                        $this->emailService->sendPendingHoursNotification(
                            $employer->email,
                            $employer->name,
                            [
                                'employee_name' => $user->name,
                                'total_hours' => $pendingHoursCount,
                                'pending_hours' => [
                                    [
                                        'employee_name' => $user->name,
                                        'hours' => $pendingHoursCount,
                                        'week' => $workDate->startOfWeek()->format('d/m/Y')
                                    ]
                                ]
                            ]
                        );

                        // Set cooldown for 24 hours
                        cache()->put($cacheKey, true, now()->addDay());
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending pending hours notification: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Horas registradas correctamente.',
                'totalHours' => $totalHours
            ]);
        }

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

    public function approveDays(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'dates' => 'required|array',
            'comment' => 'nullable|string'
        ]);

        $this->approvalService->approveDates(
            $request->employee_id,
            $request->dates,
            $request->comment
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Horas aprobadas correctamente.');
    }

    public function approveMonth(Request $request)
    {
        $month = $request->input('month');
        $user = Auth::user();

        $success = $this->approvalService->approveMonth($user->id, $month);

        if ($success) {
            return back()->with('success', 'Todas las horas del mes han sido aprobadas.');
        }

        return back()->with('error', 'No se pudieron aprobar las horas.');
    }

    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string|max:500',
        ]);

        $workHour = WorkHours::findOrFail($id);
        
        // Ensure the user has permission (employer of the employee)
        $employee = User::find($workHour->user_id);
        if (Auth::user()->tipo_usuario !== 'empleador' || $employee->empleador_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $workHour->update(['approval_comment' => $request->comment]);

        return response()->json(['success' => true, 'message' => 'Comentario actualizado.']);
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
        
        if ($user->tipo_usuario !== 'empleador' && !$user->is_manager) {
            abort(403, 'No autorizado');
        }

        $weekStart = $request->query('week') 
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $today = Carbon::today();

        $employerId = $user->tipo_usuario === 'empleador' ? $user->id : $user->empleador_id;

        $professionals = User::where('empleador_id', $employerId)
            ->orderBy('name')
            ->get()
            ->map(function ($professional, $index) use ($weekStart, $weekEnd, $today) {
                $weekHours = WorkHours::where('user_id', $professional->id)
                    ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                    ->get();

                $totalHours = $weekHours->sum('hours_worked');
                $pendingHours = $weekHours->where('approved', false)->sum('hours_worked');

                // Logic for absences: only count days up to today or week end, whichever is first
                $absences = 0;
                $daysToCheck = min(5, $today->diffInDays($weekStart) + 1);
                if ($today->lt($weekStart)) $daysToCheck = 0;
                if ($weekEnd->lt($today)) $daysToCheck = 5;

                for ($i = 0; $i < $daysToCheck; $i++) {
                    $date = $weekStart->copy()->addDays($i);
                    $dayRecord = $weekHours->first(fn($h) => 
                        ($h->work_date instanceof Carbon ? $h->work_date->format('Y-m-d') : substr($h->work_date, 0, 10)) === $date->format('Y-m-d')
                    );
                    if (!$dayRecord || $dayRecord->hours_worked == 0) {
                        $absences++;
                    }
                }

                $incompleteTasks = $professional->assignedTasks()
                    ->whereRaw('completed IS FALSE')
                    ->count();

                // Monthly stats
                $monthStart = Carbon::now()->startOfMonth();
                $monthHours = WorkHours::where('user_id', $professional->id)
                    ->whereBetween('work_date', [$monthStart->format('Y-m-d'), Carbon::now()->format('Y-m-d')])
                    ->whereRaw('approved IS TRUE')
                    ->sum('hours_worked');

                return [
                    'id' => $professional->id,
                    'name' => $professional->name,
                    'job_title' => $professional->job_title ?? 'Profesional',
                    'registered_hours' => $totalHours,
                    'absences' => $absences,
                    'incomplete_tasks' => $incompleteTasks,
                    'has_pending_weeks' => $pendingHours > 0,
                    'month_hours' => $monthHours,
                    'index' => $index + 1,
                    'professional' => $professional
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
        $currentUser = Auth::user();
        $employerId = $currentUser->tipo_usuario === 'empleador' ? $currentUser->id : $currentUser->empleador_id;
        
        if ($user->empleador_id !== $employerId || ($currentUser->tipo_usuario !== 'empleador' && !$currentUser->is_manager)) {
            abort(403, 'No autorizado');
        }

        $weekStart = $request->query('week') 
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $today = Carbon::today();

        $weekHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->get();

        $dailyHours = [];
        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        for ($i = 0; $i < 5; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $hours = $weekHours->first(fn($h) => 
                ($h->work_date instanceof Carbon ? $h->work_date->format('Y-m-d') : substr($h->work_date, 0, 10)) === $date->format('Y-m-d')
            );
            
            $status = 'Pendiente';
            if ($date->lt($today)) {
                $status = $hours && $hours->hours_worked > 0 ? 'Presente' : 'Ausente';
            } elseif ($date->isToday()) {
                $status = $hours && $hours->hours_worked > 0 ? 'Presente' : 'En curso';
            }

            $dailyHours[] = [
                'day' => $daysOfWeek[$i],
                'date' => $date->format('d/m'),
                'hours' => $hours ? $hours->hours_worked : 0,
                'status' => $status,
                'is_approved' => $hours ? (bool)$hours->approved : false
            ];
        }

        $totalHours = $weekHours->sum('hours_worked');
        $absences = collect($dailyHours)->where('status', 'Ausente')->count();

        $incompleteTasks = $user->assignedTasks()
            ->whereRaw('completed IS FALSE')
            ->count();

        $comments = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->whereNotNull('approval_comment')
            ->pluck('approval_comment')
            ->filter()
            ->unique();

        $professionalComments = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->whereNotNull('user_comment')
            ->pluck('user_comment')
            ->filter()
            ->unique();

        // Monthly context
        $monthStart = $weekStart->copy()->startOfMonth();
        $monthEnd = $weekStart->copy()->endOfMonth();
        $monthHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$monthStart->format('Y-m-d'), min($today, $monthEnd)->format('Y-m-d')])
            ->whereRaw('approved IS TRUE')
            ->sum('hours_worked');

        return view('reportes.show', [
            'professional' => $user,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'registeredHours' => $totalHours,
            'absences' => $absences,
            'incompleteTasks' => $incompleteTasks,
            'dailyHours' => $dailyHours,
            'comments' => $comments,
            'professionalComments' => $professionalComments,
            'monthHours' => $monthHours,
        ]);
    }
    /**
     * Download Weekly Report PDF
     */
    public function downloadWeeklyReport(User $user, Request $request)
    {
        $currentUser = Auth::user();
        $employerId = $currentUser->tipo_usuario === 'empleador' ? $currentUser->id : $currentUser->empleador_id;
        
        if ($user->empleador_id !== $employerId || ($currentUser->tipo_usuario !== 'empleador' && !$currentUser->is_manager)) abort(403);

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
        
        $incompleteTasks = $user->assignedTasks()
            ->whereRaw('completed IS FALSE')
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
        $currentUser = Auth::user();
        $employerId = $currentUser->tipo_usuario === 'empleador' ? $currentUser->id : $currentUser->empleador_id;

        if ($user->empleador_id !== $employerId || ($currentUser->tipo_usuario !== 'empleador' && !$currentUser->is_manager)) abort(403);

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
