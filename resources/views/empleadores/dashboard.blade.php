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
                        // Semi-circle calculations
                        // We want a bottom arc. Let's use a simple SVG dasharray trick or path.
                        // Path for background: Semi-circle
                        $radius = 35;
                        $circumference = pi() * $radius; // Semi-circle length
                        $dashArray = ($percentage / 100) * $circumference;
                        $dateRange = $currentMonth->copy()->startOfMonth()->format('M 1') . ' - ' . $currentMonth->copy()->endOfMonth()->format('M d'); // Update localization if needed
                    @endphp
                    
                    <div class="bg-[#F8F9FA] rounded-[20px] p-6 relative flex flex-col items-center shadow-sm h-[320px]">
                        
                        <!-- Header -->
                        <div class="w-full text-center mb-8 mt-6 relative">
                            <!-- Status Indicator -->
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

                        <!-- Semi Circular Chart (U Shape) -->
                        <div class="relative w-48 h-28 mb-4 flex justify-center overflow-hidden">
                             <!-- Half circle SVG -->
                             <svg viewBox="0 0 100 60" class="w-full h-full">
                                 <!-- Background Arc -->
                                 <path d="M 10,10 A 40,40 0 0 0 90,10" 
                                       fill="none" 
                                       stroke="#E2E8F0" 
                                       stroke-width="8" 
                                       stroke-linecap="round" />
                                 <!-- Progress Arc -->
                                  <path d="M 10,10 A 40,40 0 0 0 90,10" 
                                       fill="none" 
                                       stroke="#22A9C8" 
                                       stroke-width="8" 
                                       stroke-linecap="round"
                                       stroke-dasharray="{{ 126 }}" 
                                       stroke-dashoffset="{{ 126 - (126 * $percentage / 100) }}"
                                       class="transition-all duration-1000 ease-out" />
                             </svg>
                             <!-- Number -->
                             <div class="absolute top-8 text-center">
                                 <span class="text-4xl font-bold text-gray-900 block leading-none mb-1">{{ round($summary['total_hours']) }}</span>
                             </div>
                        </div>

                        <!-- Footer Text -->
                        <div class="text-center mt-auto mb-2">
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
                <div class="flex items-center gap-4 mb-6">
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

                <!-- Calendar Grid (Interactive) -->
                <div id="employer-calendar" x-data="{ 
                    selectedDay: null,
                    showModal: false,
                    isApproving: false,
                    openDetails(day) {
                        this.selectedDay = JSON.parse(JSON.stringify(day));
                        this.selectedDay.employees.forEach(emp => {
                            if (!emp.hasOwnProperty('new_comment')) emp.new_comment = '';
                        });
                        this.showModal = true;
                        
                        // Handle date format (could be ISO string or YYYY-MM-DD)
                        let dateStr = typeof day.date === 'string' ? day.date : day.date.date;
                        if (dateStr.includes('T')) dateStr = dateStr.split('T')[0];
                        else dateStr = dateStr.substring(0, 10);
                        
                        this.startPolling(dateStr);
                    },
                    
                    async approveDay(emp) {
                        if (this.isApproving) return;
                        this.isApproving = true;
                        window.showLoader();
                        
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
                        window.showLoader();
                        
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
                                emp.just_saved = true;
                                setTimeout(() => emp.just_saved = false, 3000);
                            } else {
                                alert(data.message || 'Error al actualizar el comentario');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error de conexión');
                        } finally {
                            this.isApproving = false;
                        }
                    }
                }">
                    <!-- MOBILE VIEW: Simple calendar with modal (current behavior) -->
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
                                            
                                            <!-- Red Dot Indicator -->
                                            @if(count($day['employees']) > 0)
                                                <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                                            @endif

                                            <!-- Red Badge for Pending Recoveries -->
                                            <!-- Red Badge for Pending Recoveries Removed -->
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
                                        class="relative bg-gray-50 rounded-lg p-3 min-h-[120px] flex flex-col cursor-pointer hover:bg-gray-100 transition-colors border border-transparent hover:border-[#22A9C8]"
                                    @else
                                        class="relative bg-gray-50 rounded-lg p-3 min-h-[120px] flex flex-col"
                                    @endif
                                >
                                    @if($day['is_current_month'])
                                        <!-- Day Number Badge -->
                                        <div class="flex justify-center mb-3">
                                            <span class="bg-[#22A9C8] text-white rounded-full px-3 py-1 text-xs font-bold">
                                                {{ str_pad($day['day'], 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            
                                            <!-- Indicator Dot: Red for pending, Green for all approved, None for no records -->
                                            @if(count($day['employees']) > 0)
                                                @if($day['has_pending'])
                                                    <span class="w-3 h-3 bg-red-500 rounded-full border-2 border-white shadow-sm" title="Pendiente de aprobación"></span>
                                                @else
                                                    <span class="w-3 h-3 bg-green-500 rounded-full border-2 border-white shadow-sm" title="Todo aprobado"></span>
                                                @endif
                                            @endif
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

                                        <!-- Red Badge for Pending Recoveries -->
                                        <!-- Red Badge for Pending Recoveries Removed -->
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
            <p class="text-sm text-gray-500 mb-4">El sistema tomará automáticamente la semana completa a la que corresponde el día seleccionado.</p>

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
                                            Detalle del día <span x-text="selectedDay ? new Date(selectedDay.date.slice(0, 10) + 'T12:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long' }) : ''"></span>
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
                                <div class="bg-white px-4 py-4 sm:p-6 max-h-[70vh] overflow-y-auto no-scrollbar">
                                    <template x-if="selectedDay && selectedDay.employees.length > 0">
                                        <div class="space-y-8">
                                            <template x-for="emp in selectedDay.employees" :key="emp.record_id">
                                                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all">
                                                    <!-- Employee Header with Profile Badge Style -->
                                                    <div class="flex items-start justify-between mb-6">
                                                        <div class="flex items-center gap-4">
                                                            <div class="w-14 h-14 rounded-2xl overflow-hidden border-2 border-[#22A9C8]/10 shadow-sm bg-gray-100 flex-shrink-0">
                                                                <img :src="emp.avatar ? (emp.avatar.startsWith('http') ? emp.avatar : '/avatars/' + emp.avatar) : `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.name)}&color=FFFFFF&background=22A9C8`" 
                                                                     :alt="emp.name" 
                                                                     class="w-full h-full object-cover">
                                                            </div>
                                                            <div>
                                                                <p class="font-black text-lg text-gray-900 leading-tight" x-text="emp.name"></p>
                                                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#22A9C8] text-white" x-text="emp.hours + ' hs'"></span>
                                                                    
                                                                    <template x-if="parseFloat(emp.hours) < 8">
                                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-600" x-text="(8 - parseFloat(emp.hours)) + 'h ausente'"></span>
                                                                    </template>

                                                                    <template x-if="parseFloat(emp.recovered_hours) > 0">
                                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-600" x-text="emp.recovered_hours + 'h recup.'"></span>
                                                                    </template>

                                                                    <span :class="emp.approved ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600'" 
                                                                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider" 
                                                                          x-text="emp.approved ? 'Aprobado' : 'Pendiente'"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Absence Reason (Impactful Style) -->
                                                    <template x-if="parseFloat(emp.hours) < 8 && emp.absence_reason">
                                                        <div class="mb-6 bg-red-50/50 rounded-xl p-4 border border-red-100">
                                                            <div class="flex items-center gap-2 mb-2 text-red-600">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                <span class="text-[10px] font-black uppercase tracking-widest">Motivo de Ausencia</span>
                                                            </div>
                                                            <p class="text-sm text-gray-800 font-bold ml-6" x-text="emp.absence_reason"></p>
                                                        </div>
                                                    </template>

                                                    <!-- Activities Section (Parsed from user_comment) -->
                                                    <div class="mb-6">
                                                        <div class="flex items-center gap-2 mb-3 text-gray-400">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                            <span class="text-[10px] font-black uppercase tracking-widest leading-none">Actividades Realizadas</span>
                                                        </div>
                                                        
                                                        <div class="bg-gray-50/50 rounded-xl p-4 border border-gray-100 max-h-60 overflow-y-auto">
                                                            <template x-if="emp.user_comment && emp.user_comment.trim() !== ''">
                                                                <div x-data="{ parsed: parseComment(emp.user_comment) }">
                                                                    <template x-if="parsed.activities.length > 0">
                                                                        <ul class="space-y-3 mb-4">
                                                                            <template x-for="activity in parsed.activities">
                                                                                <li class="flex items-start gap-3">
                                                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#22A9C8] mt-1.5 flex-shrink-0"></span>
                                                                                    <span class="text-sm text-gray-700 font-medium" x-text="activity"></span>
                                                                                </li>
                                                                            </template>
                                                                        </ul>
                                                                    </template>
                                                                    <template x-if="parsed.summary">
                                                                        <div :class="parsed.activities.length > 0 ? 'mt-4 pt-4 border-t border-gray-100' : ''">
                                                                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Resumen adicional</p>
                                                                            <p class="text-sm text-gray-700 font-medium whitespace-pre-line" x-text="parsed.summary"></p>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                            <template x-if="!emp.user_comment || emp.user_comment.trim() === ''">
                                                                <p class="text-sm text-gray-400 italic">No se registraron detalles de actividades.</p>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <!-- Feedback Field -->
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-3 text-[#22A9C8]">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                                            <span class="text-[10px] font-black uppercase tracking-widest leading-none">Observaciones</span>
                                                        </div>

                                                        <template x-if="emp.approved">
                                                            <textarea 
                                                                x-model="emp.comment"
                                                                placeholder="Dejar feedback..."
                                                                class="w-full rounded-xl border-gray-100 text-sm shadow-sm focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-20 transition-all bg-gray-50/30 min-h-[80px]"
                                                            ></textarea>
                                                        </template>
                                                        
                                                        <template x-if="!emp.approved">
                                                            <textarea 
                                                                x-model="emp.new_comment"
                                                                placeholder="Dejar feedback..."
                                                                class="w-full rounded-xl border-gray-100 text-sm shadow-sm focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-20 transition-all bg-gray-50/30 min-h-[80px]"
                                                            ></textarea>
                                                        </template>
                                                        
                                                        <div class="flex justify-end mt-2">
                                                            <template x-if="emp.approved">
                                                                <button 
                                                                    @click="updateComment(emp)"
                                                                    :disabled="isApproving"
                                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#22A9C8] text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-opacity-90 transition-all shadow-sm active:scale-95 disabled:opacity-50"
                                                                >
                                                                    <svg x-show="!isApproving && !emp.just_saved" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                    <svg x-show="emp.just_saved" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                    <svg x-show="isApproving" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                                    <span x-text="isApproving ? 'Guardando...' : (emp.just_saved ? '¡Guardado!' : 'Guardar Observación')"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                        
                                                        <template x-if="!emp.approved">
                                                            <button 
                                                                @click="approveDay(emp)"
                                                                :disabled="isApproving"
                                                                class="mt-4 w-full bg-green-500 hover:bg-green-600 text-white font-black py-3 px-6 rounded-xl transition-all shadow-md active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2"
                                                            >
                                                                <svg x-show="!isApproving" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                <svg x-show="isApproving" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                                <span x-text="isApproving ? 'Aprobando...' : 'Aprobar Horas'"></span>
                                                            </button>
                                                        </template>

                                                        <template x-if="emp.approved">
                                                            <p class="mt-2 text-[10px] text-gray-400 italic text-center">Horas aprobadas. Puedes editar tu feedback arriba.</p>
                                                        </template>
                                                    </div>

                                                    <!-- Recovery Request Link (Inside Employee Card) -->
                                                 </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <template x-if="!selectedDay || selectedDay.employees.length === 0">
                                        <div class="py-12 flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs text-center">No hay registros para este día</p>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 gap-3">
                                    <button 
                                        type="button" 
                                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#22A9C8] sm:mt-0 sm:w-auto sm:text-sm"
                                        @click="showModal = false"
                                    >
                                        Cerrar
                                    </button>
                                    
                                    <template x-if="selectedDay && selectedDay.employees.length > 0">
                                        <a 
                                            :href="'/empleador/detalle-diario/' + (typeof selectedDay.date === 'string' ? selectedDay.date : (selectedDay.date.date || selectedDay.date)).split(' ')[0]"
                                            class="w-full inline-flex justify-center items-center gap-2 rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#22A9C8] text-base font-bold text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#22A9C8] sm:w-auto sm:text-sm transition-all"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Ver más detalles</span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recovery Modal -->
                    <!-- Recovery Modal Removed -->
                    </div>

                    <!-- Modal: Approve Week -->
                    <div
                        x-show="showApproveWeekModal"
                        x-transition
                        style="display: none;"
                        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
                        aria-labelledby="approveWeekModalLabel"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div
                            x-show="showApproveWeekModal"
                            x-transition.opacity
                            class="fixed inset-0 bg-gray-500 bg-opacity-75"
                            @click="showApproveWeekModal = false"
                        ></div>
                    
                        <div
                            x-show="showApproveWeekModal"
                            x-transition
                            class="relative bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full z-10"
                        >
                            <form @submit.prevent="approveWeek()" class="p-6">
                                <h5 class="text-lg font-bold mb-4">Aprobar horas de la semana</h5>
                    
                                <label for="week_date" class="form-label">Selecciona cualquier día de la semana</label>
                                <input type="date" id="week_date" x-model="weekDate" class="form-control w-full mb-3 rounded-lg border-gray-300" required>
                                <p class="text-sm text-gray-500 mb-4">El sistema tomará automáticamente el lunes y viernes de esa semana.</p>
                    
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

                    <!-- Modal: Approve All Month -->
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
                                
                                <div 
                                    x-show="allMonthSuccessMessage"
                                    x-transition
                                    class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg mx-6"
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
