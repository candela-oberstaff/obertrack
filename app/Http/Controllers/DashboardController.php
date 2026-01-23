<?php

namespace App\Http\Controllers;

use App\Models\RecoveryHour;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkHours;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\WorkHoursSummaryService;
use App\Services\TaskDataService;
use App\Services\EmployeeDataService;
use App\Services\TaskManagementService;

class DashboardController extends Controller
{
    public function __construct(
        private WorkHoursSummaryService $workHoursService,
        private TaskDataService $taskDataService,
        private EmployeeDataService $employeeDataService,
        private TaskManagementService $taskManagementService,
        private \App\Services\ProfessionalActivityService $activityService
    ) {}

    public function show($role)
    {
        return view("dashboard.$role");
    }

    public function crearTareaParaEmpleado(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'employee_id' => 'required|exists:users,id',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png',
        ]);

        $task = $this->taskManagementService->createTask($request->all());

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedFilename = $file->store('task_attachments', 'local');
                
                \App\Models\TaskAttachment::create([
                    'task_id' => $task->id,
                    'uploaded_by' => auth()->id(),
                    'filename' => $file->getClientOriginalName(),
                    'stored_filename' => $storedFilename,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Clear dashboard cache so new task appears immediately
        $user = auth()->user();
        $cacheKeys = cache()->get('cache_keys_' . $user->id, []);
        foreach ($cacheKeys as $key) {
            cache()->forget($key);
        }
        cache()->forget('cache_keys_' . $user->id);

        return redirect()->back()->with('success', 'Tarea creada y asignada con éxito.');
    }

    public function verTareasEmpleados(Request $request)
    {
        $user = auth()->user();

        // Cache key único por usuario y parámetros de request
        $cacheKey = 'dashboard_' . $user->id . '_' . md5(json_encode($request->all()));

        // Track cache keys for this user (for invalidation)
        $userCacheKeys = cache()->get('cache_keys_' . $user->id, []);
        if (!in_array($cacheKey, $userCacheKeys)) {
            $userCacheKeys[] = $cacheKey;
            cache()->put('cache_keys_' . $user->id, $userCacheKeys, 3600);
        }

        // Cachear por 60 segundos para evitar queries repetidas
        $data = cache()->remember($cacheKey, 60, function () use ($user, $request) {
            // Obtener los empleados
            $employees = $this->employeeDataService->getEmployeesForUser($user);

            // Obtener todas las tareas de la organización (incluye las de los profesionales)
            $teamTasks = $this->taskManagementService->getCompanyTasks($user, $request->all());

            // Asignar las tareas individuales a cada empleado
            $employees->each(function ($employee) use ($teamTasks) {
                $employee->individualTasks = $teamTasks->filter(function($t) use ($employee) {
                    return $t->created_by == $employee->id || $t->assignees->contains('id', $employee->id);
                });
            });

            // Preparar datos para el gráfico
            $chartData = $this->taskDataService->prepareChartData($teamTasks);

            // Obtener las horas trabajadas de los empleados por semana
            $weekStart = $request->week ? Carbon::parse($request->week) : Carbon::now()->startOfWeek(Carbon::MONDAY);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);
            $workHoursSummary = $this->workHoursService->getWorkHoursSummary($employees, $weekStart, $weekEnd);

            // Obtener las semanas pendientes
            $pendingWeeks = $this->workHoursService->getPendingWeeks($employees);

            $currentMonth = Carbon::now()->startOfMonth();

            // Calcular el total de horas aprobadas para el mes actual
            $totalApprovedHours = $this->workHoursService->getTotalApprovedHoursForMonth($employees, $currentMonth);

            // Obtener información detallada de los empleados
            $empleadosInfo = $this->employeeDataService->getEmployeesInfo($employees, $currentMonth, $this->workHoursService);

            return compact(
                'teamTasks',
                'chartData',
                'workHoursSummary',
                'weekStart',
                'currentMonth',
                'totalApprovedHours',
                'pendingWeeks',
                'empleadosInfo',
                'employees'
            );
        });

        return view('empleadores.ver_tareas_empleados', $data);
    }

    public function empleadorDashboard(Request $request)
    {
        $user = auth()->user();
        
        // Use service to get employees (consistent with other methods)
        $empleados = $this->employeeDataService->getEmployeesForUser($user);

        $currentMonth = $request->month ? Carbon::parse($request->month) : Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Get all work hours for the month for all employees to calculate stats and calendar
        $monthlyHours = WorkHours::whereIn('user_id', $empleados->pluck('id'))
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get();
        
        // Group by date for faster/correct lookup in the calendar loop
        $hoursByDate = $monthlyHours->groupBy(function($item) {
            return $item->work_date->format('Y-m-d');
        });
        
        // Assign colors to employees for UI consistency
        $colors = ['bg-pink-500', 'bg-cyan-500', 'bg-green-600', 'bg-blue-500', 'bg-purple-500', 'bg-orange-500'];
        $employeeColors = [];
        foreach($empleados as $index => $emp) {
            $employeeColors[$emp->id] = $colors[$index % count($colors)];
        }

        // Professional Activity Statuses
        $professionalStatuses = $this->activityService->getStatusesForUsers($empleados);

        // Fetch all organizationally relevant tasks (Active during the month)
        $monthlyTasks = \App\Models\Task::where(function($query) use ($empleados, $user) {
            $query->whereIn('created_by', $empleados->pluck('id')->push($user->id))
                  ->orWhereHas('assignees', function($q) use ($empleados, $user) {
                      $q->whereIn('users.id', $empleados->pluck('id')->push($user->id));
                  });
        })
        ->where(function($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('end_date', [$startOfMonth, $endOfMonth])
              ->orWhereBetween('start_date', [$startOfMonth, $endOfMonth])
              ->orWhere(function($sub) use ($startOfMonth, $endOfMonth) {
                  $sub->where('start_date', '<=', $startOfMonth)
                      ->where('end_date', '>=', $endOfMonth);
              })
              ->orWhereBetween('created_at', [$startOfMonth, $endOfMonth]);
        })
        ->with('assignees')
        ->get();

        // Fetch recovery hours from the new table
        $monthlyRecoveries = RecoveryHour::whereIn('user_id', $empleados->pluck('id'))
            ->whereBetween('recovery_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function($item) {
                return $item->recovery_date->format('Y-m-d');
            });

        // Employee Summary Cards Data
        $employeeSummaries = $empleados->map(function($employee) use ($monthlyHours, $monthlyTasks, $employeeColors, $professionalStatuses) {
            $employeeHours = $monthlyHours->where('user_id', $employee->id);
            
            // Filter tasks for this specific employee
            $empTasks = $monthlyTasks->filter(function($task) use ($employee) {
                return $task->created_by == $employee->id || $task->assignees->contains('id', $employee->id);
            });

            $statusData = $professionalStatuses->firstWhere('user.id', $employee->id);
            $debtSummary = \App\Http\Controllers\RecoveryHoursController::getDebtSummary($employee->id);
            
            return [
                'user' => $employee,
                'total_hours' => $employeeHours->sum('hours_worked'),
                'days_registered' => $employeeHours->where('hours_worked', '>', 0)->count(),
                'target_hours' => 160,
                'completed_tasks' => $empTasks->filter(fn($t) => (bool)$t->completed)->count(),
                'total_tasks' => $empTasks->count(),
                'color' => $employeeColors[$employee->id] ?? 'bg-gray-500',
                'role' => $employee->job_title ?? 'Sin puesto definido',
                'initials' => strtoupper(substr($employee->name, 0, 1) . substr(strrchr($employee->name, ' ') ?: ' ' . substr($employee->name, 1), 1, 1)),
                'activity_status' => $statusData['status'] ?? 'active',
                'days_inactive' => $statusData['days_inactive'] ?? 0,
                'debt_summary' => $debtSummary,
            ];
        });

        // Sorted Recovery History for the section below calendar
        $recoveryHistory = RecoveryHour::whereIn('user_id', $empleados->pluck('id'))
            ->whereBetween('recovery_date', [$startOfMonth, $endOfMonth])
            ->with('user')
            ->orderBy('recovery_date', 'desc')
            ->get();

        // Calendar Data Generation
        $calendar = [];
        $currentDay = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY); 
        $lastDay = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        while ($currentDay <= $lastDay) {
            $dateStr = $currentDay->format('Y-m-d');
            $dayData = [
                'date' => $currentDay->copy(),
                'day' => $currentDay->day,
                'is_current_month' => $currentDay->month === $currentMonth->month,
                'has_events' => false,
                'employees' => [],
                'pending_recoveries_count' => 0
            ];

            $dayRecords = $hoursByDate->get($dateStr, collect());
            $dayRecoveries = $monthlyRecoveries->get($dateStr, collect());

                foreach ($empleados as $employee) {
                    $record = $dayRecords->where('user_id', $employee->id)->first();
                    $userRecoveries = $dayRecoveries->where('user_id', $employee->id);
                    
                    if ($record || $userRecoveries->isNotEmpty()) {
                        $dayData['has_events'] = true;
                        
                        $mappedRecoveries = $userRecoveries->map(function($r) {
                            return [
                                'id' => $r->id,
                                'hours' => $r->hours_recovered,
                                'comment' => $r->activities,
                                'approved' => $r->approved,
                            ];
                        })->values();

                        $dayData['employees'][] = [
                            'record_id' => $record ? $record->id : null,
                            'id' => $employee->id,
                            'name' => $employee->name,
                            'avatar' => $employee->avatar ? (str_starts_with($employee->avatar, 'http') ? $employee->avatar : asset('avatars/' . $employee->avatar)) : '',
                            'initials' => $employeeSummaries->firstWhere('user.id', $employee->id)['initials'],
                            'hours' => $record ? $record->hours_worked : 0,
                            'approved' => $record ? (bool)$record->approved : true,
                            'user_comment' => $record ? $record->user_comment : null,
                            'comment' => $record ? $record->approval_comment : null,
                            'new_comment' => '',
                            'absence_reason' => $record ? $record->absence_reason : null,
                            'color_class' => $employeeColors[$employee->id] ?? 'bg-gray-500',
                            
                            // Recovery list and aggregates
                            'recoveries' => $mappedRecoveries,
                            'recovered_hours' => $userRecoveries->where('approved', '!==', false)->sum('hours_recovered'),
                            'has_pending_recovery' => $userRecoveries->contains(fn($r) => $r->approved === null),
                        ];
                    }
                }
                
                // Count pending recoveries for this day (if any recovery is pending for an employee)
                $dayData['pending_recoveries_count'] = collect($dayData['employees'])
                    ->filter(fn($emp) => $emp['has_pending_recovery'])
                    ->count();
                
                // NEW: Add has_pending flag to accurately show the red dot only if there's work to do
                $dayData['has_pending'] = collect($dayData['employees'])
                    ->contains(fn($emp) => !$emp['approved'] || $emp['has_pending_recovery']);
            
            $calendar[] = $dayData;
            $currentDay->addDay();
        }

        return view('empleadores.dashboard', compact(
            'user',
            'empleados',
            'currentMonth',
            'employeeSummaries',
            'calendar',
            'recoveryHistory'
        ));
    }

    public function getDayDetailsJson($date)
    {
        $targetDate = Carbon::parse($date);
        $user = auth()->user();
        
        $empleados = $this->employeeDataService->getEmployeesForUser($user);
        
        // Needed for initials and colors
        $employeeSummaries = $this->workHoursService->getSummaries($empleados, $targetDate, $targetDate->copy()->endOfDay());
        $employeeColors = $employeeSummaries->pluck('color_class', 'user.id');

        // Fetch records for this day
        $dayRecords = WorkHours::whereIn('user_id', $empleados->pluck('id'))
            ->whereDate('work_date', $targetDate->format('Y-m-d'))
            ->get();

        $dayRecoveries = RecoveryHour::whereIn('user_id', $empleados->pluck('id'))
            ->whereDate('recovery_date', $targetDate->format('Y-m-d'))
            ->get();

        $employeesData = [];

        foreach ($empleados as $employee) {
            $record = $dayRecords->where('user_id', $employee->id)->first();
            $userRecoveries = $dayRecoveries->where('user_id', $employee->id);
            
            if ($record || $userRecoveries->isNotEmpty()) {
                
                $mappedRecoveries = $userRecoveries->map(function($r) {
                    return [
                        'id' => $r->id,
                        'hours' => $r->hours_recovered,
                        'comment' => $r->activities,
                        'approved' => $r->approved,
                    ];
                })->values();

                $initials = $employeeSummaries->firstWhere('user.id', $employee->id)['initials'] ?? strtoupper(substr($employee->name, 0, 1));

                $employeesData[] = [
                    'record_id' => $record ? $record->id : null,
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'avatar' => $employee->avatar ? (str_starts_with($employee->avatar, 'http') ? $employee->avatar : asset('avatars/' . $employee->avatar)) : '',
                    'initials' => $initials,
                    'hours' => $record ? $record->hours_worked : 0,
                    'approved' => $record ? (bool)$record->approved : true,
                    'user_comment' => $record ? $record->user_comment : null,
                    'comment' => $record ? $record->approval_comment : null,
                    'new_comment' => '',
                    'absence_reason' => $record ? $record->absence_reason : null,
                    'color_class' => $employeeColors[$employee->id] ?? 'bg-gray-500',
                    
                    'recoveries' => $mappedRecoveries,
                    'recovered_hours' => $userRecoveries->where('approved', '!==', false)->sum('hours_recovered'),
                    'has_pending_recovery' => $userRecoveries->contains(fn($r) => $r->approved === null),
                ];
            }
        }

        // Calculate Day Summary stats
        $pendingRecoveriesCount = collect($employeesData)
            ->filter(fn($emp) => $emp['has_pending_recovery'])
            ->count();
        
        $hasPending = collect($employeesData)
            ->contains(fn($emp) => !$emp['approved'] || $emp['has_pending_recovery']);

        return response()->json([
            'date' => $targetDate->format('Y-m-d'),
            'day' => $targetDate->day,
            'is_current_month' => $targetDate->isCurrentMonth(),
            'has_events' => count($employeesData) > 0,
            'employees' => $employeesData,
            'pending_recoveries_count' => $pendingRecoveriesCount,
            'has_pending' => $hasPending
        ]);
    }

    public function showMassEmailForm()
    {
        $user = auth()->user();
        
        // Security check
        if (!$user->is_superadmin && $user->tipo_usuario !== 'empleador' && !$user->is_manager) {
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver esta información.');
        }

        $allProfessionals = $this->employeeDataService->getEmployeesForUser($user);
        $allCompanies = collect(); // Managers/Employers typically don't email other companies

        // Get email stats for the sidebar
        $emailStats = [
            'total_recipients' => 0, // Placeholder - would need a log table to be accurate
            'by_segment' => [
                'professionals' => $allProfessionals->count(),
                'companies' => 0
            ],
            'total_sessions' => 0,
            'recent_logs' => collect()
        ];
        
        return view('empleadores.emails', compact('allProfessionals', 'allCompanies', 'emailStats'));
    }

    public function sendMassEmail(Request $request, \App\Services\BrevoEmailService $emailService)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_id' => 'nullable|exists:users,id',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $user = auth()->user();
        $companyName = $user->company_name ?? $user->name;

        if ($request->recipient_id) {
            $employees = \App\Models\User::where('id', $request->recipient_id)
                ->where('empleador_id', $user->id)
                ->get();
            $targetLabel = "al profesional seleccionado";
        } else {
            $employees = $this->employeeDataService->getEmployeesForUser($user);
            $targetLabel = "a tu equipo de profesionales";
        }

        if ($employees->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontró el destinatario o no tienes profesionales a cargo.');
        }

        // Process attachments
        $processedAttachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $processedAttachments[] = [
                    'path' => $file->getRealPath(),
                    'name' => $file->getClientOriginalName()
                ];
            }
        }

        $successCount = 0;
        foreach ($employees as $employee) {
            if ($employee->email) {
                $sent = $emailService->sendMassCommunication(
                    $employee->email,
                    $employee->name,
                    $request->subject,
                    nl2br(e($request->message)),
                    $companyName,
                    $processedAttachments
                );
                if ($sent) $successCount++;
            }
        }

        return redirect()->back()->with('success', "Se han enviado {$successCount} correos correctamente {$targetLabel}.");
    }

    public function sendMassWhatsapp(Request $request, \App\Services\WahaService $waha)
    {
        $request->validate([
            'message' => 'required|string',
            'recipient_id' => 'nullable|exists:users,id',
        ]);

        $user = auth()->user();
        $companyName = $user->company_name ?? $user->name;
        $sessionName = $waha->getSessionName($user->id);

        if ($request->recipient_id) {
            $employees = \App\Models\User::where('id', $request->recipient_id)
                ->where('empleador_id', $user->id)
                ->get();
            $targetLabel = "al profesional seleccionado";
        } else {
            $employees = $this->employeeDataService->getEmployeesForUser($user);
            $targetLabel = "a tu equipo de profesionales";
        }

        // Filter employees with phone number
        $employees = $employees->filter(fn($e) => !empty($e->phone_number));

        if ($employees->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron destinatarios con número de teléfono registrado.');
        }

        $count = 0;
        $delayIncrement = 60; // 1 minute as suggested

        foreach ($employees as $employee) {
            \App\Jobs\SendMassWhatsappJob::dispatch($employee->id, $request->message, $companyName, $sessionName)
                ->delay(now()->addSeconds($count * $delayIncrement));
            $count++;
        }

        return redirect()->back()->with('success', "Se han encolado {$count} mensajes de WhatsApp correctamente. El envío se realizará de forma progresiva (1 mensaje por minuto) para proteger la cuenta.");
    }

    public function getWhatsappStatus(\App\Services\WahaService $waha)
    {
        $user = auth()->user();
        $sessionName = $waha->getSessionName($user->id);
        
        $statusData = $waha->getSessionStatus($sessionName);
        $status = $statusData['status'] ?? 'STOPPED';
        
        $qr = null;
        if ($status === 'SCAN_QR_CODE') {
            $qr = $waha->getQrCode($sessionName);
        }
        
        return response()->json([
            'status' => $status,
            'qr' => $qr
        ]);
    }

    public function startWhatsappSession(Request $request, \App\Services\WahaService $waha)
    {
        $user = auth()->user();
        $sessionName = $waha->getSessionName($user->id);
        $force = $request->boolean('force', false);
        
        \Log::info("DashboardController: startWhatsappSession for [{$sessionName}] (force: " . ($force ? 'true' : 'false') . ")");
        $result = $waha->startSession($sessionName, $force);
        \Log::info("DashboardController: startWhatsappSession result", ['result' => $result]);
        
        return response()->json($result);
    }

    public function dailyDetail($date)
    {
        $user = auth()->user();
        
        // Security: Only employers, managers or superadmins
        if (!$user->is_superadmin && $user->tipo_usuario !== 'empleador' && !$user->is_manager) {
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para ver esta información.');
        }

        $targetDate = \Illuminate\Support\Carbon::parse($date);
        $empleados = $this->employeeDataService->getEmployeesForUser($user);
        
        if ($empleados->isEmpty()) {
            return redirect()->back()->with('error', 'No tienes profesionales a cargo para ver en esta fecha.');
        }

        $dayRecords = WorkHours::whereIn('user_id', $empleados->pluck('id'))
            ->whereDate('work_date', $targetDate)
            ->with('user')
            ->get();
            
        // Fetch all tasks for this day (Created by or assigned to anyone in the company)
        $dayTasks = \App\Models\Task::where(function($query) use ($empleados, $user) {
            $query->whereIn('created_by', $empleados->pluck('id')->push($user->id))
                  ->orWhereHas('assignees', function($q) use ($empleados, $user) {
                      $q->whereIn('users.id', $empleados->pluck('id')->push($user->id));
                  });
        })
        ->where(function($query) use ($targetDate) {
            $query->where(function($q) use ($targetDate) {
                $q->whereDate('start_date', '<=', $targetDate)
                  ->whereDate('end_date', '>=', $targetDate);
            })
            ->orWhereDate('created_at', $targetDate)
            ->orWhereDate('updated_at', $targetDate);
        })
        ->with(['assignees', 'createdBy'])
        ->get();

        $dayRecoveries = RecoveryHour::whereIn('user_id', $empleados->pluck('id'))
            ->whereDate('recovery_date', $targetDate)
            ->with('user')
            ->get();

        return view('empleadores.detalle_diario', compact(
            'targetDate',
            'dayRecords',
            'dayRecoveries',
            'empleados',
            'dayTasks'
        ));
    }


}
