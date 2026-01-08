<x-app-layout>
    <div class="py-10 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="font-extrabold text-3xl text-[#0D1E4C] tracking-tight mb-4">
                    Reportes de profesionales
                </h1>
                <p class="text-[#22A9C8] font-bold text-base">Profesionales registrados</p>
            </div>

            <div id="reportes-professionals-list" class="space-y-6">
                @forelse($professionals as $prof)
                <div class="bg-[#F8F9FA] rounded-[1.25rem] p-5 md:p-6 shadow-sm relative transition-all hover:shadow-md flex flex-col md:flex-row items-center gap-6">
                    <!-- Index Number Section -->
                    <div class="flex-shrink-0 w-20 flex justify-center items-center">
                        <span class="text-5xl font-black text-[#1a202c] leading-none opacity-90 tracking-tighter">{{ str_pad($prof['index'], 2, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <!-- Vertical Divider (Hidden on mobile) -->
                    <div class="hidden md:block w-px h-24 bg-gray-300 opacity-50"></div>

                    <!-- Main Info Section -->
                    <div class="flex-1 w-full relative">
                        <!-- Top Detail (Date and Comments) -->
                        <div class="absolute -top-1 right-0 text-right hidden lg:block">
                            <p class="text-xs font-medium text-gray-500">Semana del {{ $weekStart->format('d/m/Y') }} al {{ $weekEnd->format('d/m/Y') }}</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5">Comentarios: {{ $prof['comment_count'] ?? 0 }}</p>
                        </div>

                        <div class="mb-4">
                            <h3 class="text-xl font-extrabold text-[#1a202c] mb-0.5 leading-tight">
                                {{ $prof['name'] }}
                            </h3>
                            <p class="text-[#22A9C8] text-sm font-bold opacity-70">{{ $prof['job_title'] }}</p>
                        </div>

                        <!-- Stats List -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-1.5 gap-x-4 mb-6">
                            <p class="text-sm font-medium text-gray-700">Horas semanal: <span class="font-bold ml-1">{{ $prof['registered_hours'] }}</span></p>
                            <p class="text-sm font-medium text-gray-700">Ausencias: <span class="font-bold ml-1">{{ $prof['absences'] }}</span></p>
                            <p class="text-sm font-medium text-gray-700">Tareas incompletas: <span class="font-bold ml-1">{{ $prof['incomplete_tasks'] }}</span></p>
                        </div>

                        <!-- Actions & Status Row -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <a href="{{ route('reportes.show', $prof['id']) }}" 
                               class="inline-flex items-center px-6 py-2 bg-[#0D1E4C] text-white text-xs font-black uppercase tracking-widest rounded-full hover:bg-[#1a202c] transition-all shadow-md active:scale-95">
                                Ver reporte completo
                            </a>
                            
                            <div class="flex flex-col gap-2">
                                @if($prof['has_pending_weeks'])
                                    <span class="text-sm font-bold text-red-500 italic flex items-center gap-1.5">
                                        Semanas pendientes
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </span>
                                @endif

                                @if($prof['has_pending_recoveries'])
                                    <div x-data="{ isApproving: false }">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs font-bold text-orange-500 italic flex items-center gap-1.5 uppercase tracking-wider">
                                                Recuperación pendiente ({{ $prof['pending_recoveries']->count() }})
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </span>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                @foreach($prof['pending_recoveries'] as $recovery)
                                                    <button @click="if(confirm('¿Aprobar ' + '{{ $recovery->recovered_hours }}' + 'h para el ' + '{{ $recovery->work_date->format('d/m') }}' + '?')) {
                                                                isApproving = true;
                                                                fetch('{{ route('work-hours.approve-recovery', $recovery->id) }}', {
                                                                    method: 'POST',
                                                                    headers: {
                                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                        'Accept': 'application/json'
                                                                    }
                                                                }).then(r => r.json()).then(data => {
                                                                    if(data.success) window.location.reload();
                                                                    else alert(data.message);
                                                                }).finally(() => isApproving = false);
                                                            }"
                                                            :disabled="isApproving"
                                                            class="bg-orange-100 text-orange-700 hover:bg-orange-200 px-3 py-1 rounded-full text-[10px] font-bold transition-all flex items-center gap-1 border border-orange-200 transition active:scale-95 disabled:opacity-50">
                                                        <span>Aprobar {{ $recovery->work_date->format('d/m') }}: {{ $recovery->recovered_hours }}h</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(!$prof['has_pending_weeks'] && !$prof['has_pending_recoveries'])
                                    <span class="text-sm font-bold text-green-500 italic flex items-center gap-1.5">
                                        Todo al día
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-[#F8F9FA] rounded-[1.5rem] p-12 text-center shadow-sm border border-gray-100">
                    <p class="text-gray-400 font-bold text-lg uppercase tracking-widest">No hay profesionales registrados para esta semana.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
