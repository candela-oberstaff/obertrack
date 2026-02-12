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
        $userIds = $users->pluck('id')->toArray();
        $lastWorkDay = $this->getLastWorkingDay()->startOfDay();
        $dayBeforeLastWorkDay = $this->getWorkingDayBefore($lastWorkDay)->startOfDay();

        // Get existence of hours for all users on specific dates in one go or grouped
        $checkDates = [$lastWorkDay->format('Y-m-d'), $dayBeforeLastWorkDay->format('Y-m-d')];
        $hoursExistence = WorkHours::whereIn('user_id', $userIds)
            ->whereIn('work_date', $checkDates)
            ->get()
            ->groupBy('user_id');

        // Get last registration dates for all users
        $lastRegistrations = WorkHours::whereIn('user_id', $userIds)
            ->selectRaw('user_id, MAX(work_date) as last_date')
            ->groupBy('user_id')
            ->get()
            ->pluck('last_date', 'user_id');

        return $users->map(function ($professional) use ($lastWorkDay, $dayBeforeLastWorkDay, $hoursExistence, $lastRegistrations) {
            $registrationDate = $professional->created_at->startOfDay();
            
            $userHours = $hoursExistence->get($professional->id, collect());
            $hasHoursLast = $userHours->contains('work_date', $lastWorkDay->format('Y-m-d 00:00:00')) || 
                           $userHours->contains('work_date', $lastWorkDay->format('Y-m-d'));
            
            $hasHoursBefore = $userHours->contains('work_date', $dayBeforeLastWorkDay->format('Y-m-d 00:00:00')) || 
                             $userHours->contains('work_date', $dayBeforeLastWorkDay->format('Y-m-d'));

            $status = 'active';
            $daysInactive = 0;

            // Only consider inactive if they were registered on or before the target workday
            if (!$hasHoursLast && $registrationDate->lessThanOrEqualTo($lastWorkDay)) {
                $status = 'yellow';
                $daysInactive = 1;
                
                if (!$hasHoursBefore && $registrationDate->lessThanOrEqualTo($dayBeforeLastWorkDay)) {
                    $status = 'red';
                    $daysInactive = 2; // Inactive for at least 2 working days
                }
            }

            return [
                'user' => $professional,
                'status' => $status,
                'days_inactive' => $daysInactive,
                'last_registration' => isset($lastRegistrations[$professional->id]) ? Carbon::parse($lastRegistrations[$professional->id]) : null,
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
