<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkHours;

class EmployeeDataService
{
    /**
     * Get employees based on user role
     */
    public function getEmployeesForUser(User $user)
    {
        // Si es superadmin, obtener todos los empleados
        if ($user->is_superadmin) {
            return User::where('tipo_usuario', 'empleado')->get();
        }

        // Si es empleador, obtener sus empleados directos
        if ($user->tipo_usuario === 'empleador') {
            return User::where('empleador_id', $user->id)->get();
        }

        // Si es manager, obtener empleados del mismo empleador
        if ($user->is_manager) {
            return User::where('empleador_id', $user->empleador_id)->get();
        }

        return collect([]);
    }

    /**
     * Get employee info with work hours summary
     * Optimized to avoid N+1 queries
     */
    public function getEmployeesInfo($empleados, $currentMonth, WorkHoursSummaryService $workHoursService)
    {
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        
        // Single query for all employees - NO N+1!
        $allApprovedHours = WorkHours::whereIn('user_id', $empleados->pluck('id'))
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->whereRaw('approved IS TRUE')
            ->selectRaw('user_id, SUM(hours_worked) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return $empleados->map(function ($empleado) use ($currentMonth, $workHoursService, $allApprovedHours) {
            $totalApprovedHours = $allApprovedHours->get($empleado->id, 0);
            $approvedWeeks = $workHoursService->getApprovedWeeks(collect([$empleado]), $currentMonth);

            return [
                'id' => $empleado->id,
                'name' => $empleado->name,
                'is_manager' => $empleado->is_manager,
                'totalApprovedHours' => $totalApprovedHours,
                'approvedWeeks' => $approvedWeeks
            ];
        });
    }
}

