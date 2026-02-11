<?php

namespace App\Services\AiTools;

use App\Models\User;
use App\Models\RecoveryHour;

class RecoveryRequestTool extends AbstractAiTool
{
    protected string $name = 'request_recovery_hours';
    protected string $description = 'Request to recover pending/debt hours.';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'hours' => ['type' => 'number'],
                'activities' => ['type' => 'string']
            ],
            'required' => ['hours', 'activities']
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        RecoveryHour::create([
             'user_id' => $user->id,
             'recovery_date' => now()->toDateString(),
             'hours_recovered' => $input['hours'],
             'activities' => $input['activities']
        ]);

        return "Recovery request for {$input['hours']} hours submitted.";
    }
}
