<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 max-w-7xl mx-auto px-4 sm:px-0">
             <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0D1E4C] leading-tight">
                {{ __('Seguimiento de tareas') }}
                  <span class="relative group inline-flex items-center ml-1 cursor-help">
    <span class=" rounded-full bg-blue-50 text-blue-500 hover:bg-blue-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"/>
        </svg>
    </span>

    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                px-3 py-2 bg-gray-900 text-white text-[10px]
                rounded-lg opacity-0 group-hover:opacity-100
                transition-opacity duration-200
                whitespace-nowrap z-50 pointer-events-none shadow-xl">
         Monitoreo y control de tareas asignadas al equipo
    </div>
</span>
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

    <div class="py-12 bg-gray-50 min-h-screen font-sans" x-data="taskTracking({{ json_encode($currentUserData) }})">

        
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
                        <input type="text" x-model="searchQuery" placeholder="Buscar por tarea/profesional" 
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
                                data-title="{{ $task->title }}"
                                data-date="{{ $task->end_date ? $task->end_date->format('Y-m-d') : '' }}"
                                data-assignees="{{ $task->assignees->pluck('name')->join(',') }}"
                                x-show="matchRow($el)"
                            >
                                <td @click='openDetailsModal(@json($task, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT))' class="cursor-pointer py-6 pl-6 font-medium text-gray-900 bg-white rounded-l-2xl border-l border-y border-[#22A9C8] hover:text-[#22A9C8] transition-colors">
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
                                    <div class="flex justify-center">
                                        <livewire:task-status-selector :task="$task" />
                                    </div>
                                </td>
                                <td class="py-6 text-center bg-white border-y border-[#22A9C8]">
                                    <button @click.stop='openDetailsModal(@json($task, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), "comments")' class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span class="text-sm font-medium">{{ $task->comments->count() }}</span>
                                    </button>
                                </td>
                                <td class="py-6 text-center pr-6 bg-white rounded-r-2xl border-r border-y border-[#22A9C8]">
                                    <button @click.stop='openDetailsModal(@json($task, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), "files")' class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
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
                                 data-title="{{ $task->title }}"
                                 data-date="{{ $task->end_date ? $task->end_date->format('Y-m-d') : '' }}"
                                 data-assignees="{{ $task->assignees->pluck('name')->join(',') }}"
                                 x-show="matchRow($el)">
                                
                                <h4 @click='openDetailsModal(@json($task, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT))' class="font-bold text-gray-900 text-lg break-words cursor-pointer hover:text-[#22A9C8] transition-colors">{{ $task->title }}</h4>
                                
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
                                    <div class="flex justify-end order-status-selector">
                                        <livewire:task-status-selector :task="$task" />
                                    </div>
                                </div>
                                
                                 <div class="flex justify-between items-center pt-4 border-t border-gray-100 mt-2">
                                    <div class="flex items-center gap-4">
                                         <button @click.stop='openDetailsModal(@json($task, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), "comments")' class="flex items-center gap-1 text-gray-600 hover:text-[#22A9C8] transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <span class="font-bold text-sm">{{ $task->comments->count() }}</span>
                                        </button>
                                         <button @click.stop='openDetailsModal(@json($task, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), "files")' class="flex items-center gap-1 text-gray-600 hover:text-[#22A9C8] transition-colors">
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
                showWarning('Por favor, ingrese un comentario.');
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
                    showError('Error al aprobar las horas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error de conexión');
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

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('taskTracking', (currentUser) => ({
                currentUser: currentUser,
                startDate: '',
                endDate: '',
                searchQuery: '',
                isTeamTask: true,
                targetEmployeeId: null,
                
                // Modal States
                selectedTask: null,
                isDetailsModalOpen: false,
                isCreateModalOpen: false,
                currentTab: 'details',
                
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
                    type: null,
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

                isAddingComment: false,
                commentText: '',
                isSubmitting: false,
                isDeleting: false,

                formatDate(dateString) {
                    if (!dateString) return '';
                    const datePart = dateString.split('T')[0];
                    const [year, month, day] = datePart.split('-');
                    return `${day}/${month}/${year}`;
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

                openDetailsModal(task, tab = 'details') {
                    this.selectedTask = task;
                    this.currentTab = tab;
                    this.isDetailsModalOpen = true;
                    this.isCommentsModalOpen = false;
                    this.isFilesModalOpen = false;
                },
                
                openCreateTaskModal(employeeId = null, isTeam = false) {
                    this.isTeamTask = isTeam;
                    this.targetEmployeeId = employeeId;
                    this.isCreateModalOpen = true;
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
                        const response = await fetch(`/empleador/tareas/${taskId}/comments`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
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
                        const response = await fetch(`/empleador/comments/${commentId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
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
                    formData.append('task_id', this.selectedTask.id);

                    try {
                        const response = await fetch(`/empleador/tareas/${this.selectedTask.id}/files`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });

                        if (response.ok) {
                            const data = await response.json();
                            if (!this.selectedTask.attachments) this.selectedTask.attachments = [];
                            this.selectedTask.attachments.unshift(data.attachment);
                        } else {
                            showError('Error al subir el archivo.');
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
                    if (this.isDeleting) return;
                    this.isDeleting = true;

                    const { type, id } = this.deleteConfirmation;
                    
                    if (type === 'file') {
                        try {
                            const response = await fetch(`/tasks/attachments/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (response.ok) {
                                this.selectedTask.attachments = this.selectedTask.attachments.filter(a => a.id !== id);
                            } else {
                                const data = await response.json();
                                showError(data.message || 'Error al eliminar archivo');
                            }
                        } catch (e) { showError('Error de conexión'); }
                    } else if (type === 'comment') {
                        try {
                            const response = await fetch(`/empleador/comments/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (response.ok) {
                                this.selectedTask.comments = this.selectedTask.comments.filter(c => c.id !== id);
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
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                                showError(data.message || 'Error al eliminar tarea');
                                this.deleteConfirmation.isOpen = false;
                            }
                        } catch (e) { 
                            showError('Error de conexión');  
                            this.deleteConfirmation.isOpen = false;
                        } finally {
                            this.isDeleting = false;
                        }
                    } else {
                        this.isDeleting = false;
                        this.deleteConfirmation.isOpen = false;
                    }
                    this.isDeleting = false;
                },

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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(this.editTaskData)
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.selectedTask = data.task;
                            this.isEditingTask = false;
                            const currentUrl = new URL(window.location.href);
                            currentUrl.searchParams.set('t', new Date().getTime());
                            window.location.href = currentUrl.toString();
                        } else {
                            const data = await response.json();
                            if (data.errors) {
                                const errorMessages = Object.values(data.errors).flat().join('\n');
                                showWarning('Errores de validación:\n' + errorMessages);
                            } else {
                                showError(data.message || 'Error al guardar cambios');
                            }
                        }
                    } catch (e) {
                        console.error('Network error:', e);
                        showError('Error de conexión');
                    } finally {
                        this.isSavingTask = false;
                    }
                },

                confirmDeleteTask() {
                    this.deleteConfirmation = { isOpen: true, type: 'task', id: this.selectedTask.id };
                },

                async toggleTaskCompletion(task) {
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
                            task.completed = data.completed;
                            task.status = data.status;
                            if (this.selectedTask && this.selectedTask.id === task.id) {
                                this.selectedTask.completed = data.completed;
                                this.selectedTask.status = data.status;
                            }
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