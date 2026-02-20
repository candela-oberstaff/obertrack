<div x-data="{ 
    meetings: @entangle('meetings'),
    activeMeetingId: @entangle('activeMeetingId'),
    warningMeetingId: @entangle('warningMeetingId'),
    notificationMinutes: @entangle('notificationMinutes'),
    countdown: '',
    nextMeeting: null,
    isAlarmPlaying: false,
    playAlarm() {
        console.log('Attempting to play alarm...');
        let audio = document.getElementById('meeting-alarm-sound');
        if (audio) {
            // Force reload to ensure it's ready
            audio.load();
            audio.play().then(() => {
                console.log('Alarm playing successfully.');
                this.isAlarmPlaying = true;
            }).catch(e => {
                console.warn('Playback blocked or failed. Browser might require interaction.', e);
                // Fallback: Try playing again on next click if blocked
                const retryPlay = () => {
                    audio.play();
                    this.isAlarmPlaying = true;
                    document.removeEventListener('click', retryPlay);
                };
                document.addEventListener('click', retryPlay);
            });
        }
    },
    stopAlarm() {
        let audio = document.getElementById('meeting-alarm-sound');
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
            this.isAlarmPlaying = false;
            console.log('Alarm stopped.');
        }
    },
    toggleTestAlarm() {
        if (this.isAlarmPlaying) {
            this.stopAlarm();
        } else {
            this.playAlarm();
            // Auto-stop test after 10 seconds to avoid annoyance
            setTimeout(() => {
                if (this.isAlarmPlaying && !this.activeMeetingId) {
                    this.stopAlarm();
                }
            }, 10000);
        }
    },
    updateCountdown() {
        // Log for debugging
        const currentMeetings = Array.isArray(this.meetings) ? [...this.meetings] : [];
        if (currentMeetings.length === 0) {
            this.countdown = '--:--';
            this.nextMeeting = null;
            return;
        }
        
        const now = new Date();
        const nowTime = now.getTime();
        let upcoming = null;
        let minDiff = Infinity;
        
        currentMeetings.forEach((m, index) => {
            if (!m || !m.start) return;
            const startTime = new Date(m.start).getTime();
            if (isNaN(startTime)) return;

            const diff = startTime - nowTime;
            
            // Log meeting status for debugging
            if (index === 0) console.log('Checking meetings...', { firstMeeting: m.summary, diff, now: now.toISOString() });

            if (diff > 0 && diff < minDiff) {
                minDiff = diff;
                upcoming = m;
            }
        });
        
        if (upcoming) {
            this.nextMeeting = upcoming;
            const startTime = new Date(upcoming.start).getTime();
            const diff = startTime - nowTime;
            
            if (diff > 0) {
                const totalSeconds = Math.floor(diff / 1000);
                const h = Math.floor(totalSeconds / 3600);
                const m = Math.floor((totalSeconds % 3600) / 60);
                const s = totalSeconds % 60;
                
                if (h > 0) {
                    this.countdown = `${h}h ${m}m ${s.toString().padStart(2, '0')}s`;
                } else {
                    this.countdown = `${m}m ${s.toString().padStart(2, '0')}s`;
                }
            } else {
                this.countdown = 'Iniciando...';
            }
        } else {
            const hasActive = currentMeetings.some(m => m.is_active);
            this.countdown = hasActive ? 'En curso' : '--:--';
            this.nextMeeting = null;
        }
    }
}" 
x-init="
    console.log('CalendarMeetings initialized', { meetingsCount: meetings.length });

    // Audio Unlocker
    const audio = document.getElementById('meeting-alarm-sound');
    const unlockAudio = () => {
        if (audio) {
            console.log('Unlocking audio context via user interaction...');
            audio.muted = true;
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
                audio.muted = false;
                console.log('Audio context unlocked successfully.');
            }).catch(e => console.warn('Audio unlock failed:', e));
        }
    };
    document.addEventListener('click', unlockAudio, { once: true });
    document.addEventListener('keydown', unlockAudio, { once: true });

    // Title Flashing Logic
    const originalTitle = document.title;
    let titleInterval = null;
    const flashTitle = (msg) => {
        if (titleInterval) clearInterval(titleInterval);
        let flip = false;
        titleInterval = setInterval(() => {
            document.title = flip ? '🔔 ' + msg : originalTitle;
            flip = !flip;
        }, 1000);
    };
    const stopFlash = () => {
        if (titleInterval) {
            clearInterval(titleInterval);
            titleInterval = null;
        }
        document.title = originalTitle;
    };

    // Notification Permission
    if (Notification.permission === 'default') {
        setTimeout(() => Notification.requestPermission(), 2000);
    }

    const component = this;

    // Reactivity Watches
    $watch('meetings', (value) => {
        console.log('Meetings updated!', { count: value.length });
        component.updateCountdown();
    });

    $watch('activeMeetingId', value => {
        console.log('activeMeetingId changed:', value);
        if (value) {
            component.playAlarm();
            const meetingList = Array.isArray(component.meetings) ? component.meetings : [];
            const meeting = meetingList.find(m => m.id === value);
            if (meeting) {
                if (document.visibilityState !== 'visible' || !document.hasFocus()) {
                    flashTitle('Reunión por comenzar');
                    if (Notification.permission === 'granted') {
                        new Notification('Próxima Reunión', {
                            body: meeting.summary + ' está por comenzar.',
                            icon: '/favicon.ico'
                        });
                    }
               }
            }
        } else {
            component.stopAlarm();
            component.stopFlash();
        }
    });

    // Cleanup on window focus
    window.addEventListener('focus', () => {
        if (!component.activeMeetingId) stopFlash();
    });

    // Initial check for active meeting
    if (this.activeMeetingId) {
        console.log('Initial check: Meeting is active!', this.activeMeetingId);
        setTimeout(() => this.playAlarm(), 1000);
    }

    // Start countdown timer
    this.updateCountdown();
    setInterval(() => {
        component.updateCountdown();
    }, 1000);
"
wire:poll.10s="poll"
class="mb-8">

    @if(auth()->user()->isGoogleCalendarConnected())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 px-4 lg:px-0">
            {{-- Meetings List --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-[#22A9C8]/10 to-transparent px-6 py-4 flex items-center justify-between border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <i class="fa fa-calendar text-[#22A9C8]"></i>
                            <h3 class="text-sm font-bold text-gray-900">Reuniones de Hoy</h3>
                        </div>
                        <div class="flex items-center gap-4">
                            {{-- Notification Settings --}}
                            <div class="flex items-center gap-3 mr-2 border-r border-gray-100 pr-4">
                                {{-- Lead Time Selector --}}
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Alerta:</span>
                                    <select wire:change="updateNotificationMinutes($event.target.value)" 
                                            class="bg-transparent border-none text-[10px] font-bold text-[#22A9C8] focus:ring-0 p-0 cursor-pointer uppercase tracking-widest">
                                        <option value="5" {{ $notificationMinutes == 5 ? 'selected' : '' }}>5 min antes</option>
                                        <option value="1" {{ $notificationMinutes == 1 ? 'selected' : '' }}>1 min antes</option>
                                        <option value="0" {{ $notificationMinutes == 0 ? 'selected' : '' }}>Al empezar</option>
                                    </select>
                                </div>

                                {{-- Toggle Swich --}}
                                <div class="flex items-center gap-2 border-l border-gray-50 pl-3">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $notificationsEnabled ? 'ON' : 'OFF' }}</span>
                                    <button wire:click="toggleNotifications" 
                                            wire:loading.attr="disabled"
                                            class="relative inline-flex h-4 w-7 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $notificationsEnabled ? 'bg-[#22A9C8]' : 'bg-gray-200' }}">
                                        <span class="pointer-events-none inline-block h-3 w-3 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $notificationsEnabled ? 'translate-x-3' : 'translate-x-0' }}"></span>
                                    </button>
                                </div>
                            </div>
                            
                            <button wire:click="fetchMeetings" 
                                    wire:loading.attr="disabled"
                                    title="Sincronizar ahora"
                                    class="text-gray-400 hover:text-[#22A9C8] transition-colors">
                                <i class="fa fa-sync-alt text-xs" wire:loading.class="fa-spin"></i>
                            </button>

                            <button @click="toggleTestAlarm()" 
                                    :title="isAlarmPlaying ? 'Detener prueba' : 'Probar sonido'"
                                    :class="isAlarmPlaying ? 'text-red-500 animate-pulse' : 'text-gray-400 hover:text-red-500'"
                                    class="transition-colors">
                                <i :class="isAlarmPlaying ? 'fa fa-stop-circle' : 'fa fa-volume-up'" class="text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Error State Messages --}}
                    @if($errorState)
                        <div class="mx-6 mt-4 mb-2">
                            @if($errorState === 'token_expired')
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 flex items-start gap-3">
                                    <i class="fa fa-exclamation-triangle text-orange-500 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-orange-900">Sesión de Google expirada</p>
                                        <p class="text-xs text-orange-700 mt-1">Tu conexión con Google Calendar ha expirado. Por favor, reconecta tu cuenta.</p>
                                        <a href="{{ route('google-calendar.connect') }}" class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white text-xs font-bold rounded-lg hover:bg-orange-600 transition-colors">
                                            <i class="fa fa-sync text-[10px]"></i>
                                            Reconectar Google Calendar
                                        </a>
                                    </div>
                                </div>
                            @elseif($errorState === 'access_denied')
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
                                    <i class="fa fa-ban text-red-500 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-red-900">Acceso denegado</p>
                                        <p class="text-xs text-red-700 mt-1">No tienes permisos para acceder a Google Calendar. Verifica la configuración de tu cuenta.</p>
                                    </div>
                                </div>
                            @elseif($errorState === 'api_error' || $errorState === 'unknown')
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start gap-3">
                                    <i class="fa fa-exclamation-circle text-yellow-500 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-yellow-900">Error temporal</p>
                                        <p class="text-xs text-yellow-700 mt-1">No pudimos cargar tus reuniones. Intenta nuevamente en unos momentos.</p>
                                    </div>
                                </div>
                            @elseif($errorState === 'rate_limit')
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
                                    <i class="fa fa-clock text-blue-500 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-blue-900">Demasiadas actualizaciones</p>
                                        <p class="text-xs text-blue-700 mt-1">
                                            Has actualizado demasiado rápido. 
                                            @if($retryAfter)
                                                Intenta nuevamente en {{ $retryAfter }} segundos.
                                            @else
                                                Intenta nuevamente en unos momentos.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @elseif($errorState === 'quota_exceeded')
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 flex items-start gap-3">
                                    <i class="fa fa-exclamation-triangle text-purple-500 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-purple-900">Límite de Google alcanzado</p>
                                        <p class="text-xs text-purple-700 mt-1">Hemos alcanzado el límite diario de consultas a Google Calendar. Las reuniones se actualizarán automáticamente mañana.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="p-6">
                        @if(count($meetings) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($meetings as $meeting)
                                    <div class="relative group p-4 rounded-xl border transition-all duration-300 {{ $activeMeetingId === $meeting['id'] ? 'bg-red-50 border-red-200 ring-2 ring-red-100' : ($meeting['is_active'] ? 'bg-green-50/50 border-green-200' : ($warningMeetingId === $meeting['id'] ? 'bg-orange-50 border-orange-200' : 'bg-gray-50 border-transparent hover:border-gray-200')) }}">
                                        
                                        @if($activeMeetingId === $meeting['id'])
                                            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-lg uppercase tracking-widest shadow-lg animate-bounce">
                                                Está por comenzar
                                            </div>
                                        @elseif($warningMeetingId === $meeting['id'])
                                            <div class="absolute -top-2 -right-2 bg-orange-500 text-white text-[10px] font-black px-2 py-1 rounded-lg uppercase tracking-widest shadow-lg">
                                                Faltan < 10 min
                                            </div>
                                        @endif

                                        <div class="flex flex-col gap-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($meeting['start'])->format('H:i') }}</span>
                                                @if($meeting['is_active'] ?? false)
                                                    <span class="flex h-2 w-2">
                                                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <h4 class="text-sm font-bold text-gray-900 line-clamp-1 {{ $activeMeetingId === $meeting['id'] ? 'text-red-700' : '' }}">{{ $meeting['summary'] }}</h4>
                                            
                                            @if($meeting['link'] ?? false)
                                                <div class="flex gap-2 mt-2">
                                                    <a href="{{ $meeting['link'] }}" 
                                                       target="_blank"
                                                       wire:click="joinMeeting('{{ $meeting['id'] }}', '{{ $meeting['link'] }}')"
                                                        class="flex-1 flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-bold transition-all {{ $activeMeetingId === $meeting['id'] ? 'bg-red-600 text-white shadow-lg hover:bg-red-700' : ($meeting['is_active'] ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-white border border-gray-200 text-[#22A9C8] hover:bg-gray-50') }}">
                                                        <i class="fa fa-video"></i>
                                                        {{ $activeMeetingId === $meeting['id'] ? 'Unirse' : ($meeting['is_active'] ? 'Unirse' : 'Unirse') }}
                                                    </a>
                                                    
                                                    @if($activeMeetingId === $meeting['id'])
                                                        <button @click="stopAlarm(); activeMeetingId = null" 
                                                                title="Silenciar alarma"
                                                                class="px-3 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                                                            <i class="fa fa-bell-slash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="mt-2 w-full py-2 flex items-center justify-center gap-2 text-[10px] text-gray-400 italic">
                                                    Sin link de acceso
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mb-3">
                                    <i class="fa fa-calendar-check text-gray-200 text-2xl"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">No tienes reuniones para hoy</p>
                                <p class="text-[10px] text-gray-300 mt-1">¡Buen trabajo!</p>
                            </div>
                        @endif
                    </div>

                    {{-- Alarm Sound --}}
                    <audio id="meeting-alarm-sound" loop preload="auto">
                        <source src="{{ asset('sounds/meeting_alarm.ogg') }}" type="audio/ogg">
                    </audio>
                </div>
            </div>

            {{-- Countdown Timer Panel --}}
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-[#22A9C8] to-[#1B8BA6] rounded-2xl shadow-lg overflow-hidden sticky top-4">
                    <div class="px-6 py-4 border-b border-white/20">
                        <div class="flex items-center gap-2">
                            <i class="fa fa-clock text-white"></i>
                            <h3 class="text-sm font-bold text-white">Próxima Reunión</h3>
                        </div>
                    </div>
                    
                    <div class="p-8" x-show="nextMeeting">
                        <div class="text-center mb-6">
                            <div class="text-6xl font-black text-white mb-2" x-text="countdown"></div>
                            <p class="text-white/80 text-xs uppercase tracking-widest">Tiempo Restante</p>
                        </div>
                        
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <p class="text-white/60 text-[10px] uppercase tracking-wider mb-1">Tema</p>
                            <h4 class="text-white font-bold text-sm mb-3" x-text="nextMeeting?.summary"></h4>
                            
                            <p class="text-white/60 text-[10px] uppercase tracking-wider mb-1">Hora de Inicio</p>
                            <p class="text-white font-semibold text-xs" x-text="nextMeeting ? new Date(nextMeeting.start).toLocaleTimeString('es-AR', {hour: '2-digit', minute: '2-digit'}) : ''"></p>
                        </div>
                    </div>
                    
                    <div class="p-8 text-center" x-show="!nextMeeting">
                        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fa fa-check text-white text-2xl"></i>
                        </div>
                        <p class="text-white font-bold text-sm">No hay reuniones pendientes</p>
                        <p class="text-white/60 text-xs mt-1">¡Disfruta tu día!</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Gray Placeholder for Disconnected State --}}
        <div class="bg-gray-50/50 rounded-2xl border border-dashed border-gray-200 overflow-hidden px-4 lg:px-0">
            <div class="px-6 py-12 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4 border border-gray-100">
                    <i class="fa fa-calendar-alt text-gray-200 text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-400 uppercase tracking-wider">Google Calendar</h3>
                <p class="text-xs text-gray-400 mt-1 max-w-xs">
                    Conecta tu calendario para ver tus próximas reuniones y activar las alarmas inteligentes.
                </p>
                <a href="{{ route('google-calendar.connect') }}" class="mt-6 px-6 py-2 bg-white border border-gray-200 text-gray-500 rounded-lg text-xs font-bold hover:bg-gray-50 transition-all flex items-center gap-2">
                    <i class="fa fa-plug text-[10px]"></i>
                    Conectar ahora
                </a>
            </div>
        </div>
    @endif
</div>
