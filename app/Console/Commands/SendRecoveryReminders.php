<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WorkHours;
use App\Services\BrevoEmailService;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendRecoveryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-recovery-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reminders to professionals with pending recovery hours from yesterday\'s absences';

    public function __construct(
        private BrevoEmailService $emailService,
        private CalendarService $calendarService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recovery reminders process...');
        
        $yesterday = Carbon::yesterday();
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth()->locale('es')->isoFormat('D [de] MMMM');
        
        // Skip if yesterday was a weekend
        if ($yesterday->isWeekend()) {
            $this->info('Yesterday was a weekend. Skipping.');
            return 0;
        }

        // Find all professionals (empleados)
        $professionals = User::where('tipo_usuario', 'empleado')->get();
        
        $sentCount = 0;
        
        foreach ($professionals as $professional) {
            // Check if they had an absence yesterday (hours_worked < 8 on a weekday)
            $yesterdayRecord = WorkHours::where('user_id', $professional->id)
                ->whereDate('work_date', $yesterday)
                ->first();
            
            // If no record or hours < 8, they had an absence
            $hadAbsence = !$yesterdayRecord || $yesterdayRecord->hours_worked < 8;
            
            if (!$hadAbsence) {
                continue; // Skip if no absence yesterday
            }
            
            // Calculate current month's deficit using CalendarService
            $missingHours = $this->calendarService->getMissingHours($currentMonth, $professional->id);
            
            // Only send if there are pending hours
            if ($missingHours > 0) {
                try {
                    $this->emailService->sendRecoveryReminder(
                        $professional->email,
                        $professional->name,
                        $missingHours,
                        $endOfMonth
                    );
                    
                    $sentCount++;
                    $this->info("✓ Sent reminder to {$professional->name} ({$professional->email}) - {$missingHours} hours pending");
                    
                    Log::info('Recovery reminder sent', [
                        'professional_id' => $professional->id,
                        'professional_email' => $professional->email,
                        'pending_hours' => $missingHours
                    ]);
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send reminder to {$professional->name}: {$e->getMessage()}");
                    
                    Log::error('Recovery reminder failed', [
                        'professional_id' => $professional->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        $this->info("Recovery reminders process completed. Sent {$sentCount} reminder(s).");
        
        return 0;
    }
}
