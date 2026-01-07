<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ProfessionalActivityService;
use App\Services\BrevoEmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckProfessionalActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:professional-activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor professional inactivity and alert the analyst (Karen) about red status';

    /**
     * Execute the console command.
     */
    public function handle(ProfessionalActivityService $activityService, BrevoEmailService $emailService)
    {
        $this->info('Checking professional activity...');
        
        $professionalsStatus = $activityService->getProfessionalsStatus();
        $redAlerts = $professionalsStatus->where('status', 'red');

        if ($redAlerts->isEmpty()) {
            $this->info('No red alerts found today.');
            return;
        }

        $this->info('Found ' . $redAlerts->count() . ' red alerts. Notifying employers...');

        // 1. Notify Employers about their specific red alerts
        $groupedByEmployer = $redAlerts->groupBy(fn($item) => $item['user']->empleador_id);

        foreach ($groupedByEmployer as $employerId => $alerts) {
            $employer = User::find($employerId);
            if ($employer && $employer->email) {
                try {
                    $emailService->sendAnalystAlert($employer->email, $employer->name, [
                        'red_alerts' => $alerts->toArray(),
                        'total_professionals' => $professionalsStatus->where('user.empleador_id', $employerId)->count(),
                    ]);
                    $this->info("✓ Alert sent to Employer: {$employer->name}");
                } catch (\Exception $e) {
                    $this->error("✗ Failed for Employer {$employer->name}: {$e->getMessage()}");
                }
            }
        }

        // 2. Also notify the Central Analyst (Karen / Candela) for oversight
        $analystEmail = 'candela@oberstaff.com';
        $analystName = 'Analista Central';

        try {
            $emailService->sendAnalystAlert($analystEmail, $analystName, [
                'red_alerts' => $redAlerts->toArray(),
                'total_professionals' => $professionalsStatus->count(),
            ]);
            $this->info('Central alert email sent to ' . $analystEmail);
        } catch (\Exception $e) {
            $this->error('Failed to send central alert email: ' . $e->getMessage());
            Log::error('CheckProfessionalActivity central error: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
