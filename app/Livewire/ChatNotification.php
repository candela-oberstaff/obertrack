<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatNotification extends Component
{
    public $unreadCount = 0;
    public $previousUnreadCount = 0;

    public function mount()
    {
        $this->updateUnreadCount();
        $this->previousUnreadCount = $this->unreadCount;
    }

    public function poll()
    {
        $this->previousUnreadCount = $this->unreadCount;
        $this->updateUnreadCount();

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

    public function render()
    {
        return view('livewire.chat-notification');
    }
}
