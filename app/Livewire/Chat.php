<?php


namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class Chat extends Component
{
    use WithFileUploads;


    public $selectedUserId;
    public $messageText = '';
    public $attachment;
    public $contacts;

    protected $listeners = ['refreshMessages' => '$refresh'];

    protected $rules = [
        'messageText' => 'nullable|string|max:5000',
        'attachment' => 'nullable|file|max:10240',
    ];

    protected $messages = [
        'messageText.required_without' => 'Debes escribir un mensaje o adjuntar un archivo.',
        'attachment.max' => 'El archivo no puede superar los 10MB.',
    ];

    public function mount()
    {
        $this->loadContacts();
    }

    public function loadContacts()
    {
        $user = Auth::user();
        
        if ($user->tipo_usuario === 'empleador') {
            // Employers see their professionals
            $this->contacts = User::where('empleador_id', $user->id)
                ->withCount(['sentMessages as unread_messages_count' => function ($query) {
                    $query->where('to_user_id', Auth::id())
                          ->whereNull('read_at');
                }])
                ->get();
        } else {
            // Professionals see their employer AND colleagues
            $this->contacts = User::where(function($query) use ($user) {
                    $query->where('id', $user->empleador_id) // Employer
                        ->orWhere(function($q) use ($user) { // Colleagues (same employer)
                            $q->where('empleador_id', $user->empleador_id)
                              ->where('id', '!=', $user->id); // Exclude self
                        });
                })
                ->withCount(['sentMessages as unread_messages_count' => function ($query) {
                    $query->where('to_user_id', Auth::id())
                          ->whereNull('read_at');
                }])
                ->get();
        }
    }

    public function selectContact($userId)
    {
        $this->selectedUserId = $userId;
        $this->markMessagesAsRead();
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function sendMessage()
    {
        // Validate that at least one is present
        if (empty($this->messageText) && !$this->attachment) {
            $this->addError('messageText', 'Debes escribir un mensaje o adjuntar un archivo.');
            return;
        }

        $this->validate();

        $messageText = $this->messageText;
        $attachmentFile = $this->attachment;

        // Clear input immediately in backend state
        $this->messageText = '';
        $this->attachment = null;

        $attachmentPath = null;
        
        if ($attachmentFile) {
            $attachmentPath = $attachmentFile->store('chat-attachments', 'public');
        }

        // Save to database
        Message::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $this->selectedUserId,
            'message' => $messageText,
            'attachment_path' => $attachmentPath,
        ]);
    }

    public function markMessagesAsRead()
    {
        if (!$this->selectedUserId) return;

        Message::where('to_user_id', Auth::id())
            ->where('from_user_id', $this->selectedUserId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function getJitsiRoomUrl()
    {
        if (!$this->selectedUserId) {
            return null;
        }

        $ids = [Auth::id(), $this->selectedUserId];
        sort($ids);
        return 'https://meet.jit.si/Obertrack-' . implode('-', $ids);
    }

    public function render()
    {
        // Refresh contacts to update unread counts and status
        $this->loadContacts();

        $messages = [];
        
        if ($this->selectedUserId) {
            // Mark messages as read when rendering (covers polling updates)
            $this->markMessagesAsRead();
            
            $messages = Message::between(Auth::id(), $this->selectedUserId)->get();
        }

        return view('livewire.chat', [
            'messages' => $messages,
            'jitsiUrl' => $this->getJitsiRoomUrl(),
        ]);
    }
}
