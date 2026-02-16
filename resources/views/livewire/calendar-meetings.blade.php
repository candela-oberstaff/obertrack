<div x-data="{ 
    meetings: @entangle('meetings'),
    activeMeetingId: @entangle('activeMeetingId'),
    warningMeetingId: @entangle('warningMeetingId'),
    countdown: '',
    nextMeeting: null,
    playAlarm() {
        let audio = document.getElementById('meeting-alarm-sound');
        if (audio) {
            audio.play().catch(e => console.error('Error playing alarm:', e));
        }
    },
    stopAlarm() {
        let audio = document.getElementById('meeting-alarm-sound');
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
        }
    },
    updateCountdown() {
        if (!this.meetings || this.meetings.length === 0) {
            this.countdown = '';
            this.nextMeeting = null;
            return;
        }
        
        const now = new Date();
        let upcoming = null;
        
        for (let meeting of this.meetings) {
            const start = new Date(meeting.start);
            if (start > now) {
                if (!upcoming || start < new Date(upcoming.start)) {
                    upcoming = meeting;
                }
            }
        }
        
        if (upcoming) {
            this.nextMeeting = upcoming;
            const start = new Date(upcoming.start);
            const diff = start - now;
            
            if (diff > 0) {
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                if (hours > 0) {
                    this.countdown = hours + 'h ' + minutes + 'm ' + seconds + 's';
                } else if (minutes > 0) {
                    this.countdown = minutes + 'm ' + seconds + 's';
                } else {
                    this.countdown = seconds + 's';
                }
            } else {
                this.countdown = '¡Comenzó!';
            }
        } else {
            this.countdown = '';
            this.nextMeeting = null;
        }
    }
}" 
x-init="
    $watch('activeMeetingId', value => {
        if (value) playAlarm();
        else stopAlarm();
    });
    updateCountdown();
    setInterval(() => updateCountdown(), 1000);
"
wire:poll.60s="poll"
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
                        <button wire:click="fetchMeetings" class="text-gray-400 hover:text-[#22A9C8] transition-colors">
                            <i class="fa fa-sync-alt text-xs" wire:loading.class="fa-spin"></i>
                        </button>
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
                                    <div class="relative group p-4 rounded-xl border transition-all duration-300 {{ $activeMeetingId === $meeting['id'] ? 'bg-red-50 border-red-200 ring-2 ring-red-100' : ($warningMeetingId === $meeting['id'] ? 'bg-orange-50 border-orange-200' : 'bg-gray-50 border-transparent hover:border-gray-200') }}">
                                        
                                        @if($activeMeetingId === $meeting['id'])
                                            <div class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-lg uppercase tracking-widest shadow-lg animate-bounce">
                                                ¡Comenzó!
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
                                                <a href="{{ $meeting['link'] }}" 
                                                   target="_blank"
                                                   wire:click="joinMeeting('{{ $meeting['id'] }}', '{{ $meeting['link'] }}')"
                                                   class="mt-2 w-full flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-bold transition-all {{ $activeMeetingId === $meeting['id'] ? 'bg-red-600 text-white shadow-lg hover:bg-red-700' : 'bg-white border border-gray-200 text-[#22A9C8] hover:bg-gray-50' }}">
                                                    <i class="fa fa-video"></i>
                                                    {{ $activeMeetingId === $meeting['id'] ? 'Detener Alarma y Unirse' : 'Unirse a la reunión' }}
                                                </a>
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
                        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
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
