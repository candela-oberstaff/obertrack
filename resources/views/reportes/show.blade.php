<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                <div class="flex items-center gap-6">
                    <a href="{{ route('reportes.index') }}" class="w-10 h-10 bg-[#22A9C8] text-white rounded-lg flex items-center justify-center hover:opacity-90 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-5xl font-extrabold text-[#1a202c] leading-tight">
                            {{ $professional->name }}
                        </h1>
                        <p class="text-[#22A9C8] text-lg font-medium">{{ $professional->job_title ?? 'Profesional' }}</p>
                    </div>
                </div>
            </div>

            <!-- Week Navigator & Action Buttons -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-10">
                <div class="flex items-center gap-4 text-[#22A9C8] font-bold">
                    <a href="{{ route('reportes.show', ['user' => $professional->id, 'week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" class="hover:opacity-75">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <span class="text-sm">Semana del {{ $weekStart->format('Y-m-d') }} al {{ $weekEnd->format('Y-m-d') }}</span>
                    <a href="{{ route('reportes.show', ['user' => $professional->id, 'week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" class="hover:opacity-75">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('reportes.download.weekly', ['user' => $professional->id, 'week' => $weekStart->format('Y-m-d')]) }}" 
                       class="px-6 py-2 border-2 border-[#22A9C8] text-[#0D1E4C] text-sm font-bold rounded-full hover:bg-gray-50 transition-all">
                        Descargar reporte semanal
                    </a>
                    <a href="{{ route('reportes.download.monthly', ['user' => $professional->id, 'month' => $weekStart->format('Y-m-d')]) }}" 
                       class="px-6 py-2 bg-[#22A9C8] text-white text-sm font-bold rounded-full hover:opacity-90 transition-all shadow-sm">
                        Descargar reporte mensual
                    </a>
                </div>
            </div>

            <!-- Main Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                
                <!-- Average Week Hours -->
                <div class="bg-white rounded-[2.5rem] border-2 border-[#22A9C8] p-10 relative flex flex-col items-center">
                    <h3 class="text-2xl font-bold text-[#1a202c] text-center mb-4 leading-tight">Promedio de horas semanal</h3>
                    <span class="text-[10rem] font-black text-[#1a202c] leading-none mb-8">{{ $registeredHours }}</span>
                    
                    <div class="w-full space-y-2 mt-auto">
                        @foreach($dailyHours as $day)
                            <div class="flex justify-between items-center text-sm font-medium text-[#1a202c]">
                                <span>{{ $day['day'] }}:</span>
                                <span>{{ $day['hours'] > 0 ? $day['hours'] . ' horas' : $day['status'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Absences -->
                <div class="bg-[#F3F4F6] rounded-[2.5rem] p-10 flex flex-col items-center">
                    <h3 class="text-3xl font-bold text-[#1a202c] text-center mb-4 px-10">Ausencias registradas</h3>
                    <span class="text-[10rem] font-black text-[#1a202c] leading-none mb-10">{{ $absences }}</span>
                    
                    <div class="mt-auto w-full text-center">
                        @if($absences > 0)
                            @foreach($dailyHours as $day)
                                @if($day['status'] === 'Ausente')
                                    <p class="text-sm font-bold text-[#1a202c]">{{ $day['day'] }} {{ $day['date'] }}</p>
                                @endif
                            @endforeach
                        @else
                            <p class="text-sm font-medium text-gray-400">Sin ausencias registradas</p>
                        @endif
                    </div>
                </div>

                <!-- Incomplete Tasks -->
                <div class="bg-white rounded-[2.5rem] border-2 border-[#22A9C8] p-10 flex flex-col items-center">
                    <h3 class="text-3xl font-bold text-[#1a202c] text-center mb-4 px-10">Registro de tareas incompletas</h3>
                    <span class="text-[10rem] font-black text-[#1a202c] leading-none mb-10">{{ $incompleteTasks }}</span>
                    
                    <div class="mt-auto w-full text-center">
                        <p class="text-sm font-medium text-gray-300">
                            @if($incompleteTasks > 0)
                                Hay tareas pendientes de revisión
                            @else
                                Todas las tareas han sido completadas con éxito
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-[#F3F4F6] rounded-[2rem] p-10 relative">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-3xl font-bold text-[#1a202c]">Comentarios</h3>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Semana del {{ $weekStart->format('Y-m-d') }} al {{ $weekEnd->format('Y-m-d') }}</span>
                </div>
                
                <div class="space-y-4">
                    @forelse($allComments as $record)
                        @if($record->user_comment)
                            <div class="p-4 bg-white rounded-xl shadow-sm border-l-4 border-gray-300">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Profesional</span>
                                    <span class="text-[10px] text-gray-400 font-bold">
                                        {{ \Carbon\Carbon::parse($record->work_date)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 not-italic">"{{ $record->user_comment }}"</p>
                            </div>
                        @endif

                        @if($record->approval_comment)
                            <div class="p-4 bg-white rounded-xl shadow-sm border-l-4 border-[#22A9C8]">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-[#22A9C8] uppercase tracking-wider">Empresa</span>
                                    <span class="text-[10px] text-gray-400 font-bold">
                                        {{ \Carbon\Carbon::parse($record->work_date)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 not-italic">"{{ $record->approval_comment }}"</p>
                            </div>
                        @endif
                    @empty
                        <div class="text-gray-400 font-medium italic">
                            No hay comentarios sobre este profesional por los momentos
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
