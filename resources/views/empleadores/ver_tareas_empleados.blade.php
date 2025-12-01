

<x-app-layout>
 <div class="bg-white dark:bg-white min-h-screen p-12">
    <main class="max-w-7xl mx-auto">
<!--         
        <div class="text-center mb-12">
            {{-- <h1 class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-8 mt-20">Panel de Tareas</h1> --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $stats = [
                        ['icon' => 'fa-tasks', 'color' => 'indigo', 'label' => 'Tareas Totales', 'value' => $tareas->count()],
                        ['icon' => 'fa-check-circle', 'color' => 'green', 'label' => 'Completadas', 'value' => $tareas->where('completed', 1)->count()],
                        ['icon' => 'fa-clock', 'color' => 'yellow', 'label' => 'Pendientes', 'value' => $tareas->where('completed', 0)->count()],
                        ['icon' => 'fa-hourglass-half', 'color' => 'purple', 'label' => 'Horas Totales', 'value' => $tareas->sum('duration')]
                    ];
                @endphp

                @foreach($stats as $stat)
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg p-6 transform transition duration-500 hover:scale-105 hover:shadow-2xl">
                        <div class="text-{{ $stat['color'] }}-500 text-4xl mb-3">
                            <i class="fas {{ $stat['icon'] }}"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
 -->








        
<!-- <div class="py-12 bg-gray-100 dark:bg-white p-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!empty($pendingWeeks))
            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-gray-100 text-center">Semanas Pendientes de Aprobación</h2>
            @foreach($pendingWeeks as $pendingWeek)
                <div class="mb-12 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-blue-600 dark:bg-blue-800 p-4">
                        <h3 class="text-xl font-semibold text-white">Semana del {{ $pendingWeek['start']->format('d/m/Y') }} al {{ $pendingWeek['end']->format('d/m/Y') }}</h3>
                    </div>
                    <div class="p-6">
                        @foreach($pendingWeek['summary'] as $employeeId => $summary)
                            <div class="mb-8 last:mb-0 border-b border-gray-200 dark:border-gray-700 pb-8 last:border-0 last:pb-0">
                                <div class="flex flex-wrap items-center justify-between mb-4">
                                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $summary['name'] }}</h4>
                                    <div class="flex space-x-2 mt-2 sm:mt-0">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">{{ $summary['total_hours'] }}/40 horas</span>
                                        @if($summary['pending_hours'] > 0)
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">{{ $summary['pending_hours'] }} pendientes</span>
                                        @else
                                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Aprobadas</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-5 gap-2">
                                        @foreach($summary['days'] as $day)
                                            <div class="text-center">
                                                <div class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ Carbon\Carbon::parse($day['date'])->format('D') }}</div>
                                                <div class="text-lg font-semibold {{ $day['approved'] ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                                    {{ $day['hours'] }}
                                                </div>
                                                <div class="text-xs {{ $day['approved'] ? 'text-green-500' : 'text-yellow-500' }}">
                                                    {{ $day['approved'] ? 'Aprobado' : 'Pendiente' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @if($summary['pending_hours'] > 0)
                                    <div class="flex justify-end space-x-2">
                                        <form action="{{ route('work-hours.approve-week') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="week_start" value="{{ $pendingWeek['start']->format('Y-m-d') }}">
                                            <input type="hidden" name="employee_id" value="{{ $employeeId }}">
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                                Aprobar
                                            </button>
                                        </form>
                                        <button onclick="showCommentModal({{ $employeeId }}, '{{ $pendingWeek['start']->format('Y-m-d') }}')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                            Aprobar con comentarios
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

        <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-gray-100 text-center">Resumen Semana Actual</h2>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="bg-green-600 dark:bg-green-800 p-4">
                <h3 class="text-xl font-semibold text-white">{{ $weekStart->format('d/m/Y') }} al {{ $weekStart->copy()->endOfWeek(Carbon\Carbon::FRIDAY)->format('d/m/Y') }}</h3>
            </div>
            <div class="p-6">
                @foreach ($workHoursSummary as $employeeId => $summary)
                    <div class="mb-8 last:mb-0 border-b border-gray-200 dark:border-gray-700 pb-8 last:border-0 last:pb-0">
                        <div class="flex flex-wrap items-center justify-between mb-4">
                            <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $summary['name'] }}</h4>
                            <div class="flex space-x-2 mt-2 sm:mt-0">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">{{ $summary['total_hours'] }}/40 horas</span>
                                @if($summary['pending_hours'] > 0)
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">{{ $summary['pending_hours'] }} pendientes</span>
                                @else
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Aprobadas</span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-5 gap-2">
                                @foreach ($summary['days'] as $day)
                                    <div class="text-center">
                                        <div class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ Carbon\Carbon::parse($day['date'])->format('D') }}</div>
                                        <div class="text-lg font-semibold {{ $day['approved'] ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                            {{ $day['hours'] }}
                                        </div>
                                        <div class="text-xs {{ $day['approved'] ? 'text-green-500' : 'text-yellow-500' }}">
                                            {{ $day['approved'] ? 'Aprobado' : 'Pendiente' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if ($summary['pending_hours'] > 0)
                            <div class="flex justify-end space-x-2">
                                <form action="{{ route('work-hours.approve-week') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
                                    <input type="hidden" name="employee_id" value="{{ $employeeId }}">
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                        Aprobar
                                    </button>
                                </form>
                                <button onclick="showCommentModal({{ $employeeId }}, '{{ $weekStart->format('Y-m-d') }}')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                    Aprobar con comentarios
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div> -->

<div x-data="{ activeTab: 'pending' }" class="py-12 bg-gray-100 dark:bg-gray-900 p-4 sm:p-6 md:p-8 lg:p-10">
    <div class="max-w-7xl mx-auto">
        <!-- Tabs -->
        <div class="mb-8">
            <div class="sm:hidden">
                <select x-model="activeTab" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="pending">Semanas Pendientes</option>
                    <option value="current">Semana Actual</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <nav class="flex space-x-4" aria-label="Tabs">
                    <button @click="activeTab = 'pending'" :class="{'bg-indigo-100 text-indigo-700': activeTab === 'pending', 'text-gray-500 hover:text-gray-700': activeTab !== 'pending'}" class="px-3 py-2 font-medium text-sm rounded-md">
                        Semanas Pendientes
                    </button>
                    <button @click="activeTab = 'current'" :class="{'bg-indigo-100 text-indigo-700': activeTab === 'current', 'text-gray-500 hover:text-gray-700': activeTab !== 'current'}" class="px-3 py-2 font-medium text-sm rounded-md">
                        Semana Actual
                    </button>
                </nav>
            </div>
        </div>

        <!-- Pending Weeks Section -->
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
                        <div class="bg-gradient-to-r from-green-500 to-blue-500 p-4">
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
                                <div class="mb-6 last:mb-0 border-b border-gray-200 dark:border-gray-700 pb-6 last:border-0 last:pb-0">
                                    <div class="flex flex-wrap items-center justify-between mb-4">
                                        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 sm:mb-0">
                                            <i class="fas fa-user-circle mr-2"></i> {{ $summary['name'] }}
                                        </h4>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm font-semibold">
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
                                    @if($summary['pending_hours'] > 0)
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <form action="{{ route('work-hours.approve-week') }}" method="POST" onsubmit="saveScrollPosition(this)">
                                                @csrf
                                                <input type="hidden" name="week_start" value="{{ $pendingWeek['start']->format('Y-m-d') }}">
                                                <input type="hidden" name="employee_id" value="{{ $employeeId }}">
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                                    <i class="fas fa-check mr-1"></i> Aprobar
                                                </button>
                                            </form>
                                            <button onclick="showCommentModal({{ $employeeId }}, '{{ $pendingWeek['start']->format('Y-m-d') }}')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                                <i class="fas fa-comment mr-1"></i> Aprobar con comentarios
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Current Week Summary Section -->
        <section x-show="activeTab === 'current'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
            <h2 class="text-2xl sm:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100 text-center">Resumen Semana Actual</h2>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-blue-500 p-4">
                    <h3 class="text-xl font-semibold text-white">
                        <i class="far fa-calendar-check mr-2"></i> {{ $weekStart->format('d/m/Y') }} al {{ $weekStart->copy()->endOfWeek(Carbon\Carbon::FRIDAY)->format('d/m/Y') }}
                    </h3>
                </div>
                <div class="p-4 sm:p-6">
                    @foreach ($workHoursSummary as $employeeId => $summary)
                        <div class="mb-6 last:mb-0 border-b border-gray-200 dark:border-gray-700 pb-6 last:border-0 last:pb-0">
                            <div class="flex flex-wrap items-center justify-between mb-4">
                                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 sm:mb-0">
                                    <i class="fas fa-user-circle mr-2"></i> {{ $summary['name'] }}
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm font-semibold">
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
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-3 sm:grid-cols-5 gap-1 sm:gap-2">
                                    @foreach ($summary['days'] as $day)
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
                            @if($summary['pending_hours'] > 0)
                                <div class="flex flex-wrap justify-end gap-2">
                                    <form action="{{ route('work-hours.approve-week') }}" method="POST" onsubmit="saveScrollPosition(this)">
                                        @csrf
                                        <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
                                        <input type="hidden" name="employee_id" value="{{ $employeeId }}">
                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                            <i class="fas fa-check mr-1"></i> Aprobar
                                        </button>
                                    </form>
                                    <button onclick="showCommentModal({{ $employeeId }}, '{{ $weekStart->format('Y-m-d') }}')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                        <i class="fas fa-comment mr-1"></i> Aprobar con comentarios
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</div>

<div id="commentModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4  px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Aprobar con comentarios
                </h3>
                <div class="mt-2">
                    <textarea id="approvalComment" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Ingrese sus comentarios aquí"></textarea>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="approveWithComment()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Aprobar
                </button>
                <button type="button" onclick="closeCommentModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentEmployeeId, currentWeekStart;
    let scrollPosition = 0;

    function showCommentModal(employeeId, weekStart) {
        currentEmployeeId = employeeId;
        currentWeekStart = weekStart;
        document.getElementById('commentModal').classList.remove('hidden');
    }

    function closeCommentModal() {
        document.getElementById('commentModal').classList.add('hidden');
    }

    function approveWithComment() {
        const comment = document.getElementById('approvalComment').value;

        fetch('{{ route('work-hours.approve-week-with-comment') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_id: currentEmployeeId,
                week_start: currentWeekStart,
                comment: comment
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Semana aprobada con comentarios');
                location.reload();
                window.scrollTo(0, scrollPosition);
            } else {
                alert('Error al aprobar la semana');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al aprobar la semana');
        });

        closeCommentModal();
    }

    function saveScrollPosition(form) {
        scrollPosition = window.pageYOffset;
        form.submit();
    }
</script>




<!-- <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-900 shadow-2xl rounded-xl overflow-hidden mb-8 p-16">
    <h2 class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 mb-2 text-center">
        Resumen de {{ $currentMonth->translatedFormat('F Y') }}
    </h2>

    @foreach($empleadosInfo as $empleado)

    
   
        <div class="mb-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 p-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <svg class="h-6 w-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Detalles del Reporte
                </h3>
            </div>
            
            <div class="p-4">
                <div class="mb-4">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Resumen de Semanas</h4>
                        <p class="text-xl text-gray-600 dark:text-gray-800  text-start mb-4">
        Profesional: {{ $empleado['name'] }}
    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach($empleado['approvedWeeks'] as $index => $week)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2 shadow-sm transition-all duration-300 hover:shadow-md hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Semana {{ $index + 1 }}</span>
                                    <p class="text-xs text-{{ $week['approved'] ? 'green' : 'yellow' }}-600 dark:text-{{ $week['approved'] ? 'green' : 'yellow' }}-400">
                                        {{ $week['approved'] ? 'Aprobada' : 'Pendiente' }}
                                    </p>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $week['start'] }} - {{ $week['end'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                @php
                    $allApproved = collect($empleado['approvedWeeks'])->every(fn($week) => $week['approved']);
                    $canDownload = $allApproved && $empleado['totalApprovedHours'] >= 160;
                @endphp
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-inner">
                    <div class="flex items-center mb-3">
                        <input type="checkbox" id="certifyHours_{{ $empleado['id'] }}" class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                        <label for="certifyHours_{{ $empleado['id'] }}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Certifico que las horas mostradas son correctas y autorizo el pago al Profesional
                        </label>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="mb-3 sm:mb-0">
                            <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ number_format($empleado['totalApprovedHours'], 2) }}</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">horas aprobadas</span>
                        </div>
                        <button
                            onclick="downloadReport({{ $empleado['id'] }}, '{{ $empleado['name'] }}')"
                            class="w-full sm:w-auto flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white {{ $canDownload ? 'bg-blue-600 hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700' : 'bg-gray-400 cursor-not-allowed' }} transition ease-in-out duration-150"
                            {{ $canDownload ? '' : 'disabled' }}
                        >
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Descargar Reporte Mensual
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div> -->


<div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-900 shadow-2xl rounded-xl overflow-hidden mb-8 p-4 sm:p-8 md:p-12 lg:p-10">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800 dark:text-gray-100 mb-6 text-center">
            Resumen de {{ $currentMonth->translatedFormat('F Y') }}
        </h2>

        <div class="space-y-6">
            @foreach($empleadosInfo as $empleado)
            <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
                <div @click="open = !open" class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 p-4 cursor-pointer">
                    <h3 class="text-lg sm:text-xl font-bold text-white flex flex-wrap items-center justify-between">
                        <span class="flex items-center mb-2 sm:mb-0">
                            <svg class="h-6 w-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            {{ $empleado['name'] }}
                        </span>
                        <span class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full">
                            {{ number_format($empleado['totalApprovedHours'], 2) }} horas aprobadas
                        </span>
                    </h3>
                </div>

                <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="p-4">
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Resumen de Semanas</h4>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($empleado['approvedWeeks'] as $index => $week)
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $week['approved'] ? 'bg-green-500' : 'bg-yellow-500' }} text-white" title="Semana {{ $index + 1 }}: {{ $week['start'] }} - {{ $week['end'] }}">
                                    <span class="text-xs font-bold">S{{ $index + 1 }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @php
                        $allApproved = collect($empleado['approvedWeeks'])->every(fn($week) => $week['approved']);
                        $canDownload = $allApproved && $empleado['totalApprovedHours'] >= 160;
                    @endphp

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-inner">
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="certifyHours_{{ $empleado['id'] }}" class="form-checkbox h-5 w-5 text-blue-600 transition duration-150 ease-in-out">
                            <label for="certifyHours_{{ $empleado['id'] }}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Certifico que las horas son correctas y autorizo el pago
                            </label>
                        </div>
                        <button
                            onclick="downloadReport({{ $empleado['id'] }}, '{{ $empleado['name'] }}')"
                            class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white {{ $canDownload ? 'bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500' : 'bg-gray-400 cursor-not-allowed' }} transition ease-in-out duration-150"
                            {{ $canDownload ? '' : 'disabled' }}
                        >
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Descargar Reporte Mensual
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

function toggleEmployeeDetails(employeeId) {
    const detailsElement = document.getElementById(`employeeDetails_${employeeId}`);
    detailsElement.classList.toggle('hidden');
}


function downloadReport(employeeId, employeeName) {
    const certifyCheckbox = document.getElementById(`certifyHours_${employeeId}`);
    if (!certifyCheckbox.checked) {
        Swal.fire({
            title: 'Certificación requerida',
            text: 'Por favor, certifique que las horas son correctas antes de descargar el reporte.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    const totalApprovedHours = {{ $totalApprovedHours }};
    if (totalApprovedHours < 160) {
        Swal.fire({
            title: 'Horas insuficientes',
            text: 'No se pueden descargar reportes hasta que se hayan aprobado al menos 160 horas.',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    Swal.fire({
        title: `Descargando reporte de ${employeeName}`,
        text: 'Por favor, espere mientras se genera el reporte...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Simular la descarga (reemplazar con la lógica real de descarga)
    setTimeout(() => {
        Swal.fire({
            title: 'Descarga completada',
            text: `El reporte de ${employeeName} se ha descargado con éxito. Se enviará una copia por correo electrónico en breve.`,
            icon: 'success',
            confirmButtonText: 'Genial'
        });
    }, 2000);

    // Iniciar la descarga real
    window.location.href = `{{ route('work-hours.download-monthly-report', ['month' => $currentMonth->format('Y-m')]) }}?employee_id=${employeeId}`;
}
</script>







<!-- SECCIONN DE CREACION DE TAREAS -->
        <!-- <div class="max-w-6xl mb-10 mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-500 to-blue-800 p-6">
        <h2 class="text-3xl font-bold text-white">Crear Nueva Tarea</h2>
        <p class="mt-2 text-indigo-100">Complete los detalles para asignar una nueva tarea a tus profesional.</p>
    </div>
    <form action="{{ route('empleador.crear-tarea') }}" method="POST" class="p-6 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título de la Tarea</label>
                <input type="text" id="title" name="title" required 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200"
                    placeholder="Ingrese el título de la tarea">
            </div>
            <div class="col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                <textarea id="description" name="description" rows="4" 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200"
                    placeholder="Describa los detalles de la tarea"></textarea>
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Inicio</label>
                <input type="date" id="start_date" name="start_date" required 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Finalización</label>
                <input type="date" id="end_date" name="end_date" required 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200">
            </div>
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioridad</label>
                <select id="priority" name="priority" required 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200">
                    <option value="" disabled selected>Seleccione la prioridad</option>
                    <option value="low">Baja</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                </select>
            </div>
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Asignar a</label>
                <select id="employee_id" name="employee_id" required 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-200">
                    <option value="" disabled selected>Seleccione un empleado</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->id }}">{{ $empleado->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <button type="submit" 
                class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg shadow-md hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200 transform hover:-translate-y-1">
                Crear Tarea
            </button>
        </div>
    </form>
</div> -->


<!-- NUEVA TAREA DESPLEGABLE -->
<!-- <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-10">
    <div class="bg-gradient-to-r from-indigo-500 to-blue-800 p-4 cursor-pointer" onclick="toggleNewTaskForm()">
        <h2 class="text-2xl font-bold text-white flex items-center justify-between">
            Crear Nueva Tarea
            <i class="fas fa-chevron-down transform transition-transform duration-300" id="newTaskChevron"></i>
        </h2>
    </div>
    
    <div id="newTaskForm" class="hidden">
        <form action="{{ route('empleador.crear-tarea') }}" method="POST" class="p-4 space-y-4">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título de la Tarea</label>
                    <input type="text" id="title" name="title" required 
                        class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Ingrese el título de la tarea">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                    <textarea id="description" name="description" rows="3" 
                        class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Describa los detalles de la tarea"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Inicio</label>
                        <input type="date" id="start_date" name="start_date" required 
                            class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Finalización</label>
                        <input type="date" id="end_date" name="end_date" required 
                            class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                        <select id="priority" name="priority" required 
                            class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="" disabled selected>Seleccione la prioridad</option>
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asignar a</label>
                        <select id="employee_id" name="employee_id" required 
                            class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="" disabled selected>Seleccione un empleado</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id }}">{{ $empleado->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    Crear Tarea
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleNewTaskForm() {
    const form = document.getElementById('newTaskForm');
    const chevron = document.getElementById('newTaskChevron');
    form.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}
</script> -->


<h2 class="text-3xl font-black text-blue-500 dark:text-blue-500 mb-8 pb-4 border-b-2 border-blue-500 text-center">Crear nuevas tareas</h2>

<div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-10">
    <!-- Tabbed Interface -->
    <div class="flex border-b border-gray-200 dark:border-gray-700">
        <button onclick="switchTab('create')" id="createTab" class="flex-1 py-3 px-4 text-center font-medium text-blue-600 bg-white dark:bg-gray-800 dark:text-blue-400 border-b-2 border-blue-500">
            Crear Nueva Tarea
        </button>
        <button onclick="switchTab('filter')" id="filterTab" class="flex-1 py-3 px-4 text-center font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            Filtrar y Buscar
        </button>
    </div>

    <!-- Create Task Form -->
    <div id="createTaskSection" class="p-4">
        <form action="{{ route('empleador.crear-tarea') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título de la Tarea</label>
                <input type="text" id="title" name="title" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ingrese el título de la tarea">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Describa los detalles de la tarea"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Inicio</label>
                    <input type="date" id="start_date" name="start_date" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Finalización</label>
                    <input type="date" id="end_date" name="end_date" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                    <select id="priority" name="priority" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="" disabled selected>Seleccione la prioridad</option>
                        <option value="low">Baja</option>
                        <option value="medium">Media</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asignar a</label>
                    <select id="employee_id" name="employee_id" required class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="" disabled selected>Seleccione un empleado</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id }}">{{ $empleado->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    Crear Tarea
                </button>
            </div>
        </form>
    </div>

    <!-- Filter Tasks Form -->
    <div id="filterTaskSection" class="p-4 hidden">
        <form method="GET" action="{{ route('empleadores.tareas-asignadas') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                    <select name="status" id="status" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="all">Todas las tareas</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completadas</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar</label>
                    <input type="text" id="search" name="search" placeholder="Buscar tarea..." value="{{ request('search') }}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-md transition duration-300 ease-in-out flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tab) {
    const createSection = document.getElementById('createTaskSection');
    const filterSection = document.getElementById('filterTaskSection');
    const createTab = document.getElementById('createTab');
    const filterTab = document.getElementById('filterTab');

    if (tab === 'create') {
        createSection.classList.remove('hidden');
        filterSection.classList.add('hidden');
        createTab.classList.add('text-blue-600', 'bg-white', 'dark:bg-gray-800', 'dark:text-blue-400', 'border-b-2', 'border-blue-500');
        createTab.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
        filterTab.classList.remove('text-blue-600', 'bg-white', 'dark:bg-gray-800', 'dark:text-blue-400', 'border-b-2', 'border-blue-500');
        filterTab.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
    } else {
        createSection.classList.add('hidden');
        filterSection.classList.remove('hidden');
        filterTab.classList.add('text-blue-600', 'bg-white', 'dark:bg-gray-800', 'dark:text-blue-400', 'border-b-2', 'border-blue-500');
        filterTab.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
        createTab.classList.remove('text-blue-600', 'bg-white', 'dark:bg-gray-800', 'dark:text-blue-400', 'border-b-2', 'border-blue-500');
        createTab.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
    }
}
</script>


<!--         
        <div class="bg-white dark:bg-white rounded-2xl shadow-lg mb-12 p-12">
            <form method="GET" action="{{ route('empleadores.tareas-asignadas') }}" class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <div class="w-full sm:w-1/3">
                    <select name="status" class="w-full px-4 py-2 rounded-full border-2 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-300">
                        <option value="all">Todas las tareas</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completadas</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
                    </select>
                </div>
                <div class="w-full sm:w-1/2">
                    <input type="text" name="search" placeholder="Buscar tarea..." value="{{ request('search') }}" class="w-full px-4 py-2 rounded-full border-2 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-300">
                </div>
                <div class="w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-full transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                </div>
            </form>
        </div>
 -->



 <!-- VIEJO FILTRO -->
 <!-- <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6 p-4">
    <button onclick="toggleFilter()" class="w-full text-left text-gray-700 dark:text-gray-300 font-medium flex justify-between items-center">
        <span>Filtrar y Buscar</span>
        <i class="fas fa-chevron-down transform transition-transform duration-300" id="filterChevron"></i>
    </button>
    
    <form id="filterForm" method="GET" action="{{ route('empleadores.tareas-asignadas') }}" class="hidden mt-4 space-y-3">
        <div class="flex flex-wrap -mx-2">
            <div class="w-full sm:w-1/3 px-2 mb-3 sm:mb-0">
                <select name="status" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all">Todas las tareas</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completadas</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
                </select>
            </div>
            <div class="w-full sm:w-1/2 px-2 mb-3 sm:mb-0">
                <input type="text" name="search" placeholder="Buscar tarea..." value="{{ request('search') }}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div class="w-full sm:w-auto px-2">
                <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition duration-300 ease-in-out flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleFilter() {
    const form = document.getElementById('filterForm');
    const chevron = document.getElementById('filterChevron');
    form.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}
</script> -->

<style>
    .task-card {
        background-color: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .task-card:hover {
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .task-header {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .task-body {
        padding: 1rem;
    }

    .task-footer {
        padding: 1rem;
        background-color: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }

    .priority-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        display: inline-block;
    }

    .priority-urgent { background-color: #FEE2E2; color: #991B1B; }
    .priority-high { background-color: #FFEDD5; color: #9A3412; }
    .priority-medium { background-color: #FEF9C3; color: #854D0E; }
    .priority-low { background-color: #DBEAFE; color: #1E40AF; }

    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
    }

    .status-badge::before {
        content: '';
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    .status-completed {
        background-color: #D1FAE5;
        color: #065F46;
    }

    .status-completed::before {
        background-color: #10B981;
    }

    .status-in-progress {
        background-color: #FEF3C7;
        color: #92400E;
    }

    .status-in-progress::before {
        background-color: #F59E0B;
    }

    .task-button {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .task-button:hover {
        transform: translateY(-1px);
    }

    .toggle-status-button-in-progress {
        background-color: #F59E0B;
        color: white;
    }

    .toggle-status-button-in-progress:hover {
        background-color: #D97706;
    }

    .toggle-status-button-completed {
        background-color: #10B981;
        color: white;
    }

    .toggle-status-button-completed:hover {
        background-color: #059669;
    }

    .edit-form {
        background-color: #f3f4f6;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .comments-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
</style>


<!-- LA VISTA PARA MANEJAR LAS TAREAS CREADAS POR LA EMPRESA -->
<!-- <div>
<meta name="csrf-token" content="{{ csrf_token() }}">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Tareas Asignadas por mí</h2>
    <div id="employerTaskList" class="space-y-6">
        @foreach($tareasEmpleador as $tarea)
            <div id="task-{{ $tarea->id }}" class="task-card bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="task-header flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $tarea->title }}</h3>
                    <div class="flex space-x-2">
                        <span class="priority-badge priority-{{ $tarea->priority }} px-2 py-1 rounded-full text-xs font-medium">
                            {{ ucfirst($tarea->priority) }}
                        </span>
                        <span id="status-badge-{{ $tarea->id }}" class="status-badge {{ $tarea->completed ? 'status-completed' : 'status-in-progress' }} px-2 py-1 rounded-full text-xs font-medium">
                            {{ $tarea->completed ? 'Completada' : 'En Progreso' }}
                        </span>
                    </div>
                </div>
                <div class="task-body p-4">
                    <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $tarea->description }}</p>
                    <div class="flex flex-wrap gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center">
                            <i class="far fa-calendar-alt mr-1"></i>
                            {{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}
                        </span>
                        <span class="inline-flex items-center">
                            <i class="far fa-user mr-1"></i>
                            Asignado a: {{ $tarea->visibleTo->name ?? 'Usuario desconocido' }}
                        </span>
                    </div>
                </div>
                <div class="task-footer flex flex-wrap gap-2 p-4 bg-gray-50 dark:bg-gray-700">
                    <button onclick="toggleEmployerTaskCompletion({{ $tarea->id }})" 
                            id="toggle-button-{{ $tarea->id }}" 
                            class="task-button {{ $tarea->completed ? 'toggle-status-button-completed' : 'toggle-status-button-in-progress' }} px-4 py-2 rounded-md transition duration-300 ease-in-out">
                        {{ $tarea->completed ? 'Marcar como En Progreso' : 'Marcar como Completada' }}
                    </button>
                    <button onclick="showEmployerEditFields({{ $tarea->id }})" class="task-button bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out">
                        Editar
                    </button>
                    <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar esta tarea?');" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="task-button bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out">
                            Eliminar
                        </button>
                    </form>
                    <button onclick="toggleEmployerComments({{ $tarea->id }})" class="task-button bg-gray-300 hover:bg-indigo-600 text-black px-4 py-2 rounded-md transition duration-300 ease-in-out">
                        <span id="commentButtonText-{{ $tarea->id }}">Mostrar Comentarios</span>
                        <span id="commentCount-{{ $tarea->id }}" class="ml-2 bg-white text-indigo-500 px-2 py-1 rounded-full text-xs font-bold">{{ $tarea->comments->count() }}</span>
                    </button>
                </div>
                
                <form id="editForm{{ $tarea->id }}" style="display:none;" action="{{ route('tareas.update', $tarea->id) }}" method="POST" class="edit-form p-4 bg-gray-100 dark:bg-gray-700">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="title{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                            <input type="text" id="title{{ $tarea->id }}" name="title" value="{{ $tarea->title }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        </div>
                        <div>
                            <label for="description{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                            <textarea id="description{{ $tarea->id }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">{{ $tarea->description }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de inicio</label>
                                <input type="date" id="start_date{{ $tarea->id }}" name="start_date" value="{{ $tarea->start_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            </div>
                            <div>
                                <label for="end_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de fin</label>
                                <input type="date" id="end_date{{ $tarea->id }}" name="end_date" value="{{ $tarea->end_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label for="priority{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                            <select id="priority{{ $tarea->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                <option value="low" {{ $tarea->priority == 'low' ? 'selected' : '' }}>Baja</option>
                                <option value="medium" {{ $tarea->priority == 'medium' ? 'selected' : '' }}>Media</option>
                                <option value="high" {{ $tarea->priority == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ $tarea->priority == 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Guardar cambios
                        </button>
                    </div>
                </form>
                
                <div id="commentsSection-{{ $tarea->id }}" class="hidden bg-gray-50 dark:bg-gray-700 p-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Comentarios</h4>
                    <div id="commentsList-{{ $tarea->id }}" class="space-y-4 mb-6">
                        @foreach ($tarea->comments as $comment)
                            <div id="comment-{{ $comment->id }}" class="bg-white dark:bg-gray-600 p-4 rounded-lg shadow-sm">
                                <div class="flex items-start justify-between">
                                    <div class="flex-grow">
                                        <p class="text-sm text-gray-800 dark:text-gray-200">
                                            <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $comment->user->name }}:</span> 
                                            <span id="commentContent-{{ $comment->id }}">{{ $comment->content }}</span>
                                        </p>
                                        <small class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($comment->user_id == auth()->id())
                                        <div class="flex space-x-2">
                                            <button onclick="editEmployerComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs transition duration-150 ease-in-out">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button onclick="deleteEmployerComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs transition duration-150 ease-in-out">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form onsubmit="addEmployerTaskComment(event, {{ $tarea->id }})" class="mt-4">
                        @csrf
                        <div class="flex items-start space-x-4">
                            <textarea id="newComment-{{ $tarea->id }}" rows="3" class="flex-grow p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none dark:bg-gray-600 dark:border-gray-500 dark:text-white" placeholder="Añadir un comentario..."></textarea>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-150 ease-in-out">
                                <i class="fas fa-paper-plane mr-2"></i>Comentar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div> -->

<!-- <div class="bg-gray-100 dark:bg-gray-900 p-8 rounded-xl shadow-lg">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <h2 class="text-3xl font-black text-blue-500 dark:text-blue-500 mb-8 pb-4 border-b-2 border-blue-500">Asignaciones a mi equipo</h2>
    <div id="employerTaskList" class="space-y-8">
        @foreach($tareasEmpleador as $tarea)
            <div id="task-{{ $tarea->id }}" class="task-card bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:scale-102 hover:shadow-xl">
                <div class="task-header flex justify-between items-center p-6 bg-indigo-50 dark:bg-indigo-900">
                    <h3 class="text-2xl font-black text-gray-600 dark:text-white">{{ $tarea->title }}</h3>
                    <div class="flex space-x-3">
                        <span class="priority-badge priority-{{ $tarea->priority }} px-3 py-1 rounded-full text-sm font-medium">
                            {{ ucfirst($tarea->priority) }}
                        </span>
                        <span id="status-badge-{{ $tarea->id }}" class="status-badge {{ $tarea->completed ? 'status-completed' : 'status-in-progress' }} px-3 py-1 rounded-full text-sm font-medium">
                            {{ $tarea->completed ? 'Completada' : 'En Progreso' }}
                        </span>
                    </div>
                </div>
                <div class="task-body p-6">
                    <p class="text-gray-600 font-medium dark:text-gray-300 mb-4">Descripción: {{ $tarea->description }}</p>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>
                           Rango de fecha: {{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}
                        </span>
                        <span class="inline-flex items-center">
                            <i class="fas fa-user mr-2 text-indigo-500"></i>
                            Asignado a: {{ $tarea->visibleTo->name ?? 'Usuario desconocido' }}
                        </span>
                    </div>
                </div>
                <div class="task-footer flex flex-wrap gap-3 p-6 bg-gray-50 dark:bg-gray-700">
                    <button onclick="toggleEmployerTaskCompletion({{ $tarea->id }})" 
                            id="toggle-button-{{ $tarea->id }}" 
                            class="task-button {{ $tarea->completed ? 'toggle-status-button-completed' : 'toggle-status-button-in-progress' }} px-4 py-2 rounded-md transition duration-300 ease-in-out">
                        {{ $tarea->completed ? 'Marcar como En Progreso' : 'Marcar como Completada' }}
                    </button>
                    <button onclick="showEmployerEditFields({{ $tarea->id }})" class="task-button bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </button>
                    <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar esta tarea?');" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="task-button bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out">
                            <i class="fas fa-trash-alt mr-2"></i>Eliminar
                        </button>
                    </form>
                    <button onclick="toggleEmployerComments({{ $tarea->id }})" class="task-button bg-gray-300 hover:bg-blue-500 hover:text-white text-black px-4 py-2 rounded-md transition duration-300 ease-in-out">
                        <i class="fas fa-comments mr-2"></i>
                        <span id="commentButtonText-{{ $tarea->id }}">Mostrar Comentarios</span>
                        <span id="commentCount-{{ $tarea->id }}" class="ml-2 bg-white text-indigo-500 px-2 py-1 rounded-full text-xs font-bold">{{ $tarea->comments->count() }}</span>
                    </button>
                </div>
                
                <form id="editForm{{ $tarea->id }}" style="display:none;" action="{{ route('tareas.update', $tarea->id) }}" method="POST" class="edit-form p-4 bg-gray-100 dark:bg-gray-700">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="title{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                            <input type="text" id="title{{ $tarea->id }}" name="title" value="{{ $tarea->title }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        </div>
                        <div>
                            <label for="description{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                            <textarea id="description{{ $tarea->id }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">{{ $tarea->description }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de inicio</label>
                                <input type="date" id="start_date{{ $tarea->id }}" name="start_date" value="{{ $tarea->start_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            </div>
                            <div>
                                <label for="end_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de fin</label>
                                <input type="date" id="end_date{{ $tarea->id }}" name="end_date" value="{{ $tarea->end_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label for="priority{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                            <select id="priority{{ $tarea->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                <option value="low" {{ $tarea->priority == 'low' ? 'selected' : '' }}>Baja</option>
                                <option value="medium" {{ $tarea->priority == 'medium' ? 'selected' : '' }}>Media</option>
                                <option value="high" {{ $tarea->priority == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ $tarea->priority == 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Guardar cambios
                        </button>
                    </div>
                </form>
                
                <div id="commentsSection-{{ $tarea->id }}" class="hidden bg-gray-50 dark:bg-gray-700 p-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Comentarios</h4>
                    <div id="commentsList-{{ $tarea->id }}" class="space-y-4 mb-6">
                        @foreach ($tarea->comments as $comment)
                            <div id="comment-{{ $comment->id }}" class="bg-white dark:bg-gray-600 p-4 rounded-lg shadow-sm">
                                <div class="flex items-start justify-between">
                                    <div class="flex-grow">
                                        <p class="text-sm text-gray-800 dark:text-gray-200">
                                            <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $comment->user->name }}:</span> 
                                            <span id="commentContent-{{ $comment->id }}">{{ $comment->content }}</span>
                                        </p>
                                        <small class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($comment->user_id == auth()->id())
                                        <div class="flex space-x-2">
                                            <button onclick="editEmployerComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs transition duration-150 ease-in-out">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button onclick="deleteEmployerComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs transition duration-150 ease-in-out">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form onsubmit="addEmployerTaskComment(event, {{ $tarea->id }})" class="mt-4">
                        @csrf
                        <div class="flex items-start space-x-4">
                            <textarea id="newComment-{{ $tarea->id }}" rows="3" class="flex-grow p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none dark:bg-gray-600 dark:border-gray-500 dark:text-white" placeholder="Añadir un comentario..."></textarea>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-150 ease-in-out">
                                <i class="fas fa-paper-plane mr-2"></i>Comentar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div> -->




<div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 md:p-8 rounded-t-xl shadow-lg p-12">
    <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">Asignaciones de mi equipo</h2>
    <p class="text-blue-100 text-sm md:text-base">Gestiona las tareas de tu equipo de forma eficiente</p>
</div>

<div class="bg-gray-100 dark:bg-gray-900 p-4 md:p-8 rounded-b-xl shadow-lg p-10">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div id="employerTaskList" class="space-y-4">
        @if($tareasEmpleador->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
                <i class="fas fa-tasks text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No hay tareas asignadas</h3>
                <p class="text-gray-600 dark:text-gray-400">Comienza a crear tareas para tu equipo y mejora la productividad.</p>
                {{-- <button onclick="showNewTaskForm()" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>Crear nueva tarea
                </button> --}}
            </div>
        @else
            @foreach($tareasEmpleador as $tarea)
                <div id="task-{{ $tarea->id }}" class="task-card bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out hover:shadow-xl border-l-4 border-blue-500">
                    <div class="flex items-center justify-between p-4 cursor-pointer" onclick="toggleTaskDetails({{ $tarea->id }})">
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $tarea->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 hidden md:block">{{ Str::limit($tarea->description, 100) }}</p>
                        </div>
                        <div class="flex flex-col md:flex-row items-end md:items-center space-y-2 md:space-y-0 md:space-x-2">
                            <span class="priority-badge priority-{{ $tarea->priority }} px-2 py-1 rounded-full text-xs font-medium">
                                {{ ucfirst($tarea->priority) }}
                            </span>
                            <span id="status-badge-{{ $tarea->id }}" class="status-badge {{ $tarea->completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} px-2 py-1 rounded-full text-xs font-medium">
                                {{ $tarea->completed ? 'Completada' : 'En Progreso' }}
                            </span>
                            <i class="fas fa-chevron-down transform transition-transform duration-300" id="chevron-{{ $tarea->id }}"></i>
                        </div>
                    </div>

                    <div id="taskDetails-{{ $tarea->id }}" class="hidden">
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700">
                            <div class="flex flex-wrap justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center mb-2 md:mb-0">
                                    <i class="fas fa-calendar-alt mr-1 text-indigo-500"></i>
                                    {{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-user mr-1 text-indigo-500"></i>
                                    {{ $tarea->visibleTo->name ?? 'Usuario desconocido' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4 flex flex-wrap gap-2">
                            <button onclick="toggleEmployerTaskCompletion({{ $tarea->id }})"
                                    id="toggle-button-{{ $tarea->id }}"
                                    class="btn-action {{ $tarea->completed ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white">
                                <i class="fas {{ $tarea->completed ? 'fa-undo' : 'fa-check' }} mr-1"></i>
                                {{ $tarea->completed ? 'Marcar En Progreso' : 'Marcar Completada' }}
                            </button>
                            <button onclick="showEmployerEditFields({{ $tarea->id }})" class="btn-action bg-blue-500 hover:bg-blue-600 text-white">
                                <i class="fas fa-edit mr-1"></i>Editar
                            </button>
                            <form action="{{ route('tareas.destroy', $tarea->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de querer eliminar esta tarea?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action bg-red-500 hover:bg-red-600 text-white">
                                    <i class="fas fa-trash-alt mr-1"></i>Eliminar
                                </button>
                            </form>
                            <button onclick="toggleEmployerComments({{ $tarea->id }})" class="btn-action bg-gray-300 hover:bg-gray-400 text-gray-800">
                                <i class="fas fa-comments mr-1"></i>
                                <span id="commentButtonText-{{ $tarea->id }}">Comentarios</span>
                                <span id="commentCount-{{ $tarea->id }}" class="ml-1 bg-white text-blue-500 px-1.5 py-0.5 rounded-full text-xs font-bold">{{ $tarea->comments->count() }}</span>
                            </button>
                        </div>

                        <form id="editForm{{ $tarea->id }}" style="display:none;" action="{{ route('tareas.update', $tarea->id) }}" method="POST" class="edit-form p-4 bg-gray-100 dark:bg-gray-700">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label for="title{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                                    <input type="text" id="title{{ $tarea->id }}" name="title" value="{{ $tarea->title }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                </div>
                                <div class="col-span-2">
                                    <label for="description{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                    <textarea id="description{{ $tarea->id }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">{{ $tarea->description }}</textarea>
                                </div>
                                <div>
                                    <label for="start_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de inicio</label>
                                    <input type="date" id="start_date{{ $tarea->id }}" name="start_date" value="{{ $tarea->start_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                </div>
                                <div>
                                    <label for="end_date{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de fin</label>
                                    <input type="date" id="end_date{{ $tarea->id }}" name="end_date" value="{{ $tarea->end_date->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                </div>
                                <div class="col-span-2">
                                    <label for="priority{{ $tarea->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                                    <select id="priority{{ $tarea->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        <option value="low" {{ $tarea->priority == 'low' ? 'selected' : '' }}>Baja</option>
                                        <option value="medium" {{ $tarea->priority == 'medium' ? 'selected' : '' }}>Media</option>
                                        <option value="high" {{ $tarea->priority == 'high' ? 'selected' : '' }}>Alta</option>
                                        <option value="urgent" {{ $tarea->priority == 'urgent' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out transform hover:scale-105">
                                    Guardar cambios
                                </button>
                            </div>
                        </form>

                        <div id="commentsSection-{{ $tarea->id }}" class="hidden bg-gray-50 dark:bg-gray-700 p-4 rounded-b-lg">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Comentarios</h4>
                            <div id="commentsList-{{ $tarea->id }}" class="space-y-4 mb-6">
                                @foreach ($tarea->comments as $comment)
                                    <div id="comment-{{ $comment->id }}" class="flex items-start space-x-3 bg-white dark:bg-gray-600 p-4 rounded-lg shadow-sm">
                                        <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-10 h-10 rounded-full">
                                        <div class="flex-grow">
                                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                                <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $comment->user->name }}</span>
                                                <span class="text-gray-500 text-xs ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                            </p>
                                            <p id="commentContent-{{ $comment->id }}" class="mt-1">{{ $comment->content }}</p>
                                            @if($comment->user_id == auth()->id())
                                                <div class="mt-2 flex space-x-2">
                                                    <button onclick="editEmployerComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs transition duration-150 ease-in-out">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </button>
                                                    <button onclick="deleteEmployerComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs transition duration-150 ease-in-out">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <form onsubmit="addEmployerTaskComment(event, {{ $tarea->id }})" class="mt-4">
                                @csrf
                                <div class="flex items-start space-x-4">
                                    <textarea id="newComment-{{ $tarea->id }}" rows="3" class="flex-grow p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none dark:bg-gray-600 dark:border-gray-500 dark:text-white" placeholder="Añadir un comentario..."></textarea>
                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out transform hover:scale-105">
                                        <i class="fas fa-paper-plane mr-2"></i>Comentar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>




<style>
    .task-card {
        transition: all 0.3s ease;
    }
    .task-card:hover {
        transform: translateY(-5px);
    }
    .priority-badge {
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .priority-low { background-color: #E5F6FD; color: #0369A1; }
    .priority-medium { background-color: #FEF3C7; color: #92400E; }
    .priority-high { background-color: #FEE2E2; color: #B91C1C; }
    .priority-urgent { background-color: #FECACA; color: #7F1D1D; }
    .status-completed { background-color: #D1FAE5; color: #065F46; }
    .status-in-progress { background-color: #E0E7FF; color: #a67616; }
    .toggle-status-button-completed { background-color: #10B981; color: white; }
    .toggle-status-button-completed:hover { background-color: #059669; }
    .toggle-status-button-in-progress { background-color: #ffab5c; color: white; }
    .toggle-status-button-in-progress:hover { background-color: black; }
</style>

<script>


function toggleTaskDetails(taskId) {
    const detailsElement = document.getElementById(`taskDetails-${taskId}`);
    const chevronElement = document.getElementById(`chevron-${taskId}`);
    detailsElement.classList.toggle('hidden');
    chevronElement.classList.toggle('rotate-180');
}

//ESTE ES EL JS NUEVO PARA MANEJAR LAS TAREAS DE LA EMPRESA

// Función para marcar una tarea como completada o en progreso
function toggleEmployerTaskCompletion(taskId) {
    const toggleButton = document.getElementById(`toggle-button-${taskId}`);
    const statusBadge = document.getElementById(`status-badge-${taskId}`);

    if (toggleButton && statusBadge) {
        const isCompleted = toggleButton.textContent.includes('Marcar como En Progreso');

        fetch(`/empleador/tareas/${taskId}/toggle-completion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ completed: !isCompleted })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (isCompleted) {
                    toggleButton.textContent = 'Marcar como Completada';
                    toggleButton.classList.remove('toggle-status-button-completed');
                    toggleButton.classList.add('toggle-status-button-in-progress');
                    statusBadge.textContent = 'En Progreso';
                    statusBadge.classList.remove('status-completed');
                    statusBadge.classList.add('status-in-progress');
                } else {
                    toggleButton.textContent = 'Marcar como En Progreso';
                    toggleButton.classList.remove('toggle-status-button-in-progress');
                    toggleButton.classList.add('toggle-status-button-completed');
                    statusBadge.textContent = 'Completada';
                    statusBadge.classList.remove('status-in-progress');
                    statusBadge.classList.add('status-completed');
                }
                showAlert('Estado de la tarea actualizado con éxito', 'success');
            } else {
                showAlert('Error al actualizar el estado de la tarea', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al actualizar el estado de la tarea', 'error');
        });
    } else {
        console.error(`Toggle button or status badge not found for task ${taskId}`);
    }
}


function addEmployerTaskComment(event, taskId) {
    event.preventDefault();
    const newCommentTextarea = document.getElementById(`newComment-${taskId}`);
    if (newCommentTextarea) {
        const commentContent = newCommentTextarea.value;

        if (commentContent.trim() === '') {
            showAlert('Por favor, escribe un comentario antes de enviarlo.', 'error');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/empleador/tareas/${taskId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: commentContent })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Comentario agregado con éxito');
                // Agregar el comentario a la lista de comentarios
                const commentsList = document.getElementById(`commentsList-${taskId}`);
                const newComment = createEmployerCommentHTML(data.comment);
                commentsList.insertAdjacentHTML('beforeend', newComment);
                // Mostrar el mensaje de éxito al usuario
                showAlert(data.message, 'success');
                // Limpiar el textarea
                newCommentTextarea.value = '';
                // Actualizar el contador de comentarios
                const commentCount = document.getElementById(`commentCount-${taskId}`);
                if (commentCount) {
                    commentCount.textContent = parseInt(commentCount.textContent) + 1;
                }
            } else {
                throw new Error(data.message || 'Error al agregar el comentario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert(error.message, 'error');
        });
    } else {
        console.error(`New comment textarea not found for task ${taskId}`);
    }
}


// Función para crear el HTML de un nuevo comentario
function createEmployerCommentHTML(comment) {
    return `
        <div id="comment-${comment.id}" class="bg-white dark:bg-gray-600 p-4 rounded-lg shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex-grow">
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        <span class="font-medium text-indigo-600 dark:text-indigo-400">${comment.user.name}:</span>
                        <span id="commentContent-${comment.id}">${comment.content}</span>
                    </p>
                    <small class="text-xs text-gray-500 dark:text-gray-400">${new Date(comment.created_at).toLocaleString()}</small>
                </div>
                <div class="flex space-x-2">
                    <button onclick="editEmployerComment(${comment.id})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs transition duration-150 ease-in-out">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button onclick="deleteEmployerComment(${comment.id}, ${comment.task_id})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs transition duration-150 ease-in-out">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Función para editar un comentario
function editEmployerComment(commentId) {
    const commentContent = document.getElementById(`commentContent-${commentId}`);
    if (commentContent) {
        const currentContent = commentContent.textContent;
        const textarea = document.createElement('textarea');
        textarea.value = currentContent;
        textarea.classList.add('w-full', 'p-2', 'border', 'rounded', 'mt-2', 'dark:bg-gray-600', 'dark:text-white');

        const saveButton = document.createElement('button');
        saveButton.textContent = 'Guardar';
        saveButton.classList.add('mt-2', 'px-4', 'py-2', 'bg-blue-500', 'text-white', 'rounded', 'hover:bg-blue-600');

        saveButton.onclick = function() {
            updateEmployerComment(commentId, textarea.value);
        };

        commentContent.parentNode.insertBefore(textarea, commentContent.nextSibling);
        textarea.parentNode.insertBefore(saveButton, textarea.nextSibling);
        commentContent.style.display = 'none';
    } else {
        console.error(`Comment content not found for comment ${commentId}`);
    }
}

// Función para actualizar un comentario
function updateEmployerComment(commentId, newContent) {
    const taskId = document.getElementById(`comment-${commentId}`).closest('.task-card').id.split('-')[1];
    fetch(`/empleador/tareas/${taskId}/comments/${commentId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ content: newContent })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const commentElement = document.getElementById(`comment-${commentId}`);
            const commentContent = document.getElementById(`commentContent-${commentId}`);
            if (commentContent) {
                commentContent.textContent = newContent;
                commentContent.style.display = 'block';
                const textarea = commentElement.querySelector('textarea');
                const saveButton = commentElement.querySelector('button');
                if (textarea) textarea.remove();
                if (saveButton) saveButton.remove();
                showAlert('Comentario actualizado con éxito', 'success');
            }
        } else {
            showAlert('Error al actualizar el comentario', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al actualizar el comentario', 'error');
    });
}

// Función para eliminar un comentario
function deleteEmployerComment(commentId, taskId) {
    if (confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
        fetch(`/empleador/tareas/${taskId}/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentElement = document.getElementById(`comment-${commentId}`);
                if (commentElement) {
                    commentElement.remove();
                    showAlert('Comentario eliminado con éxito', 'success');

                    // Actualizar el contador de comentarios
                    const commentCount = document.getElementById(`commentCount-${taskId}`);
                    if (commentCount) {
                        commentCount.textContent = parseInt(commentCount.textContent) - 1;
                    }
                }
            } else {
                showAlert('Error al eliminar el comentario', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al eliminar el comentario', 'error');
        });
    }
}

// Función para mostrar/ocultar el formulario de edición de una tarea
function showEmployerEditFields(taskId) {
    const editForm = document.getElementById(`editForm${taskId}`);
    if (editForm) {
        editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
    } else {
        console.error(`Edit form not found for task ${taskId}`);
    }
}

// Función para mostrar/ocultar los comentarios de una tarea
function toggleEmployerComments(taskId) {
    const commentsSection = document.getElementById(`commentsSection-${taskId}`);
    const commentButtonText = document.getElementById(`commentButtonText-${taskId}`);
    if (commentsSection && commentButtonText) {
        if (commentsSection.classList.contains('hidden')) {
            commentsSection.classList.remove('hidden');
            commentButtonText.textContent = 'Ocultar Comentarios';
        } else {
            commentsSection.classList.add('hidden');
            commentButtonText.textContent = 'Mostrar Comentarios';
        }
    } else {
        console.error(`Comments section or button not found for task ${taskId}`);
    }
}

// Función auxiliar para mostrar alertas
function showAlert(message, type) {
    const alertElement = document.createElement('div');
    alertElement.className = `fixed top-4 right-4 p-4 rounded-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
    alertElement.textContent = message;
    document.body.appendChild(alertElement);

    setTimeout(() => {
        alertElement.remove();
    }, 3000);
}
</script>



      


        <!-- <div id="taskList" class="space-y-4  mt-20">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Tareas creadas por mis profesionales</h2>
            @foreach($tareas as $tarea)
                <div id="task-{{ $tarea->id }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden
                            @if($tarea->priority == 'urgent') border-l-4 border-red-500
                            @elseif($tarea->priority == 'high') border-l-4 border-orange-500
                            @elseif($tarea->priority == 'medium') border-l-4 border-yellow-500
                            @else border-l-4 border-blue-500
                            @endif">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $tarea->title }}</h3>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                             @if($tarea->priority == 'urgent') bg-red-100 text-red-800
                                             @elseif($tarea->priority == 'high') bg-orange-100 text-orange-800
                                             @elseif($tarea->priority == 'medium') bg-yellow-100 text-yellow-800
                                             @else bg-blue-100 text-blue-800
                                             @endif">
                                    {{ ucfirst($tarea->priority) }}
                                </span>
                                <button onclick="toggleCompletion({{ $tarea->id }})" class="text-gray-500 hover:text-green-500 focus:outline-none">
                                    <i id="complete-icon-{{ $tarea->id }}" class="fas fa-check-circle text-2xl {{ $tarea->completed ? 'text-green-500' : '' }}"></i>
                                </button>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $tarea->description }}</p>
                        <div class="mt-3 flex flex-wrap items-center text-xs text-gray-500 dark:text-gray-400 space-x-4">
                            <div class="flex items-center">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <span>{{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="far fa-user mr-1"></i>
                                <span>Creado por: {{ $tarea->createdBy->name ?? 'Usuario desconocido' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 flex justify-between items-center">
                        <div class="text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Estado:</span>
                            <span id="status-{{ $tarea->id }}" class="ml-1 {{ $tarea->completed ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $tarea->completed ? 'Completada' : 'En progreso' }}
                            </span>
                        </div>
                        <div>
                            <button onclick="toggleComments({{ $tarea->id }})" class="text-sm text-blue-600 hover:text-blue-800 focus:outline-none">
                                Ver comentarios
                            </button>
                        </div>
                    </div>
                    <div id="comments-{{ $tarea->id }}" class="hidden bg-gray-50 dark:bg-gray-700 p-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Comentarios</h4>
                        <div id="commentsList-{{ $tarea->id }}" class="space-y-2 mb-4">
                            @foreach ($tarea->comments as $comment)
                                <div id="comment-{{ $comment->id }}" class="bg-white p-4 rounded-lg shadow-sm">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-grow">
                                            <p class="text-sm text-gray-800">
                                                <span class="font-medium text-indigo-600">{{ $comment->user->name }}:</span> 
                                                <span id="commentContent-{{ $comment->id }}">{{ $comment->content }}</span>
                                            </p>
                                            <small class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($comment->user_id == auth()->id())
                                            <div class="flex space-x-2">
                                                <button onclick="editComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 text-xs transition duration-150 ease-in-out">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button onclick="deleteComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 text-xs transition duration-150 ease-in-out">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <form onsubmit="addComment(event, {{ $tarea->id }})" class="mt-2">
                            @csrf
                            <textarea id="newComment-{{ $tarea->id }}" rows="2" class="w-full p-2 border rounded dark:bg-gray-600 dark:text-white" placeholder="Añadir un comentario..."></textarea>
                            <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-150 ease-in-out">Comentar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        
 -->


 <!-- <div class="bg-gray-100 dark:bg-gray-900 p-8 rounded-xl shadow-lg mt-20">
    <h2 class="text-3xl font-black text-blue-500 dark:text-blue-500 mb-8 pb-4 border-b-2 border-blue-500">Actividades del equipo</h2>

    
    <div id="taskList" class="space-y-8">
        @foreach($tareas as $tarea)
            <div id="task-{{ $tarea->id }}" class="task-card bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:scale-102 hover:shadow-xl
                        @if($tarea->priority == 'urgent') border-l-4 border-red-500
                        @elseif($tarea->priority == 'high') border-l-4 border-orange-500
                        @elseif($tarea->priority == 'medium') border-l-4 border-yellow-500
                        @else border-l-4 border-blue-500
                        @endif">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class=" text-2xl font-black text-gray-700 dark:text-white">{{ $tarea->title }}</h3>
                        <div class="flex items-center space-x-3 ml-2">
                
                            <span class="priority-badge px-3 py-1 rounded-full text-sm font-medium
                                         @if($tarea->priority == 'urgent') priority-urgent
                                         @elseif($tarea->priority == 'high') priority-high
                                         @elseif($tarea->priority == 'medium') priority-medium
                                         @else priority-low
                                         @endif">
                                   {{ ucfirst($tarea->priority) }}
                            </span>
                            <button onclick="toggleCompletion({{ $tarea->id }})" class="text-gray-500 hover:text-green-500 focus:outline-none transition duration-300 ease-in-out">
                                <i id="complete-icon-{{ $tarea->id }}" class="fas fa-check-circle text-3xl {{ $tarea->completed ? 'text-green-500' : '' }}"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-600 mb-4 ml-2"><span class="text-gray-600 font-bold">Descripción: </span>{{ $tarea->description }}</p>
                    <div class="flex flex-wrap items-center text-sm text-gray-500 dark:text-gray-400 space-x-6">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>
                            <span><span class="text-gray-600 font-bold">Rango de fechas:</span> {{ $tarea->start_date->format('d/m/Y') }} al {{ $tarea->end_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user mr-2 text-indigo-500"></i>
                            <span><span class="text-gray-600 font-bold">Creado por:</span> {{ $tarea->createdBy->name ?? 'Usuario desconocido' }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-between items-center">
                    <div class="text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Estado:</span>
                        <span id="status-{{ $tarea->id }}" class="ml-2 px-2 py-1 rounded-full text-sm font-medium {{ $tarea->completed ? 'status-completed' : 'status-in-progress' }}">
                            {{ $tarea->completed ? 'Completada' : 'En progreso' }}
                        </span>
                    </div>
                    <button onclick="toggleComments({{ $tarea->id }})" class="task-button bg-gray-300 hover:bg-blue-500 hover:text-white text-black px-4 py-2 rounded-md transition duration-300 ease-in-out">
                        <i class="fas fa-comments mr-2"></i>Mostrar comentarios
                    </button>
                </div>
                <div id="comments-{{ $tarea->id }}" class="hidden bg-gray-50 dark:bg-gray-700 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Comentarios</h4>
                    <div id="commentsList-{{ $tarea->id }}" class="space-y-4 mb-6">
                        @foreach ($tarea->comments as $comment)
                            <div id="comment-{{ $comment->id }}" class="bg-white dark:bg-gray-600 p-4 rounded-lg shadow-sm">
                                <div class="flex items-start justify-between">
                                    <div class="flex-grow">
                                        <p class="text-sm text-gray-800 dark:text-gray-200">
                                            <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $comment->user->name }}:</span> 
                                            <span id="commentContent-{{ $comment->id }}">{{ $comment->content }}</span>
                                        </p>
                                        <small class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($comment->user_id == auth()->id())
                                        <div class="flex space-x-2">
                                            <button onclick="editComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs transition duration-150 ease-in-out">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button onclick="deleteComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs transition duration-150 ease-in-out">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form onsubmit="addComment(event, {{ $tarea->id }})" class="mt-4">
                        @csrf
                        <div class="flex items-start space-x-4">
                            <textarea id="newComment-{{ $tarea->id }}" rows="3" class="flex-grow p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none dark:bg-gray-600 dark:border-gray-500 dark:text-white" placeholder="Añadir un comentario..."></textarea>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-150 ease-in-out">
                                <i class="fas fa-paper-plane mr-2"></i>Comentar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div> -->



<div class="bg-gray-100 dark:bg-gray-900 p-10 rounded-xl shadow-lg mt-10 mb-16 ">
   

    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 md:p-8 rounded-t-xl shadow-lg">
    <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">Actividades del equipo</h2>
    <p class="text-blue-100 text-sm md:text-base">Gestiona las tareas de tu equipo de forma eficiente</p>
</div>

    <div id="taskList" class="space-y-4 p-16">
        @foreach($tareas as $tarea)
            <div id="task-{{ $tarea->id }}" class="task-card bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition duration-300 ease-in-out hover:shadow-md
                        @if($tarea->priority == 'urgent') border-l-4 border-red-500
                        @elseif($tarea->priority == 'high') border-l-4 border-orange-500
                        @elseif($tarea->priority == 'medium') border-l-4 border-yellow-500
                        @else border-l-4 border-blue-500
                        @endif">
                <!-- Task Header -->
                <div class="p-4 flex items-center justify-between cursor-pointer" onclick="toggleTaskDetails({{ $tarea->id }})">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-white truncate flex-grow">{{ $tarea->title }}</h3>
                    <div class="flex items-center space-x-2">
                        <span class="priority-badge px-2 py-1 rounded-full text-xs font-medium
                                     @if($tarea->priority == 'urgent') priority-urgent
                                     @elseif($tarea->priority == 'high') priority-high
                                     @elseif($tarea->priority == 'medium') priority-medium
                                     @else priority-low
                                     @endif">
                            {{ ucfirst($tarea->priority) }}
                        </span>
                        <span id="status-{{ $tarea->id }}" class="px-2 py-1 rounded-full text-xs font-medium {{ $tarea->completed ? 'status-completed' : 'status-in-progress' }}">
                            {{ $tarea->completed ? 'Completada' : 'En progreso' }}
                        </span>
                        <button onclick="toggleCompletion({{ $tarea->id }}); event.stopPropagation();" class="text-gray-500 hover:text-green-500 focus:outline-none transition duration-300 ease-in-out">
                            <i id="complete-icon-{{ $tarea->id }}" class="fas fa-check-circle text-xl {{ $tarea->completed ? 'text-green-500' : '' }}"></i>
                        </button>
                        <i class="fas fa-chevron-down transform transition-transform duration-300" id="chevron-{{ $tarea->id }}"></i>
                    </div>
                </div>

                <!-- Task Details (Collapsible) -->
                <div id="taskDetails-{{ $tarea->id }}" class="hidden">
                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-sm">
                        <p class="text-gray-600 dark:text-gray-300 mb-2"><span class="font-semibold">Descripción:</span> {{ $tarea->description }}</p>
                        <div class="flex flex-wrap items-center text-xs text-gray-500 dark:text-gray-400 space-y-1 md:space-y-0 md:space-x-4">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-1 text-indigo-500"></i>
                                <span>{{ $tarea->start_date->format('d/m/Y') }} - {{ $tarea->end_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user mr-1 text-indigo-500"></i>
                                <span>{{ $tarea->createdBy->name ?? 'Usuario desconocido' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="p-4">
                        <button onclick="toggleComments({{ $tarea->id }})" class="text-sm bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-3 py-1 rounded-md transition duration-300 ease-in-out">
                            <i class="fas fa-comments mr-1"></i>Comentarios
                        </button>
                        <div id="comments-{{ $tarea->id }}" class="hidden mt-4">
                            <div id="commentsList-{{ $tarea->id }}" class="space-y-2 mb-4">
                                @foreach ($tarea->comments as $comment)
                                    <div id="comment-{{ $comment->id }}" class="bg-white dark:bg-gray-600 p-2 rounded-md shadow-sm text-sm">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-grow">
                                                <p class="text-gray-800 dark:text-gray-200">
                                                    <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $comment->user->name }}:</span> 
                                                    <span id="commentContent-{{ $comment->id }}">{{ $comment->content }}</span>
                                                </p>
                                                <small class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                            @if($comment->user_id == auth()->id())
                                                <div class="flex space-x-2">
                                                    <button onclick="editComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteComment({{ $comment->id }}, {{ $tarea->id }})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <form onsubmit="addComment(event, {{ $tarea->id }})" class="flex items-center space-x-2">
                                @csrf
                                <input type="text" id="newComment-{{ $tarea->id }}" class="flex-grow p-2 text-sm border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white" placeholder="Añadir un comentario...">
                                <button type="submit" class="px-3 py-2 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 transition duration-150 ease-in-out">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .task-card {
        transition: all 0.3s ease;
    }
    .task-card:hover {
        transform: translateY(-5px);
    }
    .priority-badge {
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .priority-urgent { background-color: #FEE2E2; color: #991B1B; }
    .priority-high { background-color: #FEF3C7; color: #92400E; }
    .priority-medium { background-color: #E0E7FF; color: #3730A3; }
    .priority-low { background-color: #E5F6FD; color: #0369A1; }
    .status-completed { background-color: #D1FAE5; color: #065F46; }
    .status-in-progress { background-color: #FEF3C7; color: #92400E; }
</style>

        
<script>


function toggleTaskDetails(taskId) {
    const detailsElement = document.getElementById(`taskDetails-${taskId}`);
    const chevronElement = document.getElementById(`chevron-${taskId}`);
    detailsElement.classList.toggle('hidden');
    chevronElement.classList.toggle('rotate-180');
}

        function toggleCompletion(taskId) {
            const icon = document.getElementById(`complete-icon-${taskId}`);
            const statusSpan = document.getElementById(`status-${taskId}`);
            const isCompleted = icon.classList.contains('text-green-500');
            
            fetch(`/tasks/${taskId}/toggle-completion`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ completed: !isCompleted })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    icon.classList.toggle('text-green-500');
                    statusSpan.textContent = isCompleted ? 'En progreso' : 'Completada';
                    statusSpan.classList.toggle('text-green-600');
                    statusSpan.classList.toggle('text-yellow-600');
                } else {
                    showAlert('Error al actualizar el estado de la tarea', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al actualizar el estado de la tarea', 'error');
            });
        }
        
        function toggleComments(taskId) {
            const commentsSection = document.getElementById(`comments-${taskId}`);
            commentsSection.classList.toggle('hidden');
        }
        
        function addComment(event, taskId) {
            event.preventDefault();
            const content = document.getElementById(`newComment-${taskId}`).value;
            if (!content.trim()) return;
        
            fetch('{{ route('comments.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ task_id: taskId, content: content })
            })
            .then(response => response.json())
            .then(comment => {
                const commentsList = document.getElementById(`commentsList-${taskId}`);
                commentsList.insertAdjacentHTML('beforeend', createCommentHTML(comment));
                document.getElementById(`newComment-${taskId}`).value = '';
                showAlert('Comentario añadido con éxito', 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al añadir el comentario: ' + error.message, 'error');
            });
        }
        
        function createCommentHTML(comment) {
            return `
                <div id="comment-${comment.id}" class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex-grow">
                            <p class="text-sm text-gray-800">
                                <span class="font-medium text-indigo-600">${comment.user.name}:</span> 
                                <span id="commentContent-${comment.id}">${comment.content}</span>
                            </p>
                            <small class="text-xs text-gray-500">${new Date(comment.created_at).toLocaleString()}</small>
                        </div>
                        ${comment.user_id == {{ auth()->id() }} ? `
                            <div class="flex space-x-2">
                                <button onclick="editComment(${comment.id})" class="text-blue-500 hover:text-blue-700 text-xs transition duration-150 ease-in-out">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button onclick="deleteComment(${comment.id}, ${comment.task_id})" class="text-red-500 hover:text-red-700 text-xs transition duration-150 ease-in-out">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }
        
function editComment(commentId) {
    const commentElement = document.getElementById(`comment-${commentId}`);
    const contentElement = commentElement.querySelector(`#commentContent-${commentId}`);
    const currentContent = contentElement.textContent.trim();
    
    const input = document.createElement('textarea');
    input.value = currentContent;
    input.className = 'w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none';
    input.rows = 3;
    
    contentElement.replaceWith(input);
    
    input.focus();

    const saveButton = document.createElement('button');
    saveButton.textContent = 'Guardar';
    saveButton.className = 'mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-150 ease-in-out';
    saveButton.onclick = () => updateComment(commentId, input.value);

    commentElement.appendChild(saveButton);
}

function updateComment(commentId, newContent) {
    fetch(`/comments/${commentId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ content: newContent })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const commentElement = document.getElementById(`comment-${commentId}`);
            if (commentElement) {
                // Recrear el contenido del comentario
                const newCommentHTML = `
                    <div class="flex-grow">
                        <p class="text-sm text-gray-800">
                            <span class="font-medium text-indigo-600">${data.comment.user.name}:</span> 
                            <span id="commentContent-${commentId}">${data.comment.content}</span>
                        </p>
                        <small class="text-xs text-gray-500">${new Date(data.comment.updated_at).toLocaleString()}</small>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="editComment(${commentId})" class="text-blue-500 hover:text-blue-700 text-xs transition duration-150 ease-in-out">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button onclick="deleteComment(${commentId}, ${data.comment.task_id})" class="text-red-500 hover:text-red-700 text-xs transition duration-150 ease-in-out">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                `;
                
                commentElement.innerHTML = newCommentHTML;
                showAlert('Comentario actualizado con éxito', 'success');
            } else {
                console.error(`Elemento de comentario no encontrado para el ID ${commentId}`);
                showAlert('Error al actualizar el comentario: Elemento no encontrado', 'error');
            }
        } else {
            showAlert('Error al actualizar el comentario: ' + (data.message || 'Error desconocido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al actualizar el comentario: ' + error.message, 'error');
    });
}


        
        function deleteComment(commentId, taskId) {
            if (!confirm('¿Estás seguro de que quieres eliminar este comentario?')) return;
        
            fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const commentElement = document.getElementById(`comment-${commentId}`);
                    commentElement.remove();
                    showAlert('Comentario eliminado con éxito', 'success');
                } else {
                    showAlert('Error al eliminar el comentario: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al eliminar el comentario: ' + error.message, 'error');
            });
        }
        
        function showAlert(message, type) {
            const alertElement = document.createElement('div');
            alertElement.className = `fixed top-4 right-4 p-4 rounded-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
            alertElement.textContent = message;
            document.body.appendChild(alertElement);
        
            setTimeout(() => {
                alertElement.remove();
            }, 3000);
        }
        </script>

    </main>
</div>








<!-- 
<div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Crear Nueva Tarea</h3>
        <form action="{{ route('empleador.crear-tarea') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                    <input type="text" name="title" id="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de inicio</label>
                    <input type="date" name="start_date" id="start_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de fin</label>
                    <input type="date" name="end_date" id="end_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                    <select name="priority" id="priority" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="low">Baja</option>
                        <option value="medium">Media</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asignar a</label>
                    <select name="employee_id" id="employee_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id }}">{{ $empleado->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Crear Tarea
                </button>
            </div>
        </form>
    </div>
</div> -->







</x-app-layout>