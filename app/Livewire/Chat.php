<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// Cloudinary removed

#[Layout('layouts.app')]
class Chat extends Component
{
    use WithFileUploads;

    public $selectedUserId;
    public $messageText = '';
    public $attachment;
    public $contacts;
    public $previousUnreadCounts = [];

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
        // Initialize previous counts
        foreach ($this->contacts as $contact) {
            $this->previousUnreadCounts[$contact->id] = $contact->unread_messages_count;
        }
    }

    public function loadContacts()
    {
        $user = Auth::user();
        
        $contactsQuery = User::query();

        if ($user->tipo_usuario === 'empleador') {
            $contactsQuery->where('empleador_id', $user->id);
        } else {
            $contactsQuery->where(function($query) use ($user) {
                $query->where('id', $user->empleador_id)
                      ->orWhere(function($q) use ($user) {
                          $q->where('empleador_id', $user->empleador_id)
                            ->where('id', '!=', $user->id);
                      });
            });
        }

        // Optimize: Select only needed columns
        $this->contacts = $contactsQuery
            ->select('id', 'name', 'job_title', 'avatar', 'active_status') // specific columns
            ->withCount(['sentMessages as unread_messages_count' => function ($query) {
                $query->where('to_user_id', Auth::id())
                      ->whereNull('read_at');
            }])
            ->get();
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
        // 1. Authorization: Verify the recipient is in the allowed contacts list
        // Reload contacts to ensure valid state
        $this->loadContacts();
        
        if (!$this->contacts->contains('id', $this->selectedUserId)) {
            $this->addError('messageText', 'Error de seguridad: No tienes permiso para enviar mensajes a este usuario.');
            return;
        }

        // Validate that at least one is present
        if (empty($this->messageText) && !$this->attachment) {
            $this->addError('messageText', 'Debes escribir un mensaje o adjuntar un archivo.');
            return;
        }

        $this->validate();

        // 2. Sanitization & Spam Protection
        $rawMessage = $this->messageText;
        
        // Simple Spam Filter (Blocking the specific attack pattern)
        $spamKeywords = ['primefisolutions', 'credit available', 'urgent transfer'];
        foreach ($spamKeywords as $keyword) {
            if (stripos($rawMessage, $keyword) !== false) {
                 $this->addError('messageText', 'Tu mensaje ha sido bloqueado por contener términos sospechosos.');
                 return;
            }
        }

        // Strip tags to prevent XSS (although Blade escapes output, this keeps DB clean)
        $messageText = strip_tags($rawMessage);

        $attachmentFile = $this->attachment;
        // ... (rest is same)

        // Clear input immediately
        $this->messageText = '';
        $this->attachment = null;
        
        // ... (upload logic)

        $attachmentPath = null;
        
        if ($attachmentFile) {
            // Upload to Local Storage (public disk)
            $filename = $attachmentFile->store('chat_attachments', 'public');
            $attachmentPath = Storage::url($filename); // Generates /storage/chat_attachments/...
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

    public function refreshMessages()
    {
        // This method is called by wire:poll to refresh the component.
        // render() will be called automatically.
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

        // Detect new messages and dispatch event
        foreach ($this->contacts as $contact) {
            $previousCount = $this->previousUnreadCounts[$contact->id] ?? 0;
            $currentCount = $contact->unread_messages_count;
            
            // If there are new unread messages and we're not viewing this contact
            if ($currentCount > $previousCount && $this->selectedUserId != $contact->id) {
                // Get initials
                $nameParts = explode(' ', $contact->name);
                $initials = '';
                foreach ($nameParts as $part) {
                    if (!empty($part)) {
                        $initials .= strtoupper(substr($part, 0, 1));
                        if (strlen($initials) >= 2) break;
                    }
                }
                
                // Dispatch browser event
                $this->dispatch('new-message-received', [
                    'name' => $contact->name,
                    'initials' => $initials,
                    'userId' => $contact->id
                ]);
            }
            
            // Update previous count
            $this->previousUnreadCounts[$contact->id] = $currentCount;
        }

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
