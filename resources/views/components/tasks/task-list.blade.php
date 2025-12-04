@props(['tareasEmpleador'])

<div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 md:p-8 rounded-t-xl shadow-lg p-12">
    <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">Asignaciones de mi equipo</h2>
    <p class="text-blue-100 text-sm md:text-base">Gestiona las tareas de tu equipo de forma eficiente</p>
</div>

<div class="bg-gray-100 dark:bg-gray-900 p-4 md:p-8 rounded-b-xl shadow-lg p-10">
    <div id="employerTaskList" class="space-y-4">
        @if($tareasEmpleador->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
                <i class="fas fa-tasks text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No hay tareas asignadas</h3>
                <p class="text-gray-600 dark:text-gray-400">Comienza a crear tareas para tu equipo y mejora la productividad.</p>
            </div>
        @else
            @foreach($tareasEmpleador as $tarea)
                <div id="task-{{ $tarea->id }}" class="task-card bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out hover:shadow-xl border-l-4 border-blue-500">
                    <div class="flex items-center justify-between p-4 cursor-pointer" onclick="toggleTaskDetails({{ $tarea->id }})">
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $tarea->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 hidden md:block">{{ Str::limit($tarea->description, 100) }}</p>
                        </div>
                        <div class="flex flex-col md:flex-row items-end md:items-center space-y-2 md:space-y-0 md:space-x-2">
                            <span class="priority-badge priority-{{ $tarea->priority }} px-2 py-1 rounded-full text-xs font-medium">
                                {{ ucfirst($tarea->priority) }}
                            </span>
                            <span id="status-badge-{{ $tarea->id }}" class="status-badge {{ $tarea->completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} px-2 py-1 rounded-full text-xs font-medium">
                                {{ $tarea->completed ? 'Completada' : 'En Progreso' }}
                            </span>
                            <i class="fas fa-chevron-down transform transition-transform duration-300" id="chevron-{{ $tarea->id }}"></i>
                        </div>
                    </div>

                    <div id="taskDetails-{{ $tarea->id }}" class="hidden">
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700">
                            <div class="flex flex-wrap justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center mb-2 md:mb-0">
                                    <i class="fas fa-calendar-alt mr-1 text-indigo-500"></i>
                                    {{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-user mr-1 text-indigo-500"></i>
                                    {{ $tarea->visibleTo->name ?? 'Usuario desconocido' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4 flex flex-wrap gap-2">
                            <button onclick="toggleEmployerTaskCompletion({{ $tarea->id }})"
                                    id="toggle-button-{{ $tarea->id }}"
                                    class="px-3 py-1.5 rounded text-sm font-medium transition duration-300 ease-in-out {{ $tarea->completed ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white">
                                <i class="fas {{ $tarea->completed ? 'fa-undo' : 'fa-check' }} mr-1"></i>
                                {{ $tarea->completed ? 'Marcar En Progreso' : 'Marcar Completada' }}
                            </button>
                            <button onclick="showEmployerEditFields({{ $tarea->id }})" class="px-3 py-1.5 rounded text-sm font-medium transition duration-300 ease-in-out bg-blue-500 hover:bg-blue-600 text-white">
                                <i class="fas fa-edit mr-1"></i>Editar
                            </button>
                            <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar esta tarea?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded text-sm font-medium transition duration-300 ease-in-out bg-red-500 hover:bg-red-600 text-white">
                                    <i class="fas fa-trash-alt mr-1"></i>Eliminar
                                </button>
                            </form>
                            <button onclick="toggleEmployerComments({{ $tarea->id }})" class="px-3 py-1.5 rounded text-sm font-medium transition duration-300 ease-in-out bg-gray-300 hover:bg-gray-400 text-gray-800">
                                <i class="fas fa-comments mr-1"></i>
                                <span id="commentButtonText-{{ $tarea->id }}">Comentarios</span>
                                <span id="commentCount-{{ $tarea->id }}" class="ml-1 bg-white text-blue-500 px-1.5 py-0.5 rounded-full text-xs font-bold">{{ $tarea->comments->count() }}</span>
                            </button>
                        </div>

                        <form id="editForm{{ $tarea->id }}" style="display:none;" action="{{ route('tareas.update', $tarea->id) }}" method="POST" class="edit-form p-4 bg-gray-100 dark:bg-gray-700">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label for="title{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                                    <input type="text" id="title{{ $tarea->id }}" name="title" value="{{ $tarea->title }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                </div>
                                <div class="col-span-2">
                                    <label for="description{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                    <textarea id="description{{ $tarea->id }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">{{ $tarea->description }}</textarea>
                                </div>
                                <div>
                                    <label for="start_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de inicio</label>
                                    <input type="date" id="start_date{{ $tarea->id }}" name="start_date" value="{{ $tarea->start_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                </div>
                                <div>
                                    <label for="end_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de fin</label>
                                    <input type="date" id="end_date{{ $tarea->id }}" name="end_date" value="{{ $tarea->end_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                </div>
                                <div class="col-span-2">
                                    <label for="priority{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                                    <select id="priority{{ $tarea->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        <option value="low" {{ $tarea->priority == 'low' ? 'selected' : '' }}>Baja</option>
                                        <option value="medium" {{ $tarea->priority == 'medium' ? 'selected' : '' }}>Media</option>
                                        <option value="high" {{ $tarea->priority == 'high' ? 'selected' : '' }}>Alta</option>
                                        <option value="urgent" {{ $tarea->priority == 'urgent' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out transform hover:scale-105">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>

                        <div id="commentsSection-{{ $tarea->id }}" class="hidden bg-gray-50 dark:bg-gray-700 p-4 rounded-b-lg">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Comentarios</h4>
                            <div id="commentsList-{{ $tarea->id }}" class="space-y-4 mb-6">
                                @foreach ($tarea->comments as $comment)
                                    <div id="comment-{{ $comment->id }}" class="flex items-start space-x-3 bg-white dark:bg-gray-600 p-4 rounded-lg shadow-sm">
                                        <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-10 h-10 rounded-full">
                                        <div class="flex-grow">
                                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                                <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $comment->user->name }}</span>
                                                <span class="text-gray-500 text-xs ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                            </p>
                                            <p id="commentContent-{{ $comment->id }}" class="mt-1">{{ $comment->content }}</p>
                                            @if($comment->user_id == auth()->id())
                                                <div class="mt-2 flex space-x-2">
                                                    <button onclick="editEmployerComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs transition duration-150 ease-in-out">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </button>
                                                    <button onclick="deleteEmployerComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs transition duration-150 ease-in-out">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <form onsubmit="addEmployerTaskComment(event, {{ $tarea->id }})" class="mt-4">
                                @csrf
                                <div class="flex items-start space-x-4">
                                    <textarea id="newComment-{{ $tarea->id }}" rows="3" class="flex-grow p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none dark:bg-gray-600 dark:border-gray-500 dark:text-white" placeholder="Añadir un comentario..."></textarea>
                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out transform hover:scale-105">
                                        <i class="fas fa-paper-plane mr-2"></i>Comentar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<style>
    .priority-badge {
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .priority-low { background-color: #E5F6FD; color: #0369A1; }
    .priority-medium { background-color: #FEF3C7; color: #92400E; }
    .priority-high { background-color: #FEE2E2; color: #B91C1C; }
    .priority-urgent { background-color: #FECACA; color: #7F1D1D; }
</style>
