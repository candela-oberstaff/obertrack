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
            'employee_id' => 'required|exists:users,id'
        ]);

        $this->taskManagementService->createTask($request->all());

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
            $empleados = $this->employeeDataService->getEmployeesForUser($user);

            // Obtener las tareas creadas por los empleados
            $tareas = $this->taskManagementService->getEmployeeTasks($empleados, $request->all());

            // Obtener las tareas creadas para los empleados (por el empleador, manager o superadmin)
            $tareasEmpleador = $this->taskManagementService->getEmployerTasks($user, $empleados, $request->all());

            // Preparar datos para el gráfico
            $chartData = $this->taskDataService->prepareChartData($tareas->concat($tareasEmpleador));

            // Obtener las horas trabajadas de los empleados por semana
            $weekStart = $request->week ? Carbon::parse($request->week) : Carbon::now()->startOfWeek(Carbon::MONDAY);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);
            $workHoursSummary = $this->workHoursService->getWorkHoursSummary($empleados, $weekStart, $weekEnd);

            // Obtener las semanas pendientes
            $pendingWeeks = $this->workHoursService->getPendingWeeks($empleados);

            $currentMonth = Carbon::now()->startOfMonth();

            // Calcular el total de horas aprobadas para el mes actual
            $totalApprovedHours = $this->workHoursService->getTotalApprovedHoursForMonth($empleados, $currentMonth);

            // Obtener información detallada de los empleados
            $empleadosInfo = $this->employeeDataService->getEmployeesInfo($empleados, $currentMonth, $this->workHoursService);

            return compact(
                'tareas',
                'tareasEmpleador',
                'chartData',
                'workHoursSummary',
                'weekStart',
                'currentMonth',
                'totalApprovedHours',
                'pendingWeeks',
                'empleadosInfo',
                'empleados'
            );
        });

        return view('empleadores.ver_tareas_empleados', $data);
    }

    public function empleadorDashboard(Request $request)
    {
        $user = auth()->user();
        $empleados = User::where('empleador_id', $user->id)->get();

        $weekStart = $request->week ? Carbon::parse($request->week) : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

        $workHoursSummary = $this->workHoursService->getWorkHoursSummary($empleados, $weekStart, $weekEnd);

        // Redirect to the main tasks view which acts as the dashboard
        return redirect()->route('empleador.tareas.index');
    }
}
