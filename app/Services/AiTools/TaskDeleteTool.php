<?php

namespace App\Services\AiTools;

use App\Models\Task;
use App\Models\User;

class TaskDeleteTool extends AbstractAiTool
{
    protected string $name = 'delete_task';
    protected string $description = 'Delete an existing task permanently. Use get_tasks FIRST to find the task_id. Only the task creator or a manager/admin can delete tasks.';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'task_id' => [
                    'type' => 'integer',
                    'description' => 'The ID of the task to delete. Use get_tasks to find it.'
                ]
            ],
            'required' => ['task_id']
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        $task = Task::find($input['task_id']);
        if (!$task) {
            return "Task ID {$input['task_id']} not found.";
        }

        // Security: Only creator, employer, or superadmin can delete
        $canDelete = $task->created_by == $user->id
            || ($user->tipo_usuario === 'empleador' && $task->createdBy && $task->createdBy->empleador_id == $user->id)
            || $user->is_superadmin
            || $user->is_manager;

        if (!$canDelete) {
            return "Unauthorized: You do not have permission to delete task #{$input['task_id']}.";
        }

        $title = $task->title;
        
        // Delete related data first
        $task->comments()->delete();
        $task->assignees()->detach();
        $task->readBy()->detach();
        
        if (method_exists($task, 'attachments')) {
            $task->attachments()->delete();
        }
        
        $task->delete();

        return "Task #{$input['task_id']} '{$title}' has been permanently deleted.";
    }
}
