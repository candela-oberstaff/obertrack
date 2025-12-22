<x-app-layout>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="py-8 bg-white min-h-screen" x-data="hoursRegistration()">
        @php
            $currentDate = now();
            // Check if all hours in the current month are approved
            $allApproved = true;
            $monthHours = 0;
            foreach ($calendar as $week) {
                foreach ($week as $day) {
                    if (isset($day['workHours'])) {
                        $monthHours += $day['workHours']->hours_worked;
                        if (!$day['workHours']->approved) {
                            $allApproved = false;
                        }
                    }
                }
            }
            $targetHours = 160; 
        @endphp

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Sticky Header --}}
            <div class="sticky top-0 z-30 bg-white/95 backdrop-blur shadow-sm border-b border-gray-100 py-4 mb-8 -mx-4 px-8 sm:mx-0 sm:px-0 sm:rounded-b-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Registro de horas</h1>
                        <p class="text-primary font-medium text-xs">Total de horas trabajadas hasta el momento</p>
                    </div>
                    
                    {{-- Mini Stats --}}
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900 leading-none">{{ (int)$totalHours }}</p>
                            <p class="text-xs text-gray-400">de 160 hs</p>
                        </div>
                        {{-- Circular Progress Mini --}}
                         <div class="relative w-10 h-10">
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="50%" cy="50%" r="16" stroke="#E5E7EB" stroke-width="4" fill="transparent" />
                                <circle cx="50%" cy="50%" r="16" class="text-primary transition-all duration-1000 ease-out" stroke="currentColor" stroke-width="4" fill="transparent" 
                                        :stroke-dasharray="2 * Math.PI * 16" 
                                        :stroke-dashoffset="2 * Math.PI * 16 - (Math.min(({{ $totalHours }} / 160) * 100, 100) / 100) * (2 * Math.PI * 16)" 
                                        stroke-linecap="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Card (Visual Only - keeping layout) --}}
            <div class="bg-gray-50 rounded-2xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between mx-4 sm:mx-0">
                <div class="mb-4 md:mb-0">
                   <div class="flex items-center gap-4">
                        <x-user-avatar :user="auth()->user()" size="12" />
                        <div>
                             <h2 class="text-lg font-bold text-gray-900">{{ auth()->user()->name }}</h2>
                             <p class="text-gray-500 text-sm">{{ auth()->user()->job_title ?? 'Profesional' }}</p>
                        </div>
                   </div>
                </div>

                {{-- Progress Circle Big --}}
                <div class="relative w-32 h-32 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="50%" cy="50%" r="56" stroke="#E5E7EB" stroke-width="8" fill="transparent" />
                        <circle cx="50%" cy="50%" r="56" class="text-primary transition-all duration-1000 ease-out" stroke="currentColor" stroke-width="8" fill="transparent" 
                                :stroke-dasharray="2 * Math.PI * 56" 
                                :stroke-dashoffset="2 * Math.PI * 56 - (Math.min(({{ $totalHours }} / 160) * 100, 100) / 100) * (2 * Math.PI * 56)" 
                                stroke-linecap="round" />
                    </svg>
                    <span class="absolute text-3xl font-bold text-gray-900">{{ (int)$totalHours }}</span>
                </div>

                <div class="text-right mt-4 md:mt-0">
                    <p class="text-gray-400 text-sm">{{ (int)$totalHours }} de 160 horas registradas</p>
                    <p class="text-gray-400 text-sm text-right">actualmente ({{ $currentMonth->format('M 1') }} - {{ $currentMonth->copy()->endOfMonth()->format('M d') }})</p>
                </div>
            </div>

            {{-- Calendar Section --}}
            <div class="mx-4 sm:mx-0">
                <div class="flex justify-between items-end mb-4">
                     <h3 class="text-lg font-bold text-gray-800">Vistazo diario</h3>
                     <div class="flex items-center bg-gray-100 rounded-lg p-1">
                        <a href="{{ route('empleado.registrar-horas', ['month' => $currentMonth->copy()->subMonth()->format('Y-m-d')]) }}" class="p-1 hover:bg-white rounded text-gray-500 hover:text-primary transition shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <span class="px-3 text-sm font-semibold text-gray-700">{{ $currentMonth->format('F Y') }}</span>
                        <a href="{{ route('empleado.registrar-horas', ['month' => $currentMonth->copy()->addMonth()->format('Y-m-d')]) }}" class="p-1 hover:bg-white rounded text-gray-500 hover:text-primary transition shadow-sm">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                     </div>
                </div>
                
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    {{-- Grid Headers --}}
                    <div class="grid grid-cols-7 gap-4 mb-4 text-center">
                         @foreach(['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dayName)
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $dayName }}</div>
                        @endforeach
                    </div>

                    {{-- Calendar Grid --}}
                    <div class="grid grid-cols-7 gap-4">
                        @foreach ($calendar as $week)
                            @foreach ($week as $day)
                                @php
                                    $isToday = $day['date']->isToday();
                                    $isFuture = $day['date']->isFuture();
                                    $isWeekend = $day['date']->isWeekend();
                                    $hasHours = isset($day['workHours']);
                                    $hours = $hasHours ? $day['workHours']->hours_worked : 0;
                                    $isAbsence = $hasHours && $hours == 0;
                                    $statusColor = $hasHours ? ($day['workHours']->approved ? 'text-green-500' : 'text-orange-400') : 'text-gray-300';
                                    $statusText = $hasHours ? ($day['workHours']->approved ? '(Aprobado)' : '(Pendiente)') : '';
                                @endphp
                                
                                <div class="relative min-h-[140px] flex flex-col items-center justify-start py-4 rounded-xl transition-all duration-200 group
                                    {{ $day['inMonth'] ? 'hover:bg-gray-50' : 'opacity-30' }}
                                    {{ $isToday ? 'bg-blue-50/50 ring-1 ring-primary/20' : '' }}"
                                    @if($day['inMonth'] && !$isWeekend && !$isFuture)
                                        @click="openModal('{{ $day['date']->format('Y-m-d') }}', {{ $hasHours ? json_encode($day['workHours']) : 'null' }})"
                                    @endif
                                    >
                                    
                                    @if($day['inMonth'])
                                        <div class="mb-3 {{ $isToday ? 'bg-primary text-white shadow-md' : ($hasHours ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-400') }} rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold transition-colors">
                                            {{ $day['date']->format('d') }}
                                        </div>
                                    @endif

                                    @if ($day['inMonth'] && !$isWeekend)
                                        @if ($hasHours)
                                            <div class="flex flex-col items-center gap-1 w-full px-2" title="{{ $statusText }}">
                                                <div class="flex items-center gap-2 mb-1">
                                                     <x-user-avatar :user="auth()->user()" size="5" />
                                                </div>
                                                <span class="text-xs font-bold text-gray-600">{{ $isAbsence ? '0h' : $hours . 'h' }}</span>
                                                @if($isAbsence)
                                                    <span class="text-[10px] text-red-500 font-medium">Ausente</span>
                                                @endif
                                            </div>
                                        @elseif($isFuture)
                                             <span class="text-xs text-gray-300">-</span>
                                        @else
                                            <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                 <span class="text-primary text-2xl font-light hover:scale-110 transform transition">+</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Registration Modal --}}
        <div x-show="isModalOpen" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
             
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div x-show="isModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="closeModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="isModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    {{-- Modal Header --}}
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between items-center border-b border-gray-100">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Registrar horas o ausencia
                        </h3>
                         <button @click="closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                         
                        <div class="mb-6">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl mb-4">
                                <x-user-avatar :user="auth()->user()" size="10" />
                                <div>
                                    <p class="font-bold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500" x-text="formatDate(selectedDate)"></p>
                                </div>
                                <div class="ml-auto">
                                     <span class="text-xs font-semibold px-2 py-1 rounded bg-white text-gray-600 border border-gray-200 shadow-sm" x-show="existingRecord?.approved">(Aprobado)</span>
                                     <span class="text-xs font-semibold px-2 py-1 rounded bg-white text-orange-500 border border-orange-100 shadow-sm" x-show="existingRecord && !existingRecord.approved">(Pendiente)</span>
                                </div>
                            </div>
                        </div>

                        {{-- Presets --}}
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Predeterminado</h4>
                            <div class="grid grid-cols-3 gap-3">
                                <button @click="setHours(8)" 
                                        :class="hours === 8 ? 'bg-primary text-white ring-2 ring-primary ring-offset-2' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                                        class="py-2.5 px-4 rounded-xl font-semibold text-sm transition-all text-center">
                                    8 horas
                                </button>
                                <button @click="setHours(6)" 
                                        :class="hours === 6 ? 'bg-primary text-white ring-2 ring-primary ring-offset-2' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                                        class="py-2.5 px-4 rounded-xl font-semibold text-sm transition-all text-center">
                                    6 horas
                                </button>
                                <button @click="setHours(4)" 
                                        :class="hours === 4 ? 'bg-primary text-white ring-2 ring-primary ring-offset-2' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
                                        class="py-2.5 px-4 rounded-xl font-semibold text-sm transition-all text-center">
                                    4 horas
                                </button>
                            </div>
                        </div>

                        {{-- Manual Input --}}
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Manual</h4>
                            <div class="flex items-center justify-between bg-gray-50 rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-500 italic max-w-[150px]">
                                    Ingresa el tiempo exacto trabajado en el día
                                </p>
                                <div class="flex items-center gap-4">
                                     <span class="text-5xl font-bold text-gray-900 tabular-nums tracking-tight" x-text="formatTime(hours)"></span>
                                     <div class="flex flex-col gap-1">
                                         <button @click="incrementHours()" class="p-1 bg-white hover:bg-gray-100 rounded shadow-sm border border-gray-200 text-primary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                         </button>
                                         <button @click="decrementHours()" class="p-1 bg-white hover:bg-gray-100 rounded shadow-sm border border-gray-200 text-primary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                         </button>
                                     </div>
                                </div>
                            </div>
                        </div>

                         {{-- Absence Reason (Conditional) --}}
                        <div x-show="hours < 8" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo de ausencia / horas faltantes</label>
                            
                            {{-- Dropdown Trigger --}}
                            <div class="relative">
                                <button type="button" 
                                        @click="isDropdownOpen = !isDropdownOpen"
                                        class="relative w-full bg-white border border-gray-300 rounded-xl pl-4 pr-10 py-3 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm shadow-sm">
                                    <span class="block truncate" :class="!absenceReason ? 'text-gray-400' : 'text-gray-900'" x-text="absenceReason || 'Seleccionar motivo...'"></span>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                            <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </button>

                                {{-- Dropdown Menu --}}
                                <div x-show="isDropdownOpen" 
                                     @click.away="isDropdownOpen = false"
                                     class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-xl py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm custom-scrollbar">
                                    
                                     <template x-for="option in absenceOptions" :key="option">
                                         <div @click="absenceReason = option; isDropdownOpen = false; if(option === 'Otro') $nextTick(() => $refs.otherReasonInput.focus())"
                                              class="cursor-pointer hover:bg-blue-50 py-2.5 px-4 text-gray-900 flex items-center justify-between group transition-colors">
                                             <span :class="option === absenceReason ? 'font-semibold text-primary' : 'font-normal group-hover:text-primary'" x-text="option"></span>
                                             <svg x-show="option === absenceReason" class="h-5 w-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                                 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                             </svg>
                                         </div>
                                     </template>
                                </div>
                            </div>
                            
                             {{-- Other Reason Input --}}
                             <div x-show="absenceReason === 'Otro'" class="mt-3">
                                  <textarea x-ref="otherReasonInput"
                                            x-model="otherReasonText"
                                            rows="2"
                                            class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-xl" 
                                            placeholder="Especificar motivo..."></textarea>
                             </div>
                        </div>

                         <!-- Comment Info Message -->
                        <div class="rounded-md bg-blue-50 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1 md:flex md:justify-between">
                                    <p class="text-sm text-blue-700">
                                        Si registras menos de 8 horas, se marcará automáticamente un tiempo de ausencia por la diferencia.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    {{-- Footer Actions --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="button" 
                                @click="saveHours()"
                                :disabled="isSaving"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                             <span x-show="!isSaving">Registrar</span>
                             <span x-show="isSaving" class="flex items-center">
                                 <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                 Guardando...
                             </span>
                        </button>
                        <button type="button" 
                                @click="closeModal()" 
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
             Alpine.data('hoursRegistration', () => ({
                 isModalOpen: false,
                 isDropdownOpen: false,
                 selectedDate: null,
                 existingRecord: null,
                 hours: 8,
                 absenceReason: null,
                 otherReasonText: '',
                 isSaving: false,
                 
                 absenceOptions: [
                     'Cita médica',
                     'Problemas con electricidad',
                     'Problemas con internet',
                     'Trámites administrativos',
                     'Duelo',
                     'Fallos en equipo',
                     'Trámites académicos',
                     'Otro'
                 ],

                 openModal(date, record) {
                     this.selectedDate = date;
                     this.existingRecord = record;
                     
                     if (record) {
                         this.hours = parseFloat(record.hours_worked);
                         this.absenceReason = record.absence_reason || null;
                         // Check if absence reason is custom (not in options)
                         if (this.absenceReason && !this.absenceOptions.includes(this.absenceReason) && this.absenceReason !== 'Otro') {
                             this.otherReasonText = this.absenceReason;
                             this.absenceReason = 'Otro';
                         }
                     } else {
                         this.hours = 8;
                         this.absenceReason = null;
                         this.otherReasonText = '';
                     }
                     
                     this.isModalOpen = true;
                 },

                 closeModal() {
                     this.isModalOpen = false;
                     this.isDropdownOpen = false;
                 },

                 setHours(value) {
                     this.hours = value;
                 },

                 incrementHours() {
                     if (this.hours < 24) this.hours += 0.5;
                 },

                 decrementHours() {
                     if (this.hours > 0) this.hours -= 0.5;
                 },

                 formatTime(hours) {
                     const h = Math.floor(hours);
                     const m = (hours - h) * 60;
                     return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
                 },
                 
                 formatDate(dateStr) {
                    if(!dateStr) return '';
                    const date = new Date(dateStr + 'T00:00:00'); // Valid ISO parsing
                    return date.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                 },

                 saveHours() {
                     if (this.hours < 8 && !this.absenceReason) {
                         alert('Por favor selecciona un motivo de ausencia.');
                         return;
                     }

                     this.isSaving = true;

                     let finalReason = this.absenceReason;
                     if (this.absenceReason === 'Otro') {
                         finalReason = this.otherReasonText;
                     }
                     
                     // If hours are 8+, absence reason should be cleared (optional logic)
                     if (this.hours >= 8) {
                        finalReason = null;
                     }

                     fetch('{{ route('work-hours.store') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'Accept': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                         },
                         body: JSON.stringify({
                             work_date: this.selectedDate,
                             hours_worked: this.hours,
                             absence_reason: finalReason,
                             user_comment: '' // user_comment can be mapped if needed, using absence reason instead now mostly
                         })
                     })
                     .then(response => response.json())
                     .then(data => {
                         this.isSaving = false;
                         if (data.success) {
                             // Reload to prevent desync (simplest approach for now, or update UI reactively)
                             window.location.reload(); 
                         } else {
                             alert(data.message || 'Error al guardar.');
                         }
                     })
                     .catch(error => {
                         this.isSaving = false;
                         console.error('Error:', error);
                         alert('Error de conexión.');
                     });
                 }
             }));
        });
    </script>
    @endpush
</x-app-layout>