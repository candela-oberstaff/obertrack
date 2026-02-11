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
use App\Services\ProfessionalDataService;
use App\Services\TaskManagementService;

class DashboardController extends Controller
{
    public function __construct(
        private WorkHoursSummaryService $workHoursService,
        private TaskDataService $taskDataService,
        private ProfessionalDataService $professionalDataService,
        private TaskManagementService $taskManagementService,
        private \App\Services\ProfessionalActivityService $activityService
    ) {}

    public function show($role)
    {
        return view("dashboard.$role");
    }

    public function crearTareaParaProfesional(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'professional_id' => 'required|exists:users,id',
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

    public function verTareasProfesionales(Request $request)
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
            // Obtener los profesionales
            $profesionales = $this->professionalDataService->getProfessionalsForUser($user);

            // Obtener todas las tareas de la organización (incluye las de los profesionales)
            $teamTasks = $this->taskManagementService->getCompanyTasks($user, $request->all());

            // Asignar las tareas individuales a cada profesional
            $profesionales->each(function ($profesional) use ($teamTasks) {
                $profesional->individualTasks = $teamTasks->filter(function($t) use ($profesional) {
                    return $t->created_by == $profesional->id || $t->assignees->contains('id', $profesional->id);
                });
            });

            // Preparar datos para el gráfico
            $chartData = $this->taskDataService->prepareChartData($teamTasks);

            // Obtener las horas trabajadas de los profesionales por semana
            $weekStart = $request->week ? Carbon::parse($request->week) : Carbon::now()->startOfWeek(Carbon::MONDAY);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);
            $workHoursSummary = $this->workHoursService->getWorkHoursSummary($profesionales, $weekStart, $weekEnd);

            // Obtener las semanas pendientes
            $pendingWeeks = $this->workHoursService->getPendingWeeks($profesionales);

            $currentMonth = Carbon::now()->startOfMonth();

            // Calcular el total de horas aprobadas para el mes actual
            $totalApprovedHours = $this->workHoursService->getTotalApprovedHoursForMonth($profesionales, $currentMonth);

            // Obtener información detallada de los profesionales
            $profesionalesInfo = $this->professionalDataService->getProfessionalsInfo($profesionales, $currentMonth, $this->workHoursService);

            return compact(
                'teamTasks',
                'chartData',
                'workHoursSummary',
                'weekStart',
                'currentMonth',
                'totalApprovedHours',
                'pendingWeeks',
                'profesionalesInfo',
                'profesionales'
            );
        });

        return view('empresas.ver_tareas_profesionales', $data);
    }

    public function empresaDashboard(Request $request)
    {
        $user = auth()->user();
        
        // Use service to get professionals (consistent with other methods)
        $profesionales = $this->professionalDataService->getProfessionalsForUser($user);

        $currentMonth = $request->month ? Carbon::parse($request->month) : Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Get all work hours for the month for all professionals to calculate stats and calendar
        $monthlyHours = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get();
        
        // Group by date for faster/correct lookup in the calendar loop
        $hoursByDate = $monthlyHours->groupBy(function($item) {
            return $item->work_date->format('Y-m-d');
        });
        
        // Assign colors to professionals for UI consistency
        $colors = ['bg-pink-500', 'bg-cyan-500', 'bg-green-600', 'bg-blue-500', 'bg-purple-500', 'bg-orange-500'];
        $professionalColors = [];
        foreach($profesionales as $index => $prof) {
            $professionalColors[$prof->id] = $colors[$index % count($colors)];
        }

        // Professional Activity Statuses
        $professionalStatuses = $this->activityService->getStatusesForUsers($profesionales);

        // Fetch all organizationally relevant tasks (Active during the month)
        $monthlyTasks = \App\Models\Task::where(function($query) use ($profesionales, $user) {
            $query->whereIn('created_by', $profesionales->pluck('id')->push($user->id))
                  ->orWhereHas('assignees', function($q) use ($profesionales, $user) {
                      $q->whereIn('users.id', $profesionales->pluck('id')->push($user->id));
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
        $monthlyRecoveries = RecoveryHour::whereIn('user_id', $profesionales->pluck('id'))
            ->whereBetween('recovery_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function($item) {
                return $item->recovery_date->format('Y-m-d');
            });

        // Check for activity TODAY (Real-time status)
        $today = Carbon::today();
        $activeTodayIds = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
            ->whereDate('work_date', $today)
            ->pluck('user_id')
            ->toArray();

        // Professional Summary Cards Data
        $professionalSummaries = $profesionales->map(function($professional) use ($monthlyHours, $monthlyTasks, $professionalColors, $professionalStatuses, $activeTodayIds) {
            $professionalHours = $monthlyHours->where('user_id', $professional->id);
            
            // Filter tasks for this specific professional
            $profTasks = $monthlyTasks->filter(function($task) use ($professional) {
                return $task->created_by == $professional->id || $task->assignees->contains('id', $professional->id);
            });

            $statusData = $professionalStatuses->firstWhere('user.id', $professional->id);
            $debtSummary = \App\Http\Controllers\RecoveryHoursController::getDebtSummary($professional->id);
            
            return [
                'user' => $professional,
                'total_hours' => $professionalHours->sum('hours_worked'),
                'days_registered' => $professionalHours->where('hours_worked', '>', 0)->count(),
                'target_hours' => 160,
                'completed_tasks' => $profTasks->filter(fn($t) => (bool)$t->completed)->count(),
                'total_tasks' => $profTasks->count(),
                'color' => $professionalColors[$professional->id] ?? 'bg-gray-500',
                'role' => $professional->job_title ?? 'Sin puesto definido',
                'initials' => strtoupper(substr($professional->name, 0, 1) . substr(strrchr($professional->name, ' ') ?: ' ' . substr($professional->name, 1), 1, 1)),
                'activity_status' => $statusData['status'] ?? 'active',
                'days_inactive' => $statusData['days_inactive'] ?? 0,
                'last_activity' => $statusData['last_registration'] ?? null,
                'active_today' => in_array($professional->id, $activeTodayIds),
                'debt_summary' => $debtSummary,
            ];
        });

        // Sorted Recovery History for the section below calendar
        $recoveryHistory = RecoveryHour::whereIn('user_id', $profesionales->pluck('id'))
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
                'profesionales' => [],
                'pending_recoveries_count' => 0
            ];

            $dayRecords = $hoursByDate->get($dateStr, collect());
            $dayRecoveries = $monthlyRecoveries->get($dateStr, collect());

                foreach ($profesionales as $professional) {
                    $record = $dayRecords->where('user_id', $professional->id)->first();
                    $userRecoveries = $dayRecoveries->where('user_id', $professional->id);
                    
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

                        $dayData['profesionales'][] = [
                            'record_id' => $record ? $record->id : null,
                            'id' => $professional->id,
                            'name' => $professional->name,
                            'avatar' => $professional->avatar ? (str_starts_with($professional->avatar, 'http') ? $professional->avatar : asset('avatars/' . $professional->avatar)) : '',
                            'initials' => $professionalSummaries->firstWhere('user.id', $professional->id)['initials'],
                            'hours' => $record ? $record->hours_worked : 0,
                            'approved' => $record ? (bool)$record->approved : true,
                            'user_comment' => $record ? $record->user_comment : null,
                            'comment' => $record ? $record->approval_comment : null,
                            'new_comment' => '',
                            'absence_reason' => $record ? $record->absence_reason : null,
                            'color_class' => $professionalColors[$professional->id] ?? 'bg-gray-500',
                            
                            // Recovery list and aggregates
                            'recoveries' => $mappedRecoveries,
                            'recovered_hours' => $userRecoveries->where('approved', '!==', false)->sum('hours_recovered'),
                            'has_pending_recovery' => $userRecoveries->contains(fn($r) => $r->approved === null),
                        ];
                    }
                }
                
                // Count pending recoveries for this day (if any recovery is pending for a professional)
                $dayData['pending_recoveries_count'] = collect($dayData['profesionales'])
                    ->filter(fn($emp) => $emp['has_pending_recovery'])
                    ->count();
                
                // NEW: Add has_pending flag to accurately show the red dot only if there's work to do
                $dayData['has_pending'] = collect($dayData['profesionales'])
                    ->contains(fn($emp) => !$emp['approved'] || $emp['has_pending_recovery']);
            
            $calendar[] = $dayData;
            $currentDay->addDay();
        }

        return view('empresas.dashboard', compact(
            'user',
            'profesionales',
            'currentMonth',
            'professionalSummaries',
            'calendar',
            'recoveryHistory'
        ));
    }

    public function getDayDetailsJson($date)
    {
        $targetDate = Carbon::parse($date);
        $user = auth()->user();
        
        $profesionales = $this->professionalDataService->getProfessionalsForUser($user);
        
        // Needed for initials and colors
        $professionalSummaries = $this->workHoursService->getSummaries($profesionales, $targetDate, $targetDate->copy()->endOfDay());
        $professionalColors = $professionalSummaries->pluck('color_class', 'user.id');

        // Fetch records for this day
        $dayRecords = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
            ->whereDate('work_date', $targetDate->format('Y-m-d'))
            ->get();

        $dayRecoveries = RecoveryHour::whereIn('user_id', $profesionales->pluck('id'))
            ->whereDate('recovery_date', $targetDate->format('Y-m-d'))
            ->get();

        $profesionalesData = [];

        foreach ($profesionales as $professional) {
            $record = $dayRecords->where('user_id', $professional->id)->first();
            $userRecoveries = $dayRecoveries->where('user_id', $professional->id);
            
            if ($record || $userRecoveries->isNotEmpty()) {
                
                $mappedRecoveries = $userRecoveries->map(function($r) {
                    return [
                        'id' => $r->id,
                        'hours' => $r->hours_recovered,
                        'comment' => $r->activities,
                        'approved' => $r->approved,
                    ];
                })->values();

                $initials = $professionalSummaries->firstWhere('user.id', $professional->id)['initials'] ?? strtoupper(substr($professional->name, 0, 1));
 
                $profesionalesData[] = [
                    'record_id' => $record ? $record->id : null,
                    'id' => $professional->id,
                    'name' => $professional->name,
                    'avatar' => $professional->avatar ? (str_starts_with($professional->avatar, 'http') ? $professional->avatar : asset('avatars/' . $professional->avatar)) : '',
                    'initials' => $initials,
                    'hours' => $record ? $record->hours_worked : 0,
                    'approved' => $record ? (bool)$record->approved : true,
                    'user_comment' => $record ? $record->user_comment : null,
                    'comment' => $record ? $record->approval_comment : null,
                    'new_comment' => '',
                    'absence_reason' => $record ? $record->absence_reason : null,
                    'color_class' => $professionalColors[$professional->id] ?? 'bg-gray-500',
                    
                    'recoveries' => $mappedRecoveries,
                    'recovered_hours' => $userRecoveries->where('approved', '!==', false)->sum('hours_recovered'),
                    'has_pending_recovery' => $userRecoveries->contains(fn($r) => $r->approved === null),
                ];
            }
        }

        // Calculate Day Summary stats
        $pendingRecoveriesCount = collect($profesionalesData)
            ->filter(fn($emp) => $emp['has_pending_recovery'])
            ->count();
        
        $hasPending = collect($profesionalesData)
            ->contains(fn($emp) => !$emp['approved'] || $emp['has_pending_recovery']);

        return response()->json([
            'date' => $targetDate->format('Y-m-d'),
            'day' => $targetDate->day,
            'is_current_month' => $targetDate->isCurrentMonth(),
            'has_events' => count($profesionalesData) > 0,
            'profesionales' => $profesionalesData,
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

        $allProfessionals = $this->professionalDataService->getProfessionalsForUser($user);
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
        
        return view('empresas.emails', compact('allProfessionals', 'allCompanies', 'emailStats'));
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
            $query = \App\Models\User::where('id', $request->recipient_id);
            
            if ($user->is_superadmin) {
                // Superadmins can email anyone
            } elseif ($user->tipo_usuario === 'empleador') {
                $query->where('empleador_id', $user->id);
            } elseif ($user->is_manager) {
                $query->where('empleador_id', $user->empleador_id);
            } else {
                $query->whereRaw('1=0'); // Security: no access
            }
            
            $profesionales = $query->get();
            $targetLabel = "al profesional seleccionado";
        } else {
            $profesionales = $this->professionalDataService->getProfessionalsForUser($user);
            $targetLabel = "a tu equipo de profesionales";
        }
 
        if ($profesionales->isEmpty()) {
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
        foreach ($profesionales as $profesional) {
            if ($profesional->email) {
                $sent = $emailService->sendMassCommunication(
                    $profesional->email,
                    $profesional->name,
                    $request->subject,
                    $request->message,
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
            $query = \App\Models\User::where('id', $request->recipient_id);
            
            if ($user->is_superadmin) {
                // Anyone
            } elseif ($user->tipo_usuario === 'empleador') {
                $query->where('empleador_id', $user->id);
            } elseif ($user->is_manager) {
                $query->where('empleador_id', $user->empleador_id);
            } else {
                $query->whereRaw('1=0');
            }
            
            $profesionales = $query->get();
            $targetLabel = "al profesional seleccionado";
        } else {
            $profesionales = $this->professionalDataService->getProfessionalsForUser($user);
            $targetLabel = "a tu equipo de profesionales";
        }
 
        // Filter professionals with phone number
        $profesionales = $profesionales->filter(fn($e) => !empty($e->phone_number));
 
        if ($profesionales->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron destinatarios con número de teléfono registrado.');
        }

        $count = 0;
        $delayIncrement = 15; // Reduced from 60 to 15 seconds for responsiveness

        foreach ($profesionales as $profesional) {
            $delay = $count * $delayIncrement;
            \App\Jobs\SendMassWhatsappJob::dispatch($profesional->id, $request->message, $companyName, $sessionName)
                ->delay(now()->addSeconds($delay));
            
            \Log::info("DashboardController: Dispatched mass WhatsApp job for {$profesional->name}", [
                'delay_seconds' => $delay,
                'session' => $sessionName
            ]);
            
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
        $profesionales = $this->professionalDataService->getProfessionalsForUser($user);
        
        if ($profesionales->isEmpty()) {
            return redirect()->back()->with('error', 'No tienes profesionales a cargo para ver en esta fecha.');
        }
 
        $dayRecords = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
            ->whereDate('work_date', $targetDate)
            ->with('user')
            ->get();
            
        // Fetch all tasks for this day (Created by or assigned to anyone in the company)
        $dayTasks = \App\Models\Task::where(function($query) use ($profesionales, $user) {
            $query->whereIn('created_by', $profesionales->pluck('id')->push($user->id))
                  ->orWhereHas('assignees', function($q) use ($profesionales, $user) {
                      $q->whereIn('users.id', $profesionales->pluck('id')->push($user->id));
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

        $dayRecoveries = RecoveryHour::whereIn('user_id', $profesionales->pluck('id'))
            ->whereDate('recovery_date', $targetDate)
            ->with('user')
            ->get();

        return view('empresas.detalle_diario', compact(
            'targetDate',
            'dayRecords',
            'dayRecoveries',
            'profesionales',
            'dayTasks'
        ));
    }


}
