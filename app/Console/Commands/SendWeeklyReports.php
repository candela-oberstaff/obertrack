<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\BrevoEmailService;
use App\Services\ReportService;
use Carbon\Carbon;

class SendWeeklyReports extends Command
{
    protected $signature = 'reports:send-weekly';
    protected $description = 'Send weekly reports to all companies';

    public function handle(BrevoEmailService $emailService, ReportService $reportService)
    {
        $this->info('Starting weekly report generation...');
        
        // Get all companies (employers)
        $companies = User::where('tipo_usuario', 'empleador')->get();
        
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeek();
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        
        foreach ($companies as $company) {
            try {
                // Get all professionals for this company
                $professionals = $company->empleados;
                
                if ($professionals->isEmpty()) {
                    $this->warn("Company {$company->name} has no professionals. Skipping.");
                    continue;
                }
                
                // Prepare report data for all professionals
                $reportData = [];
                foreach ($professionals as $professional) {
                    $weekHours = \App\Models\WorkHours::where('user_id', $professional->id)
                        ->whereBetween('work_date', [$weekStart, $weekEnd])
                        ->get();
                    
                    $totalHours = $weekHours->sum('hours_worked');
                    $approvedHours = $weekHours->where('approved', true)->sum('hours_worked');
                    $pendingHours = $weekHours->where('approved', false)->sum('hours_worked');
                    
                    // Count absences (weekdays with 0 hours)
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
                
                // Generar PDF detallado (Desglose de actividades, etc.)
                $pdfContent = $reportService->generateClientReportPDF($company, $weekStart, $weekEnd, 'Semanal');

                // Send consolidated email with attachment
                $emailService->sendWeeklyReport(
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
                
                $this->info("Weekly report sent to {$company->name}");
                
            } catch (\Exception $e) {
                $this->error("Failed to send report to {$company->name}: {$e->getMessage()}");
                \Log::error('Weekly report failed', [
                    'company_id' => $company->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info('Weekly reports completed!');
    }
}
