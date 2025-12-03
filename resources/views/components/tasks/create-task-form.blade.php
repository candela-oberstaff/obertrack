@props(['empleados'])

\u003cdiv x-data="{ activeTab: 'create' }" class="max-w-5xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-10"\u003e
    \u003c!-- Tabbed Interface --\u003e
    \u003cdiv class="flex border-b border-gray-200 dark:border-gray-700"\u003e
        \u003cbutton 
            @click="activeTab = 'create'" 
            :class="{'text-blue-600 bg-white dark:bg-gray-800 dark:text-blue-400 border-b-2 border-blue-500': activeTab === 'create', 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== 'create'}"
            class="flex-1 py-3 px-4 text-center font-medium"\u003e
            Crear Nueva Tarea
        \u003c/button\u003e
        \u003cbutton 
            @click="activeTab = 'filter'" 
            :class="{'text-blue-600 bg-white dark:bg-gray-800 dark:text-blue-400 border-b-2 border-blue-500': activeTab === 'filter', 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== 'filter'}"
            class="flex-1 py-3 px-4 text-center font-medium"\u003e
            Filtrar y Buscar
        \u003c/button\u003e
    \u003c/div\u003e

    \u003c!-- Create Task Form --\u003e
    \u003cdiv x-show="activeTab === 'create'" class="p-4"\u003e
        \u003cform action="{{ route('empleador.crear-tarea') }}" method="POST" class="space-y-4"\u003e
            @csrf
            \u003cdiv\u003e
                \u003clabel for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eTítulo de la Tarea\u003c/label\u003e
                \u003cinput type="text" id="title" name="title" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ingrese el título de la tarea"\u003e
            \u003c/div\u003e
            \u003cdiv\u003e
                \u003clabel for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eDescripción\u003c/label\u003e
                \u003ctextarea id="description" name="description" rows="3" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Describa los detalles de la tarea"\u003e\u003c/textarea\u003e
            \u003c/div\u003e
            \u003cdiv class="grid grid-cols-2 gap-4"\u003e
                \u003cdiv\u003e
                    \u003clabel for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eFecha de Inicio\u003c/label\u003e
                    \u003cinput type="date" id="start_date" name="start_date" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"\u003e
                \u003c/div\u003e
                \u003cdiv\u003e
                    \u003clabel for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eFecha de Finalización\u003c/label\u003e
                    \u003cinput type="date" id="end_date" name="end_date" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"\u003e
                \u003c/div\u003e
            \u003c/div\u003e
            \u003cdiv class="grid grid-cols-2 gap-4"\u003e
                \u003cdiv\u003e
                    \u003clabel for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003ePrioridad\u003c/label\u003e
                    \u003cselect id="priority" name="priority" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"\u003e
                        \u003coption value="" disabled selected\u003eSeleccione la prioridad\u003c/option\u003e
                        \u003coption value="low"\u003eBaja\u003c/option\u003e
                        \u003coption value="medium"\u003eMedia\u003c/option\u003e
                        \u003coption value="high"\u003eAlta\u003c/option\u003e
                        \u003coption value="urgent"\u003eUrgente\u003c/option\u003e
                    \u003c/select\u003e
                \u003c/div\u003e
                \u003cdiv\u003e
                    \u003clabel for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eAsignar a\u003c/label\u003e
                    \u003cselect id="employee_id" name="employee_id" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"\u003e
                        \u003coption value="" disabled selected\u003eSeleccione un empleado\u003c/option\u003e
                        @foreach($empleados as $empleado)
                            \u003coption value="{{ $empleado-\u003eid }}"\u003e{{ $empleado-\u003ename }}\u003c/option\u003e
                        @endforeach
                    \u003c/select\u003e
                \u003c/div\u003e
            \u003c/div\u003e
            \u003cdiv class="flex justify-end"\u003e
                \u003cbutton type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200"\u003e
                    Crear Tarea
                \u003c/button\u003e
            \u003c/div\u003e
        \u003c/form\u003e
    \u003c/div\u003e

    \u003c!-- Filter Tasks Form --\u003e
    \u003cdiv x-show="activeTab === 'filter'" class="p-4"\u003e
        \u003cform method="GET" action="{{ route('empleadores.tareas-asignadas') }}" class="space-y-4"\u003e
            \u003cdiv class="grid grid-cols-1 sm:grid-cols-3 gap-4"\u003e
                \u003cdiv\u003e
                    \u003clabel for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eEstado\u003c/label\u003e
                    \u003cselect name="status" id="status" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"\u003e
                        \u003coption value="all"\u003eTodas las tareas\u003c/option\u003e
                        \u003coption value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}\u003eCompletadas\u003c/option\u003e
                        \u003coption value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}\u003ePendientes\u003c/option\u003e
                    \u003c/select\u003e
                \u003c/div\u003e
                \u003cdiv class="sm:col-span-2"\u003e
                    \u003clabel for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eBuscar\u003c/label\u003e
                    \u003cinput type="text" id="search" name="search" placeholder="Buscar tarea..." value="{{ request('search') }}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"\u003e
                \u003c/div\u003e
            \u003c/div\u003e
            \u003cdiv class="flex justify-end"\u003e
                \u003cbutton type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-md transition duration-300 ease-in-out flex items-center justify-center"\u003e
                    \u003ci class="fas fa-search mr-2"\u003e\u003c/i\u003eBuscar
                \u003c/button\u003e
            \u003c/div\u003e
        \u003c/form\u003e
    \u003c/div\u003e
\u003c/div\u003e
