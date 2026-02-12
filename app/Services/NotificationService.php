<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkHours;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    const CACHE_TTL = 300; // 5 minutes

    public function getPendingHoursForCompany(User $company)
    {
        $cacheKey = "notifications.company.{$company->id}.pending_hours";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($company) {
            $profesionales = $this->getCompanyProfessionals($company);

            if ($profesionales->isEmpty()) {
                return [
                    'pending_count' => 0,
                    'pending_weeks' => []
                ];
            }

            $professionalIds = $profesionales->pluck('id');

            $hasPendingHours = WorkHours::whereIn('user_id', $professionalIds)
                ->whereRaw('approved IS FALSE')
                ->exists();

            $hasPendingRecoveries = WorkHours::whereIn('user_id', $professionalIds)
                ->where('recovered_hours', '>', 0)
                ->whereRaw('recovery_approved IS FALSE')
                ->exists();

            if (!$hasPendingHours && !$hasPendingRecoveries) {
                return [
                    'pending_count' => 0,
                    'pending_weeks' => []
                ];
            }

            $workHoursSummary = $this->getWorkHoursSummary($profesionales);
            $recoveryRequests = $this->getRecoveryRequests($professionalIds);

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

    public function getRecentTasksForProfessional(User $professional)
    {
        $cacheKey = "notifications.professional.{$professional->id}.recent_tasks";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($professional) {
            $recentTasks = Task::whereHas('assignees', function ($q) use ($professional) {
                $q->where('user_id', $professional->id);
            })
                ->where('status', Task::STATUS_TODO)
                ->whereRaw('completed IS FALSE')
                ->where('created_at', '>=', now()->subDays(7))
                ->whereDoesntHave('readBy', function ($query) use ($professional) {
                    $query->where('user_id', $professional->id);
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

    public function clearCompanyCache(User $company)
    {
        Cache::forget("notifications.company.{$company->id}.pending_hours");
    }

    public function clearProfessionalCache(User $professional)
    {
        Cache::forget("notifications.professional.{$professional->id}.recent_tasks");
    }

    private function getCompanyProfessionals(User $employer)
    {
        if ($employer->tipo_usuario === 'empleador') {
            return User::where('empleador_id', $employer->id)->get();
        }

        return User::where('empleador_id', $employer->empleador_id)->get();
    }

    private function getWorkHoursSummary($profesionales)
    {
        $workHoursSummary = [];

        $pendingHoursByEmployee = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
            ->whereRaw('approved IS FALSE')
            ->selectRaw('user_id, SUM(hours_worked) as total_pending')
            ->groupBy('user_id')
            ->pluck('total_pending', 'user_id');

        foreach ($profesionales as $profesional) {
            $pendingHours = $pendingHoursByEmployee->get($profesional->id, 0);

            if ($pendingHours > 0) {
                $workHoursSummary[$profesional->id] = [
                    'name' => $profesional->name,
                    'pending_hours' => $pendingHours,
                ];
            }
        }

        return $workHoursSummary;
    }

    private function getRecoveryRequests($professionalIds)
    {
        return WorkHours::whereIn('user_id', $professionalIds)
            ->where('recovered_hours', '>', 0)
            ->whereRaw('recovery_approved IS NULL')
            ->with('user')
            ->get();
    }
}
