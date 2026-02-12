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
        class="bg-white rounded-3xl overflow-hidden shadow-xl transform transition-all w-full max-w-5xl max-h-[90vh] flex flex-col"
    >
        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-end border-b border-gray-100 shrink-0">
            <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 p-8">
            <form action="{{ auth()->user()->is_manager ? route('manager.tasks.store') : route('empresa.tareas.store') }}" method="POST" id="createTaskForm">
                @csrf
                
                <h3 class="text-[#22A9C8] font-medium text-lg mb-6" x-text="isTeamTask ? 'Crea una tarea en equipo' : 'Crea una tarea para este profesional'"></h3>

                <!-- Row 1: Title & Priority -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <input type="text" name="title" required maxlength="255" placeholder="Título de la tarea" class="w-full bg-gray-50 border-none rounded-lg py-3 px-4 text-sm text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#22A9C8] focus:bg-white transition-colors">
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

                <!-- Row 3 & 4: Horizontal Layout for Assignees and Description -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    <!-- Assignees Column (Shown for Team Tasks) -->
                    <div class="lg:col-span-1" x-show="isTeamTask">
                        <div class="bg-gray-50 rounded-xl p-4 flex flex-col border border-gray-100 shadow-sm">
                            <p class="text-[10px] text-gray-500 mb-3 uppercase font-bold tracking-widest border-b border-gray-200 pb-2">Asigna a los profesionales</p>
                            
                            <!-- Mini Search for Assignees -->
                            <div class="mb-3 relative shrink-0">
                                <input 
                                    type="text" 
                                    x-model="searchAssignee" 
                                    placeholder="Buscar..." 
                                    class="w-full bg-white border border-gray-200 rounded-lg py-2 px-3 pl-8 text-xs text-gray-700 focus:ring-1 focus:ring-[#22A9C8] transition-all"
                                >
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="space-y-1.5 overflow-y-auto max-h-[250px] custom-scrollbar pr-1">
                                @forelse($profesionales as $emp)
                                    <label 
                                        x-show="!searchAssignee || '{{ strtolower($emp->name) }}'.includes(searchAssignee.toLowerCase())"
                                        class="flex items-center space-x-3 cursor-pointer hover:bg-white p-2 rounded-lg transition-colors border border-transparent hover:border-gray-100"
                                    >
                                        <input type="checkbox" name="assignees[]" value="{{ $emp->id }}" class="rounded border-gray-300 text-[#22A9C8] focus:ring-[#22A9C8]">
                                        <span class="text-xs text-gray-700">{{ $emp->name }}</span>
                                    </label>
                                @empty
                                    <div class="text-[10px] text-gray-500 italic p-2 bg-gray-100 rounded">
                                        No hay profesionales disponibles para asignar. 
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Description Column -->
                    <div :class="isTeamTask ? 'lg:col-span-2' : 'lg:col-span-3'">
                        <div class="h-full flex flex-col">
                            <label class="text-[10px] text-gray-500 mb-2 uppercase font-bold tracking-widest flex items-center gap-2">
                                <i class="fa fa-align-left"></i>
                                Descripción de la tarea
                            </label>
                            <x-tasks.rich-text-editor 
                                name="description" 
                                placeholder='Describe detalladamente el trabajo a realizar...'
                            />
                        </div>
                    </div>
                </div>

                <!-- INDIVIDUAL Selection (Hidden Input) -->
                <input type="hidden" name="assignees[]" :value="targetEmployeeId" x-bind:disabled="isTeamTask">

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button type="submit" id="compCreateTaskBtn" class="border border-[#22A9C8] text-[#0D1E4C] hover:bg-[#22A9C8] hover:text-white font-medium py-2 px-10 rounded-full transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="submit-text">Crear tarea</span>
                        <span class="loading-text hidden">Creando...</span>
                    </button>
                </div>
            </form>

            <script>
            document.getElementById('createTaskForm').onsubmit = function() {
                const btn = document.getElementById('compCreateTaskBtn');
                if (!btn || btn.disabled) return true; // Let the other script handle it if it's the professional form, or if already disabled
                
                btn.disabled = true;
                btn.querySelector('.submit-text').classList.add('hidden');
                btn.querySelector('.loading-text').classList.remove('hidden');
                return true;
            };
            </script>
        </div>
    </div>
</div>
