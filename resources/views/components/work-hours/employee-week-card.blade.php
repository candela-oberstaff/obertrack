@props(['summary', 'employeeId', 'weekStart', 'isPending' => false])

<div class="mb-6 last:mb-0 border-b border-gray-200 dark:border-gray-700 pb-6 last:border-0 last:pb-0">
    <div class="flex flex-wrap items-center justify-between mb-4">
        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 sm:mb-0">
            <i class="fas fa-user-circle mr-2"></i> {{ $summary['name'] }}
        </h4>
        <div class="flex flex-wrap gap-2">
            <span class="px-3 py-1 bg-blue-50 text-primary-hover rounded-full text-sm font-semibold">
                <i class="fas fa-clock mr-1"></i> {{ $summary['total_hours'] }}/40h semanales
            </span>
            @if($summary['pending_hours'] > 0)
                <span class="px-3 py-1 bg-yellow-50 text-yellow-700 rounded-full text-sm font-semibold">
                    <i class="fas fa-hourglass-half mr-1"></i> {{ $summary['pending_hours'] }}h pendientes por aprobar
                </span>
            @else
                <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm font-semibold">
                    <i class="fas fa-check mr-1"></i> Aprobadas
                </span>
            @endif
        </div>
    </div>
    
    <!-- Days Grid -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
        <div class="grid grid-cols-3 sm:grid-cols-5 gap-1 sm:gap-2">
            @foreach($summary['days'] as $day)
                <div class="flex-1 flex flex-col items-center">
                    <div class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ Carbon\Carbon::parse($day['date'])->format('D') }}</div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-t-lg overflow-hidden" style="height: {{ ($day['hours'] / 8) * 100 }}%">
                        <div class="h-full {{ $day['approved'] ? 'bg-green-500' : 'bg-yellow-500' }} flex items-center justify-center text-white font-bold">
                            {{ $day['hours'] }}h
                        </div>
                    </div>
                    <div class="text-xs {{ $day['approved'] ? 'text-green-500' : 'text-yellow-500' }} mt-1">
                        {{ $day['approved'] ? 'Aprobado' : 'Pendiente' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Approval Buttons -->
    @if($summary['pending_hours'] > 0)
        <div class="flex flex-wrap justify-end gap-2">
            <form action="{{ route('work-hours.approve-week') }}" method="POST" onsubmit="saveScrollPosition(this)">
                @csrf
                <input type="hidden" name="week_start" value="{{ $weekStart }}">
                <input type="hidden" name="employee_id" value="{{ $employeeId }}">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                    <i class="fas fa-check mr-1"></i> Aprobar
                </button>
            </form>
            <button onclick="showCommentModal({{ $employeeId }}, '{{ $weekStart }}')" class="bg-primary hover:bg-primary text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                <i class="fas fa-comment mr-1"></i> Aprobar con comentarios
            </button>
        </div>
    @endif
</div>
