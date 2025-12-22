<x-app-layout>
    <div class="py-12 bg-[#F3F4F6] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="font-extrabold text-4xl text-[#0D1E4C] tracking-tight">
                    Reportes de profesionales
                </h2>
                <p class="text-[#22A9C8] font-bold mt-2 text-base">Profesionales registrados</p>
            </div>

            <div id="reportes-professionals-list" class="space-y-6">
                @forelse($professionals as $prof)
                <div class="bg-white rounded-[2rem] p-8 md:p-10 shadow-sm relative transition-all hover:shadow-md border border-gray-100 flex flex-col md:flex-row items-center gap-8">
                    <!-- Index Number -->
                    <div class="flex-shrink-0">
                        <span class="text-7xl font-black text-[#1a202c] leading-none opacity-90">{{ str_pad($prof['index'], 2, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <!-- Vertical Divider (Hidden on mobile) -->
                    <div class="hidden md:block w-px h-32 bg-gray-100"></div>

                    <!-- Main Info Section -->
                    <div class="flex-1 w-full">
                        <div class="flex flex-col md:flex-row justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-[#1a202c] mb-1 leading-tight">
                                    {{ $prof['name'] }}
                                </h3>
                                <p class="text-[#22A9C8] text-sm font-medium">{{ $prof['job_title'] }}</p>
                            </div>
                            <div class="text-right hidden md:block">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Semana del {{ $weekStart->format('Y-m-d') }} al {{ $weekEnd->format('Y-m-d') }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Comentarios: {{ $prof['comment_count'] ?? 0 }}</p>
                            </div>
                        </div>

                        <!-- Stats List -->
                        <div class="space-y-1 mb-6">
                            <p class="text-sm font-medium text-[#1a202c]">Promedio de horas semanal: <span class="font-bold">{{ $prof['registered_hours'] }}</span></p>
                            <p class="text-sm font-medium text-[#1a202c]">Ausencias registradas (con o sin justificación): <span class="font-bold">{{ $prof['absences'] }}</span></p>
                            <p class="text-sm font-medium text-[#1a202c]">Registro de tareas incompletas: <span class="font-bold">{{ $prof['incomplete_tasks'] }}</span></p>
                        </div>

                        <!-- Actions & Status -->
                        <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                            <a href="{{ route('reportes.show', $prof['id']) }}" 
                               class="inline-flex items-center px-6 py-2 bg-[#0D1E4C] text-white text-xs font-bold uppercase tracking-widest rounded-full hover:bg-[#22A9C8] transition-all">
                                Ver reporte completo
                            </a>
                            
                            <div class="flex items-center gap-2">
                                @if($prof['has_pending_weeks'])
                                    <span class="text-[13px] font-medium text-red-500 italic flex items-center gap-1.5">
                                        Tiene semanas pendientes por aprobación
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </span>
                                @else
                                    <span class="text-[13px] font-medium text-green-500 italic flex items-center gap-1.5">
                                        No hay semanas pendientes por aprobación
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-[2rem] p-16 text-center shadow-sm border border-gray-100">
                    <p class="text-gray-400 font-medium">No hay profesionales registrados para esta semana.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
