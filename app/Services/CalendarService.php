<?php

namespace App\Services;

use App\Models\WorkHours;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarService
{
    /**
     * Generate a calendar grid for the given month
     * 
     * @param Carbon $month The month to generate calendar for
     * @param int $userId The user ID to fetch work hours for
     * @return array Array of weeks, each containing 7 days
     */
    public function generateCalendar(Carbon $month, int $userId): array
    {
        $calendar = [];
        $startDate = $month->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endDate = $month->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $workHours = $this->getWorkHoursForPeriod($startDate, $endDate, $userId)
            ->keyBy(function($item) {
                return $item->work_date->format('Y-m-d');
            });

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $calendar[] = [
                'date' => $date->copy(),
                'inMonth' => $date->month === $month->month,
                'workHours' => $workHours->get($date->format('Y-m-d'))
            ];
        }

        return array_chunk($calendar, 7);
    }

    /**
     * Get work hours for a specific period
     * 
     * @param Carbon $start Start date
     * @param Carbon $end End date
     * @param int $userId User ID
     * @return Collection Collection of WorkHours models
     */
    public function getWorkHoursForPeriod(Carbon $start, Carbon $end, int $userId): Collection
    {
        return WorkHours::where('user_id', $userId)
            ->whereBetween('work_date', [$start, $end])
            ->get();
    }

    /**
     * Get total hours worked in a month
     * 
     * @param Carbon $month The month to calculate for
     * @param int $userId User ID
     * @return float Total hours worked
     */
    public function getTotalHoursForMonth(Carbon $month, int $userId): float
    {
        return WorkHours::where('user_id', $userId)
            ->whereYear('work_date', $month->year)
            ->whereMonth('work_date', $month->month)
            ->sum('hours_worked');
    }
}
