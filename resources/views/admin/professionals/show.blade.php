<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.professionals') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Perfil de {{ $professional->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Professional Profile & History -->
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="flex items-start gap-6">
                    <x-user-avatar :user="$professional" size="24" class="w-32 h-32 text-4xl"/>
                    <div class="space-y-2">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $professional->name }}</h3>
                        <p class="text-gray-500">{{ $professional->email }}</p>
                        <div class="flex items-center gap-2 pt-2">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold uppercase tracking-wider">
                                Registrado el: {{ $professional->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4 border-l border-gray-100 pl-8">
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Relación Laboral</h4>
                    <div>
                        @if($professional->empleador)
                            <div class="text-xl font-bold text-gray-900">{{ $professional->empleador->company_name ?? $professional->empleador->name }}</div>
                            <div class="text-sm text-gray-500">Empresa Actual</div>
                        @else
                            <div class="text-xl font-bold text-gray-400 italic">Sin Empresa Asignada</div>
                        @endif
                    </div>
                    
                    <a href="{{ route('reportes.show', $professional->id) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#22A9C8] text-white rounded-xl font-bold shadow-lg shadow-cyan-500/30 hover:shadow-cyan-500/50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Ver Reporte Detallado
                    </a>
                </div>
            </div>

            <!-- Performance Stats -->
            <h3 class="text-xl font-bold text-gray-900 pt-4">Estadísticas de Desempeño</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Punctuality Card -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-lg font-bold text-gray-900">Puntualidad</div>
                            <div class="text-xs text-gray-400">Consistencia en registro de horas</div>
                        </div>
                        <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-purple-600 bg-purple-200">
                                    {{ $punctualityScore }}%
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-semibold inline-block text-purple-600">
                                    {{ $daysWithHours }} / {{ $totalWorkDays }} días laborales
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-purple-100">
                            <div style="width:{{ $punctualityScore }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-purple-500 transition-all duration-1000"></div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Completion Card -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                     <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-lg font-bold text-gray-900">Cumplimiento</div>
                            <div class="text-xs text-gray-400">Tareas completadas vs asignadas</div>
                        </div>
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                    </div>

                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-bold text-gray-900">{{ $completionRate }}%</span>
                        <span class="text-sm text-gray-400 mb-1">tasa de finalización</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-center">
                        <div class="bg-green-50 rounded-lg p-2">
                            <div class="text-xl font-bold text-green-600">{{ $completedTasks }}</div>
                            <div class="text-[10px] text-green-800 uppercase font-bold">Completadas</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2">
                            <div class="text-xl font-bold text-gray-600">{{ $pendingTasks }}</div>
                            <div class="text-[10px] text-gray-500 uppercase font-bold">Pendientes</div>
                        </div>
                    </div>
                </div>

                <!-- On-Time Delivery Card -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-lg font-bold text-gray-900">Entrega a Tiempo</div>
                            <div class="text-xs text-gray-400">Tareas terminadas antes de la fecha</div>
                        </div>
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                           <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <div class="flex items-center justify-center py-4">
                        <div class="relative w-24 h-24">
                            <svg class="w-full h-full" viewBox="0 0 36 36">
                                <path class="text-gray-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
                                <path class="text-emerald-500" stroke-dasharray="{{ $completedTasks > 0 ? ($onTimeTasks / $completedTasks) * 100 : 0 }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" />
                            </svg>
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
                                <span class="text-xl font-bold text-gray-900">{{ $onTimeTasks }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center text-xs text-gray-500">
                        Tareas a tiempo de {{ $completedTasks }} finalizadas
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
