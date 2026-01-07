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

        $this->status = $newStatus;
        
        // Update both status and completed boolean for backward compatibility
        // Using DB::raw for completed because PostgreSQL with ATTR_EMULATE_PREPARES=true
        // might send booleans as integers (1/0), which fails.
        $this->task->update([
            'status' => $newStatus,
            'completed' => DB::raw($newStatus === Task::STATUS_COMPLETED ? 'true' : 'false')
        ]);

        $this->isOpen = false;
        
        // Dispatch event if needed for parent refresh
        $this->dispatch('task-status-updated');
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
