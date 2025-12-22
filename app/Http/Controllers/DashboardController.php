<?php

namespace App\Http\Controllers;

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
        private TaskManagementService $taskManagementService
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

            // Obtener las tareas creadas por los empleados
            $tasks = $this->taskManagementService->getEmployeeTasks($employees, $request->all());

            // Obtener las tareas creadas para los empleados (por el empleador, manager o superadmin)
            $teamTasks = $this->taskManagementService->getEmployerTasks($user, $employees, $request->all());

            // Asignar las tareas individuales a cada empleado
            $employees->each(function ($employee) use ($tasks) {
                $employee->individualTasks = $tasks->where('created_by', $employee->id);
            });

            // Preparar datos para el gráfico
            $chartData = $this->taskDataService->prepareChartData($tasks->concat($teamTasks));

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
                'tasks',
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

        // Employee Summary Cards Data
        $employeeSummaries = $empleados->map(function($employee) use ($monthlyHours, $employeeColors) {
            $employeeHours = $monthlyHours->where('user_id', $employee->id);
            return [
                'user' => $employee,
                'total_hours' => $employeeHours->sum('hours_worked'),
                'target_hours' => 160,
                'color' => $employeeColors[$employee->id] ?? 'bg-gray-500',
                'role' => $employee->job_title ?? 'Sin puesto definido',
                'initials' => strtoupper(substr($employee->name, 0, 1) . substr(strrchr($employee->name, ' ') ?: ' ' . substr($employee->name, 1), 1, 1)),
            ];
        });

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
                'employees' => []
            ];

            $dayRecords = $hoursByDate->get($dateStr, collect());

            foreach ($empleados as $employee) {
                $record = $dayRecords->where('user_id', $employee->id)->first();
                
                if ($record) {
                    $dayData['has_events'] = true;
                    $dayData['employees'][] = [
                        'record_id' => $record->id,
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'avatar' => $employee->avatar,
                        'initials' => $employeeSummaries->firstWhere('user.id', $employee->id)['initials'],
                        'hours' => $record->hours_worked,
                        'approved' => $record->approved,
                        'comment' => $record->approval_comment,
                        'absence_reason' => $record->absence_reason,
                        'color_class' => $employeeColors[$employee->id] ?? 'bg-gray-500',
                    ];
                }
            }
            $calendar[] = $dayData;
            $currentDay->addDay();
        }

        return view('empleadores.dashboard', compact(
            'user',
            'empleados',
            'currentMonth',
            'employeeSummaries',
            'calendar'
        ));
    }
}
