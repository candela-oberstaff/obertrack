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
        // For backward compatibility, if 'visible_para' or 'professional_id' / 'employee_id' is present, convert to array
        $assignees = $data['assignees'] ?? [];
        
        if (isset($data['professional_id'])) {
            $assignees[] = $data['professional_id'];
        }
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
        
        // Backward compatibility for single professional_id / employee_id
        if ($assignees === null && isset($data['professional_id'])) {
            $assignees = [$data['professional_id']];
        }
        if ($assignees === null && isset($data['employee_id'])) {
            $assignees = [$data['employee_id']];
        }

        if ($assignees !== null) {
            // Ensure all IDs are integers
            $assignees = array_map('intval', array_filter($assignees));
            $task->assignees()->sync($assignees);
        }

        // Remove assignees from data before updating model (it's not a fillable field)
        unset($data['assignees']);
        unset($data['employee_id']);
        unset($data['professional_id']);

        // Detect status or completion changes for notification
        $oldStatus = $task->status;
        $oldCompleted = $task->completed;

        $task->update($data);

        // Notify if status or completion changed
        if (($task->status !== $oldStatus || $task->completed !== $oldCompleted) && Auth::user()->tipo_usuario === 'empleado') {
            $this->notifyClientOfStatusChange($task, $oldStatus);
        }

        return $task;
    }

    /**
     * Delete a task and associated work hours
     */
    public function deleteTask($taskId)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($taskId) {
            $task = Task::findOrFail($taskId);
            
            // Delete associated work hours if any
            // Check if created_by is valid first to avoid errors
            if ($task->created_by) {
                WorkHours::where([
                    'user_id' => $task->created_by,
                    'work_date' => $task->created_at->toDateString(),
                ])->delete();
            }

            // Manually delete relations just in case DB cascade is missing/broken
            $task->comments()->delete();
            $task->attachments()->delete();
            $task->assignees()->detach();

            return $task->delete();
        });
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

            $oldStatus = $task->status;
            $newValue = !$task->completed;
            $result = $task->update([
                'completed' => $newValue ? \Illuminate\Support\Facades\DB::raw('true') : \Illuminate\Support\Facades\DB::raw('false')
            ]);

            Log::info('Update result: ' . ($result ? 'true' : 'false'));
            Log::info('New task data: ' . json_encode($task->fresh()->toArray()));

            if (!$result) {
                throw new \Exception('Failed to update task');
            }

            // Notify if changed by an employee
            if (Auth::user()->tipo_usuario === 'empleado') {
                $this->notifyClientOfStatusChange($task, $oldStatus);
            }

            return [
                'success' => true,
                'completed' => $newValue,
                'message' => $newValue ? 'Tarea marcada como completada' : 'Tarea marcada como en progreso'
            ];
        } catch (\Exception $e) {
            Log::error('Error toggling task completion: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            throw $e;
        }
    }

    /**
     * Notify the client (employer) when an employee updates a task status
     */
    public function notifyClientOfStatusChange(Task $task, $oldStatus)
    {
        $user = Auth::user();
        
        // 1. Determine the recipient (Client/Company)
        // Usually the company of the person who updated it
        $recipient = $user->empresa;
        
        if (!$recipient || !$recipient->email) {
             // Fallback to task creator if they are an employer
             $creator = $task->createdBy;
             if ($creator && ($creator->tipo_usuario === 'empleador' || $creator->is_superadmin)) {
                 $recipient = $creator;
             }
        }

        if ($recipient && $recipient->email) {
            try {
                $statusLabels = [
                    Task::STATUS_TODO => 'Por hacer',
                    Task::STATUS_IN_PROGRESS => 'En proceso',
                    Task::STATUS_COMPLETED => 'Finalizado',
                ];

                $brevoService = app(\App\Services\BrevoEmailService::class);
                $brevoService->sendTaskStatusNotification(
                    $recipient->email,
                    $recipient->name,
                    [
                        'id' => $task->id,
                        'title' => $task->title,
                        'status_label' => $statusLabels[$task->status] ?? ($task->completed ? 'Finalizado' : 'En proceso'),
                        'previous_status_label' => $statusLabels[$oldStatus] ?? 'Anterior',
                        'updated_by' => $user->name,
                        'completed' => $task->completed
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Failed to send task status update notification', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Get tasks created by professionals
     */
    public function getProfessionalTasks($profesionales, $filters = [])
    {
        $professionalIds = $profesionales->pluck('id');
        $query = Task::where(function($q) use ($professionalIds) {
            $q->whereIn('created_by', $professionalIds)
              ->orWhereHas('assignees', function($sub) use ($professionalIds) {
                  $sub->whereIn('users.id', $professionalIds);
              });
        })->with(['comments.user', 'createdBy', 'attachments.uploader', 'assignees']);
 
        return $this->applyFilters($query, $filters)->get();
    }

    public function getCompanyTaskSummaries($user, $profesionales, $filters = [])
    {
        $query = Task::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('created_by', $user->empleador_id)
                  ->orWhereIn('created_by', User::whereRaw('is_superadmin IS TRUE')->pluck('id'));
        })->whereHas('assignees', function($q) use ($profesionales) {
                       $q->whereIn('user_id', $profesionales->pluck('id'));
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
        
        Log::info('getCompanyTasks Debug:', [
            'user_id' => $user->id,
            'role' => $user->tipo_usuario,
            'is_manager' => $user->is_manager,
            'owner_id' => $ownerId,
        ]);

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
