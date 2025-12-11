@props(['empleados'])

<!--FORMULARIO TAREA COMPACTO FUNCIONAL-->
<div x-data="{ activeTab: 'create' }" class="max-w-3xl mx-auto bg-white rounded-xl overflow-hidden p-6 space-y-4">

 

    <!-- Create Task Form -->
    <div x-show="activeTab === 'create'">
        <form action="{{ route('empleador.crear-tarea') }}" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmit(this)" class="space-y-2">
            @csrf

            <!-- Buscador superior (solo visual) -->
            <div>
                <input type="text" placeholder="Buscar tareas" class="w-full bg-gray-200 rounded-full p-1.5 border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Título y asignar a -->
            <div class="flex flex-col md:flex-row md:items-center md:gap-3">
                <h2 class="text-blue-700 font-semibold text-base flex-shrink-0">Crea una tarea para este profesional:</h2>
                <select name="employee_id" id="employee_id" required class="mt-2 md:mt-0 bg-gray-200 rounded-lg p-1.5 border-none flex-1 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Seleccionar profesional...</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->id }}">{{ $empleado->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Título y prioridad -->
            <div class="flex flex-col md:flex-row gap-2">
                <input type="text" name="title" id="title" placeholder="Título de la tarea" required class="flex-[0.6] bg-gray-200 rounded-lg p-1.5 border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                <select name="priority" id="priority" class="flex-[0.4] bg-gray-200 rounded-lg p-1.5 border-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="" disabled selected>Seleccionar prioridad</option>
                    <option value="low">Baja</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                </select>
            </div>

            <!-- Fecha inicio y fin -->
            <div class="flex flex-col md:flex-row gap-2">
                <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-gray-200 rounded-lg p-1.5 border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                <input type="date" name="end_date" id="end_date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-gray-200 rounded-lg p-1.5 border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Descripción -->
            <div>
                <textarea name="description" id="description" rows="3" placeholder="Descripción de la asignación" class="w-full bg-gray-200 rounded-lg p-1.5 border-none focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
            </div>

     <!-- Archivos adjuntos -->
                <div class="col-span-2">
                    <label for="attachments" class="block text-sm font-medium text-gray-200 dark:text-gray-300">
                        Archivos Adjuntos (opcional)
                    </label>
                    <input 
                        type="file" 
                        name="attachments[]" 
                        id="attachments"
                        multiple 
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png"
                        class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-medium
                            file:bg-blue-500 file:text-white
                           
                            hover:file:bg-blue-600
                           
                    >
                    <p class="mt-1 text-xs text-gray-200 dark:text-gray-400"> 
                        Puedes subir múltiples archivos (PDF, Word, Excel, imágenes). Máximo 10MB por archivo.
                    </p>
                </div>
            </div>



            <!-- Botón Crear -->
            <div class="flex justify-center mt-4">
                <button type="submit" id="submitBtn" class="px-6 py-2 rounded-full border border-blue-500 bg-white text-black font-semibold hover:bg-blue-50 transition flex items-center">
                    <span id="btnText">Crear Tarea</span>
                    <span id="loadingSpinner" class="hidden ml-2">
                        <svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- Filter Tasks Form (comentado por ahora) --}}
    {{-- <div x-show="activeTab === 'filter'" class="p-6">
        <form action="{{ route('empleador.tareas.index') }}" method="GET">
            ...
        </form>
    </div> --}}

</div>




<script>
    function handleFormSubmit(form) {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const spinner = document.getElementById('loadingSpinner');

        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btnText.textContent = 'Creando...';
        spinner.classList.remove('hidden');
    }
</script>
@props(['empleados'])