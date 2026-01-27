<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkHours;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    const CACHE_TTL = 300; // 5 minutes

    public function getPendingHoursForEmployer(User $employer)
    {
        $cacheKey = "notifications.employer.{$employer->id}.pending_hours";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($employer) {
            $empleados = $this->getEmployerEmployees($employer);

            if ($empleados->isEmpty()) {
                return [
                    'pending_count' => 0,
                    'pending_weeks' => []
                ];
            }

            $employeeIds = $empleados->pluck('id');

            $hasPendingHours = WorkHours::whereIn('user_id', $employeeIds)
                ->whereRaw('approved IS FALSE')
                ->exists();

            $hasPendingRecoveries = WorkHours::whereIn('user_id', $employeeIds)
                ->where('recovered_hours', '>', 0)
                ->whereRaw('recovery_approved IS FALSE')
                ->exists();

            if (!$hasPendingHours && !$hasPendingRecoveries) {
                return [
                    'pending_count' => 0,
                    'pending_weeks' => []
                ];
            }

            $workHoursSummary = $this->getWorkHoursSummary($empleados);
            $recoveryRequests = $this->getRecoveryRequests($employeeIds);

            if (empty($workHoursSummary) && $recoveryRequests->isEmpty()) {
                return [
                    'pending_count' => 0,
                    'pending_weeks' => []
                ];
            }

            $pendingWeeks = [[
                'start' => now()->subWeek(),
                'end' => now(),
                'summary' => $workHoursSummary,
                'recovery_requests' => $recoveryRequests
            ]];

            $pendingCount = collect($workHoursSummary)
                ->filter(fn($emp) => $emp['pending_hours'] > 0)
                ->count() + $recoveryRequests->count();

            return [
                'pending_count' => $pendingCount,
                'pending_weeks' => $pendingWeeks
            ];
        });
    }

    public function getRecentTasksForEmployee(User $employee)
    {
        $cacheKey = "notifications.employee.{$employee->id}.recent_tasks";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($employee) {
            $recentTasks = Task::whereHas('assignees', function ($q) use ($employee) {
                $q->where('user_id', $employee->id);
            })
                ->whereRaw('completed IS FALSE')
                ->where('created_at', '>=', now()->subDays(7))
                ->whereDoesntHave('readBy', function ($query) use ($employee) {
                    $query->where('user_id', $employee->id);
                })
                ->with('createdBy')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return [
                'unread_count' => $recentTasks->count(),
                'recent_tasks' => $recentTasks
            ];
        });
    }

    public function clearEmployerCache(User $employer)
    {
        Cache::forget("notifications.employer.{$employer->id}.pending_hours");
    }

    public function clearEmployeeCache(User $employee)
    {
        Cache::forget("notifications.employee.{$employee->id}.recent_tasks");
    }

    private function getEmployerEmployees(User $employer)
    {
        if ($employer->tipo_usuario === 'empleador') {
            return User::where('empleador_id', $employer->id)->get();
        }

        return User::where('empleador_id', $employer->empleador_id)->get();
    }

    private function getWorkHoursSummary($empleados)
    {
        $workHoursSummary = [];

        $pendingHoursByEmployee = WorkHours::whereIn('user_id', $empleados->pluck('id'))
            ->whereRaw('approved IS FALSE')
            ->selectRaw('user_id, SUM(hours_worked) as total_pending')
            ->groupBy('user_id')
            ->pluck('total_pending', 'user_id');

        foreach ($empleados as $empleado) {
            $pendingHours = $pendingHoursByEmployee->get($empleado->id, 0);

            if ($pendingHours > 0) {
                $workHoursSummary[$empleado->id] = [
                    'name' => $empleado->name,
                    'pending_hours' => $pendingHours,
                ];
            }
        }

        return $workHoursSummary;
    }

    private function getRecoveryRequests($employeeIds)
    {
        return WorkHours::whereIn('user_id', $employeeIds)
            ->where('recovered_hours', '>', 0)
            ->whereRaw('recovery_approved IS NULL')
            ->with('user')
            ->get();
    }
}
