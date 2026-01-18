<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\WorkHours;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskManagementService
{
    /**
     * Create a new task
     */
    public function createTask(array $data)
    {
        // Handle 'employee_id' (single) or 'assignees' (multiple)
        // For backward compatibility, if 'visible_para' or 'employee_id' is present, convert to array
        $assignees = $data['assignees'] ?? [];
        
        if (isset($data['employee_id'])) {
            $assignees[] = $data['employee_id'];
        }
        if (isset($data['visible_para'])) {
            $assignees[] = $data['visible_para'];
        }
        
        // Default to just the employer if no one else? Or just empty?
        // Logic before was: default to employer_id. 
        if (empty($assignees) && Auth::user()->empleador_id) {
             // Logic unclear on default assignment, but let's keep it safe.
             // If creating for self, maybe self?
        }
        
        $assignees = array_unique($assignees);

        $task = Task::create([
            'created_by' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'priority' => $data['priority'],
            'completed' => ($data['completed'] ?? false) ? \Illuminate\Support\Facades\DB::raw('true') : \Illuminate\Support\Facades\DB::raw('false'),
        ]);

        if (!empty($assignees)) {
            $task->assignees()->attach($assignees);
            
            // Send notifications
            foreach ($assignees as $userId) {
                if ($userId !== Auth::id()) {
                     $assignedUser = User::find($userId);
                     if ($assignedUser && $assignedUser->email) {
                        try {
                            $brevoService = app(\App\Services\BrevoEmailService::class);
                            $brevoService->sendNewTaskNotification(
                                $assignedUser->email,
                                $assignedUser->name,
                                [
                                    'id' => $task->id,
                                    'title' => $task->title,
                                    'description' => $task->description,
                                    'priority' => $task->priority,
                                    'start_date' => $task->start_date,
                                    'end_date' => $task->end_date,
                                    'assigned_by' => Auth::user()->name,
                                ]
                            );
                        } catch (\Exception $e) {
                            Log::error('Failed to send task notification email', [
                                'task_id' => $task->id,
                                'user_id' => $userId,
                                'error' => $e->getMessage()
                            ]);
                        }
                     }
                }
            }
        }

        return $task;
    }

    /**
     * Update an existing task
     */
    public function updateTask(Task $task, array $data)
    {
        // Handle assignees update if provided
        $assignees = $data['assignees'] ?? null;
        
        // Backward compatibility for single employee_id
        if ($assignees === null && isset($data['employee_id'])) {
            $assignees = [$data['employee_id']];
        }

        if ($assignees !== null) {
            $task->assignees()->sync($assignees);
        }

        $task->update($data);
        return $task;
    }

    /**
     * Delete a task and associated work hours
     */
    public function deleteTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Remove pivot table entries
        $task->assignees()->detach();
        $task->readBy()->detach();
        
        // Delete related hasMany records to prevent foreign key constraint errors
        $task->comments()->delete();
        $task->attachments()->delete();
        
        return $task->delete();
    }

    /**
     * Toggle task completion status
     */
    public function toggleCompletion($taskId)
    {
        Log::info('Toggling completion for task ID: ' . $taskId);

        try {
            $task = Task::findOrFail($taskId);
            
            Log::info('Current task data: ' . json_encode($task->toArray()));

            $newStatus = !$task->completed;
            $result = $task->update([
                'completed' => $newStatus ? \Illuminate\Support\Facades\DB::raw('true') : \Illuminate\Support\Facades\DB::raw('false')
            ]);

            Log::info('Update result: ' . ($result ? 'true' : 'false'));
            Log::info('New task data: ' . json_encode($task->fresh()->toArray()));

            if (!$result) {
                throw new \Exception('Failed to update task');
            }

            return [
                'success' => true,
                'completed' => $task->completed,
                'message' => $task->completed ? 'Tarea marcada como completada' : 'Tarea marcada como en progreso'
            ];
        } catch (\Exception $e) {
            Log::error('Error toggling task completion: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            throw $e;
        }
    }

    /**
     * Get tasks created by employees
     */
    public function getEmployeeTasks($empleados, $filters = [])
    {
        $employeeIds = $empleados->pluck('id');
        $query = Task::where(function($q) use ($employeeIds) {
            $q->whereIn('created_by', $employeeIds)
              ->orWhereHas('assignees', function($sub) use ($employeeIds) {
                  $sub->whereIn('users.id', $employeeIds);
              });
        })->with(['comments.user', 'createdBy', 'attachments.uploader', 'assignees']);

        return $this->applyFilters($query, $filters)->get();
    }

    public function getEmployerTasks($user, $empleados, $filters = [])
    {
        $query = Task::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('created_by', $user->empleador_id)
                  ->orWhereIn('created_by', User::whereRaw('is_superadmin IS TRUE')->pluck('id'));
        })->whereHas('assignees', function($q) use ($empleados) {
                       $q->whereIn('user_id', $empleados->pluck('id'));
                   })
                     ->with(['comments.user', 'assignees', 'attachments.uploader', 'createdBy']);

        return $this->applyFilters($query, $filters)->get();
    }

    /**
     * Get all tasks related to a company (created by or assigned to any member)
     */
    public function getCompanyTasks($user, $filters = [])
    {
        $ownerId = $user->tipo_usuario === 'empleador' ? $user->id : $user->empleador_id;
        
        if (!$ownerId) {
            return collect([]);
        }

        $companyUserIds = User::where('empleador_id', $ownerId)
            ->pluck('id')
            ->push($ownerId);

        $query = Task::where(function($q) use ($companyUserIds) {
            $q->whereIn('created_by', $companyUserIds)
              ->orWhereHas('assignees', function($sub) use ($companyUserIds) {
                  $sub->whereIn('users.id', $companyUserIds);
              });
        })->with(['assignees', 'comments.user', 'attachments.uploader', 'createdBy']);

        return $this->applyFilters($query, $filters)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Apply common filters to task query
     */
    private function applyFilters($query, $filters)
    {
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->whereRaw($filters['status'] === 'completed' ? 'completed IS TRUE' : 'completed IS FALSE');
        }

        if (isset($filters['search']) && $filters['search']) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhereHas('assignees', function($sub) use ($searchTerm) {
                      $sub->where('name', 'like', $searchTerm)
                          ->orWhere('email', 'like', $searchTerm);
                  });
            });
        }

        return $query;
    }
}
