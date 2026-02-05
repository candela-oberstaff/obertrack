<div x-show="isCreateModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="closeCreateModal()">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                        Crear nueva tarea
                    </h3>
                    <button @click="closeCreateModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('profesionales.tasks.store') }}" id="createTaskForm">
                    @csrf
                    
                    <h3 class="text-[#22A9C8] font-medium text-lg mb-6">Crea una tarea</h3>

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
                            <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border-none rounded-lg py-3 px-4 text-sm text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#22A9C8] focus:bg-white transition-colors">
                        </div>
                        <div class="relative">
                             <label class="block text-xs text-gray-500 mb-1 ml-1">Fecha fin</label>
                            <input type="date" name="end_date" required class="w-full bg-gray-50 border-none rounded-lg py-3 px-4 text-sm text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#22A9C8] focus:bg-white transition-colors">
                        </div>
                    </div>

                    <!-- Row 3: Assignees -->
                    <div class="mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 max-h-32 overflow-y-auto">
                            <p class="text-xs text-gray-500 mb-2 uppercase font-bold tracking-wider">Asigna a tus compañeros</p>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                    <input type="checkbox" name="assignees[]" value="{{ auth()->id() }}" checked class="rounded border-gray-300 text-[#22A9C8] focus:ring-[#22A9C8]">
                                    <span class="text-sm text-gray-700 font-bold">Yo ({{ auth()->user()->name }})</span>
                                </label>
                                @if(isset($profesionales))
                                    @foreach($profesionales as $colleague)
                                        <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                            <input type="checkbox" name="assignees[]" value="{{ $colleague->id }}" class="rounded border-gray-300 text-[#22A9C8] focus:ring-[#22A9C8]">
                                            <span class="text-sm text-gray-700">{{ $colleague->name }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <textarea name="description" rows="4" maxlength="2000" placeholder="Añade una descripción de la asignación" class="w-full bg-gray-50 border-none rounded-lg py-3 px-4 text-sm text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-[#22A9C8] focus:bg-white transition-colors resize-none"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center gap-4">
                        <button type="button" @click="closeCreateModal()" class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-10 rounded-full transition-colors shadow-sm">
                            Cancelar
                        </button>
                        <button type="submit" class="border border-[#22A9C8] text-[#0D1E4C] hover:bg-[#22A9C8] hover:text-white font-medium py-2 px-10 rounded-full transition-colors shadow-sm">
                            Crear tarea
                        </button>
                    </div>
                </form>
        </div>
    </div>
</div>
