<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center max-w-7xl mx-auto px-4 sm:px-0">
             <h2 class="font-bold text-2xl md:text-3xl text-gray-800 dark:text-gray-800 leading-tight">
                {{ __('Seguimiento de tareas') }}
            </h2>
        </div>
    </x-slot>

    @php
        $employeeTaskData = [
            'currentUser' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'avatar' => auth()->user()->avatar ? (str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar)) : '',
                'initials' => substr(auth()->user()->name, 0, 1),
                'tipo_usuario' => auth()->user()->tipo_usuario,
                'is_superadmin' => auth()->user()->is_superadmin
            ],
            'pendingCount' => $pendingTasksCount,
            'completedCount' => $completedTasksCount
        ];
    @endphp

    <div class="py-8 bg-white min-h-screen" x-data="employeeTaskTracking({{ json_encode($employeeTaskData) }})"
    @task-status-updated.window="handleStatusUpdate($event.detail)"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">


            {{-- Vistazo General Cards --}}
            <section class="px-2 sm:px-0">
                <h3 class="text-primary font-medium text-base md:text-lg mb-4">Vistazo general</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
                    {{-- Pending Tasks Card --}}
                    <div class="bg-gray-100 rounded-3xl p-6 md:p-8 flex justify-between items-center shadow-none">
                        <div>
                            <p class="text-gray-800 font-bold mb-2 text-sm md:text-base">Tareas pendientes</p>
                            <p class="text-4xl md:text-6xl font-extrabold text-black" x-text="String(pendingCount).padStart(2, '0')">{{ str_pad($pendingTasksCount, 2, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="text-right text-[10px] md:text-sm text-gray-500">

                            <p>Grupales: {{ $teamTasks->where('completed', false)->count() }}</p>
                        </div>
                    </div>

                    {{-- Completed Tasks Card --}}
                    <div class="bg-gray-100 rounded-3xl p-6 md:p-8 flex justify-between items-center shadow-none">
                        <div>
                            <p class="text-gray-800 font-bold mb-2 text-sm md:text-base">Tareas completadas con éxito</p>
                            <div class="flex items-center">
                                <p class="text-4xl md:text-6xl font-extrabold text-black" x-text="String(completedCount).padStart(2, '0')">{{ str_pad($completedTasksCount, 2, '0', STR_PAD_LEFT) }}</p>
                                <div class="ml-4 bg-green-500 rounded-full p-1.5 md:p-2 text-white">
                                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Team Assignments Table --}}
            <section class="px-2 sm:px-0">
                <h3 class="text-primary font-medium text-base md:text-lg mb-4">Mis asignaciones</h3>
                
                {{-- Date Filters --}}
                <div class="flex flex-col sm:flex-row items-center gap-4 mb-6">
                    <div class="flex items-center gap-2 bg-gray-50 rounded-2xl border border-gray-200 p-2 shadow-sm w-full sm:w-auto">
                        <span class="text-xs font-bold text-gray-500 ml-2 uppercase">Filtrar por fecha:</span>
                        <input type="date" x-model="startDate" class="border-none bg-transparent focus:ring-0 text-sm p-1 rounded-lg w-full sm:w-auto" placeholder="Desde">
                        <span class="text-gray-400">/</span>
                        <input type="date" x-model="endDate" class="border-none bg-transparent focus:ring-0 text-sm p-1 rounded-lg w-full sm:w-auto" placeholder="Hasta">
                    </div>

                    {{-- Search Box --}}
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchQuery" placeholder="Buscar tarea..." class="w-full pl-10 pr-4 py-2 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary text-sm bg-gray-50">
                        <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <button @click="startDate = ''; endDate = ''; searchQuery = ''" class="text-xs text-gray-400 hover:text-primary transition-colors font-medium" x-show="startDate || endDate || searchQuery">
                        Limpiar filtros
                    </button>
                </div>
                
                <div class="bg-white rounded-3xl border-2 border-primary p-2 md:p-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-black text-sm font-bold">
                                    <th class="p-4 pl-6">Título</th>
                                    <th class="p-4">Fecha límite</th>
                                    <th class="p-4">Asignado</th>
                                    <th class="p-4">Estado</th>
                                    <th class="p-4 text-center">Comentarios</th>
                                    <th class="p-4 text-center">Archivos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamTasks as $task)
                                    <tr class="bg-gray-50 hover:bg-gray-100 transition group rounded-lg" 
                                        x-show="matches({ date: '{{ $task->end_date->format('Y-m-d') }}', title: '{{ addslashes($task->title) }}' })"
                                    >
                                        <td @click='openDetailsModal(@json($task, JSON_HEX_APOS))' class="p-4 pl-6 font-medium text-gray-800 rounded-l-lg cursor-pointer hover:text-primary transition-colors">
                                            {{ $task->title }}
                                            <div class="text-xs text-gray-500 font-normal mt-1">{{ Str::limit($task->description, 50) }}</div>
                                        </td>
                                        <td class="p-4">
                                            <span class="{{ \Carbon\Carbon::parse($task->end_date)->isPast() && !$task->completed ? 'text-red-500' : 'text-gray-600' }}">
                                                {{ \Carbon\Carbon::parse($task->end_date)->format('d-m-Y') }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex -space-x-2 overflow-hidden">
                                                @foreach($task->assignees->take(3) as $assignee)
                                                     <x-user-avatar :user="$assignee" size="8" class="ring-2 ring-white -ml-2 first:ml-0" title="{{ $assignee->name }}" />
                                                @endforeach
                                                @if($task->assignees->count() > 3)
                                                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 text-xs font-bold ring-2 ring-white">
                                                        +{{ $task->assignees->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <livewire:task-status-selector :task="$task" :wire:key="'task-status-'.$task->id" />
                                        </td>
                                        <td class="p-4 text-center">
                                            <button @click.stop="openDetailsModal(@json($task, JSON_HEX_APOS), 'comments')" class="text-gray-500 hover:text-primary transition flex items-center justify-center mx-auto space-x-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                                <span>{{ $task->comments->count() }}</span>
                                            </button>
                                        </td>
                                        <td class="p-4 text-center rounded-r-lg">
                                            <button @click.stop="openDetailsModal(@json($task, JSON_HEX_APOS), 'files')" class="text-gray-500 hover:text-primary transition flex items-center justify-center mx-auto space-x-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span>{{ $task->attachments->count() }}</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-6 text-center text-gray-500">No tienes asignaciones en equipo.</td>
                                    </tr>
                                @endforelse
                                <!-- Empty state when filtered -->
                                <tr x-show="[...$el.parentElement.children].filter(c => c.tagName === 'TR' && c.style.display !== 'none').length === 0" style="display: none;">
                                    <td colspan="6" class="p-6 text-center text-gray-500 italic">No se encontraron tareas en este rango de fechas.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>


        </div>
    {{-- Modals --}}
    @include('tareas.partials.task-details-modal')
    </div>


    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('employeeTaskTracking', (config) => ({
                currentUser: config.currentUser,
                pendingCount: config.pendingCount,
                completedCount: config.completedCount,
                startDate: '',
                endDate: '',
                searchQuery: '',
                selectedTask: null,
                isDetailsModalOpen: false,
                currentTab: 'details',
                isEditingTask: false,
                isSavingTask: false,
                editTaskData: {
                    id: null,
                    title: '',
                    description: '',
                    priority: 'low',
                    end_date: ''
                },
                isUploadingFile: false,
                newCommentText: '',
                isSubmittingComment: false,
                editingCommentId: null,
                editCommentContent: '',
                deleteConfirmation: {
                    isOpen: false,
                    type: null,
                    id: null
                },
                updatedTaskStates: {},

                matches(task) {
                    const taskDate = task.date;
                    const taskTitle = task.title.toLowerCase();
                    const q = this.searchQuery.toLowerCase();
                    if (this.searchQuery && !taskTitle.includes(q)) return false;
                    if (!this.startDate && !this.endDate) return true;
                    if (this.startDate && taskDate < this.startDate) return false;
                    if (this.endDate && taskDate > this.endDate) return false;
                    return true;
                },

                openDetailsModal(task, tab = 'details') {
                    if (this.updatedTaskStates[task.id]) {
                        task.status = this.updatedTaskStates[task.id].status;
                        task.completed = this.updatedTaskStates[task.id].completed;
                    }
                    this.selectedTask = task;
                    this.currentTab = tab;
                    this.isDetailsModalOpen = true;
                    this.isEditingTask = false;
                },

                startEditingTask() {
                    if (!this.selectedTask) return;
                    this.editTaskData = {
                        id: this.selectedTask.id,
                        title: this.selectedTask.title,
                        description: this.selectedTask.description,
                        priority: this.selectedTask.priority,
                        end_date: this.selectedTask.end_date ? this.selectedTask.end_date.split('T')[0] : ''
                    };
                    this.isEditingTask = true;
                },

                async saveTask() {
                    this.isSavingTask = true;
                    await new Promise(resolve => setTimeout(resolve, 500));
                    this.selectedTask.title = this.editTaskData.title;
                    this.selectedTask.description = this.editTaskData.description;
                    this.selectedTask.priority = this.editTaskData.priority;
                    this.selectedTask.end_date = this.editTaskData.end_date;
                    this.isSavingTask = false;
                    this.isEditingTask = false;
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const parts = dateStr.split('T')[0].split('-');
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                },

                async submitComment() {
                    if (!this.newCommentText.trim()) return;
                    this.isSubmittingComment = true;
                    const taskId = this.selectedTask.id;
                    const content = this.newCommentText;
                    const tempId = 'temp_' + Date.now();
                    const optimisticComment = {
                        id: tempId,
                        content: content,
                        created_at: new Date().toISOString(),
                        user: this.currentUser,
                        task_id: taskId
                    };
                    if (!this.selectedTask.comments) this.selectedTask.comments = [];
                    this.selectedTask.comments.unshift(optimisticComment);
                    this.newCommentText = '';
                    try {
                        const response = await fetch(`/empleados/tareas/${taskId}/comment`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ content: content })
                        });
                        if (response.ok) {
                            const data = await response.json();
                            const index = this.selectedTask.comments.findIndex(c => c.id === tempId);
                            if (index !== -1) this.selectedTask.comments[index] = data.comment;
                        } else {
                            this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== tempId);
                            alert('Error al enviar el comentario.');
                        }
                    } catch (error) {
                        this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== tempId);
                        alert('Error de conexión.');
                    } finally {
                        this.isSubmittingComment = false;
                    }
                },

                startEditingComment(comment) {
                    this.editingCommentId = comment.id;
                    this.editCommentContent = comment.content;
                },

                async updateComment(commentId) {
                    if (!this.editCommentContent.trim()) return;
                    try {
                        const response = await fetch(`/empleados/tareas/comment/${commentId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ content: this.editCommentContent })
                        });
                        if (response.ok) {
                            const data = await response.json();
                            const index = this.selectedTask.comments.findIndex(c => c.id === commentId);
                            if (index !== -1) this.selectedTask.comments[index] = data.comment;
                            this.editingCommentId = null;
                        } else {
                            alert('Error al actualizar.');
                        }
                    } catch (error) {
                        alert('Error de conexión');
                    }
                },

                confirmDeleteComment(id) {
                    this.deleteConfirmation = { isOpen: true, type: 'comment', id: id };
                },

                async uploadFile(file) {
                    if (!file || this.isUploadingFile) return;
                    this.isUploadingFile = true;
                    const formData = new FormData();
                    formData.append('file', file);
                    try {
                        const response = await fetch(`/empleados/tareas/${this.selectedTask.id}/files`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (!this.selectedTask.attachments) this.selectedTask.attachments = [];
                            this.selectedTask.attachments.unshift(data.attachment);
                        } else {
                            const data = await response.json();
                            alert(data.message || 'Error al subir el archivo.');
                        }
                    } catch (error) {
                        alert('Error de conexión.');
                    } finally {
                        this.isUploadingFile = false;
                    }
                },

                confirmDeleteFile(id) {
                    this.deleteConfirmation = { isOpen: true, type: 'file', id: id };
                },

                async performDelete() {
                    const { type, id } = this.deleteConfirmation;
                    this.deleteConfirmation.isOpen = false;
                    if (type === 'file') {
                        try {
                            const response = await fetch(`/tasks/attachments/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (response.ok) {
                                this.selectedTask.attachments = this.selectedTask.attachments.filter(a => a.id !== id);
                            } else {
                                const data = await response.json();
                                alert(data.message || 'Error al eliminar archivo');
                            }
                        } catch (e) { alert('Error de conexión'); }
                    } else if (type === 'comment') {
                        try {
                            const response = await fetch(`/empleados/tareas/comment/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (response.ok) {
                                this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== id);
                            } else {
                                alert('Error al eliminar comentario');
                            }
                        } catch (e) { alert('Error de conexión'); }
                    }
                },

                handleStatusUpdate(detail) {
                    let previousCompleted = detail.wasCompleted;
                    if (this.updatedTaskStates[detail.taskId]) {
                        previousCompleted = this.updatedTaskStates[detail.taskId].completed;
                    }
                    this.updatedTaskStates[detail.taskId] = {
                        status: detail.status,
                        completed: detail.completed
                    };
                    if (this.selectedTask && this.selectedTask.id == detail.taskId) {
                        this.selectedTask.status = detail.status;
                        this.selectedTask.completed = detail.completed;
                    }
                    if (!previousCompleted && detail.completed) {
                        this.pendingCount = Math.max(0, this.pendingCount - 1);
                        this.completedCount++;
                    } else if (previousCompleted && !detail.completed) {
                        this.pendingCount++;
                        this.completedCount = Math.max(0, this.completedCount - 1);
                    }
                }
            }));
        });
    </script>
</x-app-layout>