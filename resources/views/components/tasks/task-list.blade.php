@props(['tareasEmpleador'])

\u003cdiv class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 md:p-8 rounded-t-xl shadow-lg p-12"\u003e
    \u003ch2 class="text-2xl md:text-3xl font-bold text-white mb-2"\u003eAsignaciones de mi equipo\u003c/h2\u003e
    \u003cp class="text-blue-100 text-sm md:text-base"\u003eGestiona las tareas de tu equipo de forma eficiente\u003c/p\u003e
\u003c/div\u003e

\u003cdiv class="bg-gray-100 dark:bg-gray-900 p-4 md:p-8 rounded-b-xl shadow-lg p-10"\u003e
    \u003cdiv id="employerTaskList" class="space-y-4"\u003e
        @if($tareasEmpleador-\u003eisEmpty())
            \u003cdiv class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center"\u003e
                \u003ci class="fas fa-tasks text-4xl text-gray-400 mb-4"\u003e\u003c/i\u003e
                \u003ch3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2"\u003eNo hay tareas asignadas\u003c/h3\u003e
                \u003cp class="text-gray-600 dark:text-gray-400"\u003eComienza a crear tareas para tu equipo y mejora la productividad.\u003c/p\u003e
            \u003c/div\u003e
        @else
            @foreach($tareasEmpleador as $tarea)
                \u003cdiv id="task-{{ $tarea-\u003eid }}" class="task-card bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out hover:shadow-xl border-l-4 border-blue-500"\u003e
                    \u003cdiv class="flex items-center justify-between p-4 cursor-pointer" onclick="toggleTaskDetails({{ $tarea-\u003eid }})"\u003e
                        \u003cdiv class="flex-grow"\u003e
                            \u003ch3 class="text-lg font-semibold text-gray-800 dark:text-white"\u003e{{ $tarea-\u003etitle }}\u003c/h3\u003e
                            \u003cp class="text-sm text-gray-600 dark:text-gray-300 mt-1 hidden md:block"\u003e{{ Str::limit($tarea-\u003edescription, 100) }}\u003c/p\u003e
                        \u003c/div\u003e
                        \u003cdiv class="flex flex-col md:flex-row items-end md:items-center space-y-2 md:space-y-0 md:space-x-2"\u003e
                            \u003cspan class="priority-badge priority-{{ $tarea-\u003epriority }} px-2 py-1 rounded-full text-xs font-medium"\u003e
                                {{ ucfirst($tarea-\u003epriority) }}
                            \u003c/span\u003e
                            \u003cspan id="status-badge-{{ $tarea-\u003eid }}" class="status-badge {{ $tarea-\u003ecompleted ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} px-2 py-1 rounded-full text-xs font-medium"\u003e
                                {{ $tarea-\u003ecompleted ? 'Completada' : 'En Progreso' }}
                            \u003c/span\u003e
                            \u003ci class="fas fa-chevron-down transform transition-transform duration-300" id="chevron-{{ $tarea-\u003eid }}"\u003e\u003c/i\u003e
                        \u003c/div\u003e
                    \u003c/div\u003e

                    \u003cdiv id="taskDetails-{{ $tarea-\u003eid }}" class="hidden"\u003e
                        \u003cdiv class="px-4 py-2 bg-gray-50 dark:bg-gray-700"\u003e
                            \u003cdiv class="flex flex-wrap justify-between text-xs text-gray-500 dark:text-gray-400"\u003e
                                \u003cspan class="inline-flex items-center mb-2 md:mb-0"\u003e
                                    \u003ci class="fas fa-calendar-alt mr-1 text-indigo-500"\u003e\u003c/i\u003e
                                    {{ $tarea-\u003estart_date-\u003eformat('d/m/Y') }} - {{ $tarea-\u003eend_date-\u003eformat('d/m/Y') }}
                                \u003c/span\u003e
                                \u003cspan class="inline-flex items-center"\u003e
                                    \u003ci class="fas fa-user mr-1 text-indigo-500"\u003e\u003c/i\u003e
                                    {{ $tarea-\u003evisibleTo-\u003ename ?? 'Usuario desconocido' }}
                                \u003c/span\u003e
                            \u003c/div\u003e
                        \u003c/div\u003e
                        \u003cdiv class="p-4 flex flex-wrap gap-2"\u003e
                            \u003cbutton onclick="toggleEmployerTaskCompletion({{ $tarea-\u003eid }})"
                                    id="toggle-button-{{ $tarea-\u003eid }}"
                                    class="px-3 py-1.5 rounded text-sm font-medium transition duration-300 ease-in-out {{ $tarea-\u003ecompleted ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white"\u003e
                                \u003ci class="fas {{ $tarea-\u003ecompleted ? 'fa-undo' : 'fa-check' }} mr-1"\u003e\u003c/i\u003e
                                {{ $tarea-\u003ecompleted ? 'Marcar En Progreso' : 'Marcar Completada' }}
                            \u003c/button\u003e
                            \u003cbutton onclick="showEmployerEditFields({{ $tarea-\u003eid }})" class="px-3 py-1.5 rounded text-sm font-medium transition duration-300 ease-in-out bg-blue-500 hover:bg-blue-600 text-white"\u003e
                                \u003ci class="fas fa-edit mr-1"\u003e\u003c/i\u003eEditar
                            \u003c/button\u003e
                            \u003cform action="{{ route('tareas.destroy', $tarea-\u003eid) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar esta tarea?');" class="inline-block"\u003e
                                @csrf
                                @method('DELETE')
                                \u003cbutton type="submit" class="px-3 py-1.5 rounded text-sm font-medium transition duration-300 ease-in-out bg-red-500 hover:bg-red-600 text-white"\u003e
                                    \u003ci class="fas fa-trash-alt mr-1"\u003e\u003c/i\u003eEliminar
                                \u003c/button\u003e
                            \u003c/form\u003e
                            \u003cbutton onclick="toggleEmployerComments({{ $tarea-\u003eid }})" class="px-3 py-1.5 rounded text-sm font-medium transition duration-300 ease-in-out bg-gray-300 hover:bg-gray-400 text-gray-800"\u003e
                                \u003ci class="fas fa-comments mr-1"\u003e\u003c/i\u003e
                                \u003cspan id="commentButtonText-{{ $tarea-\u003eid }}"\u003eComentarios\u003c/span\u003e
                                \u003cspan id="commentCount-{{ $tarea-\u003eid }}" class="ml-1 bg-white text-blue-500 px-1.5 py-0.5 rounded-full text-xs font-bold"\u003e{{ $tarea-\u003ecomments-\u003ecount() }}\u003c/span\u003e
                            \u003c/button\u003e
                        \u003c/div\u003e

                        \u003cform id="editForm{{ $tarea-\u003eid }}" style="display:none;" action="{{ route('tareas.update', $tarea-\u003eid) }}" method="POST" class="edit-form p-4 bg-gray-100 dark:bg-gray-700"\u003e
                            @csrf
                            @method('PUT')
                            \u003cdiv class="grid grid-cols-1 md:grid-cols-2 gap-4"\u003e
                                \u003cdiv class="col-span-2"\u003e
                                    \u003clabel for="title{{ $tarea-\u003eid }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eTítulo\u003c/label\u003e
                                    \u003cinput type="text" id="title{{ $tarea-\u003eid }}" name="title" value="{{ $tarea-\u003etitle }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"\u003e
                                \u003c/div\u003e
                                \u003cdiv class="col-span-2"\u003e
                                    \u003clabel for="description{{ $tarea-\u003eid }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eDescripción\u003c/label\u003e
                                    \u003ctextarea id="description{{ $tarea-\u003eid }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"\u003e{{ $tarea-\u003edescription }}\u003c/textarea\u003e
                                \u003c/div\u003e
                                \u003cdiv\u003e
                                    \u003clabel for="start_date{{ $tarea-\u003eid }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eFecha de inicio\u003c/label\u003e
                                    \u003cinput type="date" id="start_date{{ $tarea-\u003eid }}" name="start_date" value="{{ $tarea-\u003estart_date-\u003eformat('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"\u003e
                                \u003c/div\u003e
                                \u003cdiv\u003e
                                    \u003clabel for="end_date{{ $tarea-\u003eid }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003eFecha de fin\u003c/label\u003e
                                    \u003cinput type="date" id="end_date{{ $tarea-\u003eid }}" name="end_date" value="{{ $tarea-\u003eend_date-\u003eformat('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"\u003e
                                \u003c/div\u003e
                                \u003cdiv class="col-span-2"\u003e
                                    \u003clabel for="priority{{ $tarea-\u003eid }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300"\u003ePrioridad\u003c/label\u003e
                                    \u003cselect id="priority{{ $tarea-\u003eid }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"\u003e
                                        \u003coption value="low" {{ $tarea-\u003epriority == 'low' ? 'selected' : '' }}\u003eBaja\u003c/option\u003e
                                        \u003coption value="medium" {{ $tarea-\u003epriority == 'medium' ? 'selected' : '' }}\u003eMedia\u003c/option\u003e
                                        \u003coption value="high" {{ $tarea-\u003epriority == 'high' ? 'selected' : '' }}\u003eAlta\u003c/option\u003e
                                        \u003coption value="urgent" {{ $tarea-\u003epriority == 'urgent' ? 'selected' : '' }}\u003eUrgente\u003c/option\u003e
                                    \u003c/select\u003e
                                \u003c/div\u003e
                            \u003c/div\u003e
                            \u003cdiv class="mt-4"\u003e
                                \u003cbutton type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out transform hover:scale-105"\u003e
                                    Guardar cambios
                                \u003c/button\u003e
                            \u003c/div\u003e
                        \u003c/form\u003e

                        \u003cdiv id="commentsSection-{{ $tarea-\u003eid }}" class="hidden bg-gray-50 dark:bg-gray-700 p-4 rounded-b-lg"\u003e
                            \u003ch4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"\u003eComentarios\u003c/h4\u003e
                            \u003cdiv id="commentsList-{{ $tarea-\u003eid }}" class="space-y-4 mb-6"\u003e
                                @foreach ($tarea-\u003ecomments as $comment)
                                    \u003cdiv id="comment-{{ $comment-\u003eid }}" class="flex items-start space-x-3 bg-white dark:bg-gray-600 p-4 rounded-lg shadow-sm"\u003e
                                        \u003cimg src="{{ $comment-\u003euser-\u003eavatar }}" alt="{{ $comment-\u003euser-\u003ename }}" class="w-10 h-10 rounded-full"\u003e
                                        \u003cdiv class="flex-grow"\u003e
                                            \u003cp class="text-sm text-gray-800 dark:text-gray-200"\u003e
                                                \u003cspan class="font-medium text-indigo-600 dark:text-indigo-400"\u003e{{ $comment-\u003euser-\u003ename }}\u003c/span\u003e
                                                \u003cspan class="text-gray-500 text-xs ml-2"\u003e{{ $comment-\u003ecreated_at-\u003ediffForHumans() }}\u003c/span\u003e
                                            \u003c/p\u003e
                                            \u003cp id="commentContent-{{ $comment-\u003eid }}" class="mt-1"\u003e{{ $comment-\u003econtent }}\u003c/p\u003e
                                            @if($comment-\u003euser_id == auth()-\u003eid())
                                                \u003cdiv class="mt-2 flex space-x-2"\u003e
                                                    \u003cbutton onclick="editEmployerComment({{ $comment-\u003eid }})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs transition duration-150 ease-in-out"\u003e
                                                        \u003ci class="fas fa-edit"\u003e\u003c/i\u003e Editar
                                                    \u003c/button\u003e
                                                    \u003cbutton onclick="deleteEmployerComment({{ $comment-\u003eid }}, {{ $tarea-\u003eid }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs transition duration-150 ease-in-out"\u003e
                                                        \u003ci class="fas fa-trash"\u003e\u003c/i\u003e Eliminar
                                                    \u003c/button\u003e
                                                \u003c/div\u003e
                                            @endif
                                        \u003c/div\u003e
                                    \u003c/div\u003e
                                @endforeach
                            \u003c/div\u003e
                            \u003cform onsubmit="addEmployerTaskComment(event, {{ $tarea-\u003eid }})" class="mt-4"\u003e
                                @csrf
                                \u003cdiv class="flex items-start space-x-4"\u003e
                                    \u003ctextarea id="newComment-{{ $tarea-\u003eid }}" rows="3" class="flex-grow p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none dark:bg-gray-600 dark:border-gray-500 dark:text-white" placeholder="Añadir un comentario..."\u003e\u003c/textarea\u003e
                                    \u003cbutton type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out transform hover:scale-105"\u003e
                                        \u003ci class="fas fa-paper-plane mr-2"\u003e\u003c/i\u003eComentar
                                    \u003c/button\u003e
                                \u003c/div\u003e
                            \u003c/form\u003e
                        \u003c/div\u003e
                    \u003c/div\u003e
                \u003c/div\u003e
            @endforeach
        @endif
    \u003c/div\u003e
\u003c/div\u003e

\u003cstyle\u003e
    .priority-badge {
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .priority-low { background-color: #E5F6FD; color: #0369A1; }
    .priority-medium { background-color: #FEF3C7; color: #92400E; }
    .priority-high { background-color: #FEE2E2; color: #B91C1C; }
    .priority-urgent { background-color: #FECACA; color: #7F1D1D; }
\u003c/style\u003e
