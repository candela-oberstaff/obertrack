<?php

namespace App\Services;

use App\Models\WorkHours;
use Illuminate\Support\Carbon;

class WorkHoursSummaryService
{
    /**
     * Get work hours summary for a list of employees within a date range
     * Optimized to avoid N+1 queries
     */
    public function getWorkHoursSummary($empleados, $weekStart, $weekEnd)
    {
        // Single query for all employees - NO N+1!
        $allWorkHours = WorkHours::whereIn('user_id', $empleados->pluck('id'))
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get()
            ->groupBy('user_id');

        $summary = [];
        foreach ($empleados as $empleado) {
            $workHours = $allWorkHours->get($empleado->id, collect([]));
    
            $summary[$empleado->id] = [
                'name' => $empleado->name,
                'total_hours' => $workHours->sum('hours_worked'),
                'approved_hours' => $workHours->where('approved', true)->sum('hours_worked'),
                'pending_hours' => $workHours->where('approved', false)->sum('hours_worked'),
                'days' => $this->getDailyHours($workHours, $weekStart, $weekEnd),
            ];
        }
        return $summary;
    }
    
    /**
     * Get daily hours breakdown for a set of work hours
     */
    public function getDailyHours($workHours, $weekStart, $weekEnd)
    {
        $days = [];
        $currentDay = $weekStart->copy();
        while ($currentDay <= $weekEnd) {
            $dayHours = $workHours->where('work_date', $currentDay->format('Y-m-d'))->first();
            $days[] = [
                'date' => $currentDay->format('Y-m-d'),
                'hours' => $dayHours ? $dayHours->hours_worked : 0,
                'approved' => $dayHours ? $dayHours->approved : false,
            ];
            $currentDay->addDay();
        }
        return $days;
    }

    /**
     * Get weeks with pending (unapproved) hours for a list of employees
     */
    public function getPendingWeeks($empleados)
    {
        $pendingWeeks = [];
        $currentWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        // Buscar hasta 4 semanas atrás, excluyendo la semana actual
        for ($i = 1; $i <= 4; $i++) {
            $weekStart = $currentWeek->copy()->subWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);
            
            $pendingHours = WorkHours::whereIn('user_id', $empleados->pluck('id'))
                ->whereBetween('work_date', [$weekStart, $weekEnd])
                ->where('approved', false)
                ->exists();
            
            if ($pendingHours) {
                $pendingWeeks[] = [
                    'start' => $weekStart,
                    'end' => $weekEnd,
                    'summary' => $this->getWorkHoursSummary($empleados, $weekStart, $weekEnd)
                ];
            }
        }
        
        return $pendingWeeks;
    }

    /**
     * Get approved weeks for a specific month
     */
    public function getApprovedWeeks($empleados, $month)
    {
        $approvedWeeks = [];
        $currentWeek = $month->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $endOfMonth = $month->copy()->endOfMonth();

        while ($currentWeek->lte($endOfMonth)) {
            $weekEnd = $currentWeek->copy()->endOfWeek(Carbon::FRIDAY);
            
            // Ajustar el inicio y fin de la semana si están fuera del mes actual
            $weekStart = max($currentWeek, $month->copy()->startOfMonth());
            $weekEnd = min($weekEnd, $endOfMonth);

            if ($weekStart->lte($endOfMonth)) {
                $isApproved = WorkHours::whereIn('user_id', $empleados->pluck('id'))
                    ->whereBetween('work_date', [$weekStart, $weekEnd])
                    ->where('approved', true)
                    ->exists();

                $approvedWeeks[] = [
                    'start' => $weekStart->format('d/m/Y'),
                    'end' => $weekEnd->format('d/m/Y'),
                    'approved' => $isApproved
                ];
            }

            $currentWeek->addWeek();
        }

        return $approvedWeeks;
    }

    /**
     * Calculate total approved hours for a specific month
     */
    public function getTotalApprovedHoursForMonth($empleados, $month)
    {
        return WorkHours::whereIn('user_id', $empleados->pluck('id'))
            ->whereYear('work_date', $month->year)
            ->whereMonth('work_date', $month->month)
            ->where('approved', true)
            ->sum('hours_worked');
    }

    /**
     * Calculate total approved hours for a specific employee in a date range
     */
    public function getEmployeeApprovedHoursInRange($employeeId, $startDate, $endDate)
    {
        return WorkHours::where('user_id', $employeeId)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->where('approved', true)
            ->sum('hours_worked');
    }
}
