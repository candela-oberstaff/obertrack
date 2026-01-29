<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tareas asignadas a mi equipo') }}
        </h2>
    </x-slot>

    @php
        $managerTaskData = [
            'tasks' => $tareas,
            'currentUser' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'avatar' => auth()->user()->avatar ? (str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar)) : '',
                'initials' => substr(auth()->user()->name, 0, 1),
                'tipo_usuario' => auth()->user()->tipo_usuario,
                'is_superadmin' => auth()->user()->is_superadmin
            ]
        ];
    @endphp

    <div class="py-12" x-data="managerTaskTracking({{ json_encode($managerTaskData['tasks']) }}, {{ json_encode($managerTaskData['currentUser']) }})">
        <div class="hidden">
            {{-- This is a temporary placeholder to keep the variable scope during the phased edit --}}
        </div>


        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">Hay errores en el formulario:</p>
                            <ul class="mt-1 text-xs text-red-600 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                        <div class="w-full lg:w-1/3 relative">
                            <input type="text" x-model="searchQuery" placeholder="Buscar por tarea/profesional." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 p-1 rounded-lg border border-gray-200 dark:border-gray-600 w-full lg:w-auto">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-2 uppercase">Fecha:</span>
                            <input type="date" x-model="startDate" class="border-none bg-transparent focus:ring-0 text-sm p-1 rounded-lg dark:text-white">
                            <span class="text-gray-400">/</span>
                            <input type="date" x-model="endDate" class="border-none bg-transparent focus:ring-0 text-sm p-1 rounded-lg dark:text-white">
                        </div>

                        <button 
                            x-show="startDate || endDate || searchQuery" 
                            @click="startDate = ''; endDate = ''; searchQuery = ''"
                            class="text-xs text-gray-400 hover:text-primary font-medium transition-colors"
                        >
                            Limpiar
                        </button>

                        <button @click="openCreateTaskModal(null, true)" class="w-full lg:w-auto text-center inline-flex items-center justify-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-hover active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Crear Nueva Tarea
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kanban View (Desktop) -->
            <div class="hidden md:grid grid-cols-3 gap-6 items-start">
                
                @foreach([
                    ['id' => 'por_hacer', 'label' => 'Por hacer', 'color' => 'bg-gray-200', 'text' => 'text-gray-700'],
                    ['id' => 'en_proceso', 'label' => 'En proceso', 'color' => 'bg-blue-100', 'text' => 'text-[#22A9C8]'],
                    ['id' => 'finalizado', 'label' => 'Finalizado', 'color' => 'bg-green-100', 'text' => 'text-green-700']
                ] as $column)
                    <div class="flex flex-col gap-4 h-full" 
                         @dragover.prevent="dragOverColumnId = '{{ $column['id'] }}'" 
                         @dragleave="dragOverColumnId = null"
                         @drop="handleDrop($event, '{{ $column['id'] }}')">
                        
                        <!-- Column Header -->
                        <div class="flex items-center justify-between mb-2 px-2 select-none group-hover:text-[#22A9C8] transition-colors">
                            <h3 class="font-bold text-gray-700 text-lg">{{ $column['label'] }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $column['color'] }} {{ $column['text'] }}">
                                {{ $managerTaskData['tasks']->where('status', $column['id'])->count() }}
                            </span>
                        </div>

                        <!-- Tasks Column -->
                        <div class="space-y-3 min-h-[200px] h-full rounded-2xl transition-all duration-200 border-2 border-transparent" 
                             :class="{
                                'bg-gray-50/50 border-dashed border-gray-300': draggedTaskId,
                                '!bg-blue-50/80 !border-blue-300 ring-2 ring-blue-100': dragOverColumnId === '{{ $column['id'] }}'
                             }">
                            @forelse($managerTaskData['tasks']->where('status', $column['id']) as $task)
                                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all cursor-grab active:cursor-grabbing group relative"
                                     draggable="true"
                                     @dragstart="dragStart($event, {{ $task->id }})"
                                     @dragend="dragEnd($event)"
                                     id="task-card-{{ $task->id }}"
                                     data-title="{{ $task->title }}"
                                     data-date="{{ $task->end_date ? $task->end_date->format('Y-m-d') : '' }}"
                                     data-assignees="{{ $task->assignees->pluck('name')->join(',') }}"
                                     x-show="matchRow($el)"
                                     @click='openDetailsModal(@json($task, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT))'>
                                    
                                    <!-- Card Header -->
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="font-bold text-gray-800 leading-snug line-clamp-2 pr-6">{{ $task->title }}</h4>
                                        
                                        @if($task->priority === 'high')
                                            <span class="w-2 h-2 rounded-full bg-red-400 absolute top-5 right-5" title="Prioridad Alta"></span>
                                        @elseif($task->priority === 'medium')
                                            <span class="w-2 h-2 rounded-full bg-yellow-400 absolute top-5 right-5" title="Prioridad Media"></span>
                                        @endif
                                    </div>

                                    <!-- Date & Assignees -->
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-4">
                                        @if($task->end_date)
                                            <div class="flex items-center gap-1 {{ $task->end_date->isPast() && $task->status !== 'finalizado' ? 'text-red-500 font-bold' : '' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span>{{ $task->end_date->format('d M') }}</span>
                                            </div>
                                        @endif
                                        
                                        <div class="flex -space-x-2">
                                            @foreach($task->assignees->take(3) as $assignee)
                                                <div class="w-6 h-6 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-[10px] uppercase font-bold text-gray-600" title="{{ $assignee->name }}">
                                                    {{ substr($assignee->name, 0, 1) }}
                                                </div>
                                            @endforeach
                                            @if($task->assignees->count() > 3)
                                                <div class="w-6 h-6 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-[9px] font-bold text-gray-500">
                                                    +{{ $task->assignees->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Footer Actions -->
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                        <!-- Status Selector (Wrapped to prevent propagation) -->
                                        <div @click.stop class="transform scale-90 origin-left">
                                            <livewire:task-status-selector :task="$task" :wire:key="'kanban-'.$task->id" />
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <button @click.stop='openDetailsModal(@json($task, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), "comments")' class="flex items-center gap-1 text-gray-400 hover:text-[#22A9C8] transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                <span class="text-xs font-medium">{{ $task->comments->count() }}</span>
                                            </button>
                                            
                                            <button @click.stop='openDetailsModal(@json($task, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), "files")' class="flex items-center gap-1 text-gray-400 hover:text-[#22A9C8] transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                <span class="text-xs font-medium">{{ $task->attachments->count() }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 opacity-50 bg-white/50 rounded-2xl border border-dashed border-gray-200">
                                    <p class="text-sm text-gray-400 italic">Vacío</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Mobile List View (Fallback for mobile, kept simple or can be updated later) -->
            <div class="md:hidden space-y-4">
                 <template x-for="tarea in filteredTasks" :key="tarea.id">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 transition duration-300 ease-in-out hover:shadow-lg cursor-pointer" 
                         @click='openDetailsModal(tarea)'>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200" x-text="tarea.title"></h3>
                                <div class="flex items-center space-x-3">
                                    <button @click.stop="toggleTaskCompletion(tarea)" 
                                            class="flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold transition-all border shadow-sm"
                                            :class="tarea.completed ? 'bg-green-100 text-green-700 border-green-200 hover:bg-white' : 'bg-gray-100 text-gray-400 border-gray-200 hover:bg-green-50 hover:text-green-600 hover:border-green-200'">
                                        <template x-if="tarea.completed">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        </template>
                                        <span x-text="tarea.completed ? 'Completado' : 'Pendiente'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                 <div x-show="filteredTasks.length === 0" class="text-center py-12 text-gray-500">
                    No se encontraron tareas con estos criterios.
                </div>
            </div>

            @include('tareas.partials.task-details-modal')
            @include('empleadores.tareas.partials.create-modal')
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('managerTaskTracking', (initialTasks, currentUser) => ({
                allTasks: initialTasks,
                currentUser: currentUser,
                startDate: '',
                endDate: '',
                searchQuery: '',
                selectedTask: null,
                isDetailsModalOpen: false,
                isCreateModalOpen: false,
                currentTab: 'details',
                isTeamTask: true,
                targetEmployeeId: null,
                
                // UI State
                hasChanges: false,
                isUploadingFile: false,
                newCommentText: '',
                isSubmittingComment: false,
                editingCommentId: null,
                editCommentContent: '',
                
                // Drag and Drop State
                draggedTaskId: null,
                dragOverColumnId: null,

                // Delete Confirmation State
                deleteConfirmation: { isOpen: false, type: null, id: null },
                
                // Task Edit State
                isEditingTask: false,
                isSavingTask: false,
                editTaskData: {
                    title: '',
                    description: '',
                    priority: '',
                    end_date: ''
                },
                
                get filteredTasks() {
                    let tasks = [...this.allTasks];
                    if (this.startDate || this.endDate) {
                        tasks = tasks.filter(t => {
                            const date = t.end_date ? t.end_date.split('T')[0] : '';
                            if (this.startDate && date < this.startDate) return false;
                            if (this.endDate && date > this.endDate) return false;
                            return true;
                        });
                    }
                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        tasks = tasks.filter(t => {
                            const matchTitle = t.title.toLowerCase().includes(q);
                            const matchAssignee = t.assignees.some(a => 
                                a.name.toLowerCase().includes(q) || 
                                (a.email && a.email.toLowerCase().includes(q))
                            );
                            return matchTitle || matchAssignee;
                        });
                    }
                    return tasks;
                },

                matchRow(el) {
                    const taskDate = el.dataset.date || '';
                    const title = (el.dataset.title || '').toLowerCase();
                    const assignees = (el.dataset.assignees || '').toLowerCase();
                    const q = this.searchQuery.toLowerCase();

                    if (this.searchQuery) {
                        if (!title.includes(q) && !assignees.includes(q)) return false;
                    }
                    
                    if (this.startDate && taskDate < this.startDate) return false;
                    if (this.endDate && taskDate > this.endDate) return false;
                    
                    return true;
                },

                formatDate(d) {
                    if (!d) return '--/--/----';
                    const parts = d.split('T')[0].split('-');
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                },

                closeModal() {
                    this.isDetailsModalOpen = false;
                    this.selectedTask = null;
                    if (this.hasChanges) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 50);
                    }
                },

                openDetailsModal(task, tab = 'details') {
                    this.selectedTask = task;
                    this.currentTab = tab;
                    this.isDetailsModalOpen = true;
                    this.hasChanges = false;
                    this.fetchTaskDetails(task.id);
                },

                async fetchTaskDetails(id) {
                     try {
                        const response = await fetch(`/tasks/${id}/details?t=${new Date().getTime()}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (this.selectedTask && this.selectedTask.id === id) {
                                this.selectedTask = { 
                                    ...this.selectedTask, 
                                    comments: data.comments, 
                                    attachments: data.attachments,
                                    completed: data.completed,
                                    status: data.status
                                };
                            }
                        }
                    } catch (error) { console.error('Error fetching details:', error); }
                },

                dragStart(event, taskId) {
                    this.draggedTaskId = taskId;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', taskId);
                    event.dataTransfer.setData('application/json', JSON.stringify({ taskId: taskId }));
                    requestAnimationFrame(() => {
                        event.target.classList.add('opacity-50', 'scale-95', 'rotate-1');
                    });
                },

                dragEnd(event) {
                    this.draggedTaskId = null;
                    this.dragOverColumnId = null;
                    event.target.classList.remove('opacity-50', 'scale-95', 'rotate-1');
                },

                async handleDrop(event, newStatus) {
                    const taskId = this.draggedTaskId;
                    this.dragOverColumnId = null;
                    this.draggedTaskId = null;
                    
                    if (!taskId) return;
                    
                    const card = document.getElementById(`task-card-${taskId}`);
                    if (card) card.classList.remove('opacity-50', 'scale-95', 'rotate-1');

                    if (card) {
                        const columnContainer = event.currentTarget.querySelector('.space-y-3');
                        if (columnContainer) {
                            columnContainer.appendChild(card);
                        }
                    }

                    await this.updateTaskStatusV2(taskId, newStatus);
                },

                async updateTaskStatusV2(taskId, newStatus) {
                   try {
                        const response = await fetch(`/tareas/${taskId}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ status: newStatus })
                        });
                        
                        const data = await response.json();

                        if (response.ok && data.success) {
                             setTimeout(() => {
                                const currentUrl = new URL(window.location.href);
                                currentUrl.searchParams.set('t', new Date().getTime());
                                window.location.href = currentUrl.toString();
                             }, 500); 
                        } else {
                            showError(data.message || 'Error al actualizar estado');
                            setTimeout(() => location.reload(), 1500); 
                        }
                   } catch (e) {
                       console.error(e);
                       showError('Error de conexión con el servidor');
                       setTimeout(() => location.reload(), 1500); 
                   }
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
                        const response = await fetch(`/manager/tasks/${taskId}/comment`, {
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
                            this.hasChanges = true;
                        } else {
                            this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== tempId);
                            showError('Error al enviar el comentario.');
                        }
                    } catch (error) {
                        this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== tempId);
                        console.error('Error:', error);
                        showError('Error de conexión.');
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
                        const response = await fetch(`/manager/comments/${commentId}`, {
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
                            this.hasChanges = true;
                        } else {
                            showError('Error al actualizar.');
                        }
                    } catch (error) {
                        console.error(error);
                        showError('Error de conexión');
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
                        const response = await fetch(`/manager/tasks/${this.selectedTask.id}/files`, {
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
                            this.hasChanges = true;
                        } else {
                            const data = await response.json();
                            showError(data.message || 'Error al subir el archivo.');
                        }
                    } catch (error) {
                        console.error(error);
                        showError('Error de conexión.');
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
                                this.hasChanges = true;
                            } else {
                                const data = await response.json();
                                showError(data.message || 'Error al eliminar archivo');
                            }
                        } catch (e) { showError('Error de conexión'); }
                    } else if (type === 'comment') {
                        try {
                            const response = await fetch(`/manager/comments/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                             if (response.ok) {
                                this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== id);
                                this.hasChanges = true;
                            } else {
                                showError('Error al eliminar comentario');
                            }
                        } catch (e) { showError('Error de conexión'); }
                    } else if (type === 'task') {
                        try {
                            const response = await fetch(`/tareas/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (response.ok) {
                                this.isDetailsModalOpen = false;
                                location.reload();
                            } else {
                                const data = await response.json();
                                showError(data.message || 'Error al eliminar tarea');
                            }
                        } catch (e) { showError('Error de conexión'); }
                    }
                },

                startEditingTask() {
                    this.editTaskData = {
                        title: this.selectedTask.title,
                        description: this.selectedTask.description || '',
                        priority: this.selectedTask.priority,
                        start_date: (this.selectedTask.start_date || '').split('T')[0],
                        end_date: (this.selectedTask.end_date || '').split('T')[0]
                    };
                    this.isEditingTask = true;
                },

                async saveTask() {
                    this.isSavingTask = true;
                    try {
                        const response = await fetch(`/tareas/${this.selectedTask.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.editTaskData)
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.selectedTask = data.task;
                            this.isEditingTask = false;
                            this.hasChanges = true;
                        } else {
                            const data = await response.json();
                            showError(data.message || 'Error al guardar cambios');
                        }
                    } catch (e) {
                        console.error(e);
                        showError('Error de conexión');
                    } finally {
                        this.isSavingTask = false;
                    }
                },

                confirmDeleteTask() {
                    this.deleteConfirmation = { isOpen: true, type: 'task', id: this.selectedTask.id };
                },
                openCreateTaskModal(employeeId = null, isTeam = false) {
                    this.isTeamTask = isTeam;
                    this.targetEmployeeId = employeeId;
                    this.isCreateModalOpen = true;
                },

                async toggleTaskCompletion(task) {
                    // Legacy toggling kept for mobile view, but ideally should use updateTaskStatusV2 too
                    // For now, let's keep it but ensure it reloads
                    if (this.isSavingTask) return;
                    this.isSavingTask = true;
                    
                    try {
                        const response = await fetch(`/tasks/${task.id}/toggle-completion`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            // Reload to sync kanban
                            location.reload();
                        } else {
                            showError(data.message || 'Error al actualizar el estado');
                        }
                    } catch (e) {
                        console.error(e);
                        showError('Error de conexión');
                    } finally {
                        this.isSavingTask = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>