<div wire:poll.5s="poll" class="relative inline-flex items-center">
    <a 
        href="{{ route('chat') }}" 
        class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out relative {{ request()->routeIs('chat') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}"
    >
        Chat
        
        {{-- Notification badge removed per user request --}}
        {{-- Notification badge removed per user request --}}
    </a>

    <audio id="notification-sound" preload="auto">
        <source src="{{ asset('sounds/Sfx_Common_001 Notice1.ogg') }}" type="audio/ogg">
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
