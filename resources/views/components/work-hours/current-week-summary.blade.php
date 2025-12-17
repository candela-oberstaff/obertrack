@props(['workHoursSummary', 'weekStart'])

<section x-show="activeTab === 'current'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
    <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100 text-center">Resumen Semana Actual</h2>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-primary p-4">
            <h3 class="text-xl font-semibold text-white">
                <i class="far fa-calendar-check mr-2"></i> {{ $weekStart->format('d/m/Y') }} al {{ $weekStart->copy()->endOfWeek(Carbon\Carbon::FRIDAY)->format('d/m/Y') }}
            </h3>
        </div>
        <div class="p-4 sm:p-6">
            @foreach ($workHoursSummary as $employeeId => $summary)
                <x-work-hours.employee-week-card 
                    :summary="$summary" 
                    :employeeId="$employeeId" 
                    :weekStart="$weekStart->format('Y-m-d')" 
                />
            @endforeach
        </div>
    </div>
</section>
