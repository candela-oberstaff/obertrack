<?php

namespace App\Services\AiTools;

use App\Models\Task;
use App\Models\User;
use App\Models\Comment;

class TaskCommentTool extends AbstractAiTool
{
    protected string $name = 'add_task_comment';
    protected string $description = 'Add a comment to an existing task. You MUST have the task_id — use get_tasks to find it if you do not know it.';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'task_id' => [
                    'type' => 'integer',
                     'description' => 'ID of the task'
                ],
                'content' => [
                    'type' => 'string',
                    'description' => 'The message body'
                ]
            ],
            'required' => ['task_id', 'content']
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        $task = Task::find($input['task_id']);
        if (!$task) return "Task not found.";

        // Deduplication: check if this exact comment was already added recently
        $existingComment = Comment::where('task_id', $input['task_id'])
            ->where('user_id', $user->id)
            ->where('content', $input['content'])
            ->where('created_at', '>=', now()->subMinutes(2))
            ->first();
        
        if ($existingComment) {
            return "Comment already exists on task #{$input['task_id']} (added moments ago). Skipping duplicate.";
        }
        
        Comment::create([
            'task_id' => $input['task_id'],
            'user_id' => $user->id,
            'content' => $input['content']
        ]);

        return "Comment added to task #{$input['task_id']}.";
    }
}
