
<style>
    .task-card {
        background-color: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .task-card:hover {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .task-header {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .task-body {
        padding: 1rem;
    }

    .task-footer {
        padding: 1rem;
        background-color: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }

    .priority-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        display: inline-block;
    }

    .priority-urgent { background-color: #FEE2E2; color: #991B1B; }
    .priority-high { background-color: #FFEDD5; color: #9A3412; }
    .priority-medium { background-color: #FEF9C3; color: #854D0E; }
    .priority-low { background-color: #DBEAFE; color: #1E40AF; }

    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
    }

    .status-badge::before {
        content: '';
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    .status-completed {
        background-color: #D1FAE5;
        color: #065F46;
    }

    .status-completed::before {
        background-color: #10B981;
    }

    .status-in-progress {
        background-color: #FEF3C7;
        color: #92400E;
    }

    .status-in-progress::before {
        background-color: #F59E0B;
    }

    .task-button {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .task-button:hover {
        transform: translateY(-1px);
    }

    .toggle-status-button-in-progress {
        background-color: #F59E0B;
        color: white;
    }

    .toggle-status-button-in-progress:hover {
        background-color: #D97706;
    }

    .toggle-status-button-completed {
        background-color: #10B981;
        color: white;
    }

    .toggle-status-button-completed:hover {
        background-color: #059669;
    }

    .edit-form {
        background-color: #f3f4f6;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .comments-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
</style>



<!-- <div class="bg-gray-100 min-h-screen">
    <div class="max-w-9xl mx-auto px-4 sm:px-6">
        <div class="bg-gray-100 min-h-screen py-8">
            <div class="container mx-auto px-4 sm:px-6">
                <h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center">
                    <span class="bg-clip-text text-transparent bg-blue-500">
                        Asignaciones de
                    </span>
                    <br>
                    <span class="text-2xl font-bold text-gray-700">
                       {{ $empleador ? $empleador->name : 'No asignado' }}
                    </span>
                </h1>

                @if(session('success'))
                    <div class="bg-green-500 text-white p-4 rounded-lg mb-8 shadow-lg animate-pulse max-w-3xl mx-auto">
                        <p class="font-semibold text-center">{{ session('success') }}</p>
                    </div>
                @endif

                @if(count($tareasEmpleador) > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                        @foreach($tareasEmpleador as $tarea)
                            <div id="task-{{ $tarea->id }}" class="bg-white shadow-xl rounded-lg overflow-hidden transform transition duration-500 hover:scale-105">
                                <div class="bg-blue-500 p-4">
                                    <h5 class="text-xl font-bold text-white mb-2">{{ $tarea->title }}</h5>
                                    <h6 class="text-sm text-indigo-100 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path></svg>
                                        Asignado por: {{ $tarea->createdBy->name ?? 'N/A' }}
                                    </h6>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-700 mb-4 text-sm">{{ Str::limit($tarea->description, 100) }}</p>
                                    <div class="flex justify-between items-center text-xs text-gray-600 mb-4">
                                        <span class="flex items-center bg-indigo-100 px-2 py-1 rounded-full">
                                            <svg class="w-3 h-3 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $tarea->start_date->format('d/m/Y') }}
                                        </span>
                                        <span class="flex items-center bg-red-100 px-2 py-1 rounded-full">
                                            <svg class="w-3 h-3 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $tarea->end_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-xs font-semibold text-gray-700">Estado:</span>
                                        <span id="status-badge-{{ $tarea->id }}" class="px-3 py-1 text-xs font-bold rounded-full {{ $tarea->completed ? 'bg-green-500 text-white' : 'bg-yellow-400 text-gray-800' }}">
                                            {{ $tarea->completed ? 'Completada' : 'Pendiente' }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <button onclick="toggleTaskCompletion({{ $tarea->id }})" 
                                                id="toggle-button-{{ $tarea->id }}" 
                                                class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl text-sm font-semibold">
                                            {{ $tarea->completed ? 'Marcar como Pendiente' : 'Marcar como Completada' }}
                                        </button>
                                        <button onclick="toggleComments({{ $tarea->id }})" 
                                                class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-300 ease-in-out flex items-center justify-center text-sm font-semibold">
                                            <span id="commentButtonText-{{ $tarea->id }}">Mostrar Comentarios</span>
                                            <span id="commentCount-{{ $tarea->id }}" class="ml-2 bg-indigo-500 text-white px-2 py-1 rounded-full text-xs">
                                                {{ $tarea->comments->count() }}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div id="commentsSection-{{ $tarea->id }}" class="hidden bg-gray-50 p-4 border-t border-gray-200">
                                    <h4 class="text-lg font-semibold mb-4 text-indigo-700">Comentarios</h4>
                                    <div id="commentsList-{{ $tarea->id }}" class="space-y-3">
                                        @foreach ($tarea->comments as $comment)
                                            <div id="comment-{{ $comment->id }}" class="bg-white p-3 rounded-lg shadow-md">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <p class="text-xs text-gray-800">
                                                            <span class="font-medium text-indigo-600">{{ $comment->user->name }}:</span> 
                                                            <span id="commentContent-{{ $comment->id }}">{{ $comment->content }}</span>
                                                        </p>
                                                        <small class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    @if($comment->user_id == auth()->id())
                                                        <div class="flex space-x-1">
                                                            <button onclick="editComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 transition duration-300">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                            </button>
                                                            <button onclick="deleteComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 transition duration-300">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <form onsubmit="addTaskComment(event, {{ $tarea->id }})" class="mt-4">
                                        @csrf
                                        <textarea id="newComment-{{ $tarea->id }}" rows="2" class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none" placeholder="Añadir un comentario..."></textarea>
                                        <button type="submit" class="mt-2 w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-black transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-xl text-sm font-semibold">
                                            Comentar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white shadow-xl rounded-lg overflow-hidden p-8 text-center">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="mt-2 text-xl font-medium text-gray-900">No hay tareas asignadas</h3>
                        <p class="mt-1 text-sm text-gray-500">Parece que aún no tienes tareas asignadas.</p>
                        <p class="mt-1 text-sm text-gray-500">Cuando se te asignen tareas, aparecerán aquí.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> -->


<div class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            Asignaciones de <span class="text-blue-600">{{ $empleador ? $empleador->name : 'No asignado' }}</span>
        </h1>

        @if(session('success'))
            <div class="bg-green-500 text-white p-2 rounded-md mb-4 text-center text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="border-b border-gray-200">
                <!-- <button onclick="toggleFilters()" class="w-full px-4 py-3 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <span class="flex items-center justify-between">
                        <span>Filtros y Búsqueda</span>
                        <svg id="filterIcon" class="h-5 w-5 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>
                <div id="filterSection" class="hidden px-4 py-3 border-t border-gray-200">
                    <form method="GET" action="{{ route('empleadores.tareas-asignadas') }}" class="space-y-3">
                        <div class="flex space-x-3">
                            <select name="status" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="all">Todas las tareas</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completadas</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
                            </select>
                            <input type="text" name="search" placeholder="Buscar tarea..." value="{{ request('search') }}" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Aplicar Filtros
                        </button>
                    </form>
                </div> -->
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($tareasEmpleador as $tarea)
                    <div id="task-{{ $tarea->id }}" class="p-4 hover:bg-gray-50 transition duration-150 ease-in-out">
                        <div class="flex items-center justify-between cursor-pointer" onclick="toggleTaskDetails({{ $tarea->id }})">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="inline-block h-2 w-2 rounded-full {{ $tarea->completed ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $tarea->title }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span id="status-badge-{{ $tarea->id }}" class="px-2 py-1 text-xs font-medium rounded-full {{ $tarea->completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $tarea->completed ? 'Completada' : 'Pendiente' }}
                                </span>
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <div id="taskDetails-{{ $tarea->id }}" class="hidden mt-4 space-y-4">
                            <div class="text-sm text-gray-700">
                                <p>{{ $tarea->description }}</p>
                                <p class="mt-2 text-xs text-gray-500">Asignado por: {{ $tarea->createdBy->name ?? 'N/A' }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="toggleTaskCompletion({{ $tarea->id }})" 
                                        id="toggle-button-{{ $tarea->id }}" 
                                        class="flex-1 px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ $tarea->completed ? 'Marcar como Pendiente' : 'Marcar como Completada' }}
                                </button>
                                <button onclick="toggleComments({{ $tarea->id }})" 
                                        class="flex-1 px-3 py-1 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <span id="commentButtonText-{{ $tarea->id }}">Comentarios</span>
                                    <span id="commentCount-{{ $tarea->id }}" class="ml-1 bg-gray-500 text-white px-1.5 py-0.5 rounded-full text-xs">
                                        {{ $tarea->comments->count() }}
                                    </span>
                                </button>
                            </div>
                            <div id="commentsSection-{{ $tarea->id }}" class="hidden space-y-3">
                                <div id="commentsList-{{ $tarea->id }}" class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach ($tarea->comments as $comment)
                                        <div id="comment-{{ $comment->id }}" class="bg-gray-50 p-2 rounded-md text-sm">
                                            <div class="flex items-start justify-between">
                                                <div class="space-y-1">
                                                    <p>
                                                        <span class="font-medium text-gray-900">{{ $comment->user->name }}:</span>
                                                        <span id="commentContent-{{ $comment->id }}" class="text-gray-700">{{ $comment->content }}</span>
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                                </div>
                                                @if($comment->user_id == auth()->id())
                                                    <div class="flex space-x-1">
                                                        <button onclick="editComment({{ $comment->id }})" class="text-blue-600 hover:text-blue-800">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </button>
                                                        <button onclick="deleteComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-600 hover:text-red-800">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <form onsubmit="addTaskComment(event, {{ $tarea->id }})" class="mt-2">
                                    @csrf
                                    <textarea id="newComment-{{ $tarea->id }}" rows="2" class="w-full px-3 py-2 text-sm border rounded-md resize-none focus:ring-blue-500 focus:border-blue-500" placeholder="Añadir un comentario..."></textarea>
                                    <button type="submit" class="mt-2 w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Comentar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay tareas asignadas</h3>
                        <p class="mt-1 text-sm text-gray-500">Cuando se te asignen tareas, aparecerán aquí.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function toggleFilters() {
    const filterSection = document.getElementById('filterSection');
    const filterIcon = document.getElementById('filterIcon');
    filterSection.classList.toggle('hidden');
    filterIcon.classList.toggle('rotate-180');
}

function toggleTaskDetails(taskId) {
    const detailsElement = document.getElementById(`taskDetails-${taskId}`);
    detailsElement.classList.toggle('hidden');
}

function toggleComments(taskId) {
    const commentsSection = document.getElementById(`commentsSection-${taskId}`);
    const buttonText = document.getElementById(`commentButtonText-${taskId}`);
    commentsSection.classList.toggle('hidden');
    buttonText.textContent = commentsSection.classList.contains('hidden') ? 'Comentarios' : 'Ocultar';
}

// Existing functions (toggleTaskCompletion, addTaskComment, editComment, deleteComment) remain unchanged
</script>




<script>
    function toggleTaskCompletion(taskId) {
    const toggleButton = document.getElementById(`toggle-button-${taskId}`);
    const statusBadge = document.getElementById(`status-badge-${taskId}`);
    
    if (toggleButton && statusBadge) {
        const isCompleted = toggleButton.textContent.includes('Marcar como Pendiente');
        
        fetch(`/empleador/tareas/${taskId}/toggle-completion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ completed: !isCompleted })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (isCompleted) {
                    toggleButton.textContent = 'Marcar como Completada';
                    toggleButton.classList.remove('bg-red-500', 'hover:bg-red-600');
                    toggleButton.classList.add('bg-green-500', 'hover:bg-green-600');
                    statusBadge.textContent = 'Pendiente';
                    statusBadge.classList.remove('bg-green-500');
                    statusBadge.classList.add('bg-gray-300');
                } else {
                    toggleButton.textContent = 'Marcar como Pendiente';
                    toggleButton.classList.remove('bg-green-500', 'hover:bg-green-600');
                    toggleButton.classList.add('bg-red-500', 'hover:bg-red-600');
                    statusBadge.textContent = 'Completada';
                    statusBadge.classList.remove('bg-gray-300');
                    statusBadge.classList.add('bg-green-500');
                }
                showAlert('Estado de la tarea actualizado con éxito', 'success');
            } else {
                showAlert('Error al actualizar el estado de la tarea', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al actualizar el estado de la tarea', 'error');
        });
    } else {
        console.error(`Toggle button or status badge not found for task ${taskId}`);
    }
}

function addTaskComment(event, taskId) {
    event.preventDefault();
    const newCommentTextarea = document.getElementById(`newComment-${taskId}`);
    if (newCommentTextarea) {
        const commentContent = newCommentTextarea.value;
        
        if (commentContent.trim() === '') {
            showAlert('Por favor, escribe un comentario antes de enviarlo.', 'error');
            return;
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/empleador/tareas/${taskId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: commentContent })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Comentario agregado con éxito');
                const commentsList = document.getElementById(`commentsList-${taskId}`);
                const newComment = createCommentHTML(data.comment);
                commentsList.insertAdjacentHTML('beforeend', newComment);
                showAlert(data.message, 'success');
                newCommentTextarea.value = '';
                updateCommentCount(taskId, 1);
            } else {
                throw new Error(data.message || 'Error al agregar el comentario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert(error.message, 'error');
        });
    } else {
        console.error(`New comment textarea not found for task ${taskId}`);
    }
}

function editComment(commentId) {
    const commentContent = document.getElementById(`commentContent-${commentId}`);
    if (commentContent) {
        const currentContent = commentContent.textContent;
        const textarea = document.createElement('textarea');
        textarea.value = currentContent;
        textarea.classList.add('w-full', 'p-2', 'border', 'rounded', 'mt-2');
        
        const saveButton = document.createElement('button');
        saveButton.textContent = 'Guardar';
        saveButton.classList.add('mt-2', 'px-4', 'py-2', 'bg-blue-500', 'text-white', 'rounded', 'hover:bg-blue-600');
        
        saveButton.onclick = function() {
            updateComment(commentId, textarea.value);
        };
        
        commentContent.parentNode.insertBefore(textarea, commentContent.nextSibling);
        textarea.parentNode.insertBefore(saveButton, textarea.nextSibling);
        commentContent.style.display = 'none';
    } else {
        console.error(`Comment content not found for comment ${commentId}`);
    }
}

function updateComment(commentId, newContent) {
    const taskId = document.getElementById(`comment-${commentId}`).closest('[id^="task-"]').id.split('-')[1];
    fetch(`/empleador/tareas/${taskId}/comments/${commentId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ content: newContent })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const commentElement = document.getElementById(`comment-${commentId}`);
            const commentContent = document.getElementById(`commentContent-${commentId}`);
            if (commentContent) {
                commentContent.textContent = newContent;
                commentContent.style.display = 'block';
                const textarea = commentElement.querySelector('textarea');
                const saveButton = commentElement.querySelector('button');
                if (textarea) textarea.remove();
                if (saveButton) saveButton.remove();
                showAlert('Comentario actualizado con éxito', 'success');
            }
        } else {
            showAlert('Error al actualizar el comentario', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al actualizar el comentario', 'error');
    });
}

function deleteComment(commentId, taskId) {
    if (confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
        fetch(`/empleador/tareas/${taskId}/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentElement = document.getElementById(`comment-${commentId}`);
                if (commentElement) {
                    commentElement.remove();
                    showAlert('Comentario eliminado con éxito', 'success');
                    updateCommentCount(taskId, -1);
                }
            } else {
                showAlert('Error al eliminar el comentario', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al eliminar el comentario', 'error');
        });
    }
}

function toggleComments(taskId) {
    const commentsSection = document.getElementById(`commentsSection-${taskId}`);
    const commentButtonText = document.getElementById(`commentButtonText-${taskId}`);
    if (commentsSection && commentButtonText) {
        if (commentsSection.classList.contains('hidden')) {
            commentsSection.classList.remove('hidden');
            commentButtonText.textContent = 'Ocultar Comentarios';
        } else {
            commentsSection.classList.add('hidden');
            commentButtonText.textContent = 'Mostrar Comentarios';
        }
    } else {
        console.error(`Comments section or button not found for task ${taskId}`);
    }
}

function showEditFields(taskId) {
    const editForm = document.getElementById(`editForm${taskId}`);
    if (editForm) {
        editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
    } else {
        console.error(`Edit form not found for task ${taskId}`);
    }
}

function createCommentHTML(comment) {
    return `
        <div id="comment-${comment.id}" class="bg-white dark:bg-gray-600 p-4 rounded-lg shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex-grow">
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        <span class="font-medium text-indigo-600 dark:text-indigo-400">${comment.user.name}:</span> 
                        <span id="commentContent-${comment.id}">${comment.content}</span>
                    </p>
                    <small class="text-xs text-gray-500 dark:text-gray-400">${new Date(comment.created_at).toLocaleString()}</small>
                </div>
                <div class="flex space-x-2">
                    <button onclick="editComment(${comment.id})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs transition duration-150 ease-in-out">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button onclick="deleteComment(${comment.id}, ${comment.task_id})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs transition duration-150 ease-in-out">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    `;
}

function updateCommentCount(taskId, change) {
    const commentCount = document.getElementById(`commentCount-${taskId}`);
    if (commentCount) {
        let currentCount = parseInt(commentCount.textContent);
        currentCount += change;
        commentCount.textContent = currentCount;
    }
}

function showAlert(message, type) {
    const alertElement = document.createElement('div');
    alertElement.className = `fixed top-4 right-4 p-4 rounded-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
    alertElement.textContent = message;
    document.body.appendChild(alertElement);

    setTimeout(() => {
        alertElement.remove();
    }, 3000);
}

// Función para inicializar los eventos después de cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Aquí puedes agregar cualquier inicialización adicional que necesites
    console.log('DOM fully loaded and parsed');
});

</script>



<!-- </div>
        <h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center mt-10">
            <span class="bg-clip-text text-transparent bg-blue-500">
                Actividades Reportadas
            </span>
        </h1>
        <div class="space-y-6">
            @foreach($tareas as $tarea)
                @if($tarea->created_by == auth()->id())
                    <div class="task-card bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="task-header flex justify-between items-center p-4 bg-blue-500 text-white">
                            <h3 class="text-xl font-semibold text-white"><strong class="font-bold">Titulo:</strong> {{ $tarea->title }}</h3>
                            <div class="flex space-x-2">
                                <span class="priority-badge priority-{{ $tarea->priority }} px-2 py-1 rounded-full text-xs font-medium">
                                    {{ ucfirst($tarea->priority) }}
                                </span>
                                <span id="status-badge-{{ $tarea->id }}" class="status-badge {{ $tarea->completed ? 'status-completed' : 'status-in-progress' }} px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $tarea->completed ? 'Completada' : 'En Progreso' }}
                                </span>
                            </div>
                        </div>
                        <div class="task-body p-4">
                            <p class="text-gray-600 mb-4"><strong class="font-bold">Descripción:</strong> {{ $tarea->description }}</p>
                            <div class="flex flex-wrap gap-2 text-sm text-gray-500">
                            <strong class="font-bold text-gray-600">Rango de fecha: </strong>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                      {{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="task-footer flex flex-wrap gap-2 p-4 bg-gray-50">
                        <button onclick="toggleTaskCompletion({{ $tarea->id }})" 
        id="toggle-button-{{ $tarea->id }}" 
        class="task-button {{ $tarea->completed ? 'toggle-status-button-completed' : 'toggle-status-button-in-progress' }} px-4 py-2 rounded-md transition duration-300 ease-in-out">
    {{ $tarea->completed ? 'Marcar como En Progreso' : 'Marcar como Completada' }}
</button>
                            <button onclick="showEditFields({{ $tarea->id }})" class="task-button bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out">
                                Editar
                            </button>
                            <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar esta tarea?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="task-button bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out">
                                    Eliminar
                                </button>
                            </form>
                            <button onclick="toggleComments({{ $tarea->id }})" class="task-button bg-gray-300 hover:bg-indigo-600 text-black px-4 py-2 rounded-md transition duration-300 ease-in-out">
                                <span id="commentButtonText-{{ $tarea->id }}">Mostrar Comentarios</span>
                                <span id="commentCount-{{ $tarea->id }}" class="ml-2 bg-white text-indigo-500 px-2 py-1 rounded-full text-xs font-bold">{{ $tarea->comments->count() }}</span>
                            </button>
                        </div>
                        
                        <form id="editForm{{ $tarea->id }}" style="display:none;" action="{{ route('tareas.update', $tarea->id) }}" method="POST" class="edit-form p-4 bg-gray-100">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label for="title{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Título</label>
                                    <input type="text" id="title{{ $tarea->id }}" name="title" value="{{ $tarea->title }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="description{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <textarea id="description{{ $tarea->id }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ $tarea->description }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="start_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
                                        <input type="date" id="start_date{{ $tarea->id }}" name="start_date" value="{{ $tarea->start_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="end_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Fecha de fin</label>
                                        <input type="date" id="end_date{{ $tarea->id }}" name="end_date" value="{{ $tarea->end_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                                <div>
                                    <label for="priority{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Prioridad</label>
                                    <select id="priority{{ $tarea->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="low" {{ $tarea->priority == 'low' ? 'selected' : '' }}>Baja</option>
                                        <option value="medium" {{ $tarea->priority == 'medium' ? 'selected' : '' }}>Media</option>
                                        <option value="high" {{ $tarea->priority == 'high' ? 'selected' : '' }}>Alta</option>
                                        <option value="urgent" {{ $tarea->priority == 'urgent' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>
                        
                        <div id="commentsSection-{{ $tarea->id }}" class="hidden bg-gray-50 p-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Comentarios</h4>
                            <div id="commentsList-{{ $tarea->id }}" class="space-y-4 mb-6">
                                @foreach ($tarea->comments as $comment)
                                    <div id="comment-{{ $comment->id }}" class="bg-white p-4 rounded-lg shadow-sm">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-grow">
                                                <p class="text-sm text-gray-800">
                                                    <span class="font-medium text-indigo-600">{{ $comment->user->name }}:</span> 
                                                    <span id="commentContent-{{ $comment->id }}">{{ $comment->content }}</span>
                                                </p>
                                                <small class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                            @if($comment->user_id == auth()->id())
                                                <div class="flex space-x-2">
                                                    <button onclick="editComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 text-xs transition duration-150 ease-in-out">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </button>
                                                    <button onclick="deleteComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 text-xs transition duration-150 ease-in-out">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <form onsubmit="addComment(event, {{ $tarea->id }})" class="mt-4">
                                @csrf
                                <div class="flex items-start space-x-4">
                                    <textarea id="newComment-{{ $tarea->id }}" rows="3" class="flex-grow p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" placeholder="Añadir un comentario..."></textarea>
                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-150 ease-in-out">
                                        <i class="fas fa-paper-plane mr-2"></i>Comentar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div> -->



<!-- <div>
    <h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center mt-10">
        <span class="bg-clip-text text-transparent bg-blue-500">
            Actividades que he creado
        </span>
    </h1>
    
    @if($tareas->where('created_by', auth()->id())->count() > 0)
        <div class="space-y-6">
            @foreach($tareas as $tarea)
                @if($tarea->created_by == auth()->id())
                    <div class="task-card bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="task-header flex justify-between items-center p-4 bg-blue-500 text-white">
                            <h3 class="text-xl font-semibold text-white"><strong class="font-bold">Titulo:</strong> {{ $tarea->title }}</h3>
                            <div class="flex space-x-2">
                                <span class="priority-badge priority-{{ $tarea->priority }} px-2 py-1 rounded-full text-xs font-medium">
                                    {{ ucfirst($tarea->priority) }}
                                </span>
                                <span id="status-badge-{{ $tarea->id }}" class="status-badge {{ $tarea->completed ? 'status-completed' : 'status-in-progress' }} px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $tarea->completed ? 'Completada' : 'En Progreso' }}
                                </span>
                            </div>
                        </div>
                        <div class="task-body p-4">
                            <p class="text-gray-600 mb-4"><strong class="font-bold">Descripción:</strong> {{ $tarea->description }}</p>
                            <div class="flex flex-wrap gap-2 text-sm text-gray-500 mb-2">
                                <strong class="font-bold text-gray-600">Rango de fecha: </strong>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-2 text-sm text-gray-500">
                                <strong class="font-bold text-gray-600">Asignado a: </strong>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $tarea->visibleTo->name ?? 'No asignado' }}
                                </span>
                            </div>
                        </div>
                        <div class="task-footer flex flex-wrap gap-2 p-4 bg-gray-50">
                            <button onclick="toggleTaskCompletion({{ $tarea->id }})" 
                                id="toggle-button-{{ $tarea->id }}" 
                                class="task-button {{ $tarea->completed ? 'toggle-status-button-completed' : 'toggle-status-button-in-progress' }} px-4 py-2 rounded-md transition duration-300 ease-in-out">
                                {{ $tarea->completed ? 'Marcar como En Progreso' : 'Marcar como Completada' }}
                            </button>
                            <button onclick="showEditFields({{ $tarea->id }})" class="task-button bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out">
                                Editar
                            </button>
                            <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar esta tarea?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="task-button bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out">
                                    Eliminar
                                </button>
                            </form>
                            <button onclick="toggleComments({{ $tarea->id }})" class="task-button bg-gray-300 hover:bg-blue-500 hover:text-white text-black px-4 py-2 rounded-md transition duration-300 ease-in-out">
                                <span id="commentButtonText-{{ $tarea->id }}">Mostrar Comentarios</span>
                                <span id="commentCount-{{ $tarea->id }}" class="ml-2 bg-white text-indigo-500 px-2 py-1 rounded-full text-xs font-bold">{{ $tarea->comments->count() }}</span>
                            </button>
                        </div>
                        
                        <form id="editForm{{ $tarea->id }}" style="display:none;" action="{{ route('tareas.update', $tarea->id) }}" method="POST" class="edit-form p-4 bg-gray-100">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label for="title{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Título</label>
                                    <input type="text" id="title{{ $tarea->id }}" name="title" value="{{ $tarea->title }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="description{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <textarea id="description{{ $tarea->id }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ $tarea->description }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="start_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
                                        <input type="date" id="start_date{{ $tarea->id }}" name="start_date" value="{{ $tarea->start_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="end_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Fecha de fin</label>
                                        <input type="date" id="end_date{{ $tarea->id }}" name="end_date" value="{{ $tarea->end_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                                <div>
                                    <label for="priority{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Prioridad</label>
                                    <select id="priority{{ $tarea->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="low" {{ $tarea->priority == 'low' ? 'selected' : '' }}>Baja</option>
                                        <option value="medium" {{ $tarea->priority == 'medium' ? 'selected' : '' }}>Media</option>
                                        <option value="high" {{ $tarea->priority == 'high' ? 'selected' : '' }}>Alta</option>
                                        <option value="urgent" {{ $tarea->priority == 'urgent' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>
                        
                        <div id="commentsSection-{{ $tarea->id }}" class="hidden bg-gray-50 p-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Comentarios</h4>
                            <div id="commentsList-{{ $tarea->id }}" class="space-y-4 mb-6">
                                @foreach ($tarea->comments as $comment)
                                    <div id="comment-{{ $comment->id }}" class="bg-white p-4 rounded-lg shadow-sm">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-grow">
                                                <p class="text-sm text-gray-800">
                                                    <span class="font-medium text-indigo-600">{{ $comment->user->name }}:</span> 
                                                    <span id="commentContent-{{ $comment->id }}">{{ $comment->content }}</span>
                                                </p>
                                                <small class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                            @if($comment->user_id == auth()->id())
                                                <div class="flex space-x-2">
                                                    <button onclick="editComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 text-xs transition duration-150 ease-in-out">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </button>
                                                    <button onclick="deleteComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 text-xs transition duration-150 ease-in-out">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <form onsubmit="addComment(event, {{ $tarea->id }})" class="mt-4">
                                @csrf
                                <div class="flex items-start space-x-4">
                                    <textarea id="newComment-{{ $tarea->id }}" rows="3" class="flex-grow p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" placeholder="Añadir un comentario..."></textarea>
                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-150 ease-in-out">
                                        <i class="fas fa-paper-plane mr-2"></i>Comentar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="bg-white shadow-xl rounded-lg overflow-hidden p-8 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <h3 class="mt-2 text-xl font-medium text-gray-900">No has reportado actividades aún</h3>
            <p class="mt-1 text-sm text-gray-500">Cuando reportes actividades, aparecerán aquí.</p>
            <p class="mt-1 text-sm text-gray-500">Puedes comenzar reportando una nueva actividad usando el formulario de creación.</p>
        </div>
    @endif
</div> -->

<div class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            Actividades que he creado
        </h1>

        @if(session('success'))
            <div class="bg-green-500 text-white p-2 rounded-md mb-4 text-center text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="divide-y divide-gray-200">
                @forelse($tareas->where('created_by', auth()->id()) as $tarea)
                    <div id="task-{{ $tarea->id }}" class="p-4 hover:bg-gray-50 transition duration-150 ease-in-out">
                        <div class="flex items-center justify-between cursor-pointer" onclick="toggleTaskDetails({{ $tarea->id }})">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="inline-block h-2 w-2 rounded-full {{ $tarea->completed ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $tarea->title }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $tarea->priority == 'high' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($tarea->priority) }}
                                </span>
                                <span id="status-badge-{{ $tarea->id }}" class="px-2 py-1 text-xs font-medium rounded-full {{ $tarea->completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $tarea->completed ? 'Completada' : 'Pendiente' }}
                                </span>
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <div id="taskDetails-{{ $tarea->id }}" class="hidden mt-4 space-y-4">
                            <div class="text-sm text-gray-700">
                                <p><strong>Descripción:</strong> {{ $tarea->description }}</p>
                                <p class="mt-2"><strong>Asignado a:</strong> {{ $tarea->visibleTo->name ?? 'No asignado' }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="toggleTaskCompletion({{ $tarea->id }})" 
                                        id="toggle-button-{{ $tarea->id }}" 
                                        class="flex-1 px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ $tarea->completed ? 'Marcar como Pendiente' : 'Marcar como Completada' }}
                                </button>
                                <button onclick="showEditFields({{ $tarea->id }})" 
                                        class="flex-1 px-3 py-1 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Editar
                                </button>
                                <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar esta tarea?');" class="inline-block flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-3 py-1 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                            <div class="mt-4">
                                <button onclick="toggleComments({{ $tarea->id }})" 
                                        class="w-full px-3 py-1 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <span id="commentButtonText-{{ $tarea->id }}">Comentarios</span>
                                    <span id="commentCount-{{ $tarea->id }}" class="ml-1 bg-gray-500 text-white px-1.5 py-0.5 rounded-full text-xs">
                                        {{ $tarea->comments->count() }}
                                    </span>
                                </button>
                            </div>
                            <div id="commentsSection-{{ $tarea->id }}" class="hidden space-y-3">
                                <div id="commentsList-{{ $tarea->id }}" class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach ($tarea->comments as $comment)
                                        <div id="comment-{{ $comment->id }}" class="bg-gray-50 p-2 rounded-md text-sm">
                                            <div class="flex items-start justify-between">
                                                <div class="space-y-1">
                                                    <p>
                                                        <span class="font-medium text-gray-900">{{ $comment->user->name }}:</span>
                                                        <span id="commentContent-{{ $comment->id }}" class="text-gray-700">{{ $comment->content }}</span>
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                                </div>
                                                @if($comment->user_id == auth()->id())
                                                    <div class="flex space-x-1">
                                                        <button onclick="editComment({{ $comment->id }})" class="text-blue-600 hover:text-blue-800">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </button>
                                                        <button onclick="deleteComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-600 hover:text-red-800">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <form onsubmit="addTaskComment(event, {{ $tarea->id }})" class="mt-2">
                                    @csrf
                                    <textarea id="newComment-{{ $tarea->id }}" rows="2" class="w-full px-3 py-2 text-sm border rounded-md resize-none focus:ring-blue-500 focus:border-blue-500" placeholder="Añadir un comentario..."></textarea>
                                    <button type="submit" class="mt-2 w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Comentar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <form id="editForm{{ $tarea->id }}" style="display:none;" action="{{ route('tareas.update', $tarea->id) }}" method="POST" class="p-4 bg-gray-100">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="title{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Título</label>
                                <input type="text" id="title{{ $tarea->id }}" name="title" value="{{ $tarea->title }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="description{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea id="description{{ $tarea->id }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ $tarea->description }}</textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
                                    <input type="date" id="start_date{{ $tarea->id }}" name="start_date" value="{{ $tarea->start_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="end_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Fecha de fin</label>
                                    <input type="date" id="end_date{{ $tarea->id }}" name="end_date" value="{{ $tarea->end_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                            </div>
                            <div>
                                <label for="priority{{ $tarea->id }}" class="block text-sm font-medium text-gray-700">Prioridad</label>
                                <select id="priority{{ $tarea->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="low" {{ $tarea->priority == 'low' ? 'selected' : '' }}>Baja</option>
                                    <option value="medium" {{ $tarea->priority == 'medium' ? 'selected' : '' }}>Media</option>
                                    <option value="high" {{ $tarea->priority == 'high' ? 'selected' : '' }}>Alta</option>
                                    <option value="urgent" {{ $tarea->priority == 'urgent' ? 'selected' : '' }}>Urgente</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                @empty
                    <div class="p-4 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No has creado actividades aún</h3>
                        <p class="mt-1 text-sm text-gray-500">Cuando crees actividades, aparecerán aquí.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>



<script>

function toggleTaskDetails(taskId) {
    const detailsElement = document.getElementById(`taskDetails-${taskId}`);
    detailsElement.classList.toggle('hidden');
}


    function toggleComments(taskId) {
        const commentsSection = document.getElementById(`commentsSection-${taskId}`);
        const buttonText = document.getElementById(`commentButtonText-${taskId}`);
        
        if (commentsSection.classList.contains('hidden')) {
            commentsSection.classList.remove('hidden');
            buttonText.textContent = 'Ocultar Comentarios';
        } else {
            commentsSection.classList.add('hidden');
            buttonText.textContent = 'Mostrar Comentarios';
        }
    }

    function showEditFields(taskId) {
        const form = document.getElementById(`editForm${taskId}`);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    function toggleComments(taskId) {
        const commentsSection = document.getElementById(`commentsSection-${taskId}`);
        const buttonText = document.getElementById(`commentButtonText-${taskId}`);
        
        if (commentsSection.classList.contains('hidden')) {
            commentsSection.classList.remove('hidden');
            buttonText.textContent = 'Ocultar Comentarios';
        } else {
            commentsSection.classList.add('hidden');
            buttonText.textContent = 'Mostrar Comentarios';
        }
    }

    function addComment(event, taskId) {
        event.preventDefault();
        const content = document.getElementById(`newComment-${taskId}`).value;
        if (!content.trim()) return;

        fetch('{{ route('comments.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ task_id: taskId, content: content })
        })
        .then(response => response.json())
        .then(comment => {
            const commentsList = document.getElementById(`commentsList-${taskId}`);
            commentsList.insertAdjacentHTML('beforeend', createCommentHTML(comment));
            document.getElementById(`newComment-${taskId}`).value = '';
            
            // Actualizar el contador de comentarios
            const commentCount = document.getElementById(`commentCount-${taskId}`);
            commentCount.textContent = parseInt(commentCount.textContent) + 1;
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al añadir el comentario: ' + error.message, 'error');
        });
    }

    function createCommentHTML(comment) {
        return `
            <div id="comment-${comment.id}" class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex-grow">
                        <p class="text-sm text-gray-800">
                            <span class="font-medium text-indigo-600">${comment.user.name}:</span> 
                            <span id="commentContent-${comment.id}">${comment.content}</span>
                        </p>
                        <small class="text-xs text-gray-500">${new Date(comment.created_at).toLocaleString()}</small>
                    </div>
                    ${comment.user_id == {{ auth()->id() }} ? `
                        <div class="flex space-x-2">
                            <button onclick="editComment(${comment.id})" class="text-blue-500 hover:text-blue-700 text-xs transition duration-150 ease-in-out">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button onclick="deleteComment(${comment.id}, ${comment.task_id})" class="text-red-500 hover:text-red-700 text-xs transition duration-150 ease-in-out">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function editComment(commentId) {
        const commentElement = document.getElementById(`comment-${commentId}`);
        const contentElement = commentElement.querySelector(`#commentContent-${commentId}`);
        const currentContent = contentElement.textContent.trim();
        
        const input = document.createElement('textarea');
        input.value = currentContent;
        input.className = 'w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none';
        input.rows = 3;
        
        contentElement.replaceWith(input);
        
        input.focus();

        const saveButton = document.createElement('button');
        saveButton.textContent = 'Guardar';
        saveButton.className = 'mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-150 ease-in-out';
        saveButton.onclick = () => updateComment(commentId, input.value);

        commentElement.appendChild(saveButton);
    }

    function updateComment(commentId, newContent) {
        fetch(`/comments/${commentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: newContent })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentElement = document.getElementById(`comment-${commentId}`);
                if (commentElement) {
                    const newCommentHTML = `
                        <div class="flex-grow">
                            <p class="text-sm text-gray-800">
                                <span class="font-medium text-indigo-600">${data.comment.user.name}:</span> 
                                <span id="commentContent-${commentId}">${data.comment.content}</span>
                            </p>
                            <small class="text-xs text-gray-500">${new Date(data.comment.updated_at).toLocaleString()}</small>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editComment(${commentId})" class="text-blue-500 hover:text-blue-700 text-xs transition duration-150 ease-in-out">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button onclick="deleteComment(${commentId}, ${data.comment.task_id})" class="text-red-500 hover:text-red-700 text-xs transition duration-150 ease-in-out">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    `;
                    
                    commentElement.innerHTML = newCommentHTML;
                    showAlert('Comentario actualizado con éxito', 'success');
                } else {
                    console.error(`Elemento de comentario no encontrado para el ID ${commentId}`);
                    showAlert('Error al actualizar el comentario: Elemento no encontrado', 'error');
                }
            } else {
                showAlert('Error al actualizar el comentario: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al actualizar el comentario: ' + error.message, 'error');
        });
    }

    function deleteComment(commentId, taskId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este comentario?')) return;

        fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentElement = document.getElementById(`comment-${commentId}`);
                commentElement.remove();
                showAlert('Comentario eliminado con éxito', 'success');
                
                // Actualizar el contador de comentarios
                const commentCount = document.getElementById(`commentCount-${taskId}`);
                commentCount.textContent = parseInt(commentCount.textContent) - 1;
            } else {
                showAlert('Error al eliminar el comentario: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al eliminar el comentario: ' + error.message, 'error');
        });
    }

    function showAlert(message, type) {
        const alertElement = document.createElement('div');
        alertElement.className = `fixed top-4 right-4 p-4 rounded-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
        alertElement.textContent = message;
        document.body.appendChild(alertElement);

        setTimeout(() => {
            alertElement.remove();
        }, 3000);
    }

    function toggleTaskCompletion(taskId) {
    const statusBadge = document.getElementById(`status-badge-${taskId}`);
    const toggleButton = document.getElementById(`toggle-button-${taskId}`);
    
    console.log('Toggling completion for task:', taskId);
    
    fetch(`/tasks/${taskId}/toggle-completion`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json().then(data => ({status: response.status, body: data}));
    })
    .then(({status, body}) => {
        console.log('Response data:', body);
        if (status === 200 && body.success) {
            statusBadge.textContent = body.completed ? 'Completada' : 'En Progreso';
            statusBadge.classList.toggle('status-completed', body.completed);
            statusBadge.classList.toggle('status-in-progress', !body.completed);
            
            toggleButton.textContent = body.completed ? 'Marcar como En Progreso' : 'Marcar como Completada';
            toggleButton.classList.toggle('toggle-status-button-completed', body.completed);
            toggleButton.classList.toggle('toggle-status-button-in-progress', !body.completed);
        } else {
            throw new Error(body.message || 'Error desconocido');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el estado de la tarea: ' + error.message);
    });
}
</script>