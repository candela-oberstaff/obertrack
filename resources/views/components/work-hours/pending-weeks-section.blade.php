@props(['pendingWeeks'])

\u003csection x-show="activeTab === 'pending'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95"\u003e
    \u003ch2 class="text-2xl sm:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100 text-center"\u003eSemanas Pendientes de Aprobación\u003c/h2\u003e

    \u003c!-- Quick Summary --\u003e
    \u003cdiv class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6"\u003e
        \u003cp class="text-lg font-semibold text-gray-700 dark:text-gray-300"\u003e
            \u003ci class="fas fa-clock mr-2"\u003e\u003c/i\u003e Total de semanas pendientes: {{ count($pendingWeeks) }}
        \u003c/p\u003e
    \u003c/div\u003e

    \u003c!-- List of Pending Weeks --\u003e
    \u003cdiv class="space-y-4"\u003e
        @foreach($pendingWeeks as $index =\u003e $pendingWeek)
            \u003cdiv x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden"\u003e
                \u003cdiv class="bg-gradient-to-r from-green-500 to-blue-500 p-4"\u003e
                    \u003cbutton @click="open = !open" class="w-full text-left focus:outline-none"\u003e
                        \u003cdiv class="flex items-center justify-between"\u003e
                            \u003ch3 class="text-xl font-semibold text-white"\u003e
                                \u003ci class="far fa-calendar-alt mr-2"\u003e\u003c/i\u003e Semana del {{ $pendingWeek['start']-\u003eformat('d/m/Y') }} al {{ $pendingWeek['end']-\u003eformat('d/m/Y') }}
                            \u003c/h3\u003e
                            \u003csvg class="w-5 h-5 text-white" :class="{'transform rotate-180': open}" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"\u003e
                                \u003cpath d="M19 9l-7 7-7-7"\u003e\u003c/path\u003e
                            \u003c/svg\u003e
                        \u003c/div\u003e
                    \u003c/button\u003e
                \u003c/div\u003e
                \u003cdiv x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="p-4 sm:p-6"\u003e
                    @foreach($pendingWeek['summary'] as $employeeId =\u003e $summary)
                        \u003cx-work-hours.employee-week-card 
                            :summary="$summary" 
                            :employeeId="$employeeId" 
                            :weekStart="$pendingWeek['start']-\u003eformat('Y-m-d')" 
                            :isPending="true" 
                        /\u003e
                    @endforeach
                \u003c/div\u003e
            \u003c/div\u003e
        @endforeach
    \u003c/div\u003e
\u003c/section\u003e
