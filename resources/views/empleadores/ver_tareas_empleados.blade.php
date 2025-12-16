<x-app-layout>
    <div class="py-8 bg-gray-50 min-h-screen font-sans" x-data="taskManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-[#0D1E4C]">Seguimiento de tareas</h2>
            </div>

            <!-- Team Assignments Section -->
            <div class="mb-12">
                <div class="hidden md:flex justify-between items-center mb-6">
                    <h3 class="text-[#22A9C8] font-medium text-lg">Asignaciones en equipo</h3>
                    <button 
                        @click="openCreateTaskModal(null, true)"
                        class="bg-[#22A9C8] hover:bg-[#1B8BA6] text-white font-medium py-2 px-6 rounded-full text-sm transition-colors shadow-sm"
                    >
                        Agregar tarea en equipo
                    </button>
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
                        <tbody>
                            @forelse($teamTasks as $task)
                                <tr class="group transition-colors">
                                    <td class="py-6 pl-6 font-medium text-gray-900 bg-gray-50 rounded-l-2xl border-l border-y border-[#22A9C8]">{{ $task->title }}</td>
                                    <td class="py-6 text-center text-red-500 font-medium bg-gray-50 border-y border-[#22A9C8]">
                                        {{ \Carbon\Carbon::parse($task->end_date)->format('d-m-Y') }}
                                    </td>
                                    <td class="py-6 bg-gray-50 border-y border-[#22A9C8]">
                                        <div class="flex justify-center -space-x-2">
                                            @foreach($task->assignees->take(3) as $assignee)
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold uppercase ring-2 ring-white overflow-hidden {{ $assignee->avatar ? 'bg-transparent' : ($assignee->color_class ?? 'bg-gray-400') }}" title="{{ $assignee->name }}">
                                                    @if($assignee->avatar)
                                                        <img src="{{ $assignee->avatar }}" alt="{{ $assignee->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-white">{{ substr($assignee->name, 0, 2) }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($task->assignees->count() > 3)
                                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 font-bold ring-2 ring-white">
                                                    +{{ $task->assignees->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-6 text-center bg-gray-50 border-y border-[#22A9C8]">
                                        <x-tasks.status-badge :status="$task->status" :priority="$task->priority" />
                                    </td>
                                    <td class="py-6 text-center bg-gray-50 border-y border-[#22A9C8]">
                                        <button @click="openComments({{ $task->id }})" class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <span class="text-sm font-medium">{{ $task->comments->count() }}</span>
                                        </button>
                                    </td>
                                    <td class="py-6 text-center pr-6 bg-gray-50 rounded-r-2xl border-r border-y border-[#22A9C8]">
                                        <button @click="openFiles({{ $task->id }})" class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span class="text-sm font-medium">{{ $task->attachments->count() }}</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">No hay tareas de equipo asignadas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Individual Assignments Header (Desktop) -->
            <div class="mb-8 hidden md:block">
                <h2 class="text-2xl font-bold text-[#0D1E4C]">Asignaciones individuales</h2>
            </div>

            <!-- Individual Assignments Loop (Desktop) -->
            @foreach($employees as $employee)
                <div class="mb-10 last:mb-0 hidden md:block">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-[#22A9C8] font-medium text-lg">
                            {{ $employee->name }} - <span class="text-gray-500 font-normal">{{ $employee->job_title ?? 'Profesional' }}</span>
                        </h3>
                        <button 
                            @click="openCreateTaskModal({{ $employee->id }}, false)"
                            class="bg-[#22A9C8] hover:bg-[#1B8BA6] text-white font-medium py-2 px-6 rounded-full text-sm transition-colors shadow-sm"
                        >
                            Agregar tarea
                        </button>
                    </div>

                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 shadow-sm overflow-x-auto">
                         <table class="w-full min-w-[800px] border-separate border-spacing-y-4">
                            <thead>
                                <tr class="text-left text-sm font-bold text-gray-900 border-b border-gray-200">
                                    <th class="pb-2 pl-6 w-1/4">Título</th>
                                    <th class="pb-2 text-center">Fecha límite</th>
                                    <th class="pb-2 text-center">Asignado</th>
                                    <th class="pb-2 text-center">Estado</th>
                                    <th class="pb-2 text-center">Comentarios</th>
                                    <th class="pb-2 text-center pr-6">Archivos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->individualTasks as $task)
                                    <tr class="group transition-colors">
                                        <td class="py-6 pl-6 font-medium text-gray-900 bg-white rounded-l-2xl border-l border-y border-[#22A9C8]">{{ $task->title }}</td>
                                        <td class="py-6 text-center text-red-500 font-medium bg-white border-y border-[#22A9C8]">
                                            {{ \Carbon\Carbon::parse($task->end_date)->format('d-m-Y') }}
                                        </td>
                                        <td class="py-6 text-center bg-white border-y border-[#22A9C8]">
                                            <div class="flex justify-center">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold uppercase overflow-hidden {{ $employee->avatar ? 'bg-transparent' : ($employee->color_class ?? 'bg-pink-400') }}">
                                                    @if($employee->avatar)
                                                        <img src="{{ $employee->avatar }}" alt="{{ $employee->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-white">{{ substr($employee->name, 0, 2) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 text-center bg-white border-y border-[#22A9C8]">
                                            <x-tasks.status-badge :status="$task->status" :priority="$task->priority" />
                                        </td>
                                        <td class="py-6 text-center bg-white border-y border-[#22A9C8]">
                                            <button @click="openComments({{ $task->id }})" class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                <span class="text-sm font-medium">{{ $task->comments->count() }}</span>
                                            </button>
                                        </td>
                                        <td class="py-6 text-center pr-6 bg-white rounded-r-2xl border-r border-y border-[#22A9C8]">
                                            <button @click="openFiles({{ $task->id }})" class="inline-flex items-center text-gray-600 hover:text-[#22A9C8] transition-colors gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                <span class="text-sm font-medium">{{ $task->attachments->count() }}</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-gray-500">No hay tareas individuales asignadas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

        </div>

        <!-- Modals would go here. I'll insert logic in next steps -->
        <!-- MOBILE VIEW (Hidden on desktop) -->
        <div class="md:hidden space-y-6 mt-8 max-w-lg mx-auto pb-10 mx-6">
            <!-- Selector Dropdown -->
            <div class="relative">
                <select 
                    x-model="mobileView" 
                    class="w-full bg-[#22A9C8] text-white font-medium py-3 px-4 rounded-full appearance-none border-none focus:ring-0 text-center"
                    style="background-image: none;"
                >
                    <option value="" disabled selected>Selecciona una asignación</option>
                    <option value="team">En equipo</option>
                    @foreach($employees as $employee)
                        <option value="individual_{{ $employee->id }}">Individual - {{ $employee->name }}</option>
                    @endforeach
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
                    @forelse($teamTasks as $task)
                        <div class="bg-white rounded-2xl p-6 border border-[#22A9C8] shadow-sm flex flex-col gap-4">
                            <!-- Title -->
                            <h4 class="font-bold text-gray-900 text-lg break-words">{{ $task->title }}</h4>
                            
                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-x-4 gap-y-4 text-sm">
                                <div class="text-gray-600 font-medium">Fecha límite</div>
                                <div class="text-right text-red-500 font-bold">{{ \Carbon\Carbon::parse($task->end_date)->format('d-m-Y') }}</div>
                                
                                <div class="text-gray-600 font-medium">Asignado</div>
                                <div class="flex justify-end -space-x-2">
                                     @foreach($task->assignees->take(3) as $assignee)
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold uppercase ring-2 ring-white overflow-hidden {{ $assignee->avatar ? 'bg-transparent' : ($assignee->color_class ?? 'bg-gray-400') }}">
                                            @if($assignee->avatar)
                                                <img src="{{ $assignee->avatar }}" alt="{{ $assignee->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-white">{{ substr($assignee->name, 0, 2) }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($task->assignees->count() > 3)
                                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 font-bold ring-2 ring-white">+{{ $task->assignees->count() - 3 }}</div>
                                    @endif
                                </div>
                                
                                <div class="text-gray-600 font-medium self-center">Estado</div>
                                <div class="flex justify-end">
                                    <x-tasks.status-badge :status="$task->status" :priority="$task->priority" />
                                </div>
                            </div>
                            
                            <!-- Footer: Comments & Files -->
                            <div class="flex justify-between pt-4 border-t border-gray-100 mt-2">
                                <div class="flex items-center gap-1 text-gray-400">Comentarios</div>
                                <div class="flex items-center gap-4">
                                     <button @click="openComments({{ $task->id }})" class="flex items-center gap-1 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span class="font-bold">{{ $task->comments->count() }}</span>
                                    </button>
                                     <button @click="openFiles({{ $task->id }})" class="flex items-center gap-1 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="font-bold">{{ $task->attachments->count() }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">No hay tareas de equipo.</div>
                    @endforelse
                </div>
            </div>

            <!-- Individual Tasks Views -->
            @foreach($employees as $employee)
                <div x-show="mobileView === 'individual_{{ $employee->id }}'" style="display: none;">
                    <h3 class="text-[#22A9C8] font-medium text-lg mb-1">Asignaciones individuales</h3>
                    <p class="text-gray-900 font-bold mb-4 text-sm">{{ $employee->name }} - <span class="font-normal">{{ $employee->job_title ?? 'Profesional' }}</span></p>

                    <!-- Add Task Button Mobile -->
                    <button 
                         @click="openCreateTaskModal({{ $employee->id }}, false)"
                         class="w-full mb-6 bg-[#22A9C8] hover:bg-[#1B8BA6] text-white font-medium py-2 rounded-full text-sm transition-colors shadow-sm"
                    >
                        Agregar tarea
                    </button>
                    
                    <div class="space-y-4">
                        @forelse($employee->individualTasks as $task)
                            <div class="bg-white rounded-2xl p-6 border border-[#22A9C8] shadow-sm flex flex-col gap-4">
                                <!-- Title -->
                                <h4 class="font-bold text-gray-900 text-lg break-words">{{ $task->title }}</h4>
                                
                                <!-- Details Grid -->
                                <div class="grid grid-cols-2 gap-x-4 gap-y-4 text-sm">
                                    <div class="text-gray-600 font-medium">Fecha límite</div>
                                    <div class="text-right text-red-500 font-bold">{{ \Carbon\Carbon::parse($task->end_date)->format('d-m-Y') }}</div>
                                    
                                    <div class="text-gray-600 font-medium">Asignado</div>
                                    <div class="flex justify-end">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold uppercase overflow-hidden {{ $employee->avatar ? 'bg-transparent' : ($employee->color_class ?? 'bg-pink-400') }}">
                                            @if($employee->avatar)
                                                <img src="{{ $employee->avatar }}" alt="{{ $employee->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-white">{{ substr($employee->name, 0, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="text-gray-600 font-medium self-center">Estado</div>
                                    <div class="flex justify-end">
                                        <x-tasks.status-badge :status="$task->status" :priority="$task->priority" />
                                    </div>
                                </div>
                                
                                <!-- Footer: Comments & Files -->
                                <div class="flex justify-between pt-4 border-t border-gray-100 mt-2">
                                    <div class="flex items-center gap-1 text-gray-400">Comentarios</div>
                                    <div class="flex items-center gap-4">
                                         <button @click="openComments({{ $task->id }})" class="flex items-center gap-1 text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <span class="font-bold">{{ $task->comments->count() }}</span>
                                        </button>
                                         <button @click="openFiles({{ $task->id }})" class="flex items-center gap-1 text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span class="font-bold">{{ $task->attachments->count() }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-4">No hay tareas individuales asignadas.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        @include('empleadores.tareas.partials.create-modal')
        @include('empleadores.tareas.partials.comments-modal')
        @include('empleadores.tareas.partials.files-modal')

    </div>

    <!-- Alpine Logic -->
    <script>
        const tasksData = @json($teamTasks->merge($employees->pluck('individualTasks')->collapse())->keyBy('id'));

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

                openCreateTaskModal(employeeId = null, isTeam = false) {
                    this.isTeamTask = isTeam;
                    this.targetEmployeeId = employeeId;
                    this.isCreateModalOpen = true;
                },

                openComments(taskId) {
                    this.activeTask = tasksData[taskId];
                    this.isCommentsModalOpen = true;
                },

                openFiles(taskId) {
                    this.activeTask = tasksData[taskId];
                    this.isFilesModalOpen = true;
                },

                async submitComment(taskId, content) {
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
                            // Refresh tasks data or push comment locally
                            // For simplicity, pushing locally if structure matches, or reload helper
                            if (!this.activeTask.comments) this.activeTask.comments = [];
                            this.activeTask.comments.unshift(data.comment);
                            // Also update global tasksData
                            tasksData[taskId].comments.unshift(data.comment);
                        } else {
                            console.error('Error submitting comment');
                            alert('Error al enviar el comentario.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error de conexión.');
                    }
                },

                async uploadFile(taskId, file) {
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('task_id', taskId);

                    try {
                        // Assuming this route exists or we create it
                        const response = await fetch(`/empleador/tareas/${taskId}/files`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });

                        if (response.ok) {
                            const data = await response.json();
                            if (!this.activeTask.attachments) this.activeTask.attachments = [];
                            this.activeTask.attachments.unshift(data.attachment);
                             // Also update global tasksData
                            tasksData[taskId].attachments.unshift(data.attachment);
                        } else {
                            console.error('Error uploading file');
                             alert('Error al subir el archivo.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                         alert('Error de conexión.');
                    }
                }
            }
        }
    </script>
</x-app-layout>