<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TaskStatusSelector extends Component
{
    public Task $task;
    public $status;
    public $isOpen = false;

    public function mount(Task $task)
    {
        $this->task = $task;
        $this->status = $task->status;
    }

    public function updateStatus($newStatus)
    {
        if (!in_array($newStatus, [Task::STATUS_TODO, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETED])) {
            return;
        }

        // Restrict employers from changing status
        $user = auth()->user();
        if ($user && $user->tipo_usuario === 'empleador') {
            $this->dispatch('notify', message: 'Solo los profesionales pueden actualizar el estado de las tareas.');
            return;
        }

        $wasCompleted = $this->task->completed;
        $oldStatusSlug = $this->task->status;
        $this->status = $newStatus;
        
        $isNowCompleted = $newStatus === Task::STATUS_COMPLETED;
        
        // Nuclear option: Direct DB update with explicit RAW boolean literals.
        // This bypasses Laravel's PDO integer binding which fails on Postgres strict booleans.
        try {
            \Illuminate\Support\Facades\DB::table('tasks')
                ->where('id', $this->task->id)
                ->update([
                    'status' => $newStatus,
                    'completed' => \Illuminate\Support\Facades\DB::raw($isNowCompleted ? 'true' : 'false'),
                    'updated_at' => \Carbon\Carbon::now()
                ]);
        } catch (\Exception $e) {
            \Log::error("Task update failed: " . $e->getMessage());
            $this->dispatch('notify', message: 'Error de base de datos: ' . $e->getMessage());
            return;
        }
        
        // Refresh model to reflect the DB change we just made
        $this->task->refresh();
        
        // Force local state sync just in case
        $this->status = $this->task->status;

        $this->isOpen = false;
        
        // Dispatch event with all necessary data for frontend sync
        $this->dispatch('task-status-updated', 
            taskId: $this->task->id, 
            status: $newStatus,
            completed: $isNowCompleted,
            wasCompleted: $wasCompleted
        );

        // Notify client (Employer) if changed by an employee
        $user = auth()->user();
        if ($user && $user->tipo_usuario === 'empleado') {
            try {
                app(\App\Services\TaskManagementService::class)->notifyClientOfStatusChange($this->task, $oldStatusSlug); 
            } catch (\Exception $e) {
                \Log::error("Failed to notify client from TaskStatusSelector: " . $e->getMessage());
            }
        }
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.task-status-selector');
    }
}
