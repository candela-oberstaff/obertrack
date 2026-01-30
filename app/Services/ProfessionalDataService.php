<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkHours;

class ProfessionalDataService
{
    /**
     * Get professionals based on user role
     */
    public function getProfessionalsForUser(User $user)
    {
        // Si es superadmin, obtener todos los profesionales
        if ($user->is_superadmin) {
            return User::where('tipo_usuario', 'empleado')->get();
        }

        // Si es empresa, obtener sus profesionales directos
        if ($user->tipo_usuario === 'empleador') {
            return User::where('empleador_id', $user->id)->get();
        }

        // Si es manager, obtener profesionales de la misma empresa
        if ($user->is_manager) {
            return User::where('empleador_id', $user->empleador_id)->get();
        }

        return collect([]);
    }

    /**
     * Get professional info with work hours summary
     * Optimized to avoid N+1 queries
     */
    public function getProfessionalsInfo($profesionales, $currentMonth, WorkHoursSummaryService $workHoursService)
    {
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        
        // Single query for all professionals - NO N+1!
        $allApprovedHours = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->whereRaw('approved IS TRUE')
            ->selectRaw('user_id, SUM(hours_worked) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return $profesionales->map(function ($profesional) use ($currentMonth, $workHoursService, $allApprovedHours) {
            $totalApprovedHours = $allApprovedHours->get($profesional->id, 0);
            $approvedWeeks = $workHoursService->getApprovedWeeks(collect([$profesional]), $currentMonth);

            return [
                'id' => $profesional->id,
                'name' => $profesional->name,
                'is_manager' => $profesional->is_manager,
                'totalApprovedHours' => $totalApprovedHours,
                'approvedWeeks' => $approvedWeeks
            ];
        });
    }
}

