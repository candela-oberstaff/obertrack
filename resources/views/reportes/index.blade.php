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
                            
                            <div class="flex items-center gap-2">
                                @if($prof['has_pending_weeks'])
                                    <span class="text-sm font-bold text-red-500 italic flex items-center gap-1.5">
                                        Semanas pendientes
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </span>
                                @else
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
