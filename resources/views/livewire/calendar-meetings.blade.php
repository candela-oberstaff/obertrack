<div x-data="{ 
    meetings: @entangle('meetings'),
    activeMeetingId: @entangle('activeMeetingId'),
    warningMeetingId: @entangle('warningMeetingId'),
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
    }
}" 
x-init="
    $watch('activeMeetingId', value => {
        if (value) playAlarm();
        else stopAlarm();
    });
"
wire:poll.60s="poll"
class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">

    @if(auth()->user()->isGoogleCalendarConnected())
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

            <div class="p-6">
                @if(count($meetings) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                                    
                                    @if($meeting['hangoutLink'] ?? false)
                                        <a href="{{ $meeting['hangoutLink'] }}" 
                                           target="_blank"
                                           wire:click="joinMeeting('{{ $meeting['id'] }}', '{{ $meeting['hangoutLink'] }}')"
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
    @endif
</div>
