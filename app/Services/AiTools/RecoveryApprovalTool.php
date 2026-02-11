<?php

namespace App\Services\AiTools;

use App\Models\User;
use App\Models\RecoveryHour;

class RecoveryApprovalTool extends AbstractAiTool
{
    protected string $name = 'approve_recovery';
    protected string $description = 'Approve or reject a recovery request (Manager only).';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'recovery_id' => ['type' => 'integer'],
                'approved' => ['type' => 'boolean']
            ],
            'required' => ['recovery_id', 'approved']
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        // 1. Check Manager permissions
        if ($user->tipo_usuario !== 'empleador' && !$user->is_superadmin) {
             return "Error: Only employers can approve recovery.";
        }

        $recovery = RecoveryHour::find($input['recovery_id']);
        if (!$recovery) return "Recovery request #{$input['recovery_id']} not found.";

        $recovery->update([
            'approved' => $input['approved'],
            'approved_at' => now()
        ]);

        $status = $input['approved'] ? 'Approved' : 'Rejected';
        return "Recovery request #{$recovery->id} has been $status.";
    }
}
