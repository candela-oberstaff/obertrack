<div id="task-{{ $task->id }}" class="p-4 hover:bg-gray-50 transition duration-150 ease-in-out">
    <div class="flex items-center justify-between cursor-pointer" onclick="toggleTaskDetails({{ $task->id }})">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <span class="inline-block h-2 w-2 rounded-full {{ $task->completed ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ $task->title }}
                </p>
                <p class="text-sm text-gray-500 truncate">
                    {{ $task->start_date->format('d/m/Y') }} - {{ $task->end_date->format('d/m/Y') }}
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span id="status-badge-{{ $task->id }}" class="px-2 py-1 text-xs font-medium rounded-full {{ $task->completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $task->completed ? 'Completada' : 'Pendiente' }}
            </span>
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <div id="taskDetails-{{ $task->id }}" class="hidden mt-4 space-y-4">
        <div class="text-sm text-gray-700">
            <p>{{ $task->description }}</p>
            <p class="mt-2 text-xs text-gray-500">Asignado por: {{ $task->createdBy->name ?? 'N/A' }}</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="toggleTaskCompletion({{ $task->id }})" 
                    id="toggle-button-{{ $task->id }}" 
                    class="flex-1 px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ $task->completed ? 'Marcar como Pendiente' : 'Marcar como Completada' }}
            </button>
            <button onclick="toggleComments({{ $task->id }})" 
                    class="flex-1 px-3 py-1 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <span id="commentButtonText-{{ $task->id }}">Comentarios</span>
                <span id="commentCount-{{ $task->id }}" class="ml-1 bg-gray-500 text-white px-1.5 py-0.5 rounded-full text-xs">
                    {{ $task->comments->count() }}
                </span>
            </button>
        </div>
        
        <x-tasks.comment-section :task="$task" />
    </div>
</div>
