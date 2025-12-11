@props([])
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

    <!-- TABLA -->
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
                {{-- Ejemplo estático --}}
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
{{-- SECCIÓN 2: ASIGNACIONES INDIVIDUALES --}}
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

    <!-- Lista por profesional -->
    <div class="space-y-8">
        {{-- Profesional de prueba --}}
        <div>
            <div class="px-4 py-2">
                <h3 class="font-bold text-blue-700">Juan Pérez - Diseñador</h3>
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
                        {{-- Ejemplo estático --}}
                        <tr class="bg-white border border-blue-300 rounded-lg p-4">
                            <td class="p-4 font-semibold text-black">Actualizar documentación</td>
                            <td class="p-4 text-black">{{ now()->addDays(3)->format('d/m/Y') }}</td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=Juan+Pérez" class="w-8 h-8 rounded-full">
                                    <span class=" text-sm">Juan Pérez</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <div x-data="{ estado: 'Finalizado', open: false }" class="relative inline-block text-left">
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
                                1</span>
                            </td>
                            <td class="p-4">
                                <span class="text-sm  px-2 py-1 rounded">
                                <i class="bi bi-paperclip"></i>    
                                5</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



