<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\BrevoEmailService;
use App\Services\ReportService;
use Carbon\Carbon;

class SendMonthlyReports extends Command
{
    protected $signature = 'reports:send-monthly';
    protected $description = 'Send monthly reports to all companies';

    public function handle(BrevoEmailService $emailService, ReportService $reportService)
    {
        $this->info('Starting monthly report generation...');
        
        $companies = User::where('tipo_usuario', 'empleador')->get();
        
        $monthStart = Carbon::now()->subMonth()->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        
        foreach ($companies as $company) {
            try {
                $professionals = $company->empleados;
                
                if ($professionals->isEmpty()) {
                    $this->warn("Company {$company->name} has no professionals. Skipping.");
                    continue;
                }
                
                $reportData = [];
                foreach ($professionals as $professional) {
                    $monthHours = \App\Models\WorkHours::where('user_id', $professional->id)
                        ->whereBetween('work_date', [$monthStart, $monthEnd])
                        ->get();
                    
                    $totalHours = $monthHours->sum('hours_worked');
                    $approvedHours = $monthHours->where('approved', true)->sum('hours_worked');
                    $pendingHours = $monthHours->where('approved', false)->sum('hours_worked');
                    
                    // Count total absences in the month
                    $absences = 0;
                    $current = $monthStart->copy();
                    while ($current->lte($monthEnd)) {
                        if (!$current->isWeekend()) {
                            $dayRecord = $monthHours->first(fn($h) => 
                                Carbon::parse($h->work_date)->format('Y-m-d') === $current->format('Y-m-d')
                            );
                            if (!$dayRecord || $dayRecord->hours_worked == 0) {
                                $absences++;
                            }
                        }
                        $current->addDay();
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
                
                // Generar PDF detallado
                $pdfContent = $reportService->generateClientReportPDF($company, $monthStart, $monthEnd, 'Mensual');

                $emailService->sendMonthlyReport(
                    $company->email,
                    $company->name,
                    [
                        'month' => $monthStart->format('F Y'),
                        'month_start' => $monthStart->format('d/m/Y'),
                        'month_end' => $monthEnd->format('d/m/Y'),
                        'professionals' => $reportData,
                        'company_name' => $company->company_name ?? $company->name,
                    ],
                    [
                        'content' => $pdfContent,
                        'name' => 'Reporte_Mensual_Actividades.pdf'
                    ]
                );
                
                $this->info("Monthly report sent to {$company->name}");
                
            } catch (\Exception $e) {
                $this->error("Failed to send report to {$company->name}: {$e->getMessage()}");
                \Log::error('Monthly report failed', [
                    'company_id' => $company->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info('Monthly reports completed!');
    }
}
