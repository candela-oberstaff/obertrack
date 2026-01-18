<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 max-w-7xl mx-auto px-4 sm:px-0">
             <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0D1E4C] leading-tight">
                {{ __('Seguimiento de tareas') }}
            </h2>
        </div>
    </x-slot>

    @php
        $currentUserData = [
            'id' => auth()->id(),
            'name' => auth()->user()->name,
            'avatar' => auth()->user()->avatar ? (str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('avatars/' . auth()->user()->avatar)) : '',
            'initials' => substr(auth()->user()->name, 0, 1),
            'tipo_usuario' => auth()->user()->tipo_usuario,
            'is_superadmin' => auth()->user()->is_superadmin
        ];
    @endphp

    <div class="py-12 bg-gray-50 min-h-screen font-sans" x-data="{
        currentUser: {{ json_encode($currentUserData) }},
        startDate: '',
        endDate: '',
        searchQuery: '',
        isTeamTask: true,
        targetEmployeeId: null,
        
        // Modal States
        selectedTask: null,
        isDetailsModalOpen: false,
        isCreateModalOpen: false,
        currentTab: 'details', // 'details', 'comments', 'files'
        
        // Mobile View State
        mobileView: '',
        
        // Comment/File State
        isUploadingFile: false,
        newCommentText: '',
        isSubmittingComment: false,
        editingCommentId: null,
        editCommentContent: '',
        
        // Delete Confirmation State
        deleteConfirmation: {
            isOpen: false,
            type: null, // 'file', 'comment', or 'task'
            id: null
        },

        // Task Edit State
        isEditingTask: false,
        isSavingTask: false,
        editTaskData: {
            title: '',
            description: '',
            priority: '',
            end_date: ''
        },

        // Comment Input State (Moved from modal)
        isAddingComment: false,
        commentText: '',
        isSubmitting: false,
        isDeleting: false,

        // Helper to check if a task matches filters
        formatDate(dateString) {
            if (!dateString) return '';
            // Handle ISO string or YYYY-MM-DD by extracting just the date part
            const datePart = dateString.split('T')[0];
            const [year, month, day] = datePart.split('-');
            return `${day}/${month}/${year}`;
        },

        matches(taskEndRaw, taskTitle, assigneeNames) {
            const taskDate = taskEndRaw.split('T')[0]; // Assuming ISO string or YYYY-MM-DD
            const q = this.searchQuery.toLowerCase();
            const title = taskTitle.toLowerCase();
            const assignees = assigneeNames.toLowerCase();

            // Search Query
            if (this.searchQuery) {
                if (!title.includes(q) && !assignees.includes(q)) return false;
            }
            
            // Date Range
            if (this.startDate && taskDate < this.startDate) return false;
            if (this.endDate && taskDate > this.endDate) return false;
            
            return true;
        },

        openDetailsModal(task, tab = 'details') {
            this.selectedTask = task;
            this.currentTab = tab;
            this.isDetailsModalOpen = true;
            this.isCommentsModalOpen = false; // Ensure legacy is closed
            this.isFilesModalOpen = false; // Ensure legacy is closed
        },
        
        openCreateTaskModal(employeeId = null, isTeam = false) {
            this.isTeamTask = isTeam;
            this.targetEmployeeId = employeeId;
            this.isCreateModalOpen = true;
        },

        // --- Comment Logic ---

        async submitComment() {
            if (!this.newCommentText.trim()) return;
            this.isSubmittingComment = true;
            
            const taskId = this.selectedTask.id;
            const content = this.newCommentText;
            
            // Optimistic Update
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
            this.newCommentText = ''; // Clear input

            try {
                const response = await fetch(`/empleador/tareas/${taskId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ content: content })
                });

                if (response.ok) {
                    const data = await response.json();
                    // Replace optimistic
                    const index = this.selectedTask.comments.findIndex(c => c.id === tempId);
                    if (index !== -1) this.selectedTask.comments[index] = data.comment;
                } else {
                    // Revert
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
                const response = await fetch(`/empleador/comments/${commentId}`, {
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
            formData.append('task_id', this.selectedTask.id); // Changed to selectedTask

            try {
                 // Use fetch instead of XMLHttpRequest for cleaner logic
                const response = await fetch(`/empleador/tareas/${this.selectedTask.id}/files`, {
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
                    alert('Error al subir el archivo.');
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
            if (this.isDeleting) return;
            this.isDeleting = true;

            const { type, id } = this.deleteConfirmation;
            // Keep the modal open while deleting to show loading state if desired, or close it. 
            // For now, keeping logic similar but adding safety.
            
            if (type === 'file') {
                try {
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
                // Delete Comment Logic
                try {
                    const response = await fetch(`/empleador/comments/${id}`, {
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
            } else if (type === 'task') {
                try {
                    const response = await fetch(`/tareas/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (response.ok) {
                        this.deleteConfirmation.isOpen = false;
                        this.isDetailsModalOpen = false;
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('t', new Date().getTime());
                        window.location.href = currentUrl.toString();
                    } else {
                        const data = await response.json();
                        alert(data.message || 'Error al eliminar tarea');
                        this.deleteConfirmation.isOpen = false;
                    }
                } catch (e) { 
                    alert('Error de conexión'); 
                    this.deleteConfirmation.isOpen = false;
                } finally {
                    this.isDeleting = false;
                }
            } else {
                // For other types (file, comment), just close and reset for now
                if (type === 'file' || type === 'comment') {
                     // ... logic simplified above, ensure finally resets isDeleting
                     this.isDeleting = false; 
                     this.deleteConfirmation.isOpen = false;
                }
            }
        },

        // --- Task Edit Actions ---

        startEditingTask() {
            this.editTaskData = {
                title: this.selectedTask.title,
                description: this.selectedTask.description || '',
                priority: this.selectedTask.priority,
                start_date: (this.selectedTask.start_date || '').split('T')[0],
                end_date: (this.selectedTask.end_date || '').split('T')[0],
                assignees: this.selectedTask.assignees ? this.selectedTask.assignees.map(a => a.id) : []
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.editTaskData)
                });

                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok) {
                    this.selectedTask = data.task;
                    this.isEditingTask = false;
                    // Force non-cached reload explicitly
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('t', new Date().getTime());
                    window.location.href = currentUrl.toString();
                } else {
                    // Handle validation errors (422)
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join('\n');
                        alert('Errores de validación:\n' + errorMessages);
                    } else {
                        alert(data.message || 'Error al guardar cambios');
                    }
                }
            } catch (e) {
                console.error('Network error:', e);
                alert('Error de conexión');
            } finally {
                this.isSavingTask = false;
            }
        },

        confirmDeleteTask() {
            this.deleteConfirmation = { isOpen: true, type: 'task', id: this.selectedTask.id };
        }
    }">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Hay errores en el formulario:
                            </p>
                            <ul class="mt-1 text-xs text-red-600 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Filters & Actions -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                 <div class="flex flex-col sm:flex-row items-center gap-3">
                    <!-- Date Filters -->
                    <div class="flex items-center gap-2 bg-white rounded-xl border border-gray-200 p-1 shadow-sm w-full sm:w-auto">
                        <span class="text-xs font-bold text-gray-500 ml-2 uppercase">Filtrar:</span>
                        <input type="date" x-model="startDate" class="border-none focus:ring-0 text-sm p-1 rounded-lg w-full sm:w-auto" placeholder="Desde">
                        <span class="text-gray-400">/</span>
                        <input type="date" x-model="endDate" class="border-none focus:ring-0 text-sm p-1 rounded-lg w-full sm:w-auto" placeholder="Hasta">
                    </div>

                    <button 
                        x-show="startDate || endDate || searchQuery" 
                        @click="startDate = ''; endDate = ''; searchQuery = ''"
                        class="text-xs text-gray-400 hover:text-[#22A9C8] font-medium transition-colors sm:ml-2"
                        style="display: none;"
                    >
                        Limpiar
                    </button>
                    
                    <!-- Search Box -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" x-model="searchQuery" placeholder="Buscar por tarea o profesional..." 
                               class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-[#22A9C8] focus:border-[#22A9C8] text-sm shadow-sm transition-all">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <button 
                        @click="openCreateTaskModal(null, true)"
                        class="w-full sm:w-auto bg-[#22A9C8] hover:bg-[#1B8BA6] text-white font-medium py-2 px-6 rounded-full text-sm transition-colors shadow-sm"
                    >
                        Agregar tarea
                    </button>
                </div>
            </div>

            <!-- Table View (Desktop) -->
            <div class="bg-white rounded-3xl p-8 border border-[#22A9C8] shadow-sm overflow-x-auto hidden md:block">
                <table class="w-full min-w-[800px] border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-left text-sm font-bold text-gray-900 border-b border-gray-100">
                            <th class="pb-2 pl-6 w-1/4">Título</th>
                            <th class="pb-2 text-center">Fecha límite</th>
                            <th class="pb-2 text-center">Asignado</th>
                            <th class="pb-2 text-center">Estado</th>
                            <th class="pb-2 text-center">Comentarios</th>
                            <th class="pb-2 text-center pr-6">Archivos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamTasks as $task)
                            <tr class="group transition-colors hover:bg-gray-50" 
                                x-show="matches('{{ $task->end_date }}', '{{ addslashes($task->title) }}', '{{ addslashes($task->assignees->pluck('name')->join(',')) }}')"
                            >
                                <td @click='openDetailsModal(@json($task, JSON_HEX_APOS))' class="cursor-pointer py-6 pl-6 font-medium text-gray-900 bg-white rounded-l-2xl border-l border-y border-[#22A9C8] hover:text-[#22A9C8] transition-colors">
                                    {{ $task->title }}
                                </td>
                                <td class="py-6 text-center text-red-500 font-medium bg-white border-y border-[#22A9C8]">
                                    {{ \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') }}
                                </td>
                                <td class="py-6 bg-white border-y border-[#22A9C8]">
                                    <div class="flex justify-center flex-wrap gap-1">
                                        @foreach($task->assignees as $assignee)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $assignee->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-6 text-center bg-white border-y border-[#22A9C8]">
                                    @php
                                        $statusClass = match($task->status) {
                                            'por_hacer' => 'bg-red-500 text-white',
                                            'en_proceso' => 'bg-yellow-400 text-white',
                                            'finalizado' => 'bg-green-400 text-white',
                                            default => 'bg-gray-200 text-gray-800'
                                        };
                                        $statusLabel = match($task->status) {
                                            'por_hacer' => 'Por hacer',
                                            'en_proceso' => 'En proceso',
                                            'finalizado' => 'Finalizado',
                                            default => $task->status
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="py-6 text-center bg-white border-y border-[#22A9C8]">
                                    <button @click.stop='openDetailsModal(@json($task, JSON_HEX_APOS), "comments")' class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span class="text-sm font-medium">{{ $task->comments->count() }}</span>
                                    </button>
                                </td>
                                <td class="py-6 text-center pr-6 bg-white rounded-r-2xl border-r border-y border-[#22A9C8]">
                                    <button @click.stop='openDetailsModal(@json($task, JSON_HEX_APOS), "files")' class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="text-sm font-medium">{{ $task->attachments->count() }}</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">No hay tareas asignadas.</td>
                            </tr>
                        @endforelse
                        <!-- Empty state when filtered -->
                        <tr x-show="!$el.parentNode.querySelectorAll('tr[x-show]:not([style*=\'display: none\'])').length" style="display: none;">
                            <td colspan="7" class="p-6 text-center text-gray-500 italic">No se encontraron tareas con estos filtros.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="md:hidden space-y-6 mt-8 max-w-lg mx-auto pb-10 px-4 sm:px-6">
                <!-- Selector -->
                 <div class="relative">
                    <select 
                        x-model="mobileView" 
                        class="w-full bg-[#22A9C8] text-white font-medium py-3 px-4 rounded-full appearance-none border-none focus:ring-0 text-center"
                        style="background-image: none;"
                    >
                        <option value="" disabled selected>Selecciona una asignación</option>
                        <option value="team">En equipo</option>
                    </select>
                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Placeholder -->
                <div x-show="!mobileView" class="text-center text-gray-400 py-10 italic">
                    No hay asignaciones que mostrar
                </div>

                <!-- Team Cards -->
                <div x-show="mobileView === 'team'" style="display: none;">
                    <div class="flex justify-between items-center mb-4">
                         <h3 class="text-[#22A9C8] font-medium text-lg">En equipo</h3>
                         <button @click="openCreateTaskModal(null, true)" class="bg-[#22A9C8] text-white text-sm px-4 py-1 rounded-full">+ Tarea</button>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($teamTasks as $task)
                            <div class="bg-white rounded-2xl p-6 border border-[#22A9C8] shadow-sm flex flex-col gap-4" 
                                 x-show="matches('{{ $task->end_date }}', '{{ addslashes($task->title) }}', '{{ addslashes($task->assignees->pluck('name')->join(',')) }}')">
                                
                                <h4 @click='openDetailsModal(@json($task, JSON_HEX_APOS))' class="font-bold text-gray-900 text-lg break-words cursor-pointer hover:text-[#22A9C8] transition-colors">{{ $task->title }}</h4>
                                
                                <div class="grid grid-cols-2 gap-x-4 gap-y-4 text-sm">
                                    <div class="text-gray-600 font-medium">Fecha límite</div>
                                    <div class="text-right text-red-500 font-bold">{{ \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') }}</div>
                                    
                                    <div class="text-gray-600 font-medium">Asignado</div>
                                    <div class="flex justify-center flex-wrap gap-1 text-right">
                                        @foreach($task->assignees as $assignee)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $assignee->name }}
                                            </span>
                                        @endforeach
                                    </div>

                                    <div class="text-gray-600 font-medium self-center">Estado</div>
                                    <div class="flex justify-end">
                                        @php
                                            $statusMobile = match($task->status) {
                                                'por_hacer' => 'Por hacer',
                                                'en_proceso' => 'En proceso',
                                                'finalizado' => 'Finalizado',
                                                default => $task->status
                                            };
                                        @endphp
                                         <span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                                            {{ $statusMobile }}
                                        </span>
                                    </div>
                                </div>
                                
                                 <div class="flex justify-between items-center pt-4 border-t border-gray-100 mt-2">
                                    <div class="flex items-center gap-4">
                                         <button @click.stop='openDetailsModal(@json($task, JSON_HEX_APOS), "comments")' class="flex items-center gap-1 text-gray-600 hover:text-[#22A9C8] transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <span class="font-bold text-sm">{{ $task->comments->count() }}</span>
                                        </button>
                                         <button @click.stop='openDetailsModal(@json($task, JSON_HEX_APOS), "files")' class="flex items-center gap-1 text-gray-600 hover:text-[#22A9C8] transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span class="font-bold text-sm">{{ $task->attachments->count() }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                         <!-- Empty state when filtered (Mobile) -->
                        <div x-show="!$el.parentNode.querySelectorAll('div[x-show]:not([style*=\'display: none\'])').length" style="display: none;" class="text-center text-gray-500 py-4 italic">
                            No se encontraron resultados
                        </div>
                    </div>
                </div>
            </div>
            

            <!-- Modals moved to bottom of scope for safety -->
            @include('tareas.partials.task-details-modal')
            @include('empleadores.tareas.partials.create-modal')
            <x-work-hours.approval-modal />
        </div>

    <!-- Re-adding script helper for Approval modal logic (not fully integrated into Alpine yet, preserved as global) -->
    <script>
        // Approval Modal Functions (Keep global as they might be called from outside Alpine or legacy code)
        let currentEmployeeId = null;
        let currentDates = [];

        function showCommentModal(employeeId, dates) {
            currentEmployeeId = employeeId;
            currentDates = dates;
            document.getElementById('commentModal').classList.remove('hidden');
        }

        function closeCommentModal() {
            document.getElementById('commentModal').classList.add('hidden');
            document.getElementById('approvalComment').value = '';
        }

        function approveWithComment() {
            const comment = document.getElementById('approvalComment').value;
            if (!comment.trim()) {
                alert('Por favor, ingrese un comentario.');
                return;
            }

            fetch("{{ route('work-hours.approve-days') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    employee_id: currentEmployeeId,
                    dates: currentDates,
                    comment: comment
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error al aprobar las horas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            });
        }

        function saveScrollPosition(form) {
            localStorage.setItem('scrollPosition', window.scrollY);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const scrollPosition = localStorage.getItem('scrollPosition');
            if (scrollPosition) {
                window.scrollTo(0, parseInt(scrollPosition));
                localStorage.removeItem('scrollPosition');
            }
        });
    </script>
</x-app-layout>