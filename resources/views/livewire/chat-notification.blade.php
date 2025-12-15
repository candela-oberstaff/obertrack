<div wire:poll.5s="poll" class="relative inline-flex items-center">
    <a 
        href="{{ route('chat') }}" 
        class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out relative {{ request()->routeIs('chat') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}"
    >
        Chat
        
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm animate-pulse">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </a>

    <!-- Audio Element -->
    <audio id="notification-sound" preload="auto">
        <source src="{{ asset('sounds/chatify/new-message-sound.wav') }}" type="audio/wav">
    </audio>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('play-new-message-sound', () => {
                const audio = document.getElementById('notification-sound');
                if (audio) {
                    audio.play().catch(error => {
                        console.log('Audio autoplay prevented:', error);
                    });
                }
            });
        });
    </script>
</div>
