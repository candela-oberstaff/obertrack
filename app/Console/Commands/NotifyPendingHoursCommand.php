<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WorkHours;
use App\Services\BrevoEmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotifyPendingHoursCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:pending-hours {--days=7 : Minimum days since work hours were created}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to companies about pending hours approval';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minDays = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($minDays);

        $this->info("Checking for pending hours older than {$minDays} days...");

        // Get all employers (companies)
        $employers = User::where('tipo_usuario', 'empleador')->get();

        $notificationsSent = 0;

        foreach ($employers as $employer) {
            // Get all employees for this employer
            $employeeIds = $employer->empleados()->pluck('id');

            if ($employeeIds->isEmpty()) {
                continue;
            }

            // Check for pending hours
            $pendingHours = WorkHours::whereIn('user_id', $employeeIds)
                ->where('approved', false)
                ->where('created_at', '<=', $cutoffDate)
                ->with('user')
                ->get();

            if ($pendingHours->isEmpty()) {
                continue;
            }

            // Group pending hours by employee
            $pendingByEmployee = $pendingHours->groupBy('user_id')->map(function ($hours, $userId) {
                $employee = $hours->first()->user;
                return [
                    'employee_name' => $employee->name,
                    'hours' => $hours->sum('hours_worked'),
                    'count' => $hours->count(),
                ];
            })->values()->toArray();

            $totalPendingHours = $pendingHours->sum('hours_worked');

            // Send notification email
            if ($employer->email) {
                try {
                    $brevoService = app(BrevoEmailService::class);
                    $brevoService->sendPendingHoursNotification(
                        $employer->email,
                        $employer->name,
                        [
                            'pending_hours' => $pendingByEmployee,
                            'total_hours' => $totalPendingHours,
                        ]
                    );

                    $notificationsSent++;
                    $this->info("✓ Notification sent to {$employer->name} ({$employer->email})");
                    
                    Log::info('Pending hours notification sent', [
                        'employer_id' => $employer->id,
                        'employer_email' => $employer->email,
                        'total_pending_hours' => $totalPendingHours,
                        'employees_count' => count($pendingByEmployee)
                    ]);
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send notification to {$employer->name}: {$e->getMessage()}");
                    
                    Log::error('Failed to send pending hours notification', [
                        'employer_id' => $employer->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $this->info("\nTotal notifications sent: {$notificationsSent}");

        return Command::SUCCESS;
    }
}
