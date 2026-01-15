<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen font-sans" x-data="taskManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header & Filters -->
            <div class="mb-8 px-4 sm:px-0">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0D1E4C]">Seguimiento de tareas</h2>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <!-- Date Filters -->
                        <div class="flex items-center gap-2 bg-white rounded-xl border border-gray-200 p-1 shadow-sm w-full sm:w-auto">
                            <input type="date" x-model="filterStartDate" class="border-none focus:ring-0 text-sm p-1 rounded-lg w-full sm:w-auto" placeholder="Desde">
                            <span class="text-gray-400">/</span>
                            <input type="date" x-model="filterEndDate" class="border-none focus:ring-0 text-sm p-1 rounded-lg w-full sm:w-auto" placeholder="Hasta">
                        </div>

                        <button 
                            x-show="filterStartDate || filterEndDate || searchQuery" 
                            @click="filterStartDate = ''; filterEndDate = ''; searchQuery = ''"
                            class="text-xs text-gray-400 hover:text-[#22A9C8] font-medium transition-colors sm:ml-2"
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
                            id="create-team-task-btn"
                        >
                            Agregar tarea
                        </button>
                    </div>
                </div>
            </div>

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
                        <tbody id="tasks-table-body">
                            <template x-for="task in filteredTasks" :key="task.id">
                                <tr class="group transition-colors">
                                    <td class="py-6 pl-6 font-medium text-gray-900 bg-white rounded-l-2xl border-l border-y border-[#22A9C8]" x-text="task.title"></td>
                                    <td class="py-6 text-center text-red-500 font-medium bg-white border-y border-[#22A9C8]" x-text="formatDate(task.end_date)"></td>
                                    <td class="py-6 bg-white border-y border-[#22A9C8]">
                                        <div class="flex justify-center -space-x-2">
                                            <template x-for="assignee in task.assignees.slice(0, 3)" :key="assignee.id">
                                                <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-white shadow-sm bg-gray-100 flex-shrink-0">
                                                    <img :src="getAvatarSrc(assignee)" :alt="assignee.name" class="w-full h-full object-cover">
                                                </div>
                                            </template>
                                            <template x-if="task.assignees.length > 3">
                                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 font-bold ring-2 ring-white">
                                                    +<span x-text="task.assignees.length - 3"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="py-6 text-center bg-white border-y border-[#22A9C8]" x-html="getStatusBadge(task.status)"></td>
                                    <td class="py-6 text-center bg-white border-y border-[#22A9C8]">
                                        <button @click="openComments(task.id)" class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <span class="text-sm font-medium" x-text="task.comments?.length || 0"></span>
                                        </button>
                                    </td>
                                    <td class="py-6 text-center pr-6 bg-white rounded-r-2xl border-r border-y border-[#22A9C8]">
                                        <button @click="openFiles(task.id)" class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span class="text-sm font-medium" x-text="task.attachments?.length || 0"></span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredTasks.length === 0">
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">No se encontraron tareas con estos filtros.</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>



        </div>

        <!-- Modals would go here. I'll insert logic in next steps -->
        <!-- MOBILE VIEW (Hidden on desktop) -->
        <div class="md:hidden space-y-6 mt-8 max-w-lg mx-auto pb-10 px-4 sm:px-6">
            <!-- Selector Dropdown -->
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

            <!-- Team Tasks View -->
            <div x-show="mobileView === 'team'" style="display: none;">
                <div class="flex justify-between items-center mb-4">
                     <h3 class="text-[#22A9C8] font-medium text-lg">En equipo</h3>
                     <button @click="openCreateTaskModal(null, true)" class="bg-[#22A9C8] text-white text-sm px-4 py-1 rounded-full">+ Tarea</button>
                </div>
                
                <div class="space-y-4">
                    <template x-for="task in filteredTasks" :key="'mobile-'+task.id">
                        <div class="bg-white rounded-2xl p-6 border border-[#22A9C8] shadow-sm flex flex-col gap-4">
                            <!-- Title -->
                            <h4 class="font-bold text-gray-900 text-lg break-words" x-text="task.title"></h4>
                            
                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-x-4 gap-y-4 text-sm">
                                <div class="text-gray-600 font-medium">Fecha límite</div>
                                <div class="text-right text-red-500 font-bold" x-text="formatDate(task.end_date)"></div>
                                
                                <div class="text-gray-600 font-medium">Asignado</div>
                                <div class="flex justify-end -space-x-2">
                                     <template x-for="assignee in task.assignees.slice(0, 3)" :key="'mobile-avatar-'+assignee.id">
                                          <div class="w-7 h-7 rounded-full overflow-hidden border-2 border-white shadow-sm bg-gray-100 flex-shrink-0">
                                              <img :src="getAvatarSrc(assignee)" :alt="assignee.name" class="w-full h-full object-cover">
                                          </div>
                                    </template>
                                    <template x-if="task.assignees.length > 3">
                                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 font-bold ring-2 ring-white">
                                            +<span x-text="task.assignees.length - 3"></span>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="text-gray-600 font-medium self-center">Estado</div>
                                <div class="flex justify-end" x-html="getStatusBadge(task.status)"></div>
                            </div>
                            
                            <!-- Footer: Comments & Files -->
                            <div class="flex justify-between pt-4 border-t border-gray-100 mt-2">
                                <div class="flex items-center gap-1 text-gray-400">Comentarios</div>
                                <div class="flex items-center gap-4">
                                     <button @click="openComments(task.id)" class="flex items-center gap-1 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span class="font-bold" x-text="task.comments?.length || 0"></span>
                                    </button>
                                     <button @click="openFiles(task.id)" class="flex items-center gap-1 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="font-bold" x-text="task.attachments?.length || 0"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="filteredTasks.length === 0">
                        <div class="text-center text-gray-500 py-4">No se encontraron tareas.</div>
                    </template>
                </div>
            </div>


        </div>

        @include('empleadores.tareas.partials.create-modal')
        @include('empleadores.tareas.partials.comments-modal')
        @include('empleadores.tareas.partials.files-modal')
        <x-work-hours.approval-modal />
        
        <!-- Mass Communication Section -->
        <div class="mt-16 bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-[#22A9C8] p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg md:text-xl font-bold text-gray-900">Comunicación con profesionales</h3>
                    <p class="text-gray-500 text-xs md:text-sm">Envía un correo electrónico a todo tu equipo o a un profesional seleccionado.</p>
                </div>
            </div>

            <form action="{{ route('empleador.mass-email') }}" method="POST" enctype="multipart/form-data" class="space-y-4 px-2 sm:px-0" onsubmit="saveScrollPosition(this)">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="recipient_id" class="block text-sm font-semibold text-gray-700 mb-1">Destinatario</label>
                        <select name="recipient_id" id="recipient_id" 
                                class="w-full rounded-xl border-gray-200 shadow-sm focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-20 transition-all">
                            <option value="">Todo el equipo de profesionales</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->job_title ?? 'Profesional' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-semibold text-gray-700 mb-1">Asunto del mensaje</label>
                        <input type="text" name="subject" id="subject" required
                               class="w-full rounded-xl border-gray-200 shadow-sm focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-20 transition-all"
                               placeholder="Ej: Anuncio importante sobre el proyecto">
                    </div>
                </div>
                <div>
                    <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">Cuerpo del mensaje</label>
                    <textarea name="message" id="message" rows="4" required
                              class="w-full rounded-xl border-gray-200 shadow-sm focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-20 transition-all"
                              placeholder="Escribe aquí tu mensaje..."></textarea>
                </div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Adjuntar archivos (opcional)</label>
                        <div class="relative">
                            <input type="file" name="attachments[]" id="attachments" multiple 
                                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
                                   class="hidden">
                            <button type="button" onclick="document.getElementById('attachments').click()"
                                    class="w-full md:w-auto flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed border-gray-200 rounded-xl text-gray-500 hover:border-[#22A9C8] hover:text-[#22A9C8] transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <span>Seleccionar documentos o imágenes</span>
                            </button>
                            <div id="file-list" class="mt-2 text-xs text-gray-500 flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    <div class="flex justify-end items-end">
                        <button type="submit" 
                                class="bg-[#22A9C8] hover:bg-[#1C8CA8] text-white font-bold py-3 px-8 rounded-xl transition-all shadow-md flex items-center gap-2">
                            <span>Enviar mensaje</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- Alpine Logic -->
    <script>
        // Inject current user data for optimistic UI
        // Inject current user data for optimistic UI
        @php
            $currentUserData = [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'avatar' => auth()->user()->avatar ? (str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar)) : '',
                'initials' => substr(auth()->user()->name, 0, 1)
            ];
        @endphp
        const currentUser = @json($currentUserData);
        
        const initialTasksData = @json($teamTasks->keyBy('id'));

        function taskManager() {
            return {
                isCreateModalOpen: false,
                isCommentsModalOpen: false,
                isFilesModalOpen: false,
                
                // Create Task State
                isTeamTask: false,
                targetEmployeeId: null,
                
                // Task Detail State
                activeTask: null,
                mobileView: '',

                init() {
                    // Initialize events
                },

                // Filtering State
                filterStartDate: '',
                filterEndDate: '',
                searchQuery: '',

                // Reactive tasks store
                tasks: initialTasksData,

                get filteredTasks() {
                    let tasksArray = Object.values(this.tasks);

                    // Date range filter
                    if (this.filterStartDate || this.filterEndDate) {
                        tasksArray = tasksArray.filter(task => {
                            const taskDate = task.end_date; // Format YYYY-MM-DD
                            if (this.filterStartDate && taskDate < this.filterStartDate) return false;
                            if (this.filterEndDate && taskDate > this.filterEndDate) return false;
                            return true;
                        });
                    }

                    // Search query filter
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        tasksArray = tasksArray.filter(task => {
                            const matchTitle = task.title.toLowerCase().includes(query);
                            const matchAssignee = task.assignees.some(v => 
                                v.name.toLowerCase().includes(query) || 
                                (v.email && v.email.toLowerCase().includes(query))
                            );
                            return matchTitle || matchAssignee;
                        });
                    }

                    return tasksArray.sort((a, b) => b.id - a.id);
                },

                getStatusBadge(status) {
                    const styles = {
                        'por_hacer': 'bg-red-500 text-white',
                        'en_proceso': 'bg-yellow-400 text-white',
                        'finalizado': 'bg-green-400 text-white',
                    };
                    const labels = {
                        'por_hacer': 'Por hacer',
                        'en_proceso': 'En proceso',
                        'finalizado': 'Finalizado',
                    };
                    const style = styles[status] || 'bg-gray-200 text-gray-800';
                    const label = labels[status] || status;
                    return `<span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-medium ${style}">${label}</span>`;
                },

                getAvatarSrc(user) {
                    if (!user.avatar) {
                        return `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&color=FFFFFF&background=22A9C8`;
                    }
                    return user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`;
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const datePart = dateStr.split('T')[0];
                    const parts = datePart.split('-');
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                },
                
                openCreateTaskModal(employeeId = null, isTeam = false) {
                    this.isTeamTask = isTeam;
                    this.targetEmployeeId = employeeId;
                    this.isCreateModalOpen = true;
                },

                openComments(taskId) {
                    this.activeTask = this.tasks[taskId];
                    this.isCommentsModalOpen = true;
                },

                openFiles(taskId) {
                    this.activeTask = this.tasks[taskId];
                    this.isFilesModalOpen = true;
                },

                async submitComment(taskId, content) {
                    if (!content.trim()) return false;
                    
                    // 1. Optimistic Update
                    const tempId = 'temp_' + Date.now();
                    const optimisticComment = {
                        id: tempId,
                        content: content,
                        created_at: new Date().toISOString(),
                        user: {
                            id: currentUser.id,
                            name: currentUser.name,
                            avatar: currentUser.avatar
                        },
                        task_id: taskId
                    };
                    
                    if (!this.activeTask.comments) this.activeTask.comments = [];
                    // Push to top immediately
                    this.activeTask.comments.unshift(optimisticComment);
                    
                    // Scroll immediately
                    this.$nextTick(() => {
                        const modalList = document.querySelector('[x-show="isCommentsModalOpen"] .overflow-y-auto');
                        if (modalList) modalList.scrollTop = 0;
                    });

                    try {
                        const response = await fetch(`/empleador/tareas/${taskId}/comments`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ content: content })
                        });

                        if (response.ok) {
                            const data = await response.json();
                            
                            // 2. Replace optimistic comment with real one
                            const index = this.activeTask.comments.findIndex(c => c.id === tempId);
                            if (index !== -1) {
                                this.activeTask.comments[index] = data.comment;
                            } else {
                                // Fallback if list changed wildly (unlikely)
                                this.activeTask.comments.unshift(data.comment);
                            }
                            
                            // Update global store (if needed for persistence across closes)
                            this.tasks[taskId].comments = this.activeTask.comments;

                            return true;
                        } else {
                            // Revert on failure
                            this.activeTask.comments = this.activeTask.comments.filter(c => c.id !== tempId);
                            console.error('Error submitting comment');
                            alert('Error al enviar el comentario.');
                            return false;
                        }
                    } catch (error) {
                        // Revert on error
                        this.activeTask.comments = this.activeTask.comments.filter(c => c.id !== tempId);
                        console.error('Error:', error);
                        alert('Error de conexión.');
                        return false;
                    }
                },

                isUploading: false,
                uploadProgress: 0,
                
                // Comment Editing State
                editingCommentId: null,
                editingContent: '',

                startEditing(comment) {
                    // Strict check: current user must be owner
                    if (comment.user.id !== currentUser.id) return;
                    
                    this.editingCommentId = comment.id;
                    this.editingContent = comment.content;
                },

                cancelEditing() {
                    this.editingCommentId = null;
                    this.editingContent = '';
                },

                async updateComment() {
                    if (!this.editingContent.trim()) return;

                    const commentId = this.editingCommentId;
                    const content = this.editingContent;

                    try {
                        const response = await fetch(`/empleador/comments/${commentId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ content: content })
                        });

                        if (response.ok) {
                            const data = await response.json();
                            
                            // Update local state
                            const index = this.activeTask.comments.findIndex(c => c.id === commentId);
                            if (index !== -1) {
                                this.activeTask.comments[index] = data.comment;
                                // Also update global store
                                if (this.tasks[this.activeTask.id]) {
                                    this.tasks[this.activeTask.id].comments = this.activeTask.comments;
                                }
                            }
                            
                            this.cancelEditing();
                        } else {
                            const data = await response.json();
                            alert(data.message || 'Error al actualizar el comentario');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error de conexión');
                    }
                },

                async deleteComment(commentId) {
                    if (!confirm('¿Estás seguro de eliminar este comentario?')) return;

                    try {
                        const response = await fetch(`/empleador/comments/${commentId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        if (response.ok) {
                            // Remove from local state
                            this.activeTask.comments = this.activeTask.comments.filter(c => c.id !== commentId);
                            
                            // Update global store
                            if (this.tasks[this.activeTask.id]) {
                                this.tasks[this.activeTask.id].comments = this.activeTask.comments;
                            }
                        } else {
                            const data = await response.json();
                            alert(data.message || 'Error al eliminar el comentario');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error de conexión');
                    }
                },

                uploadFile(taskId, file) {
                    if (!file) return;

                    this.isUploading = true;
                    this.uploadProgress = 0;

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('task_id', taskId);
                    
                    const xhr = new XMLHttpRequest();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    xhr.open('POST', `/empleador/tareas/${taskId}/files`, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // Good practice for Laravel

                    xhr.upload.onprogress = (e) => {
                        if (e.lengthComputable) {
                            this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                        }
                    };

                    xhr.onload = () => {
                        this.isUploading = false;
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (!this.activeTask.attachments) this.activeTask.attachments = [];
                                this.activeTask.attachments.unshift(data.attachment);
                                // Also update store
                                this.tasks[taskId].attachments.unshift(data.attachment);
                                
                                // Reset file input
                                if (this.$refs.fileInput) {
                                    this.$refs.fileInput.value = '';
                                }
                            } catch (e) {
                                console.error('Error parsing response', e);
                                alert('Error al procesar la respuesta del servidor.');
                            }
                        } else {
                           console.error('Error uploading file');
                           alert('Error al subir el archivo.');
                        }
                    };

                    xhr.onerror = () => {
                        this.isUploading = false;
                        console.error('Network Error');
                        alert('Error de conexión.');
                    };

                    xhr.send(formData);
                }
            }
        }

        // Approval Modal Functions
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