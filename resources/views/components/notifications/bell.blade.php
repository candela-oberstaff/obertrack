@props(['unreadCount' => 0, 'recentTasks' => []])

<div x-data="{ open: false }" class="relative">
    <!-- Notification Bell Button -->
    <button 
        @click="open = !open" 
        class="relative p-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors"
        :class="{ 'animate-wiggle': {{ $unreadCount > 0 ? 'true' : 'false' }} }"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full animate-pulse">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 overflow-hidden border border-gray-100"
        style="display: none;"
    >
        <!-- Header -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-b border-gray-100">
            <h3 class="text-gray-900 font-bold text-sm">Tareas Nuevas</h3>
            @if($unreadCount > 0)
                <button 
                    onclick="markAllAsRead()"
                    class="text-xs text-[#22A9C8] hover:text-[#1b8fa8] hover:underline transition-colors"
                >
                    Marcar toads como leídas
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($recentTasks as $task)
                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-2 h-2 bg-[#22A9C8] rounded-full mt-2"></div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $task->title }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Asignada por {{ $task->createdBy->name }}
                            </p>
                            <div class="flex items-center mt-2 space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($task->priority === 'urgent') bg-red-100 text-red-800
                                    @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    Vence: {{ $task->end_date->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No tienes tareas nuevas</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($recentTasks) > 0)
            <div class="bg-gray-50 px-4 py-2 border-t border-gray-100">
                <a href="{{ route('empleados.tasks.index') }}" class="text-sm text-[#22A9C8] hover:text-[#1b8fa8] font-medium">
                    Ver todas las tareas →
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function markAllAsRead() {
    fetch('{{ route("notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to update the notification count
            window.location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<style>
@keyframes wiggle {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-10deg); }
    75% { transform: rotate(10deg); }
}

.animate-wiggle {
    animation: wiggle 0.5s ease-in-out infinite;
}
</style>

