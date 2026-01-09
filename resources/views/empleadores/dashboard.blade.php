<x-app-layout>
    <div class="py-8 bg-white min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div class="flex items-center gap-4">
                     <h2 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B]">Monitoreo de horas</h2>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Subtitle -->
            <h3 class="text-[#22A9C8] font-medium text-base mb-6">Horas totales registradas por los profesionales</h3>
            
            <!-- Employee Stats Cards -->
            <div id="employer-stats-cards" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                @foreach($employeeSummaries as $summary)
                    @php
                        $percentage = $summary['target_hours'] > 0 ? min(100, ($summary['total_hours'] / $summary['target_hours']) * 100) : 0;
                        $dateRange = $currentMonth->copy()->startOfMonth()->format('M 1') . ' - ' . $currentMonth->copy()->endOfMonth()->format('M d');
                    @endphp
                    
                    <div class="bg-[#F8F9FA] rounded-[20px] p-6 relative flex flex-col items-center shadow-sm h-[320px]">
                        
                        <!-- Header -->
                        <div class="w-full text-center mb-8 mt-6 relative">
                            @if($summary['activity_status'] === 'red')
                                <div class="absolute -top-4 right-0 flex items-center gap-1">
                                    <span class="flex h-3 w-3 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-red-600 uppercase">Inactivo 2+ días</span>
                                </div>
                            @elseif($summary['activity_status'] === 'yellow')
                                <div class="absolute -top-4 right-0 flex items-center gap-1">
                                    <span class="flex h-3 w-3 rounded-full bg-yellow-400 animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-yellow-600 uppercase">Inactivo 1 día</span>
                                </div>
                            @endif

                            <h4 class="text-xl font-bold text-gray-900">{{ $summary['user']->name }}</h4>
                            <p class="text-gray-500 text-sm font-light">{{ $summary['role'] }}</p>
                        </div>

                        <!-- Semi Circular Chart -->
                        <div class="relative w-48 h-28 mb-4 flex justify-center overflow-hidden">
                             <svg viewBox="0 0 100 60" class="w-full h-full">
                                 <path d="M 10,10 A 40,40 0 0 0 90,10" 
                                       fill="none" 
                                       stroke="#E2E8F0" 
                                       stroke-width="8" 
                                       stroke-linecap="round" />
                                 <path d="M 10,10 A 40,40 0 0 0 90,10" 
                                       fill="none" 
                                       stroke="#22A9C8" 
                                       stroke-width="8" 
                                       stroke-linecap="round"
                                       stroke-dasharray="{{ 126 }}" 
                                       stroke-dashoffset="{{ 126 - (126 * $percentage / 100) }}"
                                       class="transition-all duration-1000 ease-out" />
                             </svg>
                             <div class="absolute top-8 text-center">
                                 <span class="text-4xl font-bold text-gray-900 block leading-none mb-1">{{ round($summary['total_hours']) }}</span>
                             </div>
                        </div>

                        <!-- Footer Text -->
                        <div class="text-center mt-auto mb-2">
                            <p class="text-gray-700 text-sm font-medium mb-1">
                                {{ $summary['days_worked'] }} días trabajados
                            </p>
                            <p class="text-gray-500 text-sm leading-tight max-w-[200px] mx-auto">
                                {{ round($summary['total_hours']) }} de {{ $summary['target_hours'] }} horas registradas actualmente ({{ $dateRange }})
                            </p>
                        </div> 
                    </div>
                @endforeach
            </div>

            <!-- Daily View Section -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Vistazo diario</h3>
                
                <!-- Month Navigation -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('empleador.dashboard', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="bg-[#22A9C8] text-white rounded p-1.5 hover:bg-primary-hover transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        
                        <span class="font-bold text-xl text-gray-900">{{ ucfirst($currentMonth->translatedFormat('F Y')) }}</span>
                        
                        <a href="{{ route('empleador.dashboard', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="bg-[#22A9C8] text-white rounded p-1.5 hover:bg-primary-hover transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Calendar Grid (Interactive) -->
                <div id="employer-calendar" x-data="{ 
     allMonthSuccessMessage: '',    
isApprovingWeek: false,     
weekSuccessMessage: '',
weekDate: '',  

                showApproveWeekModal: false,
                    selectedDay: null,
                    showModal: false,
                    showApproveAllModal: false,
                    isApproving: false,
                    isApprovingAll: false,
                    
                    openDetails(day) {
                        this.selectedDay = JSON.parse(JSON.stringify(day));
                        this.selectedDay.employees.forEach(emp => {
                            if (!emp.hasOwnProperty('new_comment')) emp.new_comment = '';
                        });
                        this.showModal = true;
                    },
                    
                    async approveDay(emp) {
                        if (this.isApproving) return;
                        this.isApproving = true;
                        
                        try {
                            const response = await fetch('{{ route('work-hours.approve-days') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    employee_id: emp.id,
                                    dates: [
                                        (typeof this.selectedDay.date === 'string' ? this.selectedDay.date : (this.selectedDay.date.date || this.selectedDay.date)).split(' ')[0].split('T')[0]
                                    ],
                                    comment: emp.new_comment || ''
                                })
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                emp.approved = true;
                                emp.comment = emp.new_comment;
                                window.location.reload(); 
                            } else {
                                alert(data.message || 'Error al aprobar las horas');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error de conexión al intentar aprobar');
                        } finally {
                            this.isApproving = false;
                        }
                    },


                    async approveWeek() {
    if (!this.weekDate) return;

    this.isApprovingWeek = true;
    this.weekSuccessMessage = '';

    try {
        const response = await fetch('{{ route('work-hours.approve-all-week') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                week_date: this.weekDate
            })
        });

        const data = await response.json();

        if (data.success) {
            this.weekSuccessMessage = '✅ Las horas de la semana fueron aprobadas correctamente';
            // Opcional: cerrar modal automáticamente después de 2 segundos
            setTimeout(() => {
                this.showApproveWeekModal = false;
                window.location.reload(); // refresca para reflejar cambios
            }, 2000);
        } else {
            alert(data.message || 'Error al aprobar la semana');
        }

    } catch (error) {
        console.error(error);
        alert('Error de conexión al intentar aprobar');
    } finally {
        this.isApprovingWeek = false;
    }
},
                    
                    async updateComment(emp) {
                        if (this.isApproving) return;
                        this.isApproving = true;
                        
                        try {
                            const response = await fetch(`/work-hours/update-comment/${emp.record_id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    comment: emp.comment
                                })
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                // Visual feedback
                            } else {
                                alert(data.message || 'Error al actualizar el comentario');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error de conexión');
                        } finally {
                            this.isApproving = false;
                        }
                    },
                    
                    async approveAllMonth() {
    if (this.isApprovingAll) return;
    this.isApprovingAll = true;
    this.allMonthSuccessMessage = '';

    try {
        const response = await fetch('{{ route('work-hours.approve-all-month') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                month: '{{ $currentMonth->format('Y-m') }}'
            })
        });

        const data = await response.json();

        if (data.success) {
            this.allMonthSuccessMessage = '✅ ' + data.message;
            // Cerrar modal después de 2 segundos y refrescar
            setTimeout(() => {
                this.showApproveAllModal = false;
                window.location.reload();
            }, 4000);
        } else {
            this.allMonthSuccessMessage = '❌ ' + (data.message || 'Error al aprobar las horas');
        }

    } catch (error) {
        console.error(error);
        this.allMonthSuccessMessage = '❌ Error de conexión al intentar aprobar';
    } finally {
        this.isApprovingAll = false;
    }
}

                }">
                    
                    <!-- Botón para aprobar todo el mes  -->
                    <div class="flex justify-end mb-4 gap-4">
                        <button 
                            @click="showApproveAllModal = true"
                            class="bg-[#22A9C8] hover:bg-[#0D5C7D]  text-white font-bold py-2.5 px-5 rounded-lg transition-all shadow-md flex items-center gap-2 text-sm hover:shadow-lg"
                        >
                           
                            Aprobar todo el mes
                        </button>

                      <!--Boton para aprobar horas de una semana-->

         <button 
    type="button" 
    class="bg-[#22A9C8] hover:bg-[#0D5C7D] text-white font-bold py-2.5 px-5 rounded-lg transition-all shadow-md flex items-center gap-2 text-sm"
    @click="showApproveWeekModal = true"
>
    Aprobar semana
</button>


                    </div>
                    
                    <!-- MOBILE VIEW -->
                    <div class="block md:hidden w-full border border-[#22A9C8] rounded-xl p-6 bg-white">
                        <div class="grid grid-cols-7 gap-4 mb-8">
                            @foreach(['Dom', 'Lun', 'Mar', 'Mier', 'Jue', 'Vie', 'Sab'] as $dayName)
                                <div class="text-center font-bold text-gray-900 text-base">{{ $dayName }}</div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-7 gap-y-6 gap-x-4">
                            @foreach($calendar as $day)
                                <div class="flex flex-col items-center justify-start min-h-[60px]">
                                    @if($day['is_current_month'])
                                        <button 
                                            @click="openDetails({{ json_encode($day) }})"
                                            class="relative w-12 h-12 rounded-full flex items-center justify-center text-base transition-colors
                                            {{ count($day['employees']) > 0 ? 'bg-gray-200 hover:bg-gray-300 text-gray-800' : 'bg-transparent text-gray-800 hover:bg-gray-100' }}"
                                        >
                                            <span class="z-10">{{ str_pad($day['day'], 2, '0', STR_PAD_LEFT) }}</span>
                                            
                                            @if(count($day['employees']) > 0)
                                                <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- DESKTOP VIEW -->
                    <div class="hidden md:block w-full border border-[#22A9C8] rounded-xl p-6 bg-white">
                        <div class="grid grid-cols-7 gap-3 mb-6">
                            @foreach(['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dayName)
                                <div class="text-center font-bold text-gray-900 text-sm">{{ $dayName }}</div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-7 gap-3">
                            @foreach($calendar as $day)
                                <div 
                                    @if($day['is_current_month'])
                                        @click="openDetails({{ json_encode($day) }})"
                                        class="bg-gray-50 rounded-lg p-3 min-h-[120px] flex flex-col cursor-pointer hover:bg-gray-100 transition-colors border border-transparent hover:border-[#22A9C8]"
                                    @else
                                        class="bg-gray-50 rounded-lg p-3 min-h-[120px] flex flex-col"
                                    @endif
                                >
                                     @if($day['is_current_month'])
                                        <div class="flex justify-center mb-3">
                                            <span class="bg-[#22A9C8] text-white rounded-full px-3 py-1 text-xs font-bold">
                                                {{ str_pad($day['day'], 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>

                                        @if(count($day['employees']) > 0)
                                            <div class="space-y-2 flex-1">
                                                @foreach($day['employees'] as $employee)
                                                    <div class="flex items-center gap-2" title="{{ $employee['name'] }}">
                                                        <x-user-avatar :name="$employee['name']" :avatar="$employee['avatar']" size="6" />
                                                        <span class="text-xs text-gray-700 font-medium">
                                                            {{ round($employee['hours']) }}h
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex-1 flex items-center justify-center">
                                                <span class="text-xs text-gray-400">-</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="opacity-30">
                                            <div class="flex justify-center mb-3">
                                                <span class="bg-gray-300 text-gray-500 rounded-full px-3 py-1 text-xs font-bold">
                                                    {{ str_pad($day['day'], 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

<!-- Modal para aprobar semana -->
<div
    x-show="showApproveWeekModal"
    x-transition
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
    aria-labelledby="approveWeekModalLabel"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop -->
    <div
        x-show="showApproveWeekModal"
        x-transition.opacity
        class="fixed inset-0 bg-gray-500 bg-opacity-75"
        @click="showApproveWeekModal = false"
    ></div>

    <!-- Panel -->
    <div
        x-show="showApproveWeekModal"
        x-transition
        class="relative bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full z-10"
    >
        <form @submit.prevent="approveWeek()" class="p-6">
            <h5 class="text-lg font-bold mb-4">Aprobar horas de la semana</h5>

            <label for="week_date" class="form-label">Selecciona cualquier día de la semana</label>
            <input type="date" id="week_date" x-model="weekDate" class="form-control w-full mb-3" required>
            <p class="text-sm text-gray-500 mb-4">El sistema tomará automáticamente el lunes y viernes de esa semana.</p>

            <!-- Mensaje de confirmación -->
            <div x-show="weekSuccessMessage" x-transition class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg">
                <p x-text="weekSuccessMessage"></p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" @click="showApproveWeekModal = false" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                <button type="submit" :disabled="isApprovingWeek"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg flex items-center gap-2 disabled:opacity-50">
                    <span x-text="isApprovingWeek ? 'Aprobando...' : 'Aprobar semana'"></span>
                </button>
            </div>
        </form>
    </div>
</div>



                    <!-- Modal para aprobar todo el mes -->
                    <div
                        x-show="showApproveAllModal"
                        style="display: none;"
                        class="fixed inset-0 z-[60] overflow-y-auto"
                        aria-labelledby="approve-all-modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div
                            x-show="showApproveAllModal"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            @click="showApproveAllModal = false"
                        ></div>

                        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                            <div
                                x-show="showApproveAllModal"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="relative bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full"
                            >
                                <div class="bg-white px-6 pt-6 pb-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-yellow-50 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900" id="approve-all-modal-title">
                                                    Aprobar todo el mes
                                                </h3>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    {{ ucfirst($currentMonth->translatedFormat('F Y')) }}
                                                </p>
                                            </div>
                                        </div>
                                        <button 
                                            @click="showApproveAllModal = false" 
                                            class="text-gray-400 hover:text-gray-500 transition-colors"
                                        >
                                            <span class="sr-only">Cerrar</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-white px-6 py-4">
                                    <div class="space-y-4">
                                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-yellow-700 font-medium">
                                                        ¡Atención! Esta acción aprobará TODAS las horas pendientes del mes.
                                                    </p>
                                                    <p class="text-sm text-yellow-600 mt-2">
                                                        • Se aprobarán horas de todos los empleados<br>
                                                        • Esta acción no se puede deshacer
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Resumen del mes</h4>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="text-center">
                                                    <p class="text-2xl font-bold text-[#22A9C8]">
                                                        {{ count($employeeSummaries) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">Empleados</p>
                                                </div>
                                                <div class="text-center">
                                                    <p class="text-2xl font-bold text-green-500">
                                                        {{ collect($calendar)->where('is_current_month', true)->sum(fn($day) => count($day['employees'])) }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">Registros totales</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Mensaje de confirmación -->
<div 
    x-show="allMonthSuccessMessage"
    x-transition
    class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg"
>
    <p x-text="allMonthSuccessMessage"></p>
</div>

                                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                                    <div class="flex justify-end gap-3">
                                        <button 
                                            type="button" 
                                            @click="showApproveAllModal = false"
                                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                                        >
                                            Cancelar
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="approveAllMonth()"
                                            :disabled="isApprovingAll"
                                            class="px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                        >
                                            <svg x-show="!isApprovingAll" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg x-show="isApprovingAll" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span x-text="isApprovingAll ? 'Aprobando...' : 'Confirmar Aprobación'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Backdrop & Modal para detalles diarios (EXISTENTE) -->
                    <div
                        x-show="showModal"
                        style="display: none;"
                        class="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <!-- Backdrop -->
                        <div
                            x-show="showModal"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            @click="showModal = false"
                        ></div>

                        <!-- Modal Panel -->
                        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                            <div
                                x-show="showModal"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full"
                            >
                                <!-- Modal Header -->
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            Detalle del día <span x-text="selectedDay ? new Date(selectedDay.date).toLocaleDateString('es-ES', { day: 'numeric', month: 'long' }) : ''"></span>
                                        </h3>
                                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Cerrar</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Modal Body -->
                                <div class="bg-white px-4 py-4 sm:p-6 max-h-[60vh] overflow-y-auto">
                                    <template x-if="selectedDay && selectedDay.employees.length > 0">
                                        <div class="space-y-6">
                                            <template x-for="emp in selectedDay.employees" :key="emp.record_id">
                                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                                    <!-- Employee Header -->
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-sm bg-gray-100 flex-shrink-0">
                                                                <img :src="emp.avatar ? (emp.avatar.startsWith('http') ? emp.avatar : '/avatars/' + emp.avatar) : `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.name)}&color=FFFFFF&background=22A9C8`" 
                                                                     :alt="emp.name" 
                                                                     class="w-full h-full object-cover">
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-gray-900" x-text="emp.name"></p>
                                                                <p class="text-xs text-gray-500">
                                                                    <span x-text="emp.hours"></span> horas registradas
                                                                    <span x-show="parseFloat(emp.hours) < 8" class="text-red-500 font-semibold ml-1" x-text="'• ' + (8 - parseFloat(emp.hours)) + 'h ausente'"></span>
                                                                    <span x-show="emp.approved" class="text-green-600 ml-1 font-semibold">(Aprobado)</span>
                                                                    <span x-show="!emp.approved" class="text-orange-500 ml-1 font-semibold">(Pendiente)</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Absence Reason Section -->
                                                    <template x-if="parseFloat(emp.hours) < 8 && emp.absence_reason">
                                                        <div class="mb-3 bg-red-50 rounded-lg p-3 border border-red-100">
                                                            <label class="block text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Motivo de Ausencia</label>
                                                            <p class="text-sm text-gray-800 font-medium" x-text="emp.absence_reason"></p>
                                                        </div>
                                                    </template>

                                                     <!-- Comment Section (Professional) -->
                                                     <div class="mt-4">
                                                         <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nota del Profesional</label>
                                                         <div class="bg-white rounded-lg p-3 border border-gray-100 shadow-sm italic text-sm text-gray-700">
                                                             <template x-if="emp.user_comment && emp.user_comment.trim() !== ''">
                                                                 <p x-text="emp.user_comment"></p>
                                                             </template>
                                                             <template x-if="!emp.user_comment || emp.user_comment.trim() === ''">
                                                                 <p class="text-gray-400">Sin notas del profesional.</p>
                                                             </template>
                                                         </div>
                                                     </div>

                                                     <!-- Comment Section (Employer / Response) -->
                                                     <div class="mt-4">
                                                         <label class="block text-xs font-bold text-primary uppercase tracking-wider mb-1">Tu Observación / Feedback</label>
                                                                                                                  <!-- If already approved, allow editing feedback -->
                                                          <template x-if="emp.approved">
                                                              <div class="space-y-3">
                                                                  <textarea 
                                                                      x-model="emp.comment"
                                                                      @blur="updateComment(emp)"
                                                                      placeholder="Agregar o editar feedback..."
                                                                      class="w-full rounded-xl border-gray-100 text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 transition-all italic text-gray-700 bg-gray-50/50"
                                                                      rows="2"
                                                                  ></textarea>
                                                                  <p class="text-[10px] text-gray-400 italic">El feedback se guarda automáticamente al salir del campo.</p>
                                                              </div>
                                                          </template>

                                                         <!-- If NOT approved, show approval form -->
                                                         <template x-if="!emp.approved || emp.approved === false || emp.approved === 0">
                                                             <div class="space-y-3">
                                                                 <textarea 
                                                                     x-model="emp.new_comment"
                                                                     placeholder="Deja un comentario opcional antes de aprobar..."
                                                                     class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-20 transition-all"
                                                                     rows="2"
                                                                 ></textarea>
                                                                 <button 
                                                                     @click="approveDay(emp)"
                                                                     :disabled="isApproving"
                                                                     class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-sm disabled:opacity-50"
                                                                 >
                                                                     <svg x-show="!isApproving" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                     </svg>
                                                                     <svg x-show="isApproving" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                         <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                         <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                     </svg>
                                                                     <span x-text="isApproving ? 'Aprobando...' : 'Aprobar Horas'"></span>
                                                                 </button>
                                                             </div>
                                                         </template>
                                                     </div>
                                                 </div>
                                             </template>
                                         </div>
                                     </template>
                                    
                                    <template x-if="!selectedDay || selectedDay.employees.length === 0">
                                        <div class="text-center py-8">
                                            <p class="text-gray-500">No hay registros de horas para este día.</p>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                    <button 
                                        type="button" 
                                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#22A9C8] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                        @click="showModal = false"
                                    >
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>