<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatNotification extends Component
{
    public $unreadCount = 0;
    public $previousUnreadCount = 0;

    protected $listeners = [
        'sync-unread-count' => 'updateCountManually',
        'sync-unread-count-reset' => 'resetCountManually'
    ];

    public function mount()
    {
        $this->updateUnreadCount();
        $this->previousUnreadCount = $this->unreadCount;
        
        // Dispatch initial count
        $this->dispatch('unread-count-changed', count: $this->unreadCount);
    }

    public function poll()
    {
        $this->previousUnreadCount = $this->unreadCount;
        $this->updateUnreadCount();

        // If count changed (up or down), notify the UI
        if ($this->unreadCount !== $this->previousUnreadCount) {
            $this->dispatch('unread-count-changed', count: $this->unreadCount);
        }

        // If count increased, play sound
        if ($this->unreadCount > $this->previousUnreadCount) {
            $this->dispatch('play-new-message-sound');
        }
    }

    public function updateUnreadCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Message::where('to_user_id', Auth::id())
                ->whereNull('read_at')
                ->count();
        }
    }

    public function updateCountManually($count)
    {
        $this->previousUnreadCount = $this->unreadCount;
        $this->unreadCount = $count;
        
        // This won't trigger play-new-message-sound if we match the existing count
        // but we still want it to be accurate.
    }

    public function resetCountManually($count)
    {
        // Force the counts to be the same so no sound is triggered on subsequent poll
        $this->unreadCount = $count;
        $this->previousUnreadCount = $count;
    }

    public function render()
    {
        return view('livewire.chat-notification');
    }
}
