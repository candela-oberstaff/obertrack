<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Asignaciones de Equipo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($tasks->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($tasks as $task)
                                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:scale-105">
                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">{{ $task->title }}</h3>
                                        <p class="text-gray-600 dark:text-gray-300 mb-4">{{ Str::limit($task->description, 100) }}</p>
                                        <div class="mt-4 mb-6 text-sm text-gray-600 dark:text-gray-400">
                                            Creada por: <span class="font-semibold">{{ $task->createdBy->name }}</span>
                                        </div>
                                        <div class="flex justify-between items-center mb-4">
                                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                                @if($task->priority == 'low') bg-green-200 text-green-800
                                                @elseif($task->priority == 'medium') bg-yellow-200 text-yellow-800
                                                @elseif($task->priority == 'high') bg-orange-200 text-orange-800
                                                @else bg-red-200 text-red-800
                                                @endif">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                                @if($task->completed) bg-blue-200 text-blue-800 @else bg-gray-200 text-gray-800 @endif">
                                                {{ $task->completed ? 'Completada' : 'En progreso' }}
                                            </span>
                                        </div>
                                        <a href="{{ route('empleados.tasks.show', $task) }}" class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                                            Ver detalles
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <h3 class="mt-2 text-xl font-medium text-gray-900 dark:text-gray-100">No hay tareas asignadas</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Parece que aún no se han asignado tareas al equipo.</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cuando se asignen tareas, aparecerán aquí.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>