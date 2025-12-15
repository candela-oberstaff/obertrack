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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                {{-- Tareas Pendientes --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <p class="text-sm text-gray-600 mb-2">Tareas pendientes</p>
                    @php
                        $totalPending = auth()->user()->assignedTasks()->where('completed', false)->count();
                    @endphp
                    <p class="text-5xl font-bold text-gray-900">{{ str_pad($totalPending, 2, '0', STR_PAD_LEFT) }}</p>
                </div>

                {{-- Horas Registradas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    @php
                        $currentPeriodStart = now()->startOfMonth();
                        $currentPeriodEnd = now();
                        $registeredHours = auth()->user()->workHours()
                            ->whereBetween('work_date', [$currentPeriodStart, $currentPeriodEnd])
                            ->get();
                        $totalHours = $registeredHours->sum('hours_worked');
                        $hasPendingApproval = $registeredHours->where('approved', false)->count() > 0;
                    @endphp
                    <p class="text-sm text-gray-600 mb-2">
                        Horas registradas 
                        <span class="text-gray-500">({{ $currentPeriodStart->format('M d') }} - {{ $currentPeriodEnd->format('M d') }})</span>
                    </p>
                    <p class="text-5xl font-bold text-gray-900 mb-2">{{ (int)$totalHours }} horas</p>
                    @if($hasPendingApproval)
                        <p class="text-xs text-red-600">
                            Tus horas siguen pendientes de aprobación
                        </p>
                    @endif
                </div>

                {{-- Tareas Completadas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <p class="text-sm text-gray-600 mb-2">Tareas completadas con éxito</p>
                    @php
                        $completedTasks = auth()->user()->assignedTasks()
                            ->where('completed', true)
                            ->whereYear('tasks.updated_at', now()->year)
                            ->whereMonth('tasks.updated_at', now()->month)
                            ->count();
                    @endphp
                    <div class="flex items-center gap-3">
                        <p class="text-5xl font-bold text-gray-900">{{ str_pad($completedTasks, 2, '0', STR_PAD_LEFT) }}</p>
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Últimas Tareas (2/3 width) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg border border-gray-200" x-data="{ 
                        selectedTask: null, 
                        isModalOpen: false,
                        openModal(task) {
                            this.selectedTask = task;
                            this.isModalOpen = true;
                        },
                        closeModal() {
                            this.isModalOpen = false;
                            this.selectedTask = null;
                        }
                    }">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Últimas tareas</h2>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha límite</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comentarios</th>
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
                                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150 ease-in-out" 
                                            @click="openModal({{ json_encode($task) }})">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $task->title }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $task->end_date->format('d-m-Y') }}</td>
                                            <td class="px-6 py-4">
                                                @if($task->visibleTo)
                                                @if($task->visibleTo)
                                                    <x-user-avatar :user="$task->visibleTo" size="8" />
                                                @endif
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($task->attachments->count() > 0)
                                                    <div class="flex items-center gap-1 text-gray-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                        </svg>
                                                        <span class="text-sm font-medium">{{ $task->attachments->count() }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($task->completed)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Completada
                                                    </span>
                                                @else
                                                    @php
                                                        $isOverdue = $task->end_date->endOfDay()->isPast();
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isOverdue ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $isOverdue ? 'Vencida' : 'Pendiente' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-1 text-gray-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                    <span class="text-sm">{{ $task->comments->count() }}</span>
                                                </div>
                                            </td>

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
                        <div x-show="isModalOpen" 
                             class="fixed inset-0 z-50 overflow-y-auto" 
                             style="display: none;"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0">
                            
                            <!-- Backdrop -->
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
            
                            <!-- Modal Panel -->
                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl flex flex-col max-h-[85vh]"
                                     @click.stop>
                                    
                                    <!-- Header -->
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between items-start flex-shrink-0 border-b border-gray-200">
                                        <div>
                                            <h3 class="text-lg font-semibold leading-6 text-gray-900" x-text="selectedTask?.title"></h3>
                                            <p class="mt-1 text-sm text-gray-500" x-text="'Creada por: ' + (selectedTask?.created_by?.name || 'Sistema')"></p>
                                        </div>
                                        <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Cerrar</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
            
                                    <!-- Body -->
                                    <div class="px-4 py-5 sm:p-6 overflow-y-auto flex-1">
                                        <div class="space-y-4">
                                            
                                            <!-- Description -->
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">Descripción</h4>
                                                <p class="mt-1 text-sm text-gray-500 whitespace-pre-line" x-text="selectedTask?.description || 'Sin descripción'"></p>
                                            </div>
            
                                            <!-- Stats Grid -->
                                            <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-md">
                                                <div>
                                                    <span class="text-xs font-medium text-gray-500 uppercase">Prioridad</span>
                                                    <p class="text-sm font-semibold capitalize" 
                                                       :class="{
                                                            'text-red-600': selectedTask?.priority === 'high' || selectedTask?.priority === 'urgent',
                                                            'text-yellow-600': selectedTask?.priority === 'medium',
                                                            'text-primary': selectedTask?.priority === 'low'
                                                       }"
                                                       x-text="selectedTask?.priority"></p>
                                                </div>
                                                <div>
                                                    <span class="text-xs font-medium text-gray-500 uppercase">Fecha Límite</span>
                                                    <p class="text-sm font-semibold text-gray-900" 
                                                       x-text="new Date(selectedTask?.end_date).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })"></p>
                                                </div>
                                                <div>
                                                    <span class="text-xs font-medium text-gray-500 uppercase">Estado</span>
                                                    <template x-if="selectedTask?.completed">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Completada
                                                        </span>
                                                    </template>
                                                    <template x-if="!selectedTask?.completed">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                              :class="new Date(selectedTask?.end_date) < new Date() ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'"
                                                              x-text="new Date(selectedTask?.end_date) < new Date() ? 'Vencida' : 'Pendiente'">
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
            
                                            <!-- Attachments -->
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    Archivos adjuntos
                                                </h4>
                                                
                                                <template x-if="selectedTask?.attachments && selectedTask.attachments.length > 0">
                                                    <ul class="divide-y divide-gray-200 border border-gray-200 rounded-md">
                                                        <template x-for="attachment in selectedTask.attachments" :key="attachment.id">
                                                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                                                <div class="w-0 flex-1 flex items-center">
                                                                    <!-- Icon based on mime/extension (simplified generic icon) -->
                                                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                                                    </svg>
                                                                    <span class="ml-2 flex-1 w-0 truncate" x-text="attachment.filename"></span>
                                                                </div>
                                                                <div class="ml-4 flex-shrink-0">
                                                                    <a :href="'/tasks/attachments/' + attachment.id + '/download'" 
                                                                       class="font-medium text-primary hover:text-primary"
                                                                       @click.stop>
                                                                        Descargar
                                                                    </a>
                                                                </div>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </template>
                                                <template x-if="!selectedTask?.attachments || selectedTask.attachments.length === 0">
                                                    <p class="text-sm text-gray-500 italic">No hay archivos adjuntos.</p>
                                                </template>
                                            </div>

                                            <!-- Comments -->
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                                    </svg>
                                                    Comentarios
                                                </h4>
                                                
                                                <template x-if="selectedTask?.comments && selectedTask.comments.length > 0">
                                                    <ul class="divide-y divide-gray-200 border border-gray-200 rounded-md bg-gray-50">
                                                        <template x-for="comment in selectedTask.comments" :key="comment.id">
                                                            <li class="px-4 py-3">
                                                                <div class="flex items-center justify-between">
                                                                    <span class="text-xs font-semibold text-gray-900" x-text="comment.user?.name || 'Usuario'"></span>
                                                                    <span class="text-xs text-gray-500" x-text="new Date(comment.created_at).toLocaleDateString() + ' ' + new Date(comment.created_at).toLocaleTimeString().slice(0,5)"></span>
                                                                </div>
                                                                <p class="mt-1 text-sm text-gray-600" x-text="comment.content"></p>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </template>
                                                <template x-if="!selectedTask?.comments || selectedTask.comments.length === 0">
                                                    <p class="text-sm text-gray-500 italic">No hay comentarios.</p>
                                                </template>
                                            </div>
            
                                        </div>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse flex-shrink-0 border-t border-gray-200">
                                        <button type="button" 
                                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                                @click="closeModal()">
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Últimos Comentarios (1/3 width) --}}
                <div class="lg:col-span-1">
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
                                            <span>{{ $comment->user->tipo_usuario === 'empleador' ? 'Cliente' : 'Equipo' }}</span>
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
</x-app-layout>
