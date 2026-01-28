<x-app-layout>
    <div class="min-h-screen bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    ¡Hola, <span class="text-primary">{{ auth()->user()->name }}</span>!
                    @if(auth()->user()->is_manager)
                        <span class="ml-2 px-3 py-1 bg-primary text-white text-xs rounded-full inline-flex items-center align-middle">Manager</span>
                    @endif
                </h1>
                <p class="text-gray-600 mt-1">
                    Aquí está tu resumen de actividades
                </p>
            </div> 

            {{-- Summary Cards --}}
            <div id="dashboard-stats-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @php
                    $debtSummary = \App\Http\Controllers\RecoveryHoursController::getDebtSummary(auth()->id());
                @endphp
                
                {{-- Tareas Pendientes --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6 shadow-sm relative group cursor-help">
                    <p class="text-[10px] md:text-sm text-gray-600 mb-2 uppercase tracking-wider font-bold">Tareas pendientes</p>
                    @php
                        $totalPending = auth()->user()->assignedTasks()->whereRaw('completed IS FALSE')->count();
                    @endphp
                    <p class="text-4xl md:text-5xl font-extrabold text-[#0D1E4C]">{{ str_pad($totalPending, 2, '0', STR_PAD_LEFT) }}</p>

                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                bg-gray-900 text-white text-[10px] rounded-lg
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                whitespace-nowrap z-50 pointer-events-none shadow-xl">
                        Tareas que aún no han sido marcadas como completadas
                    </div>
                </div>

                {{-- Horas Registradas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6 shadow-sm relative group cursor-help">
                    @php
                        $currentPeriodStart = now()->startOfMonth();
                        $currentPeriodEnd = now();
                        $registeredHours = auth()->user()->workHours()
                            ->whereBetween('work_date', [$currentPeriodStart, $currentPeriodEnd])
                            ->get();
                        $totalHours = $registeredHours->sum('hours_worked');
                        $hasPendingApproval = $registeredHours->where('approved', false)->count() > 0;
                    @endphp
                    <p class="text-[10px] md:text-sm text-gray-600 mb-2 uppercase tracking-wider font-bold">
                        Horas registradas
                        <span class="text-[8px] md:text-[10px] text-gray-400 font-medium">({{ $currentPeriodStart->format('M d') }} - {{ $currentPeriodEnd->format('M d') }})</span>
                    </p>
                    <p class="text-4xl md:text-5xl font-extrabold text-[#0D1E4C] mb-2">{{ (int)$totalHours }} h</p>
                    @if($hasPendingApproval)
                        <p class="text-[10px] text-red-500 font-bold italic line-clamp-1">
                            Pendientes de aprobación
                        </p>
                    @endif

                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                bg-gray-900 text-white text-[10px] rounded-lg
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                whitespace-nowrap z-50 pointer-events-none shadow-xl">
                        Total de horas trabajadas en el mes actual
                    </div>
                </div>

                {{-- Tareas Completadas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6 shadow-sm relative group cursor-help">
                    <p class="text-[10px] md:text-sm text-gray-600 mb-2 uppercase tracking-wider font-bold">Tareas finalizadas</p>
                    @php
                        $completedTasks = auth()->user()->assignedTasks()
                            ->whereRaw('completed IS TRUE')
                            ->whereYear('tasks.updated_at', now()->year)
                            ->whereMonth('tasks.updated_at', now()->month)
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

                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                bg-gray-900 text-white text-[10px] rounded-lg
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                whitespace-nowrap z-50 pointer-events-none shadow-xl">
                        Tareas completadas durante este mes
                    </div>
                </div>

                {{-- Recuperación de Horas --}}
                <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6 shadow-sm relative group cursor-help">
                    <p class="text-[10px] md:text-sm text-gray-600 mb-2 uppercase tracking-wider font-bold">Horas de Recuperación</p>
                    <div class="flex items-center justify-between">
                        <div>
                            @php $remainingDebt = $debtSummary['remaining_debt']; @endphp
                            <p class="text-4xl md:text-5xl font-extrabold {{ $remainingDebt > 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ $remainingDebt > 0 ? '-' . number_format($remainingDebt, 1) : '0' }}<span class="text-lg">h</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Recuperado</p>
                            <p class="text-sm font-black text-green-600">{{ number_format($debtSummary['total_recovered'], 1) }}h</p>
                        </div>
                    </div>
                    <div class="mt-2 text-[10px] text-gray-400 flex justify-between">
                        <span>Pendiente: {{ number_format($debtSummary['total_debt'], 1) }}h</span>
                        @if($debtSummary['pending_approval'] > 0)
                            <span class="text-orange-500 font-bold">+{{ number_format($debtSummary['pending_approval'], 1) }}h pnd</span>
                        @endif
                    </div>

                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                bg-gray-900 text-white text-[10px] rounded-lg
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                whitespace-nowrap z-50 pointer-events-none shadow-xl">
                        Balance total de horas que debes recuperar (ausencias previas)
                    </div>
                </div>

            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Últimas Tareas (2/3 width) --}}
                <div class="lg:col-span-2" id="dashboard-latest-tasks">
                    @php
                        $currentUserData = [
                            'id' => auth()->id(),
                            'name' => auth()->user()->name,
                            'avatar' => auth()->user()->avatar, // Helper logic is in the template/JS
                            'tipo_usuario' => auth()->user()->tipo_usuario
                        ];
                    @endphp
                    <div class="bg-white rounded-lg border border-gray-200" x-data="taskModalLogic({{ json_encode($currentUserData) }})">
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
                                            ->with(['comments.user', 'attachments.uploader', 'createdBy'])
                                            ->latest('tasks.created_at')
                                            ->take(5)
                                            ->get()
                                            ->map(function($task) {
                                                // Create a dedicated array for the modal JSON to avoid messing with the model object
                                                $taskArray = $task->toArray();
                                                
                                                // Aggressively load users to ensure data availability
                                                $task->loadMissing(['comments.user', 'attachments.uploader']);

                                                // Map Comments with forced user name resolution
                                                $taskArray['comments'] = collect($task->comments)->map(function($comment) {
                                                    $userName = null;
                                                    $userAvatar = null;
                                                    
                                                    // 1. Try loaded relation
                                                    if ($comment->user) {
                                                        $userName = $comment->user->name;
                                                        $userAvatar = $comment->user->avatar;
                                                    } 
                                                    // 2. Try auth user fallback
                                                    else if ($comment->user_id && $comment->user_id == auth()->id()) {
                                                        $userName = auth()->user()->name;
                                                        $userAvatar = auth()->user()->avatar;
                                                    }
                                                    // 3. Direct DB query
                                                    else if ($comment->user_id) {
                                                        try {
                                                            $dbUser = \App\Models\User::withoutGlobalScopes()->find($comment->user_id);
                                                            if ($dbUser) {
                                                                $userName = $dbUser->name;
                                                                $userAvatar = $dbUser->avatar;
                                                            }
                                                        } catch (\Exception $e) { /* ignore */ }
                                                    }
                                                    
                                                    // 4. Final Fallback (Debug info included implicitly if needed, but keeping clean for now)
                                                    if (!$userName) {
                                                        $userName = $comment->user_id ? 'Usuario ' . $comment->user_id : 'Usuario Anónimo';
                                                    }

                                                    $cArray = $comment->toArray();
                                                    $cArray['user_name'] = $userName;
                                                    $cArray['user_avatar'] = $userAvatar;
                                                    $cArray['user_id'] = $comment->user_id;

                                                    // Explicitly construct user object to satisfy template optional chaining checks
                                                    $cArray['user'] = [
                                                        'id' => $comment->user_id,
                                                        'name' => $userName,
                                                        'avatar' => $userAvatar
                                                    ];
                                                    
                                                    return $cArray;
                                                })->all();
                                                
                                                // Map Attachments
                                                $taskArray['attachments'] = collect($task->attachments)->map(function($attachment) {
                                                    $uploaderName = 'Desconocido';
                                                    if ($attachment->uploader) {
                                                        $uploaderName = $attachment->uploader->name;
                                                    } else {
                                                        $dbUser = \App\Models\User::withoutGlobalScopes()->find($attachment->uploaded_by);
                                                        if ($dbUser) $uploaderName = $dbUser->name;
                                                    }
                                                    
                                                    $aArray = $attachment->toArray();
                                                    $aArray['uploader_name'] = $uploaderName;
                                                    
                                                    return $aArray;
                                                })->all();

                                                // Attach formatted JSON as a property to the object
                                                $task->modal_json = json_encode($taskArray);
                                                return $task;
                                            });
                                    @endphp
                                    
                                    @forelse($latestTasks as $task)
                                        {{-- Use pre-calculated JSON --}}
                                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150 ease-in-out border-b border-gray-50" 
                                            @click="openModal({{ $task->modal_json }})">
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

                        {{-- Task Details Modal (Standardized Partial) --}}
                        @include('tareas.partials.task-details-modal', [
                            'employees' => [], // Not needed for view-only or can be passed if editing is allowed
                            'currentUser' => auth()->user()
                        ])

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
                                <div class="bg-gray-50 rounded-lg p-3 ">
                                    <p class="text-sm text-gray-800 mb-2  whitespace-nowrap   overflow-x-auto  overflow-y-hidden scrollbar-thin">{{ $comment->content }}</p>
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
    
    @include('tareas.partials.task-details-scripts')
</x-app-layout>
