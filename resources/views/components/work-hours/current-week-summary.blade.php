@props(['workHoursSummary', 'weekStart'])

\u003csection x-show="activeTab === 'current'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95"\u003e
    \u003ch2 class="text-2xl sm:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100 text-center"\u003eResumen Semana Actual\u003c/h2\u003e
    \u003cdiv class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden"\u003e
        \u003cdiv class="bg-gradient-to-r from-green-500 to-blue-500 p-4"\u003e
            \u003ch3 class="text-xl font-semibold text-white"\u003e
                \u003ci class="far fa-calendar-check mr-2"\u003e\u003c/i\u003e {{ $weekStart-\u003eformat('d/m/Y') }} al {{ $weekStart-\u003ecopy()-\u003eendOfWeek(Carbon\Carbon::FRIDAY)-\u003eformat('d/m/Y') }}
            \u003c/h3\u003e
        \u003c/div\u003e
        \u003cdiv class="p-4 sm:p-6"\u003e
            @foreach ($workHoursSummary as $employeeId =\u003e $summary)
                \u003cx-work-hours.employee-week-card 
                    :summary="$summary" 
                    :employeeId="$employeeId" 
                    :weekStart="$weekStart-\u003eformat('Y-m-d')" 
                /\u003e
            @endforeach
        \u003c/div\u003e
    \u003c/div\u003e
\u003c/section\u003e
