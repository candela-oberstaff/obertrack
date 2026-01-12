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
            <h3 class="text-[#22A9C8] font-medium text-base mb-6">Horas registradas por los profesionales</h3>
            
            <!-- Employee Stats Cards -->
            <div id="employer-stats-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                @foreach($employeeSummaries as $summary)
                    @php
                        $percentage = $summary['target_hours'] > 0 ? min(100, ($summary['total_hours'] / $summary['target_hours']) * 100) : 0;
                        $dateRange = $currentMonth->copy()->startOfMonth()->format('M 1') . ' - ' . $currentMonth->copy()->endOfMonth()->format('M d');
                    @endphp
                    
                    <div class="bg-[#F8F9FA] rounded-[2rem] p-6 relative flex flex-col items-center shadow-sm h-[320px] transition-all hover:shadow-md">
                        
                        <!-- Header -->
                        <div class="w-full text-center mb-6 mt-2 relative">
                            <!-- Status Indicator -->
                            @if($summary['activity_status'] === 'red')
                                <div class="absolute -top-4 right-0 flex items-center gap-1 bg-red-50 px-2 py-0.5 rounded-full border border-red-100">
                                    <span class="flex h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="text-[8px] font-black text-red-600 uppercase tracking-wider">Inactivo 2+ d</span>
                                </div>
                            @elseif($summary['activity_status'] === 'yellow')
                                <div class="absolute -top-4 right-0 flex items-center gap-1 bg-yellow-50 px-2 py-0.5 rounded-full border border-yellow-100">
                                    <span class="flex h-1.5 w-1.5 rounded-full bg-yellow-400 animate-pulse"></span>
                                    <span class="text-[8px] font-black text-yellow-600 uppercase tracking-wider">Inactivo 1 d</span>
                                </div>
                            @endif

                            <h4 class="text-lg font-black text-[#1a202c] leading-tight mb-0.5 truncate px-2">{{ $summary['user']->name }}</h4>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-widest">{{ $summary['role'] }}</p>
                        </div>

                        <!-- Semi Circular Chart (U Shape) -->
                        <div class="relative w-44 h-24 mb-4 flex justify-center overflow-hidden">
                             <!-- Half circle SVG -->
                             <svg viewBox="0 0 100 60" class="w-full h-full">
                                 <!-- Background Arc -->
                                 <path d="M 10,10 A 40,40 0 0 0 90,10" 
                                       fill="none" 
                                       stroke="#E2E8F0" 
                                       stroke-width="12" 
                                       stroke-linecap="round" />
                                 <!-- Progress Arc -->
                                  <path d="M 10,10 A 40,40 0 0 0 90,10" 
                                       fill="none" 
                                       stroke="#22A9C8" 
                                       stroke-width="12" 
                                       stroke-linecap="round"
                                       stroke-dasharray="{{ 126 }}" 
                                       stroke-dashoffset="{{ 126 - (126 * $percentage / 100) }}"
                                       class="transition-all duration-1000 ease-out" />
                             </svg>
                             <!-- Number -->
                             <div class="absolute top-6 text-center">
                                 <span class="text-4xl font-black text-[#1a202c] block leading-none">{{ $summary['days_registered'] }}</span>
                             </div>
                        </div>

                        <!-- Footer Text -->
                        <div class="text-center mt-auto mb-2">
                            <p class="text-[#1a202c] text-[10px] font-bold leading-tight max-w-[180px] mx-auto opacity-60">
                                {{ $summary['days_registered'] }} días registrados ({{ round($summary['total_hours']) }}/{{ $summary['target_hours'] }}h)
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
                            alert('Error de conexión al intentar aprobar');
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
                                alert(data.message || 'Error al aprobar la recuperación');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error de conexión');
                        } finally {
                            this.isApproving = false;
                        }
                    },
                    parseComment(comment) {
                        if (!comment) return { activities: [], summary: '' };
                        if (comment.includes('Resumen adicional:')) {
                            const parts = comment.split('Resumen adicional:');
                            return {
                                activities: parts[0].trim().split('\n').filter(a => a.trim() !== ''),
                                summary: parts[1].trim()
                            };
                        }
                        if (comment.includes('\n')) {
                            return {
                                activities: comment.split('\n').filter(a => a.trim() !== ''),
                                summary: ''
                            };
                        }
                        return {
                            activities: [],
                            summary: comment
                        };
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
                                            
                                            <!-- Red Dot Indicator: Only if there are unapproved hours or recoveries -->
                                            @if($day['has_pending'])
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
                            @foreach(['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dayName)
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
                                        <!-- Day Number Badge & Actionable Indicator -->
                                        <div class="flex justify-center items-center gap-2 mb-3">
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
                                                    <template x-if="parseFloat(emp.recovered_hours) > 0 && !emp.recovery_approved">
                                                        <div class="mt-6 pt-6 border-t border-dashed border-gray-100">
                                                            <div class="flex items-center justify-between bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                                                                <div class="flex items-center gap-3 text-blue-800">
                                                                    <div class="p-2 bg-blue-100 rounded-lg">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-xs font-black uppercase tracking-wider">Solicitud de Recuperación</p>
                                                                        <p class="text-sm font-bold" x-text="emp.recovered_hours + ' hs'"></p>
                                                                    </div>
                                                                </div>
                                                                <button @click="approveRecovery(emp)" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-black py-2.5 px-6 rounded-lg shadow-sm transition-all active:scale-95">
                                                                    Aprobar Recup.
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </template>
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
                                            Solicitudes de Recuperación de Horas
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
                                <div class="bg-white px-4 py-4 sm:p-6 max-h-[70vh] overflow-y-auto no-scrollbar">
                                    <template x-if="selectedRecoveries.length > 0">
                                        <div class="space-y-8">
                                            <template x-for="recovery in selectedRecoveries" :key="recovery.record_id">
                                                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all">
                                                    <!-- Employee Header -->
                                                    <div class="flex items-start justify-between mb-6">
                                                        <div class="flex items-center gap-4">
                                                            <div class="w-14 h-14 rounded-2xl overflow-hidden border-2 border-red-50 shadow-sm bg-gray-100 flex-shrink-0">
                                                                <img :src="recovery.avatar ? (recovery.avatar.startsWith('http') ? recovery.avatar : '/avatars/' + recovery.avatar) : `https://ui-avatars.com/api/?name=${encodeURIComponent(recovery.name)}&color=FFFFFF&background=F87171`" 
                                                                     :alt="recovery.name" 
                                                                     class="w-full h-full object-cover">
                                                            </div>
                                                            <div>
                                                                <p class="font-black text-lg text-gray-900 leading-tight" x-text="recovery.name"></p>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-600" x-text="recovery.recovered_hours + ' hs a recuperar'"></span>
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-100 text-orange-600">Pendiente</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Recovery Activities (Parsed from recovery_comment) -->
                                                    <div class="mb-6">
                                                        <div class="flex items-center gap-2 mb-3 text-gray-400">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                            <span class="text-[10px] font-black uppercase tracking-widest leading-none">Actividades Realizadas</span>
                                                        </div>
                                                        
                                                        <div class="bg-gray-50/50 rounded-xl p-4 border border-gray-100 max-h-60 overflow-y-auto">
                                                            <template x-if="recovery.recovery_comment && recovery.recovery_comment.trim() !== ''">
                                                                <div x-data="{ parsed: parseComment(recovery.recovery_comment) }">
                                                                    <template x-if="parsed.activities.length > 0">
                                                                        <ul class="space-y-3 mb-4">
                                                                            <template x-for="activity in parsed.activities">
                                                                                <li class="flex items-start gap-3">
                                                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 mt-1.5 flex-shrink-0"></span>
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
                                                            <template x-if="!recovery.recovery_comment || recovery.recovery_comment.trim() === ''">
                                                                <p class="text-sm text-gray-400 italic">No se registraron detalles.</p>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <!-- Action -->
                                                    <button 
                                                        @click="approveRecovery(recovery)"
                                                        :disabled="isApproving"
                                                        class="w-full bg-green-500 hover:bg-green-600 text-white font-black py-3 px-6 rounded-xl transition-all shadow-md active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2"
                                                    >
                                                        <svg x-show="!isApproving" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                        <svg x-show="isApproving" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        <span x-text="isApproving ? 'Aprobando...' : 'Aprobar Recuperación'"></span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                   
                                    <template x-if="selectedRecoveries.length === 0">
                                        <div class="py-12 flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </div>
                                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs text-center">No hay solicitudes para este día</p>
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


