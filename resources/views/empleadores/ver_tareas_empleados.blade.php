<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Control de Empresas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- Tasks Header -->
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Gestión de Tareas</h2>
            </div>

            <!-- Task Creation and Filtering -->
            <x-tasks.create-task-form :empleados="$empleados" />

            <!-- Task List -->
            <x-tasks.task-list :tareasEmpleador="$tareasEmpleador" />

        </div>
    </div>

    @vite([
        'resources/js/task-management.js'
    ])
    <x-layout.footer />
</x-app-layout>