<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tareas asignadas a mi equipo') }}
        </h2>
    </x-slot> 

    <div class="py-12" x-data="{
        currentUser: {{ json_encode([
            'id' => auth()->id(),
            'name' => auth()->user()->name,
            'avatar' => auth()->user()->avatar ? (str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar)) : '',
            'initials' => substr(auth()->user()->name, 0, 1),
            'tipo_usuario' => auth()->user()->tipo_usuario,
            'is_superadmin' => auth()->user()->is_superadmin
        ]) }},
        startDate: '',
        endDate: '',
        searchQuery: '',
        selectedTask: null,
        isDetailsModalOpen: false,
        currentTab: 'details',
        
        // UI State
        isUploadingFile: false,
        newCommentText: '',
        isSubmittingComment: false,
        editingCommentId: null,
        editCommentContent: '',
        
        // Delete Confirmation State
        deleteConfirmation: { isOpen: false, type: null, id: null },
        
        allTasks: @json($tareas),
        get filteredTasks() {
            let tasks = [...this.allTasks];
            if (this.startDate || this.endDate) {
                tasks = tasks.filter(t => {
                    const date = t.end_date.split('T')[0];
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
        formatDate(d) {
            if (!d) return '--/--/----';
            const parts = d.split('T')[0].split('-');
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        },
        openDetailsModal(task, tab = 'details') {
            this.selectedTask = task;
            this.currentTab = tab;
            this.isDetailsModalOpen = true;
        },

        // --- Comment Logic ---

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
                // Route: POST /manager/tasks/{task}/comment
                const response = await fetch(`/manager/tasks/${taskId}/comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
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
                console.error('Error:', error);
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
                // Route: PUT /manager/comments/{comment}
                const response = await fetch(`/manager/comments/${commentId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
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
                console.error(error);
                alert('Error de conexión');
            }
        },

        confirmDeleteComment(id) {
            this.deleteConfirmation = { isOpen: true, type: 'comment', id: id };
        },

        // --- File Logic ---

        async uploadFile(file) {
            if (!file) return;
            this.isUploadingFile = true;

            const formData = new FormData();
            formData.append('file', file);
            
            try {
                 // Route: POST /manager/tasks/{task}/files
                const response = await fetch(`/manager/tasks/${this.selectedTask.id}/files`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
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
                console.error(error);
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
                    // Generic Route: DELETE /tasks/attachments/{attachment}
                    const response = await fetch(`/tasks/attachments/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
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
                    // Route: DELETE /manager/comments/{comment}
                    const response = await fetch(`/manager/comments/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                        }
                    });
                     if (response.ok) {
                        this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== id);
                    } else {
                        alert('Error al eliminar comentario');
                    }
                } catch (e) { alert('Error de conexión'); }
            }
        }
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                        <div class="w-full lg:w-1/3 relative">
                            <input type="text" x-model="searchQuery" placeholder="Buscar por tarea o profesional (nombre/email)..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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

                        <a href="{{ route('manager.tasks.create') }}" class="w-full lg:w-auto text-center inline-flex items-center justify-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-hover active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Crear Nueva Tarea

                        </a>
                    </div>
                </div>
            </div>

            <template x-for="tarea in filteredTasks" :key="tarea.id">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 transition duration-300 ease-in-out hover:shadow-lg cursor-pointer" 
                     @click='openDetailsModal(tarea)'>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200" x-text="tarea.title"></h3>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-800': tarea.priority === 'low',
                                        'bg-yellow-100 text-yellow-800': tarea.priority === 'medium',
                                        'bg-orange-100 text-orange-800': tarea.priority === 'high',
                                        'bg-red-100 text-red-800': tarea.priority === 'urgent'
                                    }" x-text="tarea.priority.charAt(0).toUpperCase() + tarea.priority.slice(1)">
                                </span>
                                <button class="text-primary hover:text-blue-800 focus:outline-none flex items-center" @click.stop='openDetailsModal(tarea)'>
                                    <span class="mr-2 text-sm">Ver detalles</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </template>

            @include('tareas.partials.task-details-modal')

            <div x-show="filteredTasks.length === 0" class="text-center py-12 text-gray-500">
                No se encontraron tareas con estos criterios.
            </div>
        </div>
    </div>
</x-app-layout>