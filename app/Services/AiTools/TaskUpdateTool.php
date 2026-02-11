<?php

namespace App\Services\AiTools;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskManagementService;
use Illuminate\Support\Facades\App;

class TaskUpdateTool extends AbstractAiTool
{
    protected string $name = 'update_task';
    protected string $description = 'Update an existing task. Use get_tasks FIRST to find the task_id. You can change status, priority, completion, and assignees.';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'task_id' => [
                    'type' => 'integer',
                    'description' => 'The ID of the task to update. Use get_tasks to find the ID first.'
                ],
                'status' => [
                    'type' => 'string',
                    'enum' => ['por_hacer', 'en_proceso', 'finalizado'],
                    'description' => 'New status: por_hacer (to do), en_proceso (in progress), finalizado (done)'
                ],
                'completed' => [
                    'type' => 'boolean',
                    'description' => 'Set to true to mark as finished, false to reopen'
                ],
                'priority' => [
                    'type' => 'string',
                    'enum' => ['low', 'medium', 'high']
                ],
                'assignee_ids' => [
                    'type' => 'array',
                    'items' => ['type' => 'integer'],
                    'description' => 'Update the list of assignees'
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

        // Security Check
        $canUpdate = $task->created_by == $user->id 
            || $task->assignees->contains($user->id)
            || ($user->tipo_usuario === 'empleador' && $task->createdBy->empleador_id == $user->id)
            || $user->is_superadmin;

        if (!$canUpdate) {
            return "Unauthorized: You do not have permission to update task #{$input['task_id']}.";
        }

        $changes = [];

        // Handle status change
        if (isset($input['status'])) {
            $task->status = $input['status'];
            $changes[] = "status → {$input['status']}";
            
            // Auto-sync completed flag with status
            if ($input['status'] === 'finalizado') {
                $task->completed = true;
                $changes[] = "completed → true";
            } elseif (in_array($input['status'], ['por_hacer', 'en_proceso'])) {
                $task->completed = false;
            }
        }

        // Handle explicit completed flag
        if (isset($input['completed'])) {
            $task->completed = $input['completed'];
            $changes[] = "completed → " . ($input['completed'] ? 'true' : 'false');
            
            // Auto-sync status with completed
            if ($input['completed'] && $task->status !== 'finalizado') {
                $task->status = 'finalizado';
                $changes[] = "status → finalizado";
            }
        }

        if (isset($input['priority'])) {
            $task->priority = $input['priority'];
            $changes[] = "priority → {$input['priority']}";
        }

        if (isset($input['assignee_ids'])) {
            $task->assignees()->sync($input['assignee_ids']);
            $changes[] = "assignees updated";
        }

        if (empty($changes)) {
            return "No changes requested for task #{$input['task_id']}.";
        }

        $task->save();

        return "Task #{$input['task_id']} '{$task->title}' updated: " . implode(', ', $changes) . ".";
    }
}
