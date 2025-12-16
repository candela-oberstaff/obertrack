<!-- Create Task Modal -->
<div 
    x-show="isCreateModalOpen" 
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0"
>
    <!-- Backdrop -->
    <div 
        x-show="isCreateModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 transform transition-all"
        @click="isCreateModalOpen = false"
    >
        <div class="absolute inset-0 bg-gray-600 opacity-50"></div>
    </div>

    <!-- Modal Content -->
    <div 
        x-show="isCreateModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="bg-white rounded-3xl overflow-hidden shadow-xl transform transition-all w-full max-w-2xl max-h-[90vh] flex flex-col"
    >
        <!-- Header -->
        <div class="px-6 py-4 flex items-center gap-2 border-b border-gray-100 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" placeholder="Buscar tareas" class="w-full border-none focus:ring-0 text-sm text-gray-600 placeholder-gray-400">
            <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 p-8">
            <form action="{{ route('empleador.tareas.store') }}" method="POST" id="createTaskForm">
                @csrf
                
                <h3 class="text-[#22A9C8] font-medium text-lg mb-6" x-text="isTeamTask ? 'Crea una tarea en equipo' : 'Crea una tarea para este profesional'"></h3>

                <!-- Row 1: Title & Priority -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <input type="text" name="title" required placeholder="Título de la tarea" class="w-full bg-gray-50 border-none rounded-lg py-3 px-4 text-sm text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#22A9C8] focus:bg-white transition-colors">
                    </div>
                    <div>
                        <select name="priority" required class="w-full bg-gray-50 border-none rounded-lg py-3 px-4 text-sm text-gray-700 focus:ring-2 focus:ring-[#22A9C8] focus:bg-white transition-colors">
                            <option value="" disabled selected>Selecciona una prioridad</option>
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="relative">
                        <label class="block text-xs text-gray-500 mb-1 ml-1">Fecha inicio</label>
                        <input type="date" name="start_date" required class="w-full bg-gray-50 border-none rounded-lg py-3 px-4 text-sm text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#22A9C8] focus:bg-white transition-colors">
                    </div>
                    <div class="relative">
                         <label class="block text-xs text-gray-500 mb-1 ml-1">Fecha fin</label>
                        <input type="date" name="end_date" required class="w-full bg-gray-50 border-none rounded-lg py-3 px-4 text-sm text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#22A9C8] focus:bg-white transition-colors">
                    </div>
                </div>

                <!-- Row 3: Assignees (Conditional) -->
                <!-- TEAM Selection -->
                <div class="mb-6" x-show="isTeamTask">
                    <div class="bg-gray-50 rounded-lg p-4 max-h-32 overflow-y-auto">
                        <p class="text-xs text-gray-500 mb-2 uppercase font-bold tracking-wider">Asigna a los profesionales</p>
                        <div class="space-y-2">
                            @foreach($employees as $emp)
                                <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                    <!-- Use name='assignees[]' only if team task is visible/active? Actually, submitting empty array if not team task is fine, but we need strict separation -->
                                    <input type="checkbox" name="assignees[]" value="{{ $emp->id }}" class="rounded border-gray-300 text-[#22A9C8] focus:ring-[#22A9C8]">
                                    <span class="text-sm text-gray-700">{{ $emp->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- INDIVIDUAL Selection (Hidden Input) -->
                <!-- We insert this input ONLY if !isTeamTask to avoid conflict or duplicate names. x-if works on template, but simple x-show with Disabled attribute is safer for form submission -->
                <input type="hidden" name="assignees[]" :value="targetEmployeeId" x-bind:disabled="isTeamTask">

                <!-- Description -->
                <div class="mb-8">
                    <textarea name="description" rows="4" placeholder="Añade una descripción de la asignación" class="w-full bg-gray-50 border-none rounded-lg py-3 px-4 text-sm text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#22A9C8] focus:bg-white transition-colors resize-none"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button type="submit" class="border border-[#22A9C8] text-[#0D1E4C] hover:bg-[#22A9C8] hover:text-white font-medium py-2 px-10 rounded-full transition-colors shadow-sm">
                        Crear tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
