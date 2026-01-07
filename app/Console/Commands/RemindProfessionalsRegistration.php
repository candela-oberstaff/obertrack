<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ProfessionalActivityService;
use App\Services\BrevoEmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemindProfessionalsRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind:professional-registration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily reminder for professionals who haven\'t registered their hours';

    /**
     * Execute the console command.
     */
    public function handle(ProfessionalActivityService $activityService, BrevoEmailService $emailService)
    {
        $this->info('Finding professionals in need of reminders...');
        
        $professionalsStatus = $activityService->getProfessionalsStatus();
        // Yellow means they missed 1 day. Red means they missed 2. 
        // We should remind everyone who isn't 'active'.
        $toRemind = $professionalsStatus->whereIn('status', ['yellow', 'red']);

        if ($toRemind->isEmpty()) {
            $this->info('Everyone is up to date!');
            return;
        }

        $this->info('Sending reminders to ' . $toRemind->count() . ' professionals.');

        foreach ($toRemind as $item) {
            $user = $item['user'];
            try {
                // I need another method in BrevoEmailService for this
                $emailService->sendRegistrationReminder($user->email, $user->name);
                $this->info("✓ Sent to {$user->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed for {$user->name}: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }
}
