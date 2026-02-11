<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskNotification extends Component
{
    public $unreadCount = 0;
    
    // Polling interval can be adjusted or removed if we rely on events
    // For now, we'll poll every 10s or similar, or just relying on checking periodically
    
    protected $listeners = [
        'task-created' => 'updateUnreadCount', 
        'tasks-read' => 'updateUnreadCount',
        'refresh-navigation-menu' => '$refresh'
    ];

    public function mount()
    {
        $this->updateUnreadCount();
    }

    public function updateUnreadCount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Count tasks assigned to user that are NOT read by user AND are ToDo/Not Completed
            $this->unreadCount = Task::whereHas('assignees', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->whereDoesntHave('readBy', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->where('status', Task::STATUS_TODO)
            ->whereRaw('completed IS FALSE')
            ->count();
            
            $this->dispatch('task-unread-count-changed', count: $this->unreadCount);
        }
    }
    
    // We can add polling if we want real-time updates without page refresh
    public function poll()
    {
        $this->updateUnreadCount();
    }

    public function render()
    {
        return view('livewire.task-notification');
    }
}
