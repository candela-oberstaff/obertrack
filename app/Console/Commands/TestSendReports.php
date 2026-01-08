<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\BrevoEmailService;
use App\Services\ReportService;
use Carbon\Carbon;

class TestSendReports extends Command
{
    protected $signature = 'test:send-reports {email=candela@oberstaff.com}';
    protected $description = 'Send test reports to specific email';

    public function handle(BrevoEmailService $emailService, ReportService $reportService)
    {
        $testEmail = $this->argument('email');
        $this->info("Sending test reports to: $testEmail");
        
        $company = User::where('tipo_usuario', 'empleador')->with('empleados')->first();
        
        if (!$company) {
            $this->error('No Company (empleador) found in database to generate report from.');
            return;
        }
        
        $this->info("Using Company for Data: {$company->name}");

        // --- WEEKLY REPORT ---
        $this->info('Generating Weekly Report...');
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeek();
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        
        // ... (Skipping full logic for professionals loop as we just want the PDF content for testing ... 
        // Actually I need $professionals logic to generate valid PDF content)
        // I will copy the logic again.
        
        $professionals = $company->empleados;
        $reportDataWeekly = [];
        
        foreach ($professionals as $professional) {
            $weekHours = \App\Models\WorkHours::where('user_id', $professional->id)
                ->whereBetween('work_date', [$weekStart, $weekEnd])
                ->get();
            
            $totalHours = $weekHours->sum('hours_worked');
            $approvedHours = $weekHours->where('approved', true)->sum('hours_worked');
            $pendingHours = $weekHours->where('approved', false)->sum('hours_worked');
            $absences = 0; // Simplified for test
             for ($i = 0; $i < 5; $i++) {
                $date = $weekStart->copy()->addDays($i);
                $dayRecord = $weekHours->first(fn($h) => Carbon::parse($h->work_date)->format('Y-m-d') === $date->format('Y-m-d'));
                if (!$dayRecord || $dayRecord->hours_worked == 0) $absences++;
            }
            
            $reportDataWeekly[] = [
                'name' => $professional->name,
                'email' => $professional->email,
                'total_hours' => $totalHours,
                'approved_hours' => $approvedHours,
                'pending_hours' => $pendingHours,
                'absences' => $absences,
            ];
        }

        $pdfWeekly = $reportService->generateClientReportPDF($company, $weekStart, $weekEnd, 'Semanal');
        
        // TEST 1: Weekly Report (Template + Attachment)
        $this->info('Test 1: Sending Weekly Report (Template ID 1 + Attachment)...');
        $result1 = $emailService->sendWeeklyReport(
            $testEmail, 
            "Test Recipient",
            [
                'week_start' => $weekStart->format('d/m/Y'),
                'week_end' => $weekEnd->format('d/m/Y'),
                'professionals' => $reportDataWeekly,
                'company_name' => $company->company_name ?? $company->name,
            ],
            [
                'content' => $pdfWeekly,
                'name' => 'Review_Reporte_Semanal.pdf'
            ]
        );
        $this->info($result1 ? 'Sent.' : 'Failed.');

        // TEST 2: Simple HTML Email (No Attachment)
        $this->info('Test 2: Sending Simple HTML Email (No Attachment)...');
        $result2 = $emailService->sendEmail(
            $testEmail, 
            "Test Recipient",
            "Obertrack Test: Simple Email",
            "<p>This is a simple test email to verify delivery.</p>"
        );
        $this->info($result2 ? 'Sent.' : 'Failed.');

        // TEST 3: Simple HTML Email + Attachment
        $this->info('Test 3: Sending Simple HTML Email + Attachment...');
        $result3 = $emailService->sendEmail(
            $testEmail, 
            "Test Recipient",
            "Obertrack Test: Email with Attachment",
            "<p>This email attaches a PDF via HTML method.</p>",
            ['content' => $pdfWeekly, 'name' => 'Simple_Att.pdf']
        );
        $this->info($result3 ? 'Sent.' : 'Failed.');
        
        return Command::SUCCESS;
    }
}
