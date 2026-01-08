<x-app-layout>
   
    <div class="py-8 bg-white min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div class="flex items-center gap-4">
                     <h2 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B]">Monitoreo de tareas</h2>
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
            <h3 class="text-[#22A9C8] font-medium text-base mb-6">Horas totales de tareas registradas por los profesionales</h3>
            
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
                                    <span class="text-[10px] font-bold text-red-600 uppercase">Inactivo 2+ dÃ­as</span>
                                </div>
                            @elseif($summary['activity_status'] === 'yellow')
                                <div class="absolute -top-4 right-0 flex items-center gap-1">
                                    <span class="flex h-3 w-3 rounded-full bg-yellow-400 animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-yellow-600 uppercase">Inactivo 1 dÃ­a</span>
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
                                {{ round($summary['total_hours']) }} de {{ $summary['target_hours'] }} horas de tareas registradas actualmente ({{ $dateRange }})
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
                    showRecoveryModal: false,
                    selectedRecoveries: [],
                    isApproving: false,
                    openDetails(day) {
                        this.selectedDay = JSON.parse(JSON.stringify(day)); // Deep copy to isolate state
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
                                // Optional: Update total hours or reload if needed. 
                                // For now, just mark as approved visually in the modal.
                                window.location.reload(); 
                            } else {
                                alert(data.message || 'Error al aprobar las horas');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error de conexiÃ³n al intentar aprobar');
                        } finally {
                            this.isApproving = false;
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
                            alert('Error de conexiÃ³n');
                        } finally {
                            this.isApproving = false;
                        }
                    },
                    openRecoveryModal(day) {
                        this.selectedRecoveries = day.employees.filter(emp => emp.recovered_hours > 0 && !emp.recovery_approved);
                        this.showRecoveryModal = true;
                    },
                    async approveRecovery(recovery) {
                        if (this.isApproving) return;
                        this.isApproving = true;
                        
                        try {
                            const response = await fetch(`{{ route('work-hours.approve-recovery', '') }}/${recovery.record_id}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });
                            
                            const data = await response.json();
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Error al aprobar la recuperaciÃ³n');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error de conexiÃ³n');
                        } finally {
                            this.isApproving = false;
                        }
                    }
                }">
                    <!-- MOBILE VIEW: Simple calendar with modal (current behavior) -->
                    <div class="block md:hidden w-full border border-[#22A9C8] rounded-xl p-6 bg-white">
                        <!-- Headers -->
                         <div class="grid grid-cols-7 gap-4 mb-8">
                            @foreach(['Dom', 'Lun', 'Mar', 'Mier', 'Jue', 'Vie', 'Sab'] as $dayName)
                                <div class="text-center font-bold text-gray-900 text-base">{{ $dayName }}</div>
                            @endforeach
                         </div>

                        <!-- Days -->
                        <div class="grid grid-cols-7 gap-y-6 gap-x-4">
                            @foreach($calendar as $day)
                                <div class="flex flex-col items-center justify-start min-h-[60px]">
                                    @if($day['is_current_month'])
                                        <button 
                                            @click="{{ json_encode($day) }}.pending_recoveries_count > 0 ? openRecoveryModal({{ json_encode($day) }}) : openDetails({{ json_encode($day) }})"
                                            class="relative w-12 h-12 rounded-full flex items-center justify-center text-base transition-colors
                                            {{ count($day['employees']) > 0 ? 'bg-gray-200 hover:bg-gray-300 text-gray-800' : 'bg-transparent text-gray-800 hover:bg-gray-100' }}"
                                        >
                                            <span class="z-10">{{ str_pad($day['day'], 2, '0', STR_PAD_LEFT) }}</span>
                                            
                                            <!-- Red Dot Indicator -->
                                            @if(count($day['employees']) > 0)
                                                <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                                            @endif

                                            <!-- Red Badge for Pending Recoveries -->
                                            @if($day['pending_recoveries_count'] > 0)
                                                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-red-600 rounded-full border-2 border-white z-20">
                                                    {{ $day['pending_recoveries_count'] }}
                                                </span>
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- DESKTOP VIEW: Detailed calendar showing all hours -->
                    <div class="hidden md:block w-full border border-[#22A9C8] rounded-xl p-6 bg-white">
                        <!-- Headers -->
                        <div class="grid grid-cols-7 gap-3 mb-6">
                            @foreach(['Domingo', 'Lunes', 'Martes', 'MiÃ©rcoles', 'Jueves', 'Viernes', 'SÃ¡bado'] as $dayName)
                                <div class="text-center font-bold text-gray-900 text-sm">{{ $dayName }}</div>
                            @endforeach
                        </div>

                        <!-- Days Grid -->
                        <div class="grid grid-cols-7 gap-3">
                            @foreach($calendar as $day)
                                <div 
                                    @if($day['is_current_month'])
                                        @click="{{ json_encode($day) }}.pending_recoveries_count > 0 ? openRecoveryModal({{ json_encode($day) }}) : openDetails({{ json_encode($day) }})"
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
                                        </div>

                                        <!-- Professionals Hours -->
                                        @if(count($day['employees']) > 0)
                                            <div class="space-y-2 flex-1">
                                                @foreach($day['employees'] as $employee)
                                                    <div class="flex items-center gap-2" title="{{ $employee['name'] }}">
                                                        <!-- Avatar / Initials -->
                                                        <x-user-avatar :name="$employee['name']" :avatar="$employee['avatar']" size="6" />
                                                        
                                                        <!-- Hours -->
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
                                        @if($day['pending_recoveries_count'] > 0)
                                            <div class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-red-600 rounded-full border-2 border-white z-20">
                                                {{ $day['pending_recoveries_count'] }}
                                            </div>
                                        @endif
                                    @else
                                        <!-- Empty cell for days outside current month -->
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

                    <!-- Custom Backdrop & Modal -->
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
                                            Detalle del dÃ­a <span x-text="selectedDay ? new Date(selectedDay.date.slice(0, 10) + 'T12:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long' }) : ''"></span>
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
                                                                    <span x-text="emp.hours"></span> horas de tareas
                                                                    <span x-show="parseFloat(emp.hours) < 8" class="text-red-500 font-semibold ml-1" x-text="'â€¢ ' + (8 - parseFloat(emp.hours)) + 'h ausente'"></span>
                                                                    <span x-show="parseFloat(emp.recovered_hours) > 0" class="text-blue-600 font-bold ml-1" x-text="'â€¢ ' + emp.recovered_hours + 'h recuperadas'"></span>
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
                                                         <label class="block text-xs font-bold text-primary uppercase tracking-wider mb-1">Tu ObservaciÃ³n / Feedback</label>
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
                                                                  <p class="text-[10px] text-gray-400 italic">El feedback se guarda automÃ¡ticamente al salir del campo.</p>
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
                                                      <!-- Recovery Approval Section -->
                                                      <template x-if="parseFloat(emp.recovered_hours) > 0">
                                                          <div class="mt-4 pt-4 border-t border-gray-100">
                                                              <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">Solicitud de Recuperación</h4>
                                                              <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                                                                  <div class="flex justify-between items-start gap-3">
                                                                       <div class="flex-1">
                                                                           <p class="text-sm text-gray-900 font-bold mb-1">
                                                                              <span x-text="emp.recovered_hours"></span> horas
                                                                              <span x-show="emp.recovery_approved" class="text-green-600 text-xs ml-2 bg-green-100 px-2 py-0.5 rounded-full">Aprobado</span>
                                                                              <span x-show="!emp.recovery_approved" class="text-orange-600 text-xs ml-2 bg-orange-100 px-2 py-0.5 rounded-full">Pendiente</span>
                                                                           </p>
                                                                           
                                                                           <template x-if="emp.recovery_comment">
                                                                               <p class="text-xs text-gray-700 italic" x-text="emp.recovery_comment"></p>
                                                                           </template>
                                                                           <template x-if="!emp.recovery_comment">
                                                                               <p class="text-xs text-gray-400 italic">Sin nota.</p>
                                                                           </template>
                                                                       </div>
                                                                       
                                                                       <div x-show="!emp.recovery_approved">
                                                                            <button 
                                                                                @click="approveRecovery(emp)"
                                                                                class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-2 px-3 rounded-xl shadow-sm transition-colors whitespace-nowrap"
                                                                            >
                                                                                Aprobar
                                                                            </button>
                                                                       </div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </template>
                                                 </div>
                                             </template>
                                         </div>
                                     </template>
                                    
                                    <template x-if="!selectedDay || selectedDay.employees.length === 0">
                                        <div class="text-center py-8">
                                            <p class="text-gray-500">No hay registros de tareas para este dÃ­a.</p>
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

                    <!-- Recovery Modal -->
                    <div
                        x-show="showRecoveryModal"
                        style="display: none;"
                        class="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="recovery-modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <!-- Backdrop -->
                        <div
                            x-show="showRecoveryModal"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            @click="showRecoveryModal = false"
                        ></div>

                        <!-- Modal Panel -->
                        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                            <div
                                x-show="showRecoveryModal"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-2xl w-full"
                            >
                                <!-- Modal Header -->
                                <div class="bg-red-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-red-100">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg leading-6 font-bold text-red-900 flex items-center gap-2" id="recovery-modal-title">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Solicitudes de RecuperaciÃ³n de Horas
                                        </h3>
                                        <button @click="showRecoveryModal = false" class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Cerrar</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Modal Body -->
                                <div class="bg-white px-4 py-4 sm:p-6 max-h-[60vh] overflow-y-auto">
                                    <template x-if="selectedRecoveries.length > 0">
                                        <div class="space-y-4">
                                            <template x-for="recovery in selectedRecoveries" :key="recovery.record_id">
                                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                    <!-- Employee Header -->
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-sm bg-gray-100 flex-shrink-0">
                                                                <img :src="recovery.avatar ? (recovery.avatar.startsWith('http') ? recovery.avatar : '/avatars/' + recovery.avatar) : `https://ui-avatars.com/api/?name=${encodeURIComponent(recovery.name)}&color=FFFFFF&background=22A9C8`" 
                                                                     :alt="recovery.name" 
                                                                     class="w-full h-full object-cover">
                                                            </div>
                                                            <div>
                                                                <p class="font-bold text-gray-900" x-text="recovery.name"></p>
                                                                <p class="text-xs text-gray-500">
                                                                    <span class="font-bold text-red-600" x-text="recovery.recovered_hours + 'h'"></span> a recuperar
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Recovery Details -->
                                                    <div class="mt-3 bg-white rounded-lg p-3 border border-gray-100">
                                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Actividades Realizadas</label>
                                                        <template x-if="recovery.recovery_comment && recovery.recovery_comment.trim() !== ''">
                                                            <p class="text-sm text-gray-700 whitespace-pre-line" x-text="recovery.recovery_comment"></p>
                                                        </template>
                                                        <template x-if="!recovery.recovery_comment || recovery.recovery_comment.trim() === ''">
                                                            <p class="text-sm text-gray-400 italic">Sin detalles</p>
                                                        </template>
                                                    </div>

                                                    <!-- Approve Button -->
                                                    <div class="mt-4">
                                                        <button 
                                                            @click="approveRecovery(recovery)"
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
                                                            <span x-text="isApproving ? 'Aprobando...' : 'Aprobar RecuperaciÃ³n'"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                   
                                    <template x-if="selectedRecoveries.length === 0">
                                        <div class="text-center py-8">
                                            <p class="text-gray-500">No hay solicitudes de recuperaciÃ³n para este dÃ­a.</p>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                    <button 
                                        type="button" 
                                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                        @click="showRecoveryModal = false"
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


