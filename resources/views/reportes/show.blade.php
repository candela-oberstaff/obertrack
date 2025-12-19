<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <a href="{{ route('reportes.index') }}" class="w-12 h-12 bg-[#22A9C8]/10 text-[#22A9C8] rounded-2xl flex items-center justify-center hover:bg-[#22A9C8] hover:text-white transition-all shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="flex items-center gap-4">
                    <x-user-avatar :user="$professional" size="14" class="ring-4 ring-[#22A9C8]/10" />
                    <div>
                        <h1 class="text-3xl font-black text-[#0D1E4C] leading-none mb-1">
                            {{ $professional->name }}
                        </h1>
                        <p class="text-[#22A9C8] text-xs font-bold uppercase tracking-widest">{{ $professional->job_title ?? 'Profesional' }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100">
                <a href="{{ route('reportes.show', ['user' => $professional->id, 'week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" 
                   class="text-gray-400 hover:text-[#22A9C8] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <span class="text-sm font-bold text-[#0D1E4C] px-2 italic">
                    Semana {{ $weekStart->format('d/m') }} - {{ $weekEnd->format('d/m') }}
                </span>
                <a href="{{ route('reportes.show', ['user' => $professional->id, 'week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" 
                   class="text-gray-400 hover:text-[#22A9C8] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Quick Actions -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <a href="{{ route('reportes.download.weekly', ['user' => $professional->id, 'week' => $weekStart->format('Y-m-d')]) }}" 
                   class="px-10 py-3.5 bg-white text-[#0D1E4C] text-xs font-black uppercase tracking-widest rounded-full border-2 border-gray-100 hover:border-[#22A9C8] hover:text-[#22A9C8] transition-all shadow-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    PDF Semanal
                </a>
                <a href="{{ route('reportes.download.monthly', ['user' => $professional->id, 'month' => $weekStart->format('Y-m-d')]) }}" 
                   class="px-10 py-3.5 bg-[#22A9C8] text-white text-xs font-black uppercase tracking-widest rounded-full hover:bg-[#1B8BA6] transition-all shadow-lg inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    PDF Mensual
                </a>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                
                <!-- Hours Breakdown Card -->
                <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-[#0D1E4C] mb-1">Desglose semanal</h3>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Horas registradas por día</p>
                        </div>
                        <div class="text-right">
                            <p class="text-4xl font-black text-[#22A9C8]">{{ $registeredHours }}<span class="text-sm ml-1 opacity-50">hs</span></p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Semana</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        @foreach($dailyHours as $day)
                        <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 flex flex-col items-center group transition-all hover:bg-white hover:border-[#22A9C8]/30 hover:shadow-md">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4 group-hover:text-[#22A9C8] transition-colors">{{ $day['day'] }}</span>
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition-all {{ $day['hours'] > 0 ? 'bg-[#22A9C8]/10 text-[#22A9C8]' : 'bg-gray-200 text-gray-400' }}">
                                <span class="text-xl font-black">{{ $day['hours'] }}</span>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-tighter px-3 py-1 rounded-full {{ 
                                $day['status'] === 'Presente' ? 'bg-green-100 text-green-600' : 
                                ($day['status'] === 'Ausente' ? 'bg-red-100 text-red-500' : 
                                ($day['status'] === 'En curso' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-400'))
                            }}">
                                {{ $day['status'] }}
                            </span>
                            @if($day['is_approved'])
                                <span class="text-[8px] font-bold text-green-500 mt-2 uppercase tracking-widest">Aprobado</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Monthly & Summary Column -->
                <div class="space-y-8">
                    <!-- Monthly Highlight -->
                    <div class="bg-[#0D1E4C] rounded-[2.5rem] p-10 shadow-lg text-white relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full"></div>
                        <h3 class="text-xs font-bold uppercase tracking-[0.2em] opacity-60 mb-6">Resumen Mensual</h3>
                        <p class="text-6xl font-black mb-2">{{ $monthHours }}<span class="text-xl ml-2 opacity-40">hs</span></p>
                        <p class="text-sm font-bold opacity-60">Horas aprobadas este mes</p>
                        <div class="mt-8 pt-8 border-t border-white/10 flex justify-between items-center text-xs">
                            <div class="flex flex-col gap-1">
                                <span class="opacity-40 uppercase tracking-widest font-bold">Ausencias</span>
                                <span class="text-lg font-black text-red-400">{{ $absences }}</span>
                            </div>
                            <div class="flex flex-col gap-1 text-right">
                                <span class="opacity-40 uppercase tracking-widest font-bold">Tareas Pendientes</span>
                                <span class="text-lg font-black text-[#22A9C8]">{{ $incompleteTasks }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Status Info -->
                    <div class="bg-white rounded-[2rem] p-8 border border-gray-100 flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center text-green-500 flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Estado Tareas</p>
                            <p class="text-sm font-black text-[#0D1E4C]">
                                @if($incompleteTasks > 0)
                                    {{ $incompleteTasks }} tareas pendientes por revisar
                                @else
                                    Todo al día y completado
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Professional Comments -->
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-[#22A9C8]/10 text-[#22A9C8] flex items-center justify-center">
                            <i class="fas fa-comment-dots text-lg"></i>
                        </div>
                        <h3 class="text-xl font-black text-[#0D1E4C]">Notas del Profesional</h3>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse($professionalComments as $comment)
                            <div class="p-5 bg-gray-50 rounded-3xl border border-gray-100 relative group">
                                <p class="text-sm text-gray-600 leading-relaxed italic">"{{ $comment }}"</p>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-sm text-gray-300 font-bold uppercase tracking-widest italic">Sin notas esta semana</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Employer Comments -->
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-[#0D1E4C]/10 text-[#0D1E4C] flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-lg"></i>
                        </div>
                        <h3 class="text-xl font-black text-[#0D1E4C]">Feedback del Empleador</h3>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse($comments as $comment)
                            <div class="p-5 bg-[#0D1E4C]/5 rounded-3xl border border-[#0D1E4C]/10">
                                <p class="text-sm text-[#0D1E4C] leading-relaxed font-medium">{{ $comment }}</p>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-sm text-gray-300 font-bold uppercase tracking-widest italic">Sin comentarios registrados</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
