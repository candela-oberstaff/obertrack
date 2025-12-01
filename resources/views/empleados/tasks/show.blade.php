<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $task->title }}
        </h2>
    </x-slot>

    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <a href="{{ route('empleados.tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver a tareas
                </a>
            </div>

            
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600">
                    <h2 class="text-2xl font-bold text-white">{{ $task->title }}</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 dark:text-gray-300 text-lg">{{ $task->description }}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span id="taskStatus" class="px-4 py-2 rounded-full text-sm font-semibold {{ $task->completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $task->completed ? 'Completada' : 'En progreso' }}
                        </span>
                        <button id="toggleCompletion" 
                                class="px-4 py-2 text-white font-medium rounded-md transition duration-300 focus:outline-none focus:ring-2 focus:ring-opacity-50"
                                data-task-id="{{ $task->id }}"
                                data-completed="{{ $task->completed ? 'true' : 'false' }}">
                            {{ $task->completed ? 'Marcar como en progreso' : 'Marcar como completada' }}
                        </button>
                    </div>
                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        Creada por: <span class="font-semibold">{{ $task->createdBy->name }}</span>
                    </div>
                </div>
            </div>

            
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Detalles de la tarea</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Prioridad</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $task->priority == 'high' ? 'bg-red-100 text-red-800' : 
                                   ($task->priority == 'medium' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($task->priority == 'urgent' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800')) }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de inicio</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $task->start_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de finalización</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $task->end_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Días restantes</p>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            @php
                                $now = now()->startOfDay();
                                $endDate = $task->end_date->startOfDay();
                                $daysRemaining = $now->diffInDays($endDate, false);
                                $status = $daysRemaining > 0 ? ($daysRemaining <= 3 ? 'text-red-600' : ($daysRemaining <= 7 ? 'text-yellow-600' : 'text-green-600')) : 'text-gray-600';
                            @endphp
                            <span class="{{ $status }}">
                                {{ $daysRemaining }} {{ $daysRemaining === 1 ? 'día' : 'días' }}
                                @if($daysRemaining === 0 && $now->lte($endDate))
                                    (Hoy es el último día)
                                @elseif($daysRemaining < 0)
                                    (Tarea vencida)
                                @endif
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Comentarios</h3>
                </div>
                <div id="comments-container" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($comments as $comment)
                        <div class="comment p-6" id="comment-{{ $comment->id }}">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $comment->user->name }}">
                                </div>
                                <div class="flex-grow">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $comment->user->name }}
                                        @if($comment->user_id == $task->created_by)
                                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Manager</span>
                                        @endif
                                    </p>
                                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $comment->content }}
                                    </p>
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex justify-between items-center">
                                        <span>{{ $comment->created_at->diffForHumans() }}</span>
                                        @if($comment->user_id == Auth::id())
                                            <div class="flex space-x-2">
                                                <button class="edit-comment text-blue-500 hover:text-blue-600 transition duration-300" data-comment-id="{{ $comment->id }}">Editar</button>
                                                <button class="delete-comment text-red-500 hover:text-red-600 transition duration-300" data-comment-id="{{ $comment->id }}">Eliminar</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-700">
                    <form id="add-comment-form" data-task-id="{{ $task->id }}">
                        @csrf
                        <textarea name="content" rows="3" class="w-full px-3 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300 resize-none" placeholder="Añade un comentario..."></textarea>
                        <button type="submit" class="mt-3 w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            Añadir comentario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
  
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleCompletion');
            const taskStatus = document.getElementById('taskStatus');
            const commentsContainer = document.getElementById('comments-container');
            const addCommentForm = document.getElementById('add-comment-form');

            function updateToggleButton(completed) {
                toggleButton.textContent = completed ? 'Marcar como en progreso' : 'Marcar como completada';
                toggleButton.className = `px-4 py-2 text-white font-medium rounded-md transition duration-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 ${completed ? 'bg-green-500 hover:bg-green-600 focus:ring-green-500' : 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-500'}`;
            }

            updateToggleButton(toggleButton.dataset.completed === 'true');

            // Toggle task completion
            toggleButton.addEventListener('click', function() {
                const taskId = this.dataset.taskId;
                const completed = this.dataset.completed === 'true';

                fetch(`/empleados/tareas/${taskId}/toggle-completion`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ completed: !completed })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.dataset.completed = (!completed).toString();
                        updateToggleButton(!completed);
                        taskStatus.textContent = completed ? 'En progreso' : 'Completada';
                        taskStatus.className = `px-4 py-2 rounded-full text-sm font-semibold ${completed ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}`;
                    }
                });
            });

            // Add new comment
            addCommentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const content = this.querySelector('textarea[name="content"]').value;
                const taskId = this.dataset.taskId;

                fetch(`/empleados/tareas/${taskId}/comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ content: content })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const newComment = createCommentElement(data.comment);
                        commentsContainer.insertAdjacentHTML('afterbegin', newComment);
                        this.reset();
                    }
                });
            });

            // Edit comment
            commentsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('edit-comment')) {
                    const commentId = e.target.dataset.commentId;
                    const commentElement = document.getElementById(`comment-${commentId}`);
                    const commentContent = commentElement.querySelector('p:nth-child(2)').textContent.trim();

                    const editForm = `
                        <form class="edit-comment-form" data-comment-id="${commentId}">
                            <textarea class="w-full p-2 border rounded-lg resize-none">${commentContent}</textarea>
                            <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded transition duration-300">Guardar</button>
                            <button type="button" class="cancel-edit mt-2 bg-gray-500 hover:bg-gray-600 text-white font-bold py-1 px-3 rounded transition duration-300">Cancelar</button>
                        </form>
                    `;

                    commentElement.querySelector('.flex-grow').innerHTML = editForm;
                }
            });

            // Handle edit comment submission
            commentsContainer.addEventListener('submit', function(e) {
                if (e.target.classList.contains('edit-comment-form')) {
                    e.preventDefault();
                    const commentId = e.target.dataset.commentId;
                    const content = e.target.querySelector('textarea').value;

                    fetch(`/empleados/tareas/comment/${commentId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ content: content })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const updatedComment = createCommentElement(data.comment);
                            document.getElementById(`comment-${commentId}`).outerHTML = updatedComment;
                        }
                    });
                }
            });

            // Cancel edit
            commentsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('cancel-edit')) {
                    const commentId = e.target.closest('form').dataset.commentId;
                    const commentElement = document.getElementById(`comment-${commentId}`);
                    const originalComment = createCommentElement(JSON.parse(commentElement.dataset.originalComment));
                    commentElement.outerHTML = originalComment;
                }
            });

            // Delete comment
                        // Delete comment
                        commentsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-comment')) {
                    if (confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
                        const commentId = e.target.dataset.commentId;

                        fetch(`/empleados/tareas/comment/${commentId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById(`comment-${commentId}`).remove();
                            }
                        });
                    }
                }
            });

            function createCommentElement(comment) {
                const isManager = comment.user_id == {{ $task->created_by }};
                const isCurrentUser = comment.user_id == {{ Auth::id() }};
                return `
                    <div class="comment p-6" id="comment-${comment.id}" data-original-comment='${JSON.stringify(comment)}'>
                        <div class="flex space-x-3">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&color=7F9CF5&background=EBF4FF" alt="${comment.user.name}">
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    ${comment.user.name}
                                    ${isManager ? '<span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Manager</span>' : ''}
                                </p>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                    ${comment.content}
                                </p>
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex justify-between items-center">
                                    <span>${new Date(comment.created_at).toLocaleString()}</span>
                                    ${isCurrentUser ? `
                                        <div class="flex space-x-2">
                                            <button class="edit-comment text-blue-500 hover:text-blue-600 transition duration-300" data-comment-id="${comment.id}">Editar</button>
                                            <button class="delete-comment text-red-500 hover:text-red-600 transition duration-300" data-comment-id="${comment.id}">Eliminar</button>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
    </script>
</x-app-layout>