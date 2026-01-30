<?php

namespace App\Services;

use App\Models\WorkHours;
use App\Models\RecoveryHour;
use Illuminate\Support\Carbon;

class WorkHoursSummaryService
{
    /**
     * Get work hours summary for a list of professionals within a date range
     * Optimized to avoid N+1 queries
     */
    public function getWorkHoursSummary($profesionales, $weekStart, $weekEnd)
    {
        // Single query for all professionals - NO N+1!
        $allWorkHours = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
            ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        $summary = [];
        foreach ($profesionales as $profesional) {
            $workHours = $allWorkHours->get($profesional->id, collect([]));
    
            $summary[$profesional->id] = [
                'name' => $profesional->name,
                'total_hours' => $workHours->sum('hours_worked'),
                'approved_hours' => $workHours->filter(fn($h) => (bool)$h->approved)->sum('hours_worked'),
                'pending_hours' => $workHours->filter(fn($h) => !(bool)$h->approved)->sum('hours_worked'),
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
            $dateStr = $currentDay->format('Y-m-d');
            // Compare as strings to avoid Carbon instance mismatches in collection where()
            $dayHours = $workHours->first(fn($h) => 
                ($h->work_date instanceof Carbon ? $h->work_date->format('Y-m-d') : substr($h->work_date, 0, 10)) === $dateStr
            );

            $days[] = [
                'date' => $dateStr,
                'hours' => $dayHours ? $dayHours->hours_worked : 0,
                'approved' => $dayHours ? (bool)$dayHours->approved : false,
            ];
            $currentDay->addDay();
        }
        return $days;
    }

    /**
     * Get weeks with pending (unapproved) hours for a list of professionals
     */
    public function getPendingWeeks($profesionales)
    {
        $pendingWeeks = [];
        $currentWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        // Buscar hasta 4 semanas atrás, incluyendo la semana actual
        for ($i = 0; $i <= 4; $i++) {
            $weekStart = $currentWeek->copy()->subWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
            
            $pendingHoursExists = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
                ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                ->whereRaw('approved IS FALSE')
                ->exists();

            if (!$pendingHoursExists) {
                $pendingHoursExists = RecoveryHour::whereIn('user_id', $profesionales->pluck('id'))
                    ->whereBetween('recovery_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                    ->whereRaw('approved IS FALSE')
                    ->exists();
            }
            
            if ($pendingHoursExists) {
                $summary = $this->getWorkHoursSummary($profesionales, $weekStart, $weekEnd);
                
                // Add recovery pending data
                $recoveryPending = RecoveryHour::whereIn('user_id', $profesionales->pluck('id'))
                    ->whereBetween('recovery_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                    ->whereRaw('approved IS FALSE')
                    ->get();

                // Merge with old recoveries for transition period
                $oldRecoveryPending = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
                    ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                    ->where('recovered_hours', '>', 0)
                    ->whereRaw('recovery_approved IS FALSE')
                    ->get();
                
                // We'll pass the new ones primarily, but keep legacy support if needed in view
                // For now, let's just use the new ones as the source of truth for "RecoveryHour" objects
                
                $pendingWeeks[] = [
                    'start' => $weekStart,
                    'end' => $weekEnd,
                    'summary' => $summary,
                    'recovery_requests' => $recoveryPending
                ];
            }
        }
        
        return $pendingWeeks;
    }

    /**
     * Get approved weeks for a specific month
     */
    public function getApprovedWeeks($profesionales, $month)
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
                $isApproved = WorkHours::whereIn('user_id', $profesionales->pluck('id'))
                    ->whereBetween('work_date', [$weekStart, $weekEnd])
                    ->whereRaw('approved IS TRUE')
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
    public function getTotalApprovedHoursForMonth($profesionales, $month)
    {
        return WorkHours::whereIn('user_id', $profesionales->pluck('id'))
            ->whereYear('work_date', $month->year)
            ->whereMonth('work_date', $month->month)
            ->whereRaw('approved IS TRUE')
            ->sum('hours_worked');
    }

    /**
     * Calculate total approved hours for a specific professional in a date range
     */
    public function getProfessionalApprovedHoursInRange($professionalId, $startDate, $endDate)
    {
        return WorkHours::where('user_id', $professionalId)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->whereRaw('approved IS TRUE')
            ->sum('hours_worked');
    }
}
