@props(['pendingCount' => 0, 'pendingWeeks' => []])

<div x-data="{ employerNotificationOpen: false }" class="relative">
    <button 
        @click="employerNotificationOpen = !employerNotificationOpen" 
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

    <div 
        x-show="employerNotificationOpen" 
        @click.away="employerNotificationOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        x-cloak
        style="display: none;"
        class="fixed sm:absolute left-0 right-0 sm:left-auto sm:right-0 top-16 sm:top-auto mt-0 sm:mt-2 w-full sm:w-96 max-w-md mx-auto sm:mx-0 bg-white rounded-lg shadow-xl z-[9999] overflow-hidden border border-gray-100 px-4 sm:px-0"
    >
        <div class="bg-white px-4 py-3 border-b border-gray-100">
            <h3 class="text-gray-900 font-bold text-sm">Horas Pendientes de Aprobación</h3>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($pendingWeeks as $week)
                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                                <p class="text-sm font-semibold text-gray-900">
                                    Semana {{ $week['start']->format('d/m') }} - {{ $week['end']->format('d/m/Y') }}
                                </p>
                            </div>
                            
                            @php
                                $employeesWithPending = collect($week['summary'])->filter(function($employee) {
                                    return $employee['pending_hours'] > 0;
                                });
                            @endphp

                            @if($employeesWithPending->count() > 0)
                                <div class="ml-4 space-y-1 mb-3">
                                    <p class="text-xs font-bold text-gray-500 uppercase">Horas Normales Pendientes</p>
                                @foreach($employeesWithPending as $employeeId => $employee)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600">
                                            {{ $employee['name'] }}
                                        </span>
                                        <span class="font-medium text-orange-600">
                                            {{ number_format($employee['pending_hours'], 2) }}h pendientes
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            @endif

                            @if(isset($week['recovery_requests']) && $week['recovery_requests']->count() > 0)
                                <div class="ml-4 space-y-1 bg-red-50 rounded-lg p-2 border border-red-100">
                                    <p class="text-xs font-bold text-red-600 uppercase flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Recuperaciones Pendientes
                                    </p>
                                    @foreach($week['recovery_requests'] as $recovery)
                                        @php
                                            $hours = $recovery->hours_recovered ?? $recovery->recovered_hours;
                                            $date = $recovery->recovery_date ?? $recovery->work_date;
                                        @endphp
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-700 font-medium">
                                                {{ $recovery->user->name }}
                                            </span>
                                            <span class="font-bold text-red-600">
                                                {{ $hours }}h • {{ \Carbon\Carbon::parse($date)->format('d/m') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No hay horas pendientes de aprobación</p>
                </div>
            @endforelse
        </div>

        @if(count($pendingWeeks) > 0)
            <div class="bg-gray-50 px-4 py-2 border-t border-gray-100">
                <a href="{{ route('empresa.dashboard') }}" class="text-sm text-orange-600 hover:text-orange-800 font-medium">
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
