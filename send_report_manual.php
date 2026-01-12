<?php
use App\Models\User;
use App\Services\BrevoEmailService;
use App\Services\ReportService;
use Carbon\Carbon;

try {
    $company = User::where('email', 'candela@oberstaff.com')->first();
    if (!$company) {
        echo "Company not found for candela@oberstaff.com\n";
        exit;
    }

    $reportService = app(ReportService::class);
    $emailService = app(BrevoEmailService::class);

    $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeek();
    $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

    $professionals = $company->empleados;
    $reportData = [];
    foreach ($professionals as $professional) {
        $weekHours = \App\Models\WorkHours::where('user_id', $professional->id)
            ->whereBetween('work_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->get();
        
        $totalHours = $weekHours->sum('hours_worked');
        $approvedHours = $weekHours->where('approved', true)->sum('hours_worked');
        $pendingHours = $weekHours->where('approved', false)->sum('hours_worked');
        
        $absences = 0;
        for ($i = 0; $i < 5; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $dayRecord = $weekHours->first(fn($h) => 
                Carbon::parse($h->work_date)->format('Y-m-d') === $date->format('Y-m-d')
            );
            if (!$dayRecord || $dayRecord->hours_worked == 0) {
                $absences++;
            }
        }
        
        $reportData[] = [
            'name' => $professional->name,
            'email' => $professional->email,
            'total_hours' => $totalHours,
            'approved_hours' => $approvedHours,
            'pending_hours' => $pendingHours,
            'absences' => $absences,
        ];
    }

    $pdfContent = $reportService->generateClientReportPDF($company, $weekStart, $weekEnd, 'Semanal');

    $result = $emailService->sendWeeklyReport(
        $company->email,
        $company->name,
        [
            'week_start' => $weekStart->format('d/m/Y'),
            'week_end' => $weekEnd->format('d/m/Y'),
            'professionals' => $reportData,
            'company_name' => $company->company_name ?? $company->name,
        ],
        [
            'content' => $pdfContent,
            'name' => 'Reporte_Semanal_Actividades.pdf'
        ]
    );

    if ($result) {
        echo "Report sent successfully to {$company->email}\n";
    } else {
        echo "Failed to send report to {$company->email}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
