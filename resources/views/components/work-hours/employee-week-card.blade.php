@props(['summary', 'employeeId', 'weekStart', 'isPending' =\u003e false])

\u003cdiv class="mb-6 last:mb-0 border-b border-gray-200 dark:border-gray-700 pb-6 last:border-0 last:pb-0"\u003e
    \u003cdiv class="flex flex-wrap items-center justify-between mb-4"\u003e
        \u003ch4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 sm:mb-0"\u003e
            \u003ci class="fas fa-user-circle mr-2"\u003e\u003c/i\u003e {{ $summary['name'] }}
        \u003c/h4\u003e
        \u003cdiv class="flex flex-wrap gap-2"\u003e
            \u003cspan class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm font-semibold"\u003e
                \u003ci class="fas fa-clock mr-1"\u003e\u003c/i\u003e {{ $summary['total_hours'] }}/40h semanales
            \u003c/span\u003e
            @if($summary['pending_hours'] \u003e 0)
                \u003cspan class="px-3 py-1 bg-yellow-50 text-yellow-700 rounded-full text-sm font-semibold"\u003e
                    \u003ci class="fas fa-hourglass-half mr-1"\u003e\u003c/i\u003e {{ $summary['pending_hours'] }}h pendientes por aprobar
                \u003c/span\u003e
            @else
                \u003cspan class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm font-semibold"\u003e
                    \u003ci class="fas fa-check mr-1"\u003e\u003c/i\u003e Aprobadas
                \u003c/span\u003e
            @endif
        \u003c/div\u003e
    \u003c/div\u003e
    
    \u003c!-- Days Grid --\u003e
    \u003cdiv class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4"\u003e
        \u003cdiv class="grid grid-cols-3 sm:grid-cols-5 gap-1 sm:gap-2"\u003e
            @foreach($summary['days'] as $day)
                \u003cdiv class="flex-1 flex flex-col items-center"\u003e
                    \u003cdiv class="text-sm font-bold text-gray-600 dark:text-gray-300"\u003e{{ Carbon\Carbon::parse($day['date'])-\u003eformat('D') }}\u003c/div\u003e
                    \u003cdiv class="w-full bg-gray-200 dark:bg-gray-600 rounded-t-lg overflow-hidden" style="height: {{ ($day['hours'] / 8) * 100 }}%"\u003e
                        \u003cdiv class="h-full {{ $day['approved'] ? 'bg-green-500' : 'bg-yellow-500' }} flex items-center justify-center text-white font-bold"\u003e
                            {{ $day['hours'] }}h
                        \u003c/div\u003e
                    \u003c/div\u003e
                    \u003cdiv class="text-xs {{ $day['approved'] ? 'text-green-500' : 'text-yellow-500' }} mt-1"\u003e
                        {{ $day['approved'] ? 'Aprobado' : 'Pendiente' }}
                    \u003c/div\u003e
                \u003c/div\u003e
            @endforeach
        \u003c/div\u003e
    \u003c/div\u003e
    
    \u003c!-- Approval Buttons --\u003e
    @if($summary['pending_hours'] \u003e 0)
        \u003cdiv class="flex flex-wrap justify-end gap-2"\u003e
            \u003cform action="{{ route('work-hours.approve-week') }}" method="POST" onsubmit="saveScrollPosition(this)"\u003e
                @csrf
                \u003cinput type="hidden" name="week_start" value="{{ $weekStart }}"\u003e
                \u003cinput type="hidden" name="employee_id" value="{{ $employeeId }}"\u003e
                \u003cbutton type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300"\u003e
                    \u003ci class="fas fa-check mr-1"\u003e\u003c/i\u003e Aprobar
                \u003c/button\u003e
            \u003c/form\u003e
            \u003cbutton onclick="showCommentModal({{ $employeeId }}, '{{ $weekStart }}')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300"\u003e
                \u003ci class="fas fa-comment mr-1"\u003e\u003c/i\u003e Aprobar con comentarios
            \u003c/button\u003e
        \u003c/div\u003e
    @endif
\u003c/div\u003e
