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
    public $search = '';
    
    // Broadcast properties
    public $isBroadcastMode = false;
    public $broadcastTarget = 'all'; // 'all', 'professionals', 'companies'

    protected $listeners = ['refreshMessages' => '$refresh'];

    protected $rules = [
        'messageText' => 'nullable|string|max:5000',
        'attachment' => 'nullable|file|max:10240',
    ];

    protected $messages = [
        'messageText.required_without' => 'Debes escribir un mensaje o adjuntar un archivo.',
        'attachment.max' => 'El archivo no puede superar los 10MB.',
    ];

    public function mount($userId = null)
    {
        $this->loadContacts();
        
        if ($userId && $this->contacts->contains('id', $userId)) {
            $this->selectContact($userId);
        }

        // Initialize previous counts
        foreach ($this->contacts as $contact) {
            $this->previousUnreadCounts[$contact->id] = $contact->unread_messages_count;
        }
    }

    public function loadContacts()
    {
        // Only load full contacts if not already loaded or if searching
        if ($this->contacts && empty($this->search) && !$this->isBroadcastMode) {
            $this->refreshUnreadCounts();
            return;
        }

        $user = Auth::user();
        
        $contactsQuery = User::query()->where('id', '!=', $user->id);

        if ($user->is_superadmin) {
            // Superadmins see everyone
        } elseif ($user->tipo_usuario === 'empleador') {
            // Companies see their professionals
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

        if (!empty($this->search)) {
            $contactsQuery->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('company_name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('job_title', 'ilike', '%' . $this->search . '%');
            });
        }

        // Optimize: Select only needed columns and add last message timestamp
        $this->contacts = $contactsQuery
            ->select('id', 'name', 'job_title', 'avatar', 'company_name', 'tipo_usuario')
            ->addSelect(['last_message_at' => Message::select('created_at')
                ->where(function($q) {
                    $q->whereColumn('from_user_id', 'users.id')
                      ->where('to_user_id', Auth::id());
                })->orWhere(function($q) {
                    $q->whereColumn('to_user_id', 'users.id')
                      ->where('from_user_id', Auth::id());
                })
                ->latest()
                ->limit(1)
            ])
            ->withCount(['sentMessages as unread_messages_count' => function ($query) {
                $query->where('to_user_id', Auth::id())
                      ->whereNull('read_at');
            }])
            ->orderBy('unread_messages_count', 'desc')
            ->orderByRaw('last_message_at DESC NULLS LAST')
            ->orderBy('name')
            ->get();
    }

    public function refreshUnreadCounts()
    {
        // Heavy optimization: Only query counts, map to existing contacts
        // This avoids hydrating all User models again
        if (!$this->contacts) return;

        $user = Auth::user();
        
        // Get all unread counts for current user grouped by sender
        $unreadCounts = Message::where('to_user_id', $user->id)
            ->whereNull('read_at')
            ->selectRaw('from_user_id, count(*) as count')
            ->groupBy('from_user_id')
            ->pluck('count', 'from_user_id');

        // Update the collection in memory
        $this->contacts->transform(function ($contact) use ($unreadCounts) {
            $contact->unread_messages_count = $unreadCounts[$contact->id] ?? 0;
            return $contact;
        });
    }

    public function updatedSearch()
    {
        // Force reload when searching
        $this->contacts = null; 
        $this->loadContacts();
    }

    public function toggleBroadcastMode()
    {
        if (!Auth::user()->is_superadmin) return;
        
        $this->isBroadcastMode = !$this->isBroadcastMode;
        if ($this->isBroadcastMode) {
            $this->selectedUserId = null;
            $this->contacts = null; // Force reload for broadcast filtering if needed
        }
    }

    public function setBroadcastTarget($target)
    {
        $this->broadcastTarget = $target;
    }

    public function selectContact($userId)
    {
        $this->isBroadcastMode = false;
        $this->selectedUserId = $userId;
        $this->markMessagesAsRead();
        // Don't force reload contacts here, just update view
    }

    public function removeAttachment()
    {
        $this->attachment = null;
    }

    public function sendMessage()
    {
        $user = Auth::user();

        // 1. Authorization & Target Selection
        if ($this->isBroadcastMode && $user->is_superadmin) {
            $recipientsQuery = User::query()->where('id', '!=', $user->id);
            if ($this->broadcastTarget === 'professionals') {
                $recipientsQuery->where('tipo_usuario', 'empleado');
            } elseif ($this->broadcastTarget === 'companies') {
                $recipientsQuery->where('tipo_usuario', 'empleador');
            }
            $recipientIds = $recipientsQuery->pluck('id');
        } else {
            // Ensure contacts are loaded to validate existence
            if (!$this->contacts) $this->loadContacts();
            
            if (!$this->contacts->contains('id', $this->selectedUserId)) {
                $this->addError('messageText', 'Error de seguridad: No tienes permiso para enviar mensajes a este usuario.');
                return;
            }
            $recipientIds = [$this->selectedUserId];
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
        $attachmentPath = null;
        
        if ($attachmentFile) {
            // Upload to Local Storage (public disk)
            $filename = $attachmentFile->store('chat_attachments', 'public');
            $attachmentPath = $filename; // Store relative path (key) only
        }

        // Save to database (multiple if broadcast)
        foreach ($recipientIds as $recipientId) {
            Message::create([
                'from_user_id' => $user->id,
                'to_user_id' => $recipientId,
                'message' => $messageText,
                'attachment_path' => $attachmentPath,
            ]);
        }

        // Clear input immediately
        $this->messageText = '';
        $this->attachment = null;
        
        if ($this->isBroadcastMode) {
            $this->isBroadcastMode = false;
            session()->flash('message', 'Mensaje masivo enviado con éxito.');
        }
    }

    public function markMessagesAsRead()
    {
        if (!$this->selectedUserId) return;

        Message::where('to_user_id', Auth::id())
            ->where('from_user_id', $this->selectedUserId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        // Update memory immediately
        if ($this->contacts) {
            $this->contacts->transform(function ($contact) {
                if ($contact->id === $this->selectedUserId) {
                    $contact->unread_messages_count = 0;
                }
                return $contact;
            });
        }

        // Dispatch global event to update navbar immediately
        $unreadCount = Message::where('to_user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
        $this->dispatch('unread-count-changed', count: $unreadCount);
        $this->dispatch('sync-unread-count-reset', count: $unreadCount);
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
        // Refresh contacts/counts
        $this->loadContacts();

        // Detect new messages and dispatch event
        if ($this->contacts) {
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

                    // Update total count and sync ChatNotification
                    $totalUnread = Message::where('to_user_id', Auth::id())
                        ->whereNull('read_at')
                        ->count();
                    $this->dispatch('unread-count-changed', count: $totalUnread);
                    $this->dispatch('sync-unread-count', count: $totalUnread);
                }
                
                // Update previous count
                $this->previousUnreadCounts[$contact->id] = $currentCount;
            }
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
