<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkHours;
use App\Models\RecoveryHour;
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
    
    
        // Regular work hours registration
        if ($request->hours_worked > 8) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No puedes registrar más de 8 horas por día.']);
            }
            return back()->with('error', 'No puedes registrar más de 8 horas por día.');
        }
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
    
        $existingRecord = WorkHours::where('user_id', auth()->id())
            ->where('work_date', $request->work_date)
            ->where('recovered_hours', 0) // Only get non-recovery records
            ->first();

        if ($existingRecord && $existingRecord->approved) {
            // If already approved, only allow updating the comment, NOT the hours or absence reason
            $existingRecord->update([
                'user_comment' => $request->user_comment
            ]);
        } else {
            $absenceHours = $request->absence_hours ?? ($request->hours_worked < 8 ? (8 - $request->hours_worked) : 0);
            
            WorkHours::updateOrCreate(
                [
                    'user_id' => auth()->id(), 
                    'work_date' => $request->work_date,
                    'recovered_hours' => 0 // Ensure we're not updating recovery records
                ],
                [
                    'hours_worked' => $request->hours_worked, 
                    'user_comment' => $request->user_comment,
                    'absence_reason' => $request->absence_reason,
                    'absence_hours' => $absenceHours
                ]
            );
        }
    
        $currentMonth = Carbon::parse($request->work_date)->startOfMonth();
        $totalHours = WorkHours::where('user_id', auth()->id())
            ->whereYear('work_date', $currentMonth->year)
            ->whereMonth('work_date', $currentMonth->month)
            ->sum(DB::raw('hours_worked + CASE WHEN recovery_approved = true THEN recovered_hours ELSE 0 END'));
    
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
            
            
            // Send notification to the professional if absence is recorded (< 8 hours)
            if ($request->hours_worked < 8) {
                $endOfMonthFormatted = $currentMonth->copy()->endOfMonth()->locale('es')->isoFormat('D [de] MMMM');
                $this->emailService->sendAbsenceNotification(
                    $user->email,
                    $user->name,
                    $workDate->format('d/m/Y'),
                    $endOfMonthFormatted
                );
            }


        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending notifications: ' . $e->getMessage());
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
        $missingHours = $this->calendarService->getMissingHours($currentMonth, auth()->id());

        if (session('totalHours')) {
            $totalHours = session('totalHours');
        }

        // Task Stats
        $user = auth()->user();
        $completedTasksCount = $user->assignedTasks()
            ->whereRaw('completed IS TRUE')
            ->whereMonth('updated_at', $currentMonth->month)
            ->whereYear('updated_at', $currentMonth->year)
            ->count();
            
        $pendingTasksCount = $user->assignedTasks()
            ->whereRaw('completed IS FALSE')
            ->count();
            
        return view('empleados.registrar_horas', compact('calendar', 'currentMonth', 'totalHours', 'missingHours', 'completedTasksCount', 'pendingTasksCount'));
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

        $employee = User::findOrFail($request->employee_id);
        $user = Auth::user();

        // Security check: superadmin, employer of the employee, or manager of the same employer
        $isAuthorized = $user->is_superadmin || 
                        ($user->tipo_usuario === 'empleador' && $employee->empleador_id === $user->id) ||
                        ($user->is_manager && $employee->empleador_id === $user->empleador_id);

        if (!$isAuthorized) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para aprobar estas horas.'], 403);
        }

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
        
        // Ensure the user has permission (employer of the employee, or manager, or superadmin)
        $employee = User::find($workHour->user_id);
        $user = Auth::user();

        $isAuthorized = $user->is_superadmin || 
                        ($user->tipo_usuario === 'empleador' && $employee->empleador_id === $user->id) ||
                        ($user->is_manager && $employee->empleador_id === $user->empleador_id);

        if (!$isAuthorized) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $workHour->update(['approval_comment' => $request->comment]);

        return response()->json(['success' => true, 'message' => 'Comentario actualizado.']);
    }

    public function approveRecovery(Request $request, $id)
    {
        $workHour = WorkHours::findOrFail($id);
        $user = auth()->user();

        // Check if user is the employer of the professional
        $professional = User::findOrFail($workHour->user_id);
        if (!$user->is_superadmin && sprintf('%s', $professional->empleador_id) !== sprintf('%s', $user->id)) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $workHour->update(['recovery_approved' => DB::raw('TRUE')]);

        return response()->json(['success' => true, 'message' => 'Recuperación aprobada correctamente.']);
    }


    public function downloadMonthlyReport($month, Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
        ]);

        $employeeId = $request->query('employee_id');
        $employee = User::findOrFail($employeeId);
        // Clean month parameter if it contains query string artifacts
        if ($month && str_contains($month, '?')) {
            $month = explode('?', $month)[0];
        }
        $monthDate = Carbon::parse($month);

        try {
            \Log::info('Monthly report download initiated', [
                'employee_id' => $employeeId,
                'month' => $month,
                'user_id' => auth()->id()
            ]);

            $result = $this->reportService->generateMonthlyReportOrchestration($employee, $monthDate);
            
            // Dispatch Zapier notification asynchronously to prevent blocking the download
            dispatch(function() use ($monthDate, $result, $employee) {
                try {
                    app(\App\Services\ZapierService::class)->notifyReportDownload(
                        $monthDate, 
                        $result['csvContent'], 
                        $employee, 
                        $result['summary']
                    );
                } catch (\Exception $e) {
                    \Log::error('Zapier notification failed', [
                        'employee_id' => $employee->id,
                        'month' => $monthDate->format('Y-m'),
                        'error' => $e->getMessage()
                    ]);
                }
            })->afterResponse();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$result['fileName']}\"",
            ];

            \Log::info('Monthly report generated successfully', [
                'employee_id' => $employeeId,
                'month' => $month,
                'file_name' => $result['fileName']
            ]);

            return response($result['csvContent'], 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Monthly report generation failed', [
                'employee_id' => $employeeId,
                'month' => $month,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Check if it's an AJAX request or expects JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }
            
            // For regular requests, redirect to reports index with error
            return redirect()->route('reportes.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Show list of all professionals with weekly statistics
     */
    public function reportsIndex(Request $request)
    {
        $user = Auth::user();
        
        if ($user->is_superadmin) {
            $professionalsQuery = User::where('tipo_usuario', 'empleado');
        } else {
            if ($user->tipo_usuario !== 'empleador' && !$user->is_manager) {
                abort(403, 'No autorizado');
            }
            $employerId = $user->tipo_usuario === 'empleador' ? $user->id : $user->empleador_id;
            $professionalsQuery = User::where('empleador_id', $employerId);
        }

        $weekInput = $request->query('week');
        if ($weekInput && str_contains($weekInput, '?')) {
            $weekInput = explode('?', $weekInput)[0];
        }

        $weekStart = $weekInput 
            ? Carbon::parse($weekInput)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $today = Carbon::today();

        $professionals = $professionalsQuery
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

                $commentsCount = WorkHours::where('user_id', $professional->id)
                    ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                    ->where(function($q) {
                        $q->whereNotNull('approval_comment')->orWhereNotNull('user_comment');
                    })
                    ->count();

                // Monthly stats
                $monthStart = Carbon::now()->startOfMonth();
                $monthWorkHours = WorkHours::where('user_id', $professional->id)
                    ->whereBetween('work_date', [$monthStart->format('Y-m-d'), $today->format('Y-m-d')])
                    ->whereRaw('approved IS TRUE')
                    ->sum('hours_worked');
                
                $monthRecoveredNew = RecoveryHour::where('user_id', $professional->id)
                    ->whereBetween('recovery_date', [$monthStart->format('Y-m-d'), $today->format('Y-m-d')])
                    ->whereRaw('approved IS TRUE')
                    ->sum('hours_recovered');
                
                $monthRecoveredOld = WorkHours::where('user_id', $professional->id)
                    ->whereBetween('work_date', [$monthStart->format('Y-m-d'), $today->format('Y-m-d')])
                    ->where('recovery_approved', true)
                    ->sum('recovered_hours');

                $monthHours = $monthWorkHours + $monthRecoveredNew + $monthRecoveredOld;

                // Pending recoveries (New table)
                $pendingRecoveries = RecoveryHour::where('user_id', $professional->id)
                    ->whereRaw('approved IS FALSE')
                    ->get()
                    ->map(function($r) {
                        // Map to a consistent structure for the view
                        return (object)[
                            'id' => $r->id,
                            'recovered_hours' => $r->hours_recovered,
                            'work_date' => $r->recovery_date,
                            'is_new' => true
                        ];
                    });

                // Add legacy pending recoveries if any
                $legacyPending = WorkHours::where('user_id', $professional->id)
                    ->where('recovered_hours', '>', 0)
                    ->where('recovery_approved', false)
                    ->get()
                    ->map(function($r) {
                        return (object)[
                            'id' => $r->id,
                            'recovered_hours' => $r->recovered_hours,
                            'work_date' => $r->work_date,
                            'is_new' => false
                        ];
                    });
                
                $pendingRecoveries = $pendingRecoveries->concat($legacyPending);

                return [
                    'id' => $professional->id,
                    'name' => $professional->name,
                    'job_title' => $professional->job_title ?? 'Profesional',
                    'registered_hours' => $totalHours,
                    'absences' => $absences,
                    'incomplete_tasks' => $incompleteTasks,
                    'comment_count' => $commentsCount,
                    'has_pending_weeks' => $pendingHours > 0,
                    'pending_recoveries' => $pendingRecoveries,
                    'has_pending_recoveries' => $pendingRecoveries->count() > 0,
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
        
        if (!$currentUser->is_superadmin) {
            $employerId = $currentUser->tipo_usuario === 'empleador' ? $currentUser->id : $currentUser->empleador_id;
            if ($user->empleador_id !== $employerId || ($currentUser->tipo_usuario !== 'empleador' && !$currentUser->is_manager)) {
                abort(403, 'No autorizado');
            }
        }

        $weekInput = $request->query('week');
        if ($weekInput && str_contains($weekInput, '?')) {
            $weekInput = explode('?', $weekInput)[0];
        }

        $weekStart = $weekInput 
            ? Carbon::parse($weekInput)->startOfWeek(Carbon::MONDAY)
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

        $allComments = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->where(function($q) {
                $q->whereNotNull('approval_comment')->orWhereNotNull('user_comment');
            })
            ->orderBy('work_date', 'asc')
            ->get();

        // Monthly context
        $monthStart = $weekStart->copy()->startOfMonth();
        $monthEnd = $weekStart->copy()->endOfMonth();
        $monthHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$monthStart->format('Y-m-d'), min($today, $monthEnd)->format('Y-m-d')])
            ->whereRaw('approved IS TRUE')
            ->sum(DB::raw('hours_worked + CASE WHEN recovery_approved = true THEN recovered_hours ELSE 0 END'));

        return view('reportes.show', [
            'professional' => $user,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'registeredHours' => $totalHours,
            'absences' => $absences,
            'incompleteTasks' => $incompleteTasks,
            'dailyHours' => $dailyHours,
            'allComments' => $allComments,
            'monthHours' => $monthHours,
        ]);
    }
    /**
     * Download Weekly Report PDF
     */
    public function downloadWeeklyReport(User $user, Request $request)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser->is_superadmin) {
            $employerId = $currentUser->tipo_usuario === 'empleador' ? $currentUser->id : $currentUser->empleador_id;
            if ($user->empleador_id !== $employerId || ($currentUser->tipo_usuario !== 'empleador' && !$currentUser->is_manager)) {
                abort(403);
            }
        }

        $weekInput = $request->query('week');
        if ($weekInput && str_contains($weekInput, '?')) {
            $weekInput = explode('?', $weekInput)[0];
        }

        $weekStart = $weekInput 
            ? Carbon::parse($weekInput)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Data gathering (similar to report view)
        $weekHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get();

        $dailyHours = [];
        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $today = Carbon::today();
        $absences = 0;
        $daysToCheck = min(5, $today->diffInDays($weekStart) + 1);
        if ($today->lt($weekStart)) $daysToCheck = 0;
        if ($weekEnd->lt($today)) $daysToCheck = 5;

        for ($i = 0; $i < 5; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $hours = $weekHours->where('work_date', $date->format('Y-m-d'))->first();
            $dailyHours[] = [
                'day' => $daysOfWeek[$i],
                'hours' => $hours ? $hours->hours_worked : 0,
            ];

            if ($i < $daysToCheck && (!$hours || $hours->hours_worked == 0)) {
                $absences++;
            }
        }

        $totalHours = $weekHours->sum('hours_worked');
        $weeklyAverage = $totalHours > 0 ? round($totalHours / 5, 1) : 0;
        
        $incompleteTasks = $user->assignedTasks()
            ->whereRaw('completed IS FALSE')
            ->count();

        $comments = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->where(function($q) {
                $q->whereNotNull('approval_comment')->orWhereNotNull('user_comment');
            })
            ->get();

        // Generate PDF
    $pdf = Pdf::loadView('reportes.pdf.weekly', [
        'professional' => $user,
        'weekStart' => $weekStart,
        'weekEnd' => $weekEnd,
        'totalHours' => $totalHours,
        'weeklyAverage' => $weeklyAverage,
        'incompleteTasks' => $incompleteTasks,
        'absences' => $absences,
        'dailyHours' => $dailyHours,
        'comments' => $comments
    ]);

    $fileName = "Reporte_Semanal_{$user->name}_{$weekStart->format('d-m-Y')}.pdf";

    if ($request->has('send_email')) {
        $this->emailService->sendEmail(
            Auth::user()->email,
            Auth::user()->name,
            "📋 Reporte Semanal: {$user->name}",
            "<p>Hola,</p><p>Adjunto encontrarás el reporte semanal solicitado para <strong>{$user->name}</strong> correspondiente a la semana del {$weekStart->format('d/m/Y')} al {$weekEnd->format('d/m/Y')}.</p><p>Saludos,<br>Equipo Obertrack</p>",
            ['content' => $pdf->output(), 'name' => $fileName]
        );
    }

    return $pdf->download($fileName);
    }

    /**
     * Download Monthly Report PDF
     */
    public function downloadMonthlyReportPdf(User $user, Request $request)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser->is_superadmin) {
            $employerId = $currentUser->tipo_usuario === 'empleador' ? $currentUser->id : $currentUser->empleador_id;
            if ($user->empleador_id !== $employerId || ($currentUser->tipo_usuario !== 'empleador' && !$currentUser->is_manager)) {
                abort(403);
            }
        }

        $monthInput = $request->query('month');
        if ($monthInput && str_contains($monthInput, '?')) {
            $monthInput = explode('?', $monthInput)[0];
        }

        $date = $monthInput ? Carbon::parse($monthInput) : Carbon::now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Get all hours for the month
        $monthHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get();
            
        $totalApprovedHours = $monthHours->where('approved', true)->sum('hours_worked') + $monthHours->where('recovery_approved', true)->sum('recovered_hours');

        // Calculate absences for the month
        $absences = 0;
        $today = Carbon::today();
        $endCheck = min($today, $endOfMonth);
        $current = $startOfMonth->copy();
        while ($current->lte($endCheck)) {
            if (!$current->isWeekend()) {
                $dayRecord = $monthHours->first(fn($h) => (Carbon::parse($h->work_date))->format('Y-m-d') === $current->format('Y-m-d'));
                if (!$dayRecord || $dayRecord->hours_worked == 0) $absences++;
            }
            $current->addDay();
        }

        $incompleteTasks = $user->assignedTasks()->whereRaw('completed IS FALSE')->count();

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
            'absences' => $absences,
            'incompleteTasks' => $incompleteTasks,
            'weeksData' => $weeksData,
            'comments' => $monthHours
        ]);

    $fileName = "Reporte_Mensual_{$user->name}_{$startOfMonth->format('F_Y')}.pdf";

    if ($request->has('send_email')) {
        $this->emailService->sendEmail(
            Auth::user()->email,
            Auth::user()->name,
            "📊 Reporte Mensual: {$user->name}",
            "<p>Hola,</p><p>Adjunto encontrarás el reporte mensual solicitado para <strong>{$user->name}</strong> correspondiente al mes de {$startOfMonth->translatedFormat('F Y')}.</p><p>Saludos,<br>Equipo Obertrack</p>",
            ['content' => $pdf->output(), 'name' => $fileName]
        );
    }

    return $pdf->download($fileName);
    }

    /**
     * Aprobar todas las horas del mes actual para todos los empleados del empleador
     */
    public function approveAllMonth(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);
    
        $user = Auth::user();
        $month = $request->input('month');
        
        // Validar que el usuario es empleador
        if ($user->tipo_usuario !== 'empleador' && !$user->is_superadmin) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
            }
            return back()->with('error', 'No autorizado');
        }
    
        // Obtener todos los empleados del empleador
        $employerId = $user->is_superadmin ? null : $user->id;
        $employeesQuery = User::where('tipo_usuario', 'empleado');
        
        if ($employerId) {
            $employeesQuery->where('empleador_id', $employerId);
        }
        
        $employees = $employeesQuery->get();
        
        if ($employees->isEmpty()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No hay empleados registrados']);
            }
            return back()->with('error', 'No hay empleados registrados');
        }
    
        $monthDate = Carbon::parse($month);
        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();
    
        $totalApproved = 0;
        $totalHours = 0;
        $approvedEmployees = [];
    
        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                // Aprobar todas las horas del mes para este empleado
                $approvedRecords = DB::update(
                    "UPDATE work_hours 
                     SET approved = true, 
                         approved_at = ?, 
                         updated_at = ? 
                     WHERE user_id = ? 
                       AND work_date BETWEEN ? AND ? 
                       AND approved = false",
                    [
                        now(),
                        now(),
                        $employee->id,
                        $startOfMonth,
                        $endOfMonth
                    ]
                );
    
                if ($approvedRecords > 0) {
                    $employeeHours = WorkHours::where('user_id', $employee->id)
                        ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
                        ->sum('hours_worked');
                        
                    $totalApproved += $approvedRecords;
                    $totalHours += $employeeHours;
                    $approvedEmployees[] = [
                        'name' => $employee->name,
                        'records' => $approvedRecords,
                        'hours' => $employeeHours
                    ];
                }
            }
    
            DB::commit();
    
            $response = [
                'success' => true,
                'message' => "Se aprobaron {$totalApproved} registros de horas para {$monthDate->translatedFormat('F Y')}.",
                'details' => [
                    'total_approved' => $totalApproved,
                    'total_hours' => $totalHours,
                    'month' => $monthDate->format('Y-m'),
                    'employees_count' => count($approvedEmployees)
                ]
            ];
    
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($response);
            }
    
            return back()->with('success', $response['message']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving all month hours: ' . $e->getMessage());
    
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al aprobar las horas: ' . $e->getMessage()
                ], 500);
            }
    
            return back()->with('error', 'Error al aprobar las horas: ' . $e->getMessage());
        }
    }
    
    /**
     * Aprobar todas las horas de una semana específica para todos los empleados del empleador
     */
    public function approveAllWeek(Request $request)
    {
        $request->validate([
            'week_date' => 'required|date',
        ]);
    
        $user = Auth::user();
        $weekDate = Carbon::parse($request->week_date);
        
        // Validar que el usuario es empleador o superadmin
        if ($user->tipo_usuario !== 'empleador' && !$user->is_superadmin) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
            }
            return back()->with('error', 'No autorizado');
        }
    
        // Obtener todos los empleados del empleador
        $employeesQuery = User::where('tipo_usuario', 'empleado');
        if (!$user->is_superadmin) {
            $employeesQuery->where('empleador_id', $user->id);
        }
        $employees = $employeesQuery->get();
    
        if ($employees->isEmpty()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No hay empleados registrados']);
            }
            return back()->with('error', 'No hay empleados registrados');
        }
    
        $startOfWeek = $weekDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $weekDate->copy()->endOfWeek(Carbon::FRIDAY); // Solo días hábiles
    
        $totalApproved = 0;
        $totalHours = 0;
        $approvedEmployees = [];
    
        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                $approvedRecords = DB::update(
                    "UPDATE work_hours 
                     SET approved = true, 
                         approved_at = ?, 
                         updated_at = ? 
                     WHERE user_id = ? 
                       AND work_date BETWEEN ? AND ? 
                       AND approved = false",
                    [
                        now(),
                        now(),
                        $employee->id,
                        $startOfWeek,
                        $endOfWeek
                    ]
                );
    
                if ($approvedRecords > 0) {
                    $employeeHours = WorkHours::where('user_id', $employee->id)
                        ->whereBetween('work_date', [$startOfWeek, $endOfWeek])
                        ->sum('hours_worked');
    
                    $totalApproved += $approvedRecords;
                    $totalHours += $employeeHours;
                    $approvedEmployees[] = [
                        'name' => $employee->name,
                        'records' => $approvedRecords,
                        'hours' => $employeeHours
                    ];
                }
            }
    
            DB::commit();
    
            $response = [
                'success' => true,
                'message' => "Se aprobaron {$totalApproved} registros de horas para la semana {$startOfWeek->format('d/m/Y')} - {$endOfWeek->format('d/m/Y')}.",
                'details' => [
                    'total_approved' => $totalApproved,
                    'total_hours' => $totalHours,
                    'week_start' => $startOfWeek->format('Y-m-d'),
                    'week_end' => $endOfWeek->format('Y-m-d'),
                    'employees_count' => count($approvedEmployees)
                ]
            ];
    
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($response);
            }
    
            return back()->with('success', $response['message']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving all week hours: ' . $e->getMessage());
    
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al aprobar las horas: ' . $e->getMessage()
                ], 500);
            }
    
            return back()->with('error', 'Error al aprobar las horas: ' . $e->getMessage());
        }
    }
}
