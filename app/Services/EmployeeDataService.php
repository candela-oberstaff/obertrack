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
     */
    public function getEmployeesInfo($empleados, $currentMonth, WorkHoursSummaryService $workHoursService)
    {
        return $empleados->map(function ($empleado) use ($currentMonth, $workHoursService) {
            $startOfMonth = $currentMonth->copy()->startOfMonth();
            $endOfMonth = $currentMonth->copy()->endOfMonth();

            $totalApprovedHours = $workHoursService->getEmployeeApprovedHoursInRange(
                $empleado->id, 
                $startOfMonth, 
                $endOfMonth
            );

            $approvedWeeks = $workHoursService->getApprovedWeeks(collect([$empleado]), $currentMonth);

            return [
                'id' => $empleado->id,
                'name' => $empleado->name,
                'totalApprovedHours' => $totalApprovedHours,
                'approvedWeeks' => $approvedWeeks
            ];
        });
    }
}
