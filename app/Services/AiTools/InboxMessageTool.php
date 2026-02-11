<?php

namespace App\Services\AiTools;

use App\Models\User;
use App\Models\Message;

class InboxMessageTool extends AbstractAiTool
{
    protected string $name = 'send_direct_message';
    protected string $description = 'Send a private message to another user. Use search_users to find recipient ID first.';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'recipient_id' => ['type' => 'integer'],
                'message' => ['type' => 'string']
            ],
            'required' => ['recipient_id', 'message']
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        // Simple create message
        Message::create([
             'from_user_id' => $user->id,
             'to_user_id' => $input['recipient_id'],
             'message' => $input['message']
        ]);

        return "Message sent to User ID {$input['recipient_id']}.";
    }
}
