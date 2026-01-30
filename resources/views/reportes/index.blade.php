<x-app-layout>
    <div class="py-10 bg-white min-h-screen" x-data="{ searchQuery: '', emailQuery: '' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="font-extrabold text-3xl text-[#0D1E4C] tracking-tight mb-4 flex items-center">
                    Reportes de profesionales
                    <span class="relative group inline-flex items-center ml-2 cursor-help">
                        <span class="rounded-full bg-blue-50 text-blue-500 hover:bg-blue-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"/>
                            </svg>
                        </span>

                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                                    px-3 py-2 bg-gray-900 text-white text-[10px]
                                    rounded-lg opacity-0 group-hover:opacity-100
                                    transition-opacity duration-200
                                    whitespace-nowrap z-50 pointer-events-none shadow-xl">
                            Reporte de actividades de los profesionales
                        </div>
                    </span>
                </h1>
                <p class="text-[#22A9C8] font-bold text-base">Profesionales registrados</p>
            </div>

            <!-- Filters Section -->
            <div class="mb-8 p-1">
                <form method="GET" action="{{ route('reportes.index', [], false) }}" 
                      class="bg-gray-50 p-6 rounded-3xl border border-gray-200 shadow-sm flex flex-col md:flex-row gap-5 items-end relative z-10">
                    
                    <!-- Date Filter (Server Side) -->
                    <div class="w-full md:w-auto flex-1">
                        <label for="week" class="block text-xs font-extrabold text-[#0D1E4C] uppercase tracking-widest mb-2 ml-1">
                            Fecha (Semana)
                        </label>
                        <input type="date" id="week" name="week" 
                               value="{{ request('week', $weekStart->format('Y-m-d')) }}" 
                               class="w-full bg-white border-gray-200 rounded-xl py-3 px-4 text-sm font-medium focus:ring-2 focus:ring-[#22A9C8] transition-all shadow-sm">
                    </div>

                    <!-- Name Filter (Client Side) -->
                    <div class="w-full md:w-auto flex-1">
                        <label for="name" class="block text-xs font-extrabold text-[#0D1E4C] uppercase tracking-widest mb-2 ml-1">
                            Nombre Profesional
                        </label>
                        <input type="text" id="name" x-model="searchQuery" 
                               placeholder="Filtrar por nombre..." 
                               class="w-full bg-white border-gray-200 rounded-xl py-3 px-4 text-sm font-medium focus:ring-2 focus:ring-[#22A9C8] transition-all shadow-sm">
                    </div>

                    <!-- Email Filter (Client Side) -->
                    <div class="w-full md:w-auto flex-1">
                        <label for="email" class="block text-xs font-extrabold text-[#0D1E4C] uppercase tracking-widest mb-2 ml-1">
                            Correo Electrónico
                        </label>
                        <input type="text" id="email" x-model="emailQuery" 
                               placeholder="Filtrar por correo..." 
                               class="w-full bg-white border-gray-200 rounded-xl py-3 px-4 text-sm font-medium focus:ring-2 focus:ring-[#22A9C8] transition-all shadow-sm">
                    </div>

                    <!-- Actions -->
                    <div class="w-full md:w-auto">
                        <button type="submit" 
                                class="w-full md:w-auto bg-[#0D1E4C] hover:bg-[#1a202c] text-white font-bold py-3 px-8 rounded-xl text-sm transition-all shadow-md active:scale-95 flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Actualizar Semana
                        </button>
                    </div>
                </form>
            </div>

            <div id="reportes-professionals-list" class="space-y-6">
                @forelse($professionals as $prof)
                <div class="bg-[#F8F9FA] rounded-[1.25rem] p-5 md:p-6 shadow-sm relative transition-all hover:shadow-md flex flex-col md:flex-row items-center gap-6"
                     data-name="{{ strtolower($prof['name']) }}"
                     data-email="{{ strtolower($prof['professional']->email ?? '') }}"
                     x-show="$el.dataset.name.includes(searchQuery.toLowerCase()) && $el.dataset.email.includes(emailQuery.toLowerCase())"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                     
                    <!-- Index Number Section -->
                    <div class="flex-shrink-0 w-20 flex justify-center items-center">
                        <span class="text-5xl font-black text-[#1a202c] leading-none opacity-90 tracking-tighter">{{ str_pad($prof['index'], 2, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <!-- Vertical Divider (Hidden on mobile) -->
                    <div class="hidden md:block w-px h-24 bg-gray-300 opacity-50"></div>

                    <!-- Main Info Section -->
                    <div class="flex-1 w-full relative">
                        <!-- Top Detail (Date and Comments) -->
                        <div class="text-left sm:text-right flex flex-col items-start sm:items-end space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-[#22A9C8]">Semana del {{ $weekStart->format('d/m/Y') }} al {{ $weekEnd->format('d/m/Y') }}</p>
                            <div class="flex items-center gap-3 justify-end">
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold inline-flex items-center">
                                    <i class="bi bi-chat-dots-fill mr-1.5 text-[#22A9C8]"></i>
                                    {{ $prof['comment_count'] ?? 0 }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h3 class="text-xl font-extrabold text-[#1a202c] mb-0.5 leading-tight">
                                {{ $prof['name'] }}
                            </h3>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <p class="text-[#22A9C8] text-sm font-bold opacity-70 uppercase tracking-tight">{{ $prof['job_title'] }}</p>
                                <span class="text-[10px] text-gray-400 font-medium lowercase flex items-center gap-1">
                                    <i class="bi bi-envelope-fill"></i>
                                    {{ $prof['professional']->email ?? '' }}
                                </span>
                            </div>
                        </div>

                        <!-- Stats List -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-1.5 gap-x-4 mb-6">
                            <p class="text-sm font-medium text-gray-700">Horas semanal: <span class="font-bold ml-1">{{ $prof['registered_hours'] }}</span></p>
                            
                            <div class="relative group inline-block">
                                <p class="text-sm font-medium text-gray-700 cursor-help">Ausencias: <span class="font-bold ml-1">{{ $prof['absences'] }}</span></p>
                                <!-- Tooltip -->
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                            bg-gray-900 text-white text-[10px] rounded-lg
                                            opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                            whitespace-nowrap z-50 pointer-events-none shadow-xl border border-gray-700">
                                    Días sin registros de actividad en esta semana
                                </div>
                            </div>

                            <div class="relative group inline-block">
                                <p class="text-sm font-medium text-gray-700 cursor-help">Tareas incompletas: <span class="font-bold ml-1">{{ $prof['incomplete_tasks'] }}</span></p>
                                <!-- Tooltip -->
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                            bg-gray-900 text-white text-[10px] rounded-lg
                                            opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                            whitespace-nowrap z-50 pointer-events-none shadow-xl border border-gray-700">
                                    Tareas asignadas que aún no han sido finalizadas
                                </div>
                            </div>
                        </div>

                        <!-- Actions & Status Row -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mt-2 pt-6 border-t border-gray-50">
                            <a href="{{ route('reportes.show', $prof['id'], false) }}" 
                               class="inline-flex items-center px-8 py-3 bg-[#0D1117] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-[#1a202c] transition-all shadow-md active:scale-95">
                                Ver reporte completo
                            </a>
                            
                            <div class="flex flex-wrap items-center justify-start sm:justify-end gap-3">
                                @if($prof['has_pending_weeks'])
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-red-50 text-red-600 border border-red-100 shadow-sm shadow-red-100/50">
                                        <i class="bi bi-calendar-x-fill mr-1.5"></i>
                                        Semanas pendientes
                                    </span>
                                @endif
                                
                                @if($prof['has_pending_recoveries'])
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-100 shadow-sm shadow-orange-100/50">
                                        <i class="bi bi-clock-history mr-1.5"></i>
                                        Recuperación pendiente ({{ $prof['pending_recoveries']->count() }})
                                    </span>
                                @endif
                                
                                @if(!$prof['has_pending_weeks'] && !$prof['has_pending_recoveries'])
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-green-50 text-green-600 border border-green-100 shadow-sm shadow-green-100/50">
                                        <i class="bi bi-check-circle-fill mr-1.5"></i>
                                        Todo al día
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-[#F8F9FA] rounded-[1.5rem] p-12 text-center shadow-sm border border-gray-100">
                    <p class="text-gray-400 font-bold text-lg uppercase tracking-widest">No hay profesionales registrados.</p>
                </div>
                @endforelse
                
                <!-- No Results Message (Client Side) -->
                 <div x-show="document.querySelectorAll('#reportes-professionals-list > div[data-name]').length > 0 && Array.from(document.querySelectorAll('#reportes-professionals-list > div[data-name]')).every(el => el.style.display === 'none')" 
                      class="bg-[#F8F9FA] rounded-[1.5rem] p-12 text-center shadow-sm border border-gray-100" 
                      style="display: none;">
                    <p class="text-gray-400 font-bold text-lg uppercase tracking-widest">No se encontraron coincidencias.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
