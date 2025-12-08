<?php

// AÑADIR ESTOS MÉTODOS AL FINAL DE WorkHoursController.php
// IMPORTANTE: Asegúrate de importar el Facade PDF al inicio del archivo:
// use Barryvdh\DomPDF\Facade\Pdf;

    /**
     * Download Weekly Report PDF
     */
    public function downloadWeeklyReport(User $user, Request $request)
    {
        $employer = Auth::user();
        if ($user->empleador_id !== $employer->id) abort(403);

        $weekStart = $request->query('week') 
            ? Carbon::parse($request->query('week'))->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Data gathering (similar to report view)
        $weekHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->get();

        $dailyHours = [];
        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        for ($i = 0; $i < 5; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $hours = $weekHours->where('work_date', $date->format('Y-m-d'))->first();
            $dailyHours[] = [
                'day' => $daysOfWeek[$i],
                'hours' => $hours ? $hours->hours_worked : 0,
            ];
        }

        $totalHours = $weekHours->sum('hours_worked');
        $weeklyAverage = $totalHours > 0 ? round($totalHours / 5, 1) : 0;
        
        $incompleteTasks = Task::where('visible_para', $user->id)
            ->where('completed', false)
            ->count();

        $comments = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->whereNotNull('approval_comment')
            ->pluck('approval_comment')
            ->filter();

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.pdf.weekly', compact(
            'professional', 'weekStart', 'weekEnd', 'totalHours', 
            'weeklyAverage', 'incompleteTasks', 'dailyHours', 'comments'
        ));

        // Variable correction for view
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.pdf.weekly', [
            'professional' => $user,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'totalHours' => $totalHours,
            'weeklyAverage' => $weeklyAverage,
            'incompleteTasks' => $incompleteTasks,
            'dailyHours' => $dailyHours,
            'comments' => $comments
        ]);

        return $pdf->download("Reporte_Semanal_{$user->name}_{$weekStart->format('d-m-Y')}.pdf");
    }

    /**
     * Download Monthly Report PDF
     */
    public function downloadMonthlyReportPdf(User $user, Request $request)
    {
        $employer = Auth::user();
        if ($user->empleador_id !== $employer->id) abort(403);

        $date = $request->query('month') ? Carbon::parse($request->query('month')) : Carbon::now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Get all hours for the month
        $monthHours = WorkHours::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get();
            
        $totalApprovedHours = $monthHours->where('approved', true)->sum('hours_worked');

        // Calculate weekly breakdown
        $weeksData = [];
        $currentDate = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        
        while ($currentDate->lte($endOfMonth)) {
            $weekEnd = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);
            
            // Filter hours for this week
            $weekH = $monthHours->filter(function($h) use ($currentDate, $weekEnd) {
                return Carbon::parse($h->work_date)->between($currentDate, $weekEnd);
            });
            
            $wTotal = $weekH->sum('hours_worked');
            
            // Only add week if it falls within the month (at least partially)
            if ($currentDate->month == $startOfMonth->month || $weekEnd->month == $startOfMonth->month) {
                $weeksData[] = [
                    'period' => $currentDate->format('d/m') . ' - ' . $weekEnd->format('d/m'),
                    'hours' => $wTotal,
                    'approved' => $weekH->where('approved', true)->count() > 0 && $weekH->where('approved', false)->count() == 0
                ];
            }
            
            $currentDate->addWeek();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.pdf.monthly', [
            'professional' => $user,
            'monthDate' => $startOfMonth,
            'totalApprovedHours' => $totalApprovedHours,
            'weeksData' => $weeksData
        ]);

        return $pdf->download("Reporte_Mensual_{$user->name}_{$startOfMonth->format('F_Y')}.pdf");
    }
