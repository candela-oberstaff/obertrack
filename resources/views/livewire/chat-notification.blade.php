<div wire:poll.5s="poll" class="hidden">
    <audio id="notification-sound" preload="auto">
        <source src="{{ asset('sounds/Sfx_Common_001 Notice1.ogg') }}" type="audio/ogg">
    </audio>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const audio = document.getElementById('notification-sound');
            
            // Audio Unlocker: Browsers block sound until a user interaction occurs.
            // On the first click anywhere, we play/pause quickly to "unlock" the audio context.
            const unlockAudio = () => {
                if (audio) {
                    audio.muted = true; // Mute for the unlock play
                    audio.play().then(() => {
                        audio.pause();
                        audio.currentTime = 0;
                        audio.muted = false; // Restore sound for real notifications
                        document.removeEventListener('click', unlockAudio);
                        console.log('Audio notification channel unlocked silently.');
                    }).catch(e => console.log('Unlock failed:', e));
                }
            };
            document.addEventListener('click', unlockAudio);

            Livewire.on('play-new-message-sound', () => {
                if (audio) {
                    audio.pause();
                    audio.currentTime = 0;
                    audio.play().catch(error => {
                        console.log('Audio playback failed (interaction required?):', error);
                    });
                }
            });
        });
    </script>
</div>
