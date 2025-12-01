<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tareas asignadas a mi equipo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="w-full md:w-1/2 relative">
                            <input type="text" id="search" placeholder="Buscar tareas..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <svg class="w-6 h-6 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <a href="{{ route('manager.tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Crear Nueva Tarea
                        </a>
                    </div>
                </div>
            </div>

            @foreach($tareas as $tarea)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 transition duration-300 ease-in-out hover:shadow-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $tarea->title }}</h3>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($tarea->priority == 'low') bg-green-100 text-green-800
                                @elseif($tarea->priority == 'medium') bg-yellow-100 text-yellow-800
                                @elseif($tarea->priority == 'high') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($tarea->priority) }}
                            </span>
                            <button class="text-blue-600 hover:text-blue-800 focus:outline-none toggle-details flex items-center" data-task-id="{{ $tarea->id }}">
                                <span class="mr-2 text-sm">Detalles</span>
                                <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="task-details hidden" id="task-details-{{ $tarea->id }}">
                        <div class="mb-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-gray-600 dark:text-gray-400">{{ $tarea->description }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Asignado a</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $tarea->visibleTo->name }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fechas</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100" id="status-{{ $tarea->id }}">
                                    {{ $tarea->completed ? 'Completada' : 'Pendiente' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Comentarios</h4>
                            <div class="comments-container space-y-4" id="comments-container-{{ $tarea->id }}">
                                @foreach($tarea->comments as $comment)
                                    @if($comment->user_id == Auth::id() || $comment->user_id == $tarea->visible_para)
                                    <div class="comment bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-600" id="comment-{{ $comment->id }}">
                                        <p class="text-gray-800 dark:text-gray-200 mb-2">{{ $comment->content }}</p>
                                        <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                                            <span>{{ $comment->user->name }} - {{ $comment->created_at->diffForHumans() }}</span>
                                            @if($comment->user_id == Auth::id())
                                            <div>
                                                <button class="edit-comment text-blue-500 hover:text-blue-700 mr-2 transition duration-150 ease-in-out" data-comment-id="{{ $comment->id }}">Editar</button>
                                                <button class="delete-comment text-red-500 hover:text-red-700 transition duration-150 ease-in-out" data-comment-id="{{ $comment->id }}">Eliminar</button>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <form class="add-comment-form mt-4" data-task-id="{{ $tarea->id }}">
                                @csrf
                                <textarea name="content" rows="3" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none" placeholder="Añade un comentario..."></textarea>
                                <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300 ease-in-out">
                                    Añadir comentario
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="mt-4">
                {{ $tareas->links() }}
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const taskCards = document.querySelectorAll('.bg-white.dark\\:bg-gray-800');

        // Búsqueda de tareas
        searchInput.addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            taskCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Toggle detalles de la tarea
        document.querySelectorAll('.toggle-details').forEach(button => {
            button.addEventListener('click', function() {
                const taskId = this.getAttribute('data-task-id');
                const detailsDiv = document.getElementById(`task-details-${taskId}`);
                detailsDiv.classList.toggle('hidden');
                const arrow = this.querySelector('svg');
                arrow.classList.toggle('rotate-180');
            });
        });

        // Añadir comentario
        document.querySelectorAll('.add-comment-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const taskId = this.getAttribute('data-task-id');
                const content = this.querySelector('textarea[name="content"]').value;

                fetch(`/manager/tasks/${taskId}/comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ content: content })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const commentsContainer = document.getElementById(`comments-container-${taskId}`);
                        commentsContainer.insertAdjacentHTML('beforeend', createCommentElement(data.comment));
                        this.reset();
                    } else {
                        alert('Error al añadir el comentario.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al añadir el comentario.');
                });
            });
        });

        // Editar comentario
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-comment')) {
                const commentId = e.target.getAttribute('data-comment-id');
                const commentElement = document.getElementById(`comment-${commentId}`);
                const commentContent = commentElement.querySelector('p').textContent;

                const textarea = document.createElement('textarea');
                textarea.value = commentContent;
                textarea.classList.add('w-full', 'px-3', 'py-2', 'text-gray-700', 'border', 'rounded-lg', 'focus:outline-none', 'focus:ring-2', 'focus:ring-blue-500', 'dark:bg-gray-700', 'dark:border-gray-600', 'dark:text-white', 'resize-none');

                const saveButton = document.createElement('button');
                saveButton.textContent = 'Guardar';
                saveButton.classList.add('mt-2', 'px-4', 'py-2', 'bg-blue-500', 'text-white', 'rounded-md', 'hover:bg-blue-600', 'transition', 'duration-300', 'ease-in-out');

                const cancelButton = document.createElement('button');
                cancelButton.textContent = 'Cancelar';
                cancelButton.classList.add('mt-2', 'ml-2', 'px-4', 'py-2', 'bg-gray-500', 'text-white', 'rounded-md', 'hover:bg-gray-600', 'transition', 'duration-300', 'ease-in-out');

                const buttonContainer = document.createElement('div');
                buttonContainer.appendChild(saveButton);
                buttonContainer.appendChild(cancelButton);

                const originalContent = commentElement.innerHTML;
                commentElement.innerHTML = '';
                commentElement.appendChild(textarea);
                commentElement.appendChild(buttonContainer);

                saveButton.addEventListener('click', function() {
                    updateComment(commentId, textarea.value);
                });

                cancelButton.addEventListener('click', function() {
                    commentElement.innerHTML = originalContent;
                });
            }
        });

        // Actualizar comentario
        function updateComment(commentId, content) {
            fetch(`/manager/comments/${commentId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ content: content })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const commentElement = document.getElementById(`comment-${commentId}`);
                    commentElement.outerHTML = createCommentElement(data.comment);
                } else {
                    alert('Error al actualizar el comentario.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el comentario.');
            });
        }

        // Eliminar comentario
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-comment')) {
                if (confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
                    const commentId = e.target.getAttribute('data-comment-id');
                    deleteComment(commentId);
                }
            }
        });

        function deleteComment(commentId) {
            fetch(`/manager/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const commentElement = document.getElementById(`comment-${commentId}`);
                    commentElement.remove();
                } else {
                    alert('Error al eliminar el comentario.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el comentario.');
            });
        }

        function createCommentElement(comment) {
            return `
                <div class="comment bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-600" id="comment-${comment.id}">
                    <p class="text-gray-800 dark:text-gray-200 mb-2">${comment.content}</p>
                    <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                        <span>${comment.user.name} - ${new Date(comment.created_at).toLocaleString()}</span>
                        <div>
                            <button class="edit-comment text-blue-500 hover:text-blue-700 mr-2 transition duration-150 ease-in-out" data-comment-id="${comment.id}">Editar</button>
                            <button class="delete-comment text-red-500 hover:text-red-700 transition duration-150 ease-in-out" data-comment-id="${comment.id}">Eliminar</button>
                        </div>
                    </div>
                </div>
            `;
        }
    });
    </script>
</x-app-layout>