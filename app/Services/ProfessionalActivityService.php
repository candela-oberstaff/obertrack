<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkHours;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProfessionalActivityService
{
    /**
     * Get inactivity status for a specific group of users.
     */
    public function getStatusesForUsers(Collection $users): Collection
    {
        return $users->map(function ($professional) {
            $lastWorkDay = $this->getLastWorkingDay();
            $dayBeforeLastWorkDay = $this->getWorkingDayBefore($lastWorkDay);

            $hasHoursLast = $this->hasHoursOn($professional->id, $lastWorkDay);
            $hasHoursBefore = $this->hasHoursOn($professional->id, $dayBeforeLastWorkDay);

            $status = 'active';
            $daysInactive = 0;

            if (!$hasHoursLast) {
                $status = 'yellow';
                $daysInactive = 1;
                
                if (!$hasHoursBefore) {
                    $status = 'red';
                    $daysInactive = 2; // Simplification: at least 2
                }
            }

            return [
                'user' => $professional,
                'status' => $status,
                'days_inactive' => $daysInactive,
                'last_registration' => $this->getLastRegistrationDate($professional->id),
            ];
        });
    }

    public function getProfessionalsStatus(): Collection
    {
        $professionals = User::where('tipo_usuario', 'empleado')->get();
        return $this->getStatusesForUsers($professionals);
    }

    private function hasHoursOn($userId, Carbon $date): bool
    {
        return WorkHours::where('user_id', $userId)
            ->whereDate('work_date', $date->format('Y-m-d'))
            ->exists();
    }

    private function getLastRegistrationDate($userId)
    {
        return WorkHours::where('user_id', $userId)
            ->orderBy('work_date', 'desc')
            ->first()?->work_date;
    }

    private function getLastWorkingDay(): Carbon
    {
        $date = Carbon::now();
        // If today is Monday, last working day was Friday
        if ($date->isWeekend() || $date->isMonday()) {
             // Logic to find last weekday
             // For simplicity in this logic-focused phase:
             $date = $date->subDay();
             while($date->isWeekend()) {
                 $date->subDay();
             }
        } else {
            // If today is Tuesday-Friday, last working day was yesterday
            $date = $date->subDay();
            if ($date->isWeekend()) {
                $date->subDay();
                if ($date->isWeekend()) $date->subDay();
            }
        }
        return $date;
    }

    private function getWorkingDayBefore(Carbon $date): Carbon
    {
        $prev = $date->copy()->subDay();
        while($prev->isWeekend()) {
            $prev->subDay();
        }
        return $prev;
    }
}
