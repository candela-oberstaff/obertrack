<div wire:poll.5s="poll" class="hidden">
    <audio id="notification-sound" preload="auto">
        <source src="{{ asset('sounds/Sfx_Common_001 Notice1.ogg') }}" type="audio/ogg">
    </audio>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const audio = document.getElementById('notification-sound');
            
            // Audio Unlocker
            const unlockAudio = () => {
                if (audio) {
                    audio.muted = true;
                    audio.play().then(() => {
                        audio.pause();
                        audio.currentTime = 0;
                        audio.muted = false;
                        document.removeEventListener('click', unlockAudio);
                        console.log('Audio notification channel unlocked silently.');
                    }).catch(e => console.log('Unlock failed:', e));
                }
            };
            document.addEventListener('click', unlockAudio);

            // Title & Favicon Alert Logic
            const originalTitle = document.title;
            let notificationInterval = null;
            let originalFavicon = null;

            // Get or create favicon link
            const getFaviconLink = () => {
                let link = document.querySelector("link[rel~='icon']");
                if (!link) {
                    link = document.createElement('link');
                    link.rel = 'icon';
                    document.getElementsByTagName('head')[0].appendChild(link);
                }
                return link;
            };

            // Save original favicon
            const saveOriginalFavicon = () => {
                const link = getFaviconLink();
                originalFavicon = link.href;
            };
            saveOriginalFavicon();

            // Generate Favicon with Red Dot
            const setAlertFavicon = () => {
                const link = getFaviconLink();
                const canvas = document.createElement('canvas');
                canvas.width = 32;
                canvas.height = 32;
                const ctx = canvas.getContext('2d');

                const img = new Image();
                img.crossOrigin = 'Anonymous';
                img.onload = () => {
                    ctx.drawImage(img, 0, 0, 32, 32);
                    
                    // Draw red dot
                    ctx.beginPath();
                    ctx.arc(24, 8, 5, 0, 2 * Math.PI, false);
                    ctx.fillStyle = 'red';
                    ctx.fill();
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = 'white';
                    ctx.stroke();

                    link.href = canvas.toDataURL('image/png');
                };

                // Handle case where original favicon might be distinct
                if (originalFavicon) {
                    img.src = originalFavicon;
                } else {
                    // Fallback if no favicon exists
                    ctx.beginPath();
                    ctx.arc(16, 16, 10, 0, 2 * Math.PI);
                    ctx.fillStyle = '#22A9C8'; // App Theme Color
                    ctx.fill();
                     // Draw red dot
                    ctx.beginPath();
                    ctx.arc(24, 8, 5, 0, 2 * Math.PI, false);
                    ctx.fillStyle = 'red';
                    ctx.fill();
                    link.href = canvas.toDataURL('image/png');
                }
            };

            const resetFavicon = () => {
                if (originalFavicon) {
                    const link = getFaviconLink();
                    link.href = originalFavicon;
                }
            };

            const flashTitle = () => {
                let isAlert = false;
                if (notificationInterval) clearInterval(notificationInterval);
                
                // Set Favicon once
                setAlertFavicon();

                notificationInterval = setInterval(() => {
                    document.title = isAlert ? '(1) Nuevo Mensaje!' : '🔔 Tienes un mensaje';
                    isAlert = !isAlert;
                }, 1000);
            };

            const resetTitle = () => {
                if (notificationInterval) {
                    clearInterval(notificationInterval);
                    notificationInterval = null;
                }
                document.title = originalTitle;
                resetFavicon();
            };

            window.addEventListener('focus', resetTitle);
            window.addEventListener('click', resetTitle); // Extra backup

            Livewire.on('play-new-message-sound', () => {
                if (audio) {
                    audio.volume = 1.0; // Ensure max volume
                    audio.pause();
                    audio.currentTime = 0;
                    
                    audio.play().catch(error => {
                        console.log('Audio playback failed (interaction required?):', error);
                    });

                    // Visual Alert if not focused (covers background tabs AND side-by-side unfocused)
                    if (!document.hasFocus()) {
                        flashTitle();
                    }
                }
            });
        });
    </script>
</div>
