<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Support\Facades\Auth;

class TaskFilesModal extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $taskId;
    public $search = '';
    public $newFile;
    public $dateFrom;
    public $dateTo;
    
    #[On('open-task-files')]
    public function open($taskId)
    {
        $this->taskId = $taskId;
        $this->isOpen = true;
        $this->reset(['search', 'newFile']);
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function updatedNewFile()
    {
        $this->validate([
            'newFile' => 'file|max:5120', // 5MB max
        ]);

        $task = Task::findOrFail($this->taskId);
        
        $path = $this->newFile->store('task_attachments', 'local');
        
        $attachment = new TaskAttachment([
            'task_id' => $this->taskId,
            'uploaded_by' => auth()->id(),
            'filename' => $this->newFile->getClientOriginalName(),
            'stored_filename' => $path,
            'mime_type' => $this->newFile->getMimeType(),
            'file_size' => $this->newFile->getSize(),
        ]);
        
        $attachment->save();

        $this->newFile = null;
        session()->flash('success', 'Archivo subido con éxito.');
    }

    public function render()
    {
        $files = collect();
        
        if ($this->taskId) {
            $query = TaskAttachment::where('task_id', $this->taskId)
                ->orderBy('created_at', 'desc');

            if (!empty($this->search)) {
                $query->where('filename', 'like', '%' . $this->search . '%');
            }

            if (!empty($this->dateFrom)) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            }

            if (!empty($this->dateTo)) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            }
            
            $files = $query->get();
        }

        return view('livewire.task-files-modal', [
            'files' => $files
        ]);
    }
}
