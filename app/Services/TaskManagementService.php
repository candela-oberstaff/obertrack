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
        // Handle both 'employee_id' (from form) and 'visible_para' (legacy)
        $visiblePara = $data['employee_id'] ?? $data['visible_para'] ?? Auth::user()->empleador_id;
        
        $task = Task::create([
            'created_by' => Auth::id(),
            'visible_para' => $visiblePara,
            'title' => $data['title'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'priority' => $data['priority'],
            'completed' => $data['completed'] ?? false,
        ]);

        // Send notification to assigned employee
        if ($visiblePara && $visiblePara !== Auth::id()) {
            $assignedUser = User::find($visiblePara);
            if ($assignedUser) {
                // For now, we'll log the notification
                // In the future, you can implement email/database notifications
                Log::info('Task assigned', [
                    'task_id' => $task->id,
                    'assigned_to' => $assignedUser->name,
                    'assigned_by' => Auth::user()->name
                ]);
            }
        }

        return $task;
    }

    /**
     * Update an existing task
     */
    public function updateTask(Task $task, array $data)
    {
        $task->update($data);
        return $task;
    }

    /**
     * Delete a task and associated work hours
     */
    public function deleteTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Delete associated work hours if any
        WorkHours::where([
            'user_id' => $task->created_by,
            'work_date' => $task->created_at->toDateString(),
        ])->delete();

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

            $task->completed = !$task->completed;
            $result = $task->save();

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
        $query = Task::whereIn('created_by', $empleados->pluck('id'))
            ->with('comments', 'createdBy');

        return $this->applyFilters($query, $filters)->get();
    }

    /**
     * Get tasks created for employees (by employer/manager)
     */
    public function getEmployerTasks($user, $empleados, $filters = [])
    {
        $query = Task::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('created_by', $user->empleador_id)
                  ->orWhereIn('created_by', User::where('is_superadmin', true)->pluck('id'));
        })->whereIn('visible_para', $empleados->pluck('id'))
          ->with('comments', 'visibleTo');

        return $this->applyFilters($query, $filters)->get();
    }

    /**
     * Apply common filters to task query
     */
    private function applyFilters($query, $filters)
    {
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('completed', $filters['status'] === 'completed' ? 1 : 0);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query;
    }
}
