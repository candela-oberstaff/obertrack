<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class TaskCommentsModal extends Component
{
    public $isOpen = false;
    public $taskId;
    public $search = '';
    public $newCommentContent = '';
    public $dateFrom;
    public $dateTo;
    
    // Properties for creating a new comment
    public $showCreateForm = false;

    #[On('open-task-comments')]
    public function open($taskId)
    {
        $this->taskId = $taskId;
        $this->isOpen = true;
        $this->reset(['search', 'newCommentContent', 'showCreateForm']);
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function addComment()
    {
        $this->validate([
            'newCommentContent' => 'required|string|max:65535',
        ]);

        $task = Task::findOrFail($this->taskId);
        
        // Ensure user can comment on this task
        // We might need to check pivot or if created_by matches.
        // Assuming if they can view the task they can comment.
        
        $comment = new Comment([
            'content' => $this->newCommentContent,
            'user_id' => Auth::id(),
            'task_id' => $this->taskId
        ]);
        
        $comment->save();

        $this->newCommentContent = '';
        $this->showCreateForm = false; // Hide form after submit? Or keep open? The mockup shows "Añadir comentario" button at bottom.
        
        // Refresh needed? Livewire re-renders automatically
    }

    public function render()
    {
        $comments = collect();
        
        if ($this->taskId) {
            $query = Comment::where('task_id', $this->taskId)
                ->with('user')
                ->orderBy('created_at', 'desc');

            if (!empty($this->search)) {
                $query->where('content', 'like', '%' . $this->search . '%');
            }

            if (!empty($this->dateFrom)) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            }

            if (!empty($this->dateTo)) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            }
            
            $comments = $query->get();
        }

        return view('livewire.task-comments-modal', [
            'comments' => $comments
        ]);
    }
}
