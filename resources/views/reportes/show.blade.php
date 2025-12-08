<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('reportes.index') }}" class="text-blue-500 hover:text-blue-700 p-1 bg-blue-50 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $professional->name }}
                </h1>
                <p class="text-blue-400 text-sm mt-1">{{ $professional->job_title ?? 'Auxiliar Administrativo' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Week Navigation -->
            <div class="flex items-center justify-between mb-8 max-w-4xl mx-auto">
                <a href="{{ route('reportes.show', ['user' => $professional->id, 'week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" 
                   class="text-blue-500 hover:text-blue-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                
                <div class="text-center group cursor-pointer">
                    <p class="text-gray-400 text-sm underline group-hover:text-gray-600 transition">
                        Semana del {{ $weekStart->format('Y-m-d') }} al {{ $weekEnd->format('Y-m-d') }}
                    </p>
                </div>
                
                <a href="{{ route('reportes.show', ['user' => $professional->id, 'week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" 
                   class="text-blue-500 hover:text-blue-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Outline Download Buttons -->
            <div class="flex justify-center gap-6 mb-12">
                <a href="{{ route('reportes.download.weekly', ['user' => $professional->id, 'week' => $weekStart->format('Y-m-d')]) }}" 
                   class="px-8 py-2.5 bg-white text-blue-500 text-sm font-semibold rounded-full border-2 border-blue-500 hover:bg-blue-50 transition shadow-sm inline-block text-center no-underline">
                    Descargar reporte semanal
                </a>
                <a href="{{ route('reportes.download.monthly', ['user' => $professional->id, 'month' => $weekStart->format('Y-m-d')]) }}" 
                   class="px-8 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-full border-2 border-blue-600 hover:bg-blue-700 transition shadow-lg inline-block text-center no-underline">
                    Descargar reporte mensual
                </a>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Weekly Average Card (Blue Border) -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border-2 border-blue-400 h-full relative">
                    <div class="absolute top-6 right-6 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    
                    <h3 class="text-gray-700 font-medium text-sm leading-tight mb-4">
                        Promedio de horas<br>semanal
                    </h3>
                    
                    <p class="text-6xl font-bold text-gray-900 mb-8">{{ $weeklyAverage }}</p>
                    
                    <div class="space-y-2 text-sm">
                        @foreach($dailyHours as $day)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">{{ $day['day'] }}:</span>
                            <span class="font-medium {{ $day['hours'] > 0 ? 'text-gray-900' : 'text-red-400' }}">
                                {{ $day['hours'] > 0 ? $day['hours'] . ' horas' : 'Ausente' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Absences Card (Light Gray/White Background) -->
                <div class="bg-gray-50 rounded-3xl p-8 shadow-sm h-full relative border border-gray-100">
                    <div class="absolute top-6 right-6 w-8 h-8 bg-orange-200 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>

                    <h3 class="text-gray-700 font-medium text-sm leading-tight mb-4">
                        Ausencias<br>registradas
                    </h3>
                    
                    <p class="text-6xl font-bold text-gray-900 mb-8">{{ $absences }}</p>
                    
                    @if($absences > 0)
                    <div class="space-y-1">
                        @foreach($dailyHours as $day)
                            @if($day['hours'] == 0)
                                <p class="text-sm text-gray-500 font-medium">{{ $day['day'] }} {{ $weekStart->copy()->addDays(array_search($day['day'], ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']))->format('d-m') }}</p>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Incomplete Tasks Card (Blue Border) -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border-2 border-blue-400 h-full relative">
                    <div class="absolute top-6 right-6 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>

                    <h3 class="text-gray-700 font-medium text-sm leading-tight mb-4">
                        Registro de tareas<br>incompletas
                    </h3>
                    
                    <p class="text-6xl font-bold text-gray-900 mb-8">{{ $incompleteTasks }}</p>
                    
                    @if($incompleteTasks == 0)
                        <p class="text-xs text-gray-400 mt-auto">Todas las tareas han sido completadas con éxito</p>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Comentarios</h3>
                <p class="text-xs text-gray-500 mb-4">
                    Semana del {{ $weekStart->format('Y-m-d') }} al {{ $weekEnd->format('Y-m-d') }}
                </p>
                
                @forelse($comments as $comment)
                    <div class="mb-2 p-3 bg-white rounded shadow-sm">
                        <p class="text-sm text-gray-600">{{ $comment }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic">
                        No hay comentarios sobre este profesional por los momentos
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
