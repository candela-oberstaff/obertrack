<x-app-layout>
    <div class="min-h-screen bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    ¡Hola, <span class="text-primary">{{ auth()->user()->name }}</span>!
                </h1>
                <p class="text-gray-600 mt-1">
                    Aquí está tu resumen de actividades
                </p>
            </div>

            {{-- Summary Cards --}}
            <div id="dashboard-stats-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @php
                    $debtSummary = \App\Http\Controllers\RecoveryHoursController::getDebtSummary(auth()->id());
                @endphp
                
                {{-- Tareas Pendientes --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6 shadow-sm">
                    <p class="text-[10px] md:text-sm text-gray-600 mb-2 uppercase tracking-wider font-bold">Tareas pendientes</p>
                    @php
                        $totalPending = auth()->user()->assignedTasks()
                            ->whereRaw('tasks.completed IS FALSE')
                            ->whereMonth('tasks.end_date', now()->month)
                            ->whereYear('tasks.end_date', now()->year)
                            ->count();
                    @endphp
                    <p class="text-4xl md:text-5xl font-extrabold text-[#0D1E4C]">{{ str_pad($totalPending, 2, '0', STR_PAD_LEFT) }}</p>
                </div>

                {{-- Horas de tareas (Eliminado) --}}

                {{-- Tareas Completadas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6 shadow-sm">
                    <p class="text-[10px] md:text-sm text-gray-600 mb-2 uppercase tracking-wider font-bold">Tareas finalizadas</p>
                    @php
                        $completedTasks = auth()->user()->assignedTasks()
                            ->whereRaw('tasks.completed IS TRUE')
                            ->whereMonth('tasks.end_date', now()->month)
                            ->whereYear('tasks.end_date', now()->year)
                            ->count();
                    @endphp
                    <div class="flex items-center gap-3">
                        <p class="text-4xl md:text-5xl font-extrabold text-[#0D1E4C]">{{ str_pad($completedTasks, 2, '0', STR_PAD_LEFT) }}</p>
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Recuperación de Horas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6 shadow-sm">
                    <p class="text-[10px] md:text-sm text-gray-600 mb-2 uppercase tracking-wider font-bold">Horas de recuperación</p>
                    <div class="flex items-center justify-between">
                        <div>
                            @php $remainingDebt = $debtSummary['remaining_debt']; @endphp
                            <p class="text-4xl md:text-5xl font-extrabold {{ $remainingDebt > 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ $remainingDebt > 0 ? '-' . number_format($remainingDebt, 1) : 'Ok' }}<span class="text-lg">h</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Recuperado</p>
                            <p class="text-sm font-black text-green-600">{{ number_format($debtSummary['total_recovered'], 1) }}h</p>
                        </div>
                    </div>
                    <div class="mt-2 text-[10px] text-gray-400 flex justify-between">
                        <span>Adeudado: {{ number_format($debtSummary['total_debt'], 1) }}h</span>
                        @if($debtSummary['pending_approval'] > 0)
                            <span class="text-orange-500 font-bold">+{{ number_format($debtSummary['pending_approval'], 1) }}h pnd</span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Últimas Tareas (2/3 width) --}}
                <div class="lg:col-span-2" id="dashboard-latest-tasks">
                    <div class="bg-white rounded-lg border border-gray-200" 
                         x-data="taskModal()" 
                         @task-modal-init.window="init()">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Últimas tareas</h2>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest">Título</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest hidden sm:table-cell">Límite</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest hidden lg:table-cell">Asignado</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest hidden md:table-cell">Archivos</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest">Estado</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-[10px] md:text-xs font-bold text-gray-500 uppercase tracking-widest hidden lg:table-cell">Comentarios</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $latestTasks = auth()->user()->assignedTasks()
                                            ->with(['visibleTo', 'comments.user', 'attachments', 'createdBy'])
                                            ->latest('tasks.created_at')
                                            ->take(5)
                                            ->get();
                                    @endphp
                                    
                                    @forelse($latestTasks as $task)
                                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150 ease-in-out border-b border-gray-50" 
                                            @click='openDetailsModal(@json($task))'>
                                            <td class="px-4 md:px-6 py-4">
                                                <div class="text-sm font-bold text-gray-900 line-clamp-1">{{ $task->title }}</div>
                                                <div class="text-[10px] text-gray-400 mt-0.5 sm:hidden">{{ $task->end_date->format('d/m/Y') }}</div>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 text-sm text-gray-600 whitespace-nowrap hidden sm:table-cell">{{ $task->end_date->format('d/m/Y') }}</td>
                                            <td class="px-4 md:px-6 py-4 hidden lg:table-cell">
                                                @if($task->visibleTo)
                                                    <x-user-avatar :user="$task->visibleTo" size="8" />
                                                @endif
                                            </td>
                                            <td class="px-4 md:px-6 py-4 hidden md:table-cell">
                                                @if($task->attachments->count() > 0)
                                                    <div class="flex items-center gap-1 text-gray-500">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                        </svg>
                                                        <span class="text-xs font-bold">{{ $task->attachments->count() }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 md:px-6 py-4">
                                                @if($task->completed)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-green-100 text-green-700">
                                                        Hecho
                                                    </span>
                                                @else
                                                    @php $isOverdue = $task->end_date->endOfDay()->isPast(); @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $isOverdue ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                        {{ $isOverdue ? 'Vencida' : 'Pendiente' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 md:px-6 py-4 hidden lg:table-cell">
                                                <div class="flex items-center gap-1 text-gray-500">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                    <span class="text-xs font-bold">{{ $task->comments->count() }}</span>
                                                </div>
                                            </td>
                                        </tr>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                                No tienes tareas asignadas
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Task Details Modal --}}
                        @include('tareas.partials.task-details-modal')

                    </div>
                </div>

                {{-- Últimos Comentarios (1/3 width) --}}
                <div class="lg:col-span-1" id="dashboard-latest-comments">
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Últimos comentarios</h2>
                        </div>
                        
                        <div class="p-4 space-y-3">
                            @php
                                // Get latest comments from tasks the user is involved in
                                $userTaskIds = auth()->user()->assignedTasks()->pluck('tasks.id');
                                $latestComments = \App\Models\Comment::whereIn('task_id', $userTaskIds)
                                    ->with(['user', 'task'])
                                    ->latest()
                                    ->take(3)
                                    ->get();
                            @endphp
                            
                            @forelse($latestComments as $comment)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-sm text-gray-800 mb-2">{{ $comment->content }}</p>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-500">{{ $comment->created_at->format('Y.m.d') }}</span>
                                        <div class="flex items-center gap-1 text-gray-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span>{{ $comment->user->tipo_usuario === 'empleador' ? 'Empresa' : 'Equipo' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 text-sm">
                                    No hay comentarios recientes
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Footer --}}
    <x-layout.footer />

    <script>
        function taskModal() {
            return {
                selectedTask: null, 
                isDetailsModalOpen: false,
                currentTab: 'details',
                currentUser: @json(auth()->user()),
                newCommentText: '',
                editingCommentId: null,
                editCommentContent: '',
                isSubmittingComment: false,
                isUploadingFile: false,
                deleteConfirmation: {
                    isOpen: false,
                    type: null,
                    id: null
                },
                
                // Helper to refresh data from server
                async refreshTaskDetails() {
                    if (!this.selectedTask?.id) return;
                    try {
                        const response = await fetch(`/tasks/${this.selectedTask.id}/details`);
                        if (response.ok) {
                            this.selectedTask = await response.json();
                        }
                    } catch (error) {
                        console.error('Error refreshing details:', error);
                    }
                },

                async openDetailsModal(task) {
                    this.selectedTask = task;
                    this.isDetailsModalOpen = true;
                    this.currentTab = 'details';
                    this.newCommentText = '';
                    await this.refreshTaskDetails();
                },

                closeModal() {
                    this.isDetailsModalOpen = false;
                    this.selectedTask = null;
                    this.currentTab = 'details';
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const datePart = dateStr.split('T')[0];
                    const parts = datePart.split('-');
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                },

                async submitComment() {
                    if (!this.newCommentText.trim() || this.isSubmittingComment) return;
                    
                    this.isSubmittingComment = true;
                    try {
                        const response = await fetch(`/tasks/${this.selectedTask.id}/comments`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ 
                                content: this.newCommentText,
                                task_id: this.selectedTask.id
                            })
                        });
                        
                        if (response.ok) {
                            this.newCommentText = '';
                            await this.refreshTaskDetails();
                        }
                    } catch (error) {
                        console.error('Error submitting comment:', error);
                    } finally {
                        this.isSubmittingComment = false;
                    }
                },

                startEditingComment(comment) {
                    this.editingCommentId = comment.id;
                    this.editCommentContent = comment.content;
                },

                async updateComment(commentId) {
                    try {
                        const response = await fetch(`/tasks/comments/${commentId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ content: this.editCommentContent })
                        });
                        
                        if (response.ok) {
                            // Local update is fine for text edit
                            const comment = this.selectedTask.comments.find(c => c.id === commentId);
                            if (comment) comment.content = this.editCommentContent;
                            this.editingCommentId = null;
                        }
                    } catch (error) {
                        console.error('Error updating comment:', error);
                    }
                },

                confirmDeleteComment(commentId) {
                    this.deleteConfirmation = { isOpen: true, type: 'comment', id: commentId };
                },

                confirmDeleteFile(fileId) {
                    this.deleteConfirmation = { isOpen: true, type: 'file', id: fileId };
                },

                async performDelete() {
                    const { type, id } = this.deleteConfirmation;
                    try {
                        const url = type === 'comment' ? `/tasks/comments/${id}` : `/tasks/attachments/${id}`;
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        if (response.ok) {
                            await this.refreshTaskDetails();
                        }
                    } catch (error) {
                        console.error('Error deleting:', error);
                    } finally {
                        this.deleteConfirmation = { isOpen: false, type: null, id: null };
                    }
                },

                async uploadFile(file) {
                    if (!file || this.isUploadingFile) return;
                    
                    this.isUploadingFile = true;
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    try {
                        const response = await fetch(`/tasks/${this.selectedTask.id}/attachments`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        });
                        
                        if (response.ok) {
                            // Force refresh to get correct file paths and IDs
                            await this.refreshTaskDetails();
                        }
                    } catch (error) {
                        console.error('Error uploading file:', error);
                    } finally {
                        this.isUploadingFile = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
