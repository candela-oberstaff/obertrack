@props(['tareasEmpleador']) 

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

{{-- ========================================================= --}}
{{-- SECCIÓN 1: ASIGNACIONES EN EQUIPO --}}
{{-- ========================================================= --}}
<div class="w-full bg-white rounded-xl shadow mb-10">

    <!-- HEADER -->
    <div class="flex justify-between items-center px-6 py-3 bg-white">
        <h2 class="text-lg font-semibold text-blue-700">Asignaciones en Equipo</h2>

        <button 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full shadow">
            Agregar tarea en equipo
        </button>
    </div>

    <!-- TABLA (manteniendo ejemplo estático) -->
    <div class="overflow-x-auto p-6">
        <table class="min-w-full border border-blue-500 rounded-lg ">
            <thead>
                <tr class="text-left text-sm text-black">
                    <th class="p-4">Título</th>
                    <th class="p-4">Fecha límite</th>
                    <th class="p-4">Asignado</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4">Comentarios</th>
                    <th class="p-4">Archivos</th>
                </tr>
            </thead>

            <tbody class="space-y-3">
                <tr class="bg-gray-100 rounded-lg">
                    <td class="p-4 font-semibold text-black">Revisión de proyecto</td>
                    <td class="p-4 text-black">{{ now()->addDays(5)->format('d/m/Y') }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Equipo+Demo" class="w-8 h-8 rounded-full">
                            <span class=" text-sm">Equipo Demo</span>
                        </div>
                    </td>
                    <td class="p-4">
                        <div x-data="{ estado: 'En proceso', open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" :class="{
                                'bg-green-400 text-white': estado === 'Finalizado',
                                'bg-yellow-300 text-white': estado === 'En proceso',
                                'bg-red-400 text-white': estado === 'Por hacer'
                            }" class="rounded-full px-4 py-1 w-full font-semibold cursor-pointer focus:outline-none">
                                <span x-text="estado"></span>
                                <svg class="inline-block ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute mt-1 w-full bg-white shadow-lg rounded-md z-10">
                                <template x-for="option in ['Finalizado','En proceso','Por hacer']">
                                    <div @click="estado = option; open=false" class="px-4 py-2 text-black cursor-pointer hover:bg-gray-100" x-text="option"></div>
                                </template>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <span class="text-sm  px-2 py-1 rounded">
                        <i class="bi bi-chat-left-text"></i>    
                        2</span>
                    </td>
                    <td class="p-4">
                        <span class="text-sm  px-2 py-1 rounded">
                        <i class="bi bi-paperclip"></i>    
                        3</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ========================================================= --}}
{{-- SECCIÓN 2: ASIGNACIONES INDIVIDUALES--}}
{{-- ========================================================= --}}
<div class="w-full bg-white rounded-xl shadow mb-10 p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-black">Asignaciones Individuales</h2>

        <!-- Botón abre modal -->
        <button 
            onclick="window.dispatchEvent(new CustomEvent('open-individual-task-modal'))"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full shadow">
            Agregar tarea
        </button>
    </div>

    <!-- Lista por profesional - DINÁMICO -->
    <div class="space-y-8">
        @php
            $grouped = $tareasEmpleador->groupBy(function($t){
                return optional($t->visibleTo)->name ?? 'Sin asignar';
            });
        @endphp

        @forelse($grouped as $profesionalName => $tareas)
            <div>
                <div class="px-4 py-2 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @php $first = $tareas->first(); @endphp
                        @if(optional($first->visibleTo)->avatar)
                            <img src="{{ $first->visibleTo->avatar }}" alt="{{ $profesionalName }}" class="w-10 h-10 rounded-full">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($profesionalName) }}&background=ffffff&color=0d6efd" alt="{{ $profesionalName }}" class="w-10 h-10 rounded-full">
                        @endif
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-blue-700">{{ $profesionalName }}</h3>
                            @if(optional($first->visibleTo)->job_title ?? false)
                                <p class="text-sm text-blue-700">{{ $first->visibleTo->job_title }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="text-sm text-gray-600">
                        <span class="font-medium">{{ $tareas->count() }}</span> tareas
                    </div>
                </div>

                <div class="bg-gray-100 p-4 rounded-lg space-y-3">
                    <table class="min-w-full bg-gray-100 rounded-lg">
                        <thead>
                            <tr class="text-left text-sm text-black">
                                <th class="p-4">Título</th>
                                <th class="p-4">Fecha límite</th>
                                <th class="p-4">Asignado</th>
                                <th class="p-4">Estado</th>
                                <th class="p-4">Comentarios</th>
                                <th class="p-4">Archivos</th>
                            </tr>
                        </thead>

                        <tbody class="space-y-3">
                            @foreach($tareas as $tarea)
                                <tr id="task-row-{{ $tarea->id }}" class="bg-white border border-blue-300 rounded-lg p-4">
                                    <td class="p-4 font-semibold text-black">
                                        <div class="flex items-center gap-2">
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-800">{{ $tarea->title }}</h4>
                                                @if($tarea->priority ?? false)
                                                    <span class="priority-badge priority-{{ $tarea->priority }} inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium">
                                                        {{ ucfirst($tarea->priority) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="p-4 text-black">
                                        @if($tarea->end_date ?? false)
                                            {{ \Carbon\Carbon::parse($tarea->end_date)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            @if(optional($tarea->visibleTo)->avatar)
                                                <img src="{{ $tarea->visibleTo->avatar }}" class="w-8 h-8 rounded-full">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode(optional($tarea->visibleTo)->name ?? 'Usuario') }}" class="w-8 h-8 rounded-full">
                                            @endif
                                            <span class="text-sm text-blue-700">{{ optional($tarea->visibleTo)->name ?? 'Sin asignar' }}</span>
                                        </div>
                                    </td>

                                    <td class="p-4">
                                        <div x-data="{ open: false, estado: '{{ $tarea->completed ? 'Finalizado' : 'En proceso' }}' }" class="relative inline-block text-left">
                                            <button @click="open = !open" :class="{
                                                'bg-green-400 text-white': estado === 'Finalizado',
                                                'bg-yellow-300 text-white': estado === 'En proceso',
                                                'bg-red-400 text-white': estado === 'Por hacer'
                                            }" class="rounded-full px-4 py-1 w-full font-semibold focus:outline-none">
                                                <span x-text="estado"></span>
                                                <svg class="inline-block ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            <div x-show="open" @click.away="open = false" class="absolute mt-1 w-full bg-white shadow-lg rounded-md z-10">
                                                <template x-for="option in ['Finalizado','En proceso','Por hacer']">
                                                    <div @click="estado = option; open=false" class="px-4 py-2 text-black cursor-pointer hover:bg-gray-100" x-text="option"></div>
                                                </template>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="p-4">
                                        <span class="text-sm px-2 py-1 rounded">
                                            <i class="bi bi-chat-left-text"></i> {{ optional($tarea->comments)->count() ?? 0 }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="text-sm px-2 py-1 rounded">
                                            <i class="bi bi-paperclip"></i> {{ optional($tarea->archivos)->count() ?? 0 }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <i class="fas fa-tasks text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay tareas asignadas</h3>
                <p class="text-gray-600">Comienza a crear tareas para tus profesionales y mejora la productividad.</p>
            </div> 
        @endforelse
    </div>
</div>

{{-- PRIORITY BADGE STYLES  --}}
<style>
    .priority-badge {
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .priority-low { background-color: #E5F6FD; color: #0369A1; }
    .priority-medium { background-color: #FEF3C7; color: #92400E; }
    .priority-high { background-color: #FEE2E2; color: #B91C1C; }
    .priority-urgent { background-color: #FECACA; color: #7F1D1D; }
</style>


