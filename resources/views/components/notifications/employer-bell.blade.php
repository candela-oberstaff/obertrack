@props(['pendingCount' => 0, 'pendingWeeks' => []])

<div x-data="{ open: false }" class="relative">
    <!-- Notification Bell Button -->
    <button 
        @click="open = !open" 
        class="relative p-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors"
        :class="{ 'animate-wiggle': {{ $pendingCount > 0 ? 'true' : 'false' }} }"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        
        @if($pendingCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-orange-600 rounded-full animate-pulse">
                {{ $pendingCount }}
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
        class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl z-50 overflow-hidden"
        style="display: none;"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-red-600 px-4 py-3">
            <h3 class="text-white font-semibold text-sm">Horas Pendientes de Aprobación</h3>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($pendingWeeks as $week)
                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <div class="w-2 h-2 bg-orange-500 rounded-full mr-2 animate-pulse"></div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Semana {{ $week['start']->format('d/m') }} - {{ $week['end']->format('d/m/Y') }}
                                </p>
                            </div>
                            
                            @php
                                $employeesWithPending = collect($week['summary'])->filter(function($employee) {
                                    return $employee['pending_hours'] > 0;
                                });
                            @endphp

                            <div class="ml-4 space-y-1">
                                @foreach($employeesWithPending as $employeeId => $employee)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600 dark:text-gray-400">
                                            {{ $employee['name'] }}
                                        </span>
                                        <span class="font-medium text-orange-600 dark:text-orange-400">
                                            {{ number_format($employee['pending_hours'], 2) }}h pendientes
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No hay horas pendientes de aprobación</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($pendingWeeks) > 0)
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-2">
                <a href="{{ route('empleadores.tareas-asignadas') }}" class="text-sm text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 font-medium">
                    Ir a aprobar horas →
                </a>
            </div>
        @endif
    </div>
</div>

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
