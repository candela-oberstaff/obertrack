<?php

namespace App\Services\AiTools;

use App\Models\User;
use Illuminate\Support\Facades\Http;
// We should reuse the Controller logic via a Service if possible, 
// but WorkHoursController has logic inside store method.
// For MVP, we will replicate the basic creation logic or call the route internally? 
// Calling route internally is cleaner but tricky with Auth. 
// Let's implement logic using Models directly to be safe and fast.

use App\Models\WorkHours;

class WorkLogTool extends AbstractAiTool
{
    protected string $name = 'log_work_hours';
    protected string $description = 'Register work hours for a specific date. You can also log absences (less than 8 hours).';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'hours' => [
                    'type' => 'number',
                    'description' => 'Number of hours worked (e.g. 8, 4.5)'
                ],
                'date' => [
                    'type' => 'string',
                    'format' => 'date',
                    'description' => 'Date of work (YYYY-MM-DD)'
                ],
                'description' => [
                    'type' => 'string',
                    'description' => 'Description of activities done'
                ],
                'absence_reason' => [
                    'type' => 'string',
                    'description' => 'If hours < 8, reason for absence'
                ]
            ],
            'required' => ['hours', 'date', 'description']
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        // Validation logic from Controller...
        
        $hours = $input['hours'];
        $date = $input['date'];
        
        if ($hours > 8) return "Error: Cannot log more than 8 hours per day.";
        
        // Check for weekend?
        $carbonDate = \Carbon\Carbon::parse($date);
        if ($carbonDate->isWeekend()) return "Error: Cannot log hours on weekends.";

        // Basic Check for existing
        $existing = WorkHours::where('user_id', $user->id)->where('work_date', $date)->first();
        if ($existing) {
             return "Error: You already have hours logged for $date. Update support not yet available via chat.";
        }

        WorkHours::create([
            'user_id' => $user->id,
            'work_date' => $date,
            'hours_worked' => $hours,
            'user_comment' => $input['description'],
            'absence_reason' => $input['absence_reason'] ?? null,
            'absence_hours' => $hours < 8 ? (8 - $hours) : 0
        ]);

        return "Successfully logged $hours hours for $date.";
    }
}
