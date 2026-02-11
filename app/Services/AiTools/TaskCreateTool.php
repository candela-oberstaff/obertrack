<?php

namespace App\Services\AiTools;

use App\Models\User;
use App\Models\Task;
use App\Services\TaskManagementService;
use Illuminate\Support\Facades\App;

class TaskCreateTool extends AbstractAiTool
{
    protected string $name = 'create_task';
    protected string $description = 'Create a NEW task. If your intent is to EDIT, UPDATE, or MOVE an existing task, use update_task instead. If similar tasks already exist, this tool will return them first — you must then ask the user if they want a new task or to edit an existing one.';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'description' => 'Title of the task'
                ],
                'description' => [
                    'type' => 'string',
                    'description' => 'Detailed description (optional)'
                ],
                'assignee_ids' => [
                    'type' => 'array',
                    'items' => ['type' => 'integer'],
                    'description' => 'List of User IDs to assign the task to. Use search_users to find IDs.'
                ],
                'dates' => [
                    'type' => 'object',
                    'properties' => [
                        'start_date' => ['type' => 'string', 'format' => 'date'],
                        'end_date' => ['type' => 'string', 'format' => 'date']
                    ]
                ],
                'priority' => [
                    'type' => 'string',
                    'enum' => ['low', 'medium', 'high']
                ],
                'confirm_create' => [
                    'type' => 'boolean',
                    'description' => 'Set to true ONLY after the user has confirmed they want a NEW task (not editing an existing one). Required when similar tasks exist.'
                ]
            ],
            'required' => ['title']
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        $service = App::make(TaskManagementService::class);
        $confirmCreate = $input['confirm_create'] ?? false;
        
        // Safety check: look for tasks with similar titles before creating
        if (!$confirmCreate) {
            $similarTasks = Task::where('title', 'like', '%' . $input['title'] . '%')
                ->where(function($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhereHas('assignees', function($sub) use ($user) {
                          $sub->where('users.id', $user->id);
                      });
                })
                ->with(['assignees:id,name'])
                ->limit(5)
                ->get();
            
            if ($similarTasks->count() > 0) {
                $taskList = $similarTasks->map(function($t) {
                    $assignees = $t->assignees->pluck('name')->implode(', ') ?: 'Sin asignar';
                    $status = $t->status ?? 'por_hacer';
                    $date = $t->end_date ? $t->end_date->format('Y-m-d') : 'Sin fecha';
                    return "- ID: {$t->id} | \"{$t->title}\" | Estado: {$status} | Asignada a: {$assignees} | Fecha: {$date}";
                })->implode("\n");
                
                return "SIMILAR TASKS FOUND. Do NOT create a new task yet. Ask the user what they want to do.\n\n"
                     . "Existing tasks with similar names:\n{$taskList}\n\n"
                     . "OPTIONS FOR THE USER:\n"
                     . "1. If the user wants to EDIT one of these tasks, use update_task with the correct task_id.\n"
                     . "2. If the user wants to DELETE one, use delete_task.\n"  
                     . "3. If the user wants to ADD A COMMENT, use add_task_comment.\n"
                     . "4. ONLY if the user explicitly confirms they want a NEW task, call create_task again with confirm_create=true.";
            }
        }
        
        // No similar tasks found, or user confirmed — proceed with creation
        $data = [
            'title' => $input['title'],
            'description' => $input['description'] ?? '',
            'priority' => $input['priority'] ?? 'medium',
            'assignees' => $input['assignee_ids'] ?? [],
            'start_date' => $input['dates']['start_date'] ?? now()->toDateString(),
            'end_date' => $input['dates']['end_date'] ?? now()->addDay()->toDateString(),
            'completed' => false
        ];

        try {
            $task = $service->createTask($data);
            return "Task '{$task->title}' created successfully (ID: {$task->id}). Assigned to " . count($data['assignees']) . " users.";
        } catch (\Exception $e) {
            return "Failed to create task: " . $e->getMessage();
        }
    }
}
