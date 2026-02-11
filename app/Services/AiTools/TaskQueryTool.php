<?php

namespace App\Services\AiTools;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TaskQueryTool extends AbstractAiTool
{
    protected string $name = 'get_tasks';
    protected string $description = 'Search and list tasks. ALWAYS use this tool FIRST to find a task ID before updating or commenting on a task. Never assume a task ID.';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'enum' => ['por_hacer', 'en_proceso', 'finalizado', 'all'],
                    'description' => 'Filter by status: por_hacer (to do), en_proceso (in progress), finalizado (done), all (no filter). Default: all',
                ],
                'priority' => [
                    'type' => 'string',
                    'enum' => ['low', 'medium', 'high'],
                    'description' => 'Filter by priority level'
                ],
                'search' => [
                    'type' => 'string',
                    'description' => 'Keywords to search in task title or description'
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Max number of tasks to return (default 10)'
                ]
            ],
            'required' => []
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        $limit = $input['limit'] ?? 10;
        $status = $input['status'] ?? 'all';

        $query = Task::query();

        // 1. Security Scope (Read Permissions)
        $companyId = $user->tipo_usuario === 'empleador' ? $user->id : $user->empleador_id;
        
        if ($user->tipo_usuario === 'empleador' || $user->is_manager) {
             $query->where(function($q) use ($companyId) {
                 $q->whereIn('created_by', User::where('empleador_id', $companyId)->pluck('id'))
                   ->orWhere('created_by', $companyId);
             });
        } else {
             $query->where(function($q) use ($user) {
                 $q->where('created_by', $user->id)
                   ->orWhereHas('assignees', function($sub) use ($user) {
                       $sub->where('users.id', $user->id);
                   });
             });
        }

        // 2. Filters
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if (isset($input['priority'])) {
            $query->where('priority', $input['priority']);
        }

        if (isset($input['search'])) {
            $searchTerm = '%' . $input['search'] . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }

        $tasks = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->with(['assignees:id,name', 'createdBy:id,name'])
            ->get();

        if ($tasks->isEmpty()) {
            return "No tasks found matching your criteria.";
        }

        return $tasks->map(function($t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'status' => $t->status ?? ($t->completed ? 'finalizado' : 'por_hacer'),
                'priority' => $t->priority,
                'created_by' => $t->createdBy ? $t->createdBy->name : 'Unknown',
                'due_date' => $t->end_date ? $t->end_date->format('Y-m-d') : null,
                'assigned_to' => $t->assignees->pluck('name')->implode(', '),
                'comments_count' => $t->comments()->count()
            ];
        });
    }
}
