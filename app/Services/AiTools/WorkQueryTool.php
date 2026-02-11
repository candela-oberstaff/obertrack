<?php

namespace App\Services\AiTools;

use App\Models\User;
use App\Models\WorkHours;
use Carbon\Carbon;

class WorkQueryTool extends AbstractAiTool
{
    protected string $name = 'get_work_hours';
    protected string $description = 'Get summary of hours worked for a period.';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'start_date' => ['type' => 'string', 'format' => 'date'],
                'end_date' => ['type' => 'string', 'format' => 'date'],
                'user_id' => ['type' => 'integer', 'description' => 'Optional (Manager/Admin only)']
            ],
            'required' => ['start_date', 'end_date']
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        $targetUserId = $input['user_id'] ?? $user->id;

        // Security
        if ($targetUserId != $user->id) {
             // Check if manager/employer logic...
             if (!$user->is_superadmin && $user->tipo_usuario !== 'empleador') {
                  return "Error: You can only check your own hours.";
             }
        }

        $hours = WorkHours::where('user_id', $targetUserId)
            ->whereBetween('work_date', [$input['start_date'], $input['end_date']])
            ->get();

        $total = $hours->sum('hours_worked');
        
        return [
            'total_hours' => $total,
            'days_logged' => $hours->count(),
            'period' => "{$input['start_date']} to {$input['end_date']}"
        ];
    }
}
