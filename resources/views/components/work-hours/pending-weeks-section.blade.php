@props(['pendingWeeks'])

<section x-show="activeTab === 'pending'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
    <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100 text-center">Semanas Pendientes de Aprobación</h2>

    <!-- Quick Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6">
        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            <i class="fas fa-clock mr-2"></i> Total de semanas pendientes: {{ count($pendingWeeks) }}
        </p>
    </div>

    <!-- List of Pending Weeks -->
    <div class="space-y-4">
        @foreach($pendingWeeks as $index => $pendingWeek)
            <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-primary p-4">
                    <button @click="open = !open" class="w-full text-left focus:outline-none">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white">
                                <i class="far fa-calendar-alt mr-2"></i> Semana del {{ $pendingWeek['start']->format('d/m/Y') }} al {{ $pendingWeek['end']->format('d/m/Y') }}
                            </h3>
                            <svg class="w-5 h-5 text-white" :class="{'transform rotate-180': open}" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                </div>
                <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="p-4 sm:p-6">
                    @foreach($pendingWeek['summary'] as $employeeId => $summary)
                        <x-work-hours.employee-week-card 
                            :summary="$summary" 
                            :employeeId="$employeeId" 
                            :weekStart="$pendingWeek['start']->format('Y-m-d')" 
                            :isPending="true" 
                        />
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
