<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WorkHours;
use Carbon\Carbon;
use App\Services\ProfessionalActivityService;
use App\Services\BrevoEmailService;
use Illuminate\Console\Command;

class RemindProfessionalsRegistration extends Command
{
    protected $signature = 'remind:professional-registration';
    protected $description = 'Reminder for professionals to register hours (Mon/Wed/Fri)';

    public function handle(ProfessionalActivityService $activityService, BrevoEmailService $emailService)
    {
        $this->info('Starting scheduled reminder check...');

        $professionals = User::where('tipo_usuario', 'empleado')->get();
        // Use the existing service to determine if they are lagging behind
        $statuses = $activityService->getStatusesForUsers($professionals);

        $count = 0;
        foreach ($statuses as $data) {
            $user = $data['user'];
            $status = $data['status']; // 'active', 'yellow', 'red'
            
            // Check absences in the current week up to today
            $absences = WorkHours::where('user_id', $user->id)
                ->where('absence_hours', '>', 0)
                ->whereBetween('work_date', [Carbon::now()->startOfWeek(), Carbon::now()])
                ->pluck('work_date')
                ->toArray();
            
            // Logic: Send reminder if they are inactive (missing hours) OR if they have registered absences to confirm
            if ($status !== 'active' || !empty($absences)) {
                try {
                    $emailService->sendRegistrationReminder($user->email, $user->name, $absences);
                    $this->info("✓ Sent to {$user->name} (Status: {$status}, Absences: " . count($absences) . ")");
                    $count++;
                } catch (\Exception $e) {
                    $this->error("✗ Failed for {$user->name}: {$e->getMessage()}");
                }
            } else {
                 $this->info("- Skipped {$user->name} (Active, No Absences)");
            }
        }

        $this->info("Done. Sent $count reminders.");
        return Command::SUCCESS;
    }
}
