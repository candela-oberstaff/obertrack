<x-app-layout>
    <x-slot name="header" id="reportes-header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-3xl text-[#0D1E4C] leading-tight">
                    Reportes de profesionales
                </h2>
                <p class="text-[#22A9C8] font-bold mt-2 uppercase tracking-widest text-xs">Estadísticas de rendimiento semanal</p>
            </div>
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-2xl shadow-sm border border-gray-100">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Semana:</span>
                <span class="text-sm font-bold text-[#0D1E4C]">
                    {{ $weekStart->format('d/m/Y') }} - {{ $weekEnd->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div id="reportes-professionals-list" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            @forelse($professionals as $prof)
            <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm relative overflow-hidden reportes-professional-card border border-gray-100 transition-all hover:shadow-md group">
                <!-- Top Section: Profile & Quick Stats -->
                <div class="flex flex-col md:flex-row items-start gap-8 mb-10">
                    <div class="flex-shrink-0 relative">
                        <x-user-avatar :user="$prof['professional']" size="24" class="ring-4 ring-[#22A9C8]/10" />
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-full bg-[#0D1E4C] border-4 border-white flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            {{ $prof['index'] }}
                        </div>
                    </div>

                    <div class="flex-1 w-full">
                        <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-8">
                            <div>
                                <h3 class="text-3xl font-extrabold text-[#0D1E4C] group-hover:text-[#22A9C8] transition-colors leading-none mb-2">
                                    {{ $prof['name'] }}
                                </h3>
                                <p class="text-[#22A9C8] text-sm font-bold uppercase tracking-widest">{{ $prof['job_title'] }}</p>
                            </div>
                            
                            @if($prof['has_pending_weeks'])
                                <div class="px-4 py-2 bg-red-50 text-red-600 rounded-full text-[10px] font-extrabold uppercase tracking-widest flex items-center gap-2 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                    Horas pendientes
                                </div>
                            @endif
                        </div>
                        
                        <!-- Mini Stats Grid -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-gray-50 rounded-3xl p-5 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Horas Semanales</p>
                                <p class="text-2xl font-black text-[#0D1E4C]">{{ $prof['registered_hours'] }}<span class="text-xs ml-1 text-gray-400">h</span></p>
                            </div>
                            <div class="bg-gray-50 rounded-3xl p-5 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Ausencias</p>
                                <p class="text-2xl font-black {{ $prof['absences'] > 0 ? 'text-red-500' : 'text-[#0D1E4C]' }}">{{ $prof['absences'] }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-3xl p-5 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tareas Pendientes</p>
                                <p class="text-2xl font-black text-[#0D1E4C]">{{ $prof['incomplete_tasks'] }}</p>
                            </div>
                            <div class="bg-[#22A9C8]/5 rounded-3xl p-5 border border-[#22A9C8]/10">
                                <p class="text-[10px] font-bold text-[#22A9C8] uppercase tracking-widest mb-1">Total Mes (Aprobado)</p>
                                <p class="text-2xl font-black text-[#22A9C8]">{{ $prof['month_hours'] }}<span class="text-xs ml-1 opacity-50 text-[#22A9C8]">h</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-between border-t border-gray-100 pt-8">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('reportes.show', $prof['id']) }}" 
                           class="inline-flex items-center gap-2 px-8 py-3 bg-[#0D1E4C] text-white text-xs font-extrabold uppercase tracking-widest rounded-full hover:bg-[#22A9C8] transition-all shadow-lg hover:translate-y-[-2px]">
                            Ver reporte detallado
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>

                    <div class="hidden sm:flex items-center gap-2">
                        @if($prof['has_pending_weeks'])
                            <span class="text-[10px] font-black text-red-400 uppercase tracking-widest italic">Acción requerida en dashboard</span>
                        @else
                            <span class="text-[10px] font-black text-green-500 uppercase tracking-widest italic flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                                Al día
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-[2.5rem] p-16 text-center shadow-sm border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-[#0D1E4C] mb-2">No hay profesionales registrados</h4>
                <p class="text-gray-400 text-sm">Cuando agregues profesionales, aparecerán aquí con sus estadísticas.</p>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
