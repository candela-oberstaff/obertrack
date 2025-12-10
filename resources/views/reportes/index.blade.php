<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-900 leading-tight">
            Reportes de profesionales
        </h2>
        <p class="text-blue-500 font-medium mt-2">Profesionales registrados</p>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @forelse($professionals as $prof)
            <div class="bg-white rounded-[2rem] p-6 md:p-8 shadow-sm relative overflow-hidden">
                <div class="flex flex-col md:flex-row items-start gap-6 md:gap-8">
                    <!-- Number Badge with Blue Underline -->
                    <div class="flex-shrink-0 w-full md:w-auto flex justify-center md:block">
                        <div class="flex flex-col items-center">
                            <span class="text-6xl font-extrabold text-black tracking-tighter">
                                {{ str_pad($prof['index'], 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <div class="h-2 w-full bg-blue-500 mt-[-5px] z-0"></div> <!-- Thicker blue underline effect -->
                        </div>
                    </div>

                    <!-- Professional Info -->
                    <div class="flex-1 pt-2 w-full">
                        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $prof['name'] }}</h3>
                                <p class="text-blue-400 text-sm font-medium">{{ $prof['job_title'] }}</p>
                            </div>
                            
                            <div class="text-left md:text-right text-xs text-gray-500 space-y-1 w-full md:w-auto">
                                <p>Semana del {{ $weekStart->format('Y-m-d') }} al {{ $weekEnd->format('Y-m-d') }}</p>
                                <p>Comentarios: 0</p>
                            </div>
                        </div>
                        
                        <div class="mt-6 space-y-1 text-sm text-gray-700">
                            <p>
                                Promedio de horas semanales: <span class="font-normal">{{ $prof['weekly_average'] }}</span>
                            </p>
                            <p>
                                Ausencias registradas (con o sin justificación): <span class="font-normal">{{ $prof['absences'] }}</span>
                            </p>
                            <p>
                                Registro de tareas incompletas: <span class="font-normal">{{ $prof['incomplete_tasks'] }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
                    <div class="w-full md:w-auto">
                        <a href="{{ route('reportes.show', $prof['id']) }}" 
                           class="inline-block px-8 py-2.5 bg-[#0f172a] text-white text-sm font-medium rounded-full hover:bg-black transition shadow-lg w-full md:w-auto">
                            Ver reporte completo
                        </a>
                    </div>

                    <!-- Status Indicator -->
                    <div class="w-full md:w-auto flex justify-center md:justify-end">
                        @if($prof['has_pending_weeks'])
                            <p class="text-sm text-[#ef4444] italic flex items-center justify-center md:justify-start gap-2 font-medium">
                                Tiene semanas pendientes por aprobación
                                <svg class="w-5 h-5 border-2 border-[#ef4444] rounded-full p-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </p>
                        @else
                            <p class="text-sm text-[#22c55e] italic flex items-center justify-center md:justify-start gap-2 font-medium">
                                No hay semanas pendientes por aprobación
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <p class="text-gray-500">No hay profesionales registrados.</p>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
