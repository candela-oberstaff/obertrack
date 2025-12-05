@props(['empleados'])

<div x-data="{ activeTab: 'create' }" class="max-w-5xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-10">
    <!-- Tabbed Interface -->
    <div class="flex border-b border-gray-200 dark:border-gray-700">
        <button 
            @click="activeTab = 'create'" 
            :class="{'text-blue-600 bg-white dark:bg-gray-800 dark:text-blue-400 border-b-2 border-blue-500': activeTab === 'create', 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== 'create'}"
            class="flex-1 py-3 px-4 text-center font-medium transition-colors duration-200">
            Crear Nueva Tarea
        </button>
        <button 
            @click="activeTab = 'filter'" 
            :class="{'text-blue-600 bg-white dark:bg-gray-800 dark:text-blue-400 border-b-2 border-blue-500': activeTab === 'filter', 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== 'filter'}"
            class="flex-1 py-3 px-4 text-center font-medium transition-colors duration-200">
            Filtrar Tareas
        </button>
    </div>

    <!-- Create Task Form -->
    <div x-show="activeTab === 'create'" class="p-6">
        <form action="{{ route('empleador.crear-tarea') }}" method="POST" onsubmit="handleFormSubmit(this)">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título de la Tarea</label>
                    <input type="text" name="title" id="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asignar a</label>
                    <select name="employee_id" id="employee_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Seleccionar profesional...</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id }}">{{ $empleado->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                    <select name="priority" id="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="low">Baja</option>
                        <option value="medium" selected>Media</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Inicio</label>
                    <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Fin</label>
                    <input type="date" name="end_date" id="end_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm transition duration-150 ease-in-out flex items-center">
                    <span id="btnText">Crear Tarea</span>
                    <span id="loadingSpinner" class="hidden ml-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Filter Tasks Form -->
    <div x-show="activeTab === 'filter'" class="p-6">
        <form action="{{ route('empleador.tareas.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="all">Todas las tareas</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completadas</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar</label>
                    <div class="flex gap-2">
                        <input type="text" id="search" name="search" placeholder="Buscar por título..." value="{{ request('search') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <button type="submit" class="mt-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md shadow-sm transition duration-150 ease-in-out">
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
