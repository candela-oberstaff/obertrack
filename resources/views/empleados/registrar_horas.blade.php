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
                        <h1 class="text-2xl font-bold text-gray-900">Notificar tareas</h1>
                        <p class="text-primary font-medium text-xs">Total de tareas notificadas hasta el momento</p>
                    </div>
                    {{-- Mini Stats --}}
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                             <p class="text-xl font-bold text-orange-500 leading-none">{{ $pendingTasksCount }}</p>
                             <p class="text-xs text-gray-400">Pendientes</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Card (Task Centric) --}}
            <div class="bg-gray-50 rounded-2xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between mx-4 sm:mx-0 shadow-sm border border-gray-100">
                <div class="mb-4 md:mb-0">
                   <div class="flex items-center gap-4">
                        <x-user-avatar :user="auth()->user()" size="12" />
                        <div>
                             <h2 class="text-lg font-bold text-gray-900">{{ auth()->user()->name }}</h2>
                             <p class="text-gray-500 text-sm">{{ auth()->user()->job_title ?? 'Profesional' }}</p>
                        </div>
                   </div>
                </div>

                <div class="flex gap-8 items-center mt-4 md:mt-0">
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-primary">{{ $completedTasksCount }}</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Realizadas</span>
                        <span class="block text-[10px] text-gray-400 font-medium">(Este mes)</span>
                    </div>
                    <div class="h-12 w-px bg-gray-200"></div>
                    <div class="text-center">
                        <span class="block text-3xl font-bold text-orange-500">{{ $pendingTasksCount }}</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Por Realizar</span>
                         <span class="block text-[10px] text-gray-400 font-medium">(Total)</span>
                    </div>
                </div>

                <div class="mt-6 md:mt-0">
                    <button @click="openRecoveryModal()" 
                            class="inline-flex items-center gap-2 bg-[#22A9C8] text-white px-6 py-3 rounded-xl font-bold text-sm shadow-md hover:bg-[#1d91ac] transition active:scale-95">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Recuperar horas
                    </button>
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
                
                <div class="bg-white rounded-3xl border border-[#22A9C8] shadow-sm p-6">
                    {{-- Grid Headers --}}
                    <div class="grid grid-cols-7 gap-1 md:gap-4 mb-6">
                        @foreach(['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'] as $dayName)
                            <div class="text-center font-bold text-gray-400 text-[10px] md:hidden uppercase tracking-wider">{{ $dayName }}</div>
                        @endforeach
                        @foreach(['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dayName)
                            <div class="text-center font-bold text-gray-900 text-sm hidden md:block uppercase tracking-wider">{{ $dayName }}</div>
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
                                
                                <div class="relative min-h-[100px] md:min-h-[140px] flex flex-col items-center justify-start py-3 md:py-4 px-1 md:px-2 rounded-xl border border-transparent transition-all duration-200 group
                                    {{ $day['inMonth'] ? ($isWeekend ? 'bg-gray-50/20 cursor-not-allowed opacity-60' : 'bg-gray-50 hover:bg-gray-100 hover:border-[#22A9C8] cursor-pointer') : 'opacity-20' }}
                                    {{ $isToday ? 'bg-[#22A9C8]/5 ring-1 ring-[#22A9C8]/30 shadow-sm' : '' }}"
                                    @if($day['inMonth'] && !$isWeekend && !$isFuture)
                                        @click="openModal('{{ $day['date']->format('Y-m-d') }}', {{ $hasHours ? json_encode($day['workHours']) : 'null' }})"
                                    @endif
                                    >
                                    
                                    @if($day['inMonth'])
                                        <div class="flex justify-center mb-2 md:mb-4">
                                            <span class="{{ $isToday ? 'bg-[#22A9C8] text-white shadow-md' : ($hasHours ? 'bg-[#22A9C8] text-white' : ($isWeekend ? 'bg-gray-200 text-gray-400' : 'bg-gray-300 text-white')) }} rounded-full px-2 md:px-3 py-0.5 md:py-1 text-[10px] md:text-xs font-bold transition-all">
                                                {{ $day['date']->format('d') }}
                                            </span>
                                        </div>
                                    @endif

                                    @if ($day['inMonth'] && !$isWeekend)
                                        <div class="flex-1 w-full flex flex-col items-center justify-center">
                                            @if ($hasHours)
                                                <div class="flex flex-col items-center gap-1 w-full text-center">
                                                    <div class="mb-1 hidden lg:block">
                                                         <x-user-avatar :user="auth()->user()" size="6" />
                                                    </div>
                                                    
                                                    @if($hours < 8)
                                                         @if($hours > 0)
                                                            <div class="flex flex-col items-center">
                                                                <svg class="h-4 w-4 text-orange-500 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <span class="text-[9px] md:text-[10px] font-bold text-orange-600 leading-tight">Parcial</span>
                                                            </div>
                                                         @endif
                                                         <div class="flex flex-col items-center mt-1">
                                                            <span class="text-[8px] md:text-[9px] font-bold text-red-500 leading-tight uppercase tracking-wide">
                                                                Ausencia
                                                            </span>
                                                         </div>
                                                    @else
                                                         <div class="flex flex-col items-center">
                                                            <svg class="h-5 w-5 text-green-500 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span class="text-[9px] md:text-[10px] font-bold text-gray-700 leading-tight">Completado</span>
                                                         </div>
                                                    @endif
                                                </div>
                                            @elseif($isFuture)
                                                 <span class="text-[10px] text-gray-300">-</span>
                                            @else
                                                <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                     <span class="bg-[#22A9C8]/10 text-[#22A9C8] w-5 h-5 md:w-6 md:h-6 rounded-full flex items-center justify-center text-sm md:text-lg font-bold hover:scale-110 transform transition">+</span>
                                                </div>
                                            @endif
                                        </div>
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
                     class="relative inline-block align-bottom bg-white rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    {{-- Modal Header --}}
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between items-center border-b border-gray-100 rounded-t-2xl">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Registrar jornada o ausencia
                        </h3>
                         <button @click="closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        
                        {{-- Section Title --}}
                        <div class="mb-6">
                            <h4 class="text-[#22A9C8] font-bold text-lg mb-1">Registro de jornada</h4>
                            <p class="text-xs text-gray-500 italic">Si estuviste ausente durante parte de la jornada, registra tanto las horas de ausencia como las actividades realizadas durante el tiempo que trabajaste</p>
                        </div>
                        
                        {{-- Work Choice --}}
                        <div class="mb-6">
                            <h5 class="text-gray-800 font-medium mb-3">¿Trabajaste la jornada completa?</h5>
                            <div class="flex gap-6">
                                <div @click="setFullDay('yes')" class="flex items-center gap-2 cursor-pointer select-none">
                                    <div class="w-6 h-6 rounded flex items-center justify-center transition-colors border"
                                         :class="workedFullDay === 'yes' ? 'bg-[#22A9C8] border-[#22A9C8]' : 'bg-gray-100 border-gray-300'">
                                         <svg x-show="workedFullDay === 'yes'" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-gray-600">Si</span>
                                </div>
                                
                                <div @click="setFullDay('no')" class="flex items-center gap-2 cursor-pointer select-none">
                                    <div class="w-6 h-6 rounded flex items-center justify-center transition-colors border"
                                         :class="workedFullDay === 'no' ? 'bg-[#22A9C8] border-[#22A9C8]' : 'bg-gray-100 border-gray-300'">
                                         <svg x-show="workedFullDay === 'no'" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-gray-600">No</span>
                                </div>
                            </div>
                        </div>

                        {{-- Full Day Logic (Activities) --}}
                        <div x-show="workedFullDay" x-transition>
                            <label class="block text-gray-800 font-medium mb-3">Registra las actividades que realizaste en el día</label>
                            
                            {{-- Toggle --}}
                            <div class="flex gap-2 mb-4">
                                <button @click="descriptionMode = 'list'" 
                                        class="px-6 py-1.5 rounded-full text-sm font-medium transition-colors border focus:outline-none"
                                        :class="descriptionMode === 'list' ? 'bg-[#22A9C8] text-white border-[#22A9C8]' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50'">
                                    Lista
                                </button>
                                <button @click="descriptionMode = 'text'" 
                                        class="px-6 py-1.5 rounded-full text-sm font-medium transition-colors border focus:outline-none"
                                        :class="descriptionMode === 'text' ? 'bg-[#22A9C8] text-white border-[#22A9C8]' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50'">
                                    Texto
                                </button>
                            </div>

                            {{-- List Mode --}}
                            <div x-show="descriptionMode === 'list'">
                                <div class="bg-gray-50 rounded-lg p-2 flex items-center mb-4 border border-gray-100 focus-within:ring-1 focus-within:ring-[#22A9C8]">
                                    <input type="text" x-model="newActivity" @keydown.enter.prevent="addActivity()" 
                                           placeholder="Escribe aquí la actividad realizada" 
                                           class="bg-transparent border-none focus:ring-0 w-full text-gray-600 text-sm placeholder-gray-400 italic">
                                    <button @click="addActivity()" class="p-1 text-[#22A9C8] hover:bg-gray-100 rounded-full">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                                    </button>
                                </div>
                                
                                <ul class="space-y-3 pl-1">
                                    <template x-for="(activity, index) in activities" :key="index">
                                        <li class="flex items-start gap-3">
                                            <span class="w-3 h-3 rounded-full bg-[#22A9C8] mt-1.5 flex-shrink-0"></span>
                                            <span class="flex-1 text-sm text-gray-700" x-text="activity"></span>
                                            <button @click="removeActivity(index)" class="text-gray-400 hover:text-red-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            {{-- Text Mode --}}
                            <div x-show="descriptionMode === 'text'">
                                <textarea x-model="userComment" rows="4" 
                                          class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22A9C8] focus:border-[#22A9C8] text-sm text-gray-700"
                                          placeholder="Escribe aquí el resumen de tu jornada..."></textarea>
                            </div>
                        </div>

                        {{-- Absence Logic --}}
                        <div x-show="workedFullDay === 'no'" x-transition>
                             
                             <div class="bg-gray-50 rounded-xl p-6 mb-6 flex items-center justify-between">
                                 <div class="text-gray-500 italic text-sm w-1/2">
                                     Ingresa el tiempo exacto ausente en el día
                                 </div>
                                 <div class="flex items-center gap-4">
                                     <span class="text-5xl font-bold text-black tracking-widest" x-text="formatAbsenceTime()"></span>
                                     <div class="flex flex-col gap-1">
                                         <button @click="incrementAbsence()" class="w-8 h-8 rounded bg-[#d0eef5] text-[#22A9C8] flex items-center justify-center hover:bg-[#b0e0eb] active:scale-95 transition focus:outline-none">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                         </button>
                                         <button @click="decrementAbsence()" class="w-8 h-8 rounded bg-[#d0eef5] text-[#22A9C8] flex items-center justify-center hover:bg-[#b0e0eb] active:scale-95 transition focus:outline-none">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                         </button>
                                     </div>
                                 </div>
                             </div>

                             <div class="mb-4">
                                <label class="block text-gray-800 font-medium mb-2">Motivo de la ausencia</label>
                                <div class="relative">
                                    <button type="button" 
                                            @click="isDropdownOpen = !isDropdownOpen"
                                            class="w-full bg-[#0F172A] text-white rounded-full px-5 py-3 text-left flex justify-between items-center text-sm shadow-md hover:bg-[#1e293b] transition-colors focus:outline-none">
                                        <span x-text="absenceReason || 'Selecciona un motivo'"></span>
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                     <div x-show="isDropdownOpen" 
                                          @click.away="isDropdownOpen = false"
                                          class="absolute z-10 bottom-full mb-1 w-full bg-white shadow-xl rounded-xl py-2 max-h-48 overflow-auto border border-gray-100">
                                          <template x-for="option in absenceOptions" :key="option">
                                              <div @click="absenceReason = option; isDropdownOpen = false; if(option === 'Otro') $nextTick(() => $refs.otherReasonInput.focus())"
                                                   class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-700 hover:text-[#22A9C8] flex justify-between items-center transition-colors">
                                                   <span x-text="option"></span>
                                                   <svg x-show="absenceReason === option" class="w-4 h-4 text-[#22A9C8]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                              </div>
                                          </template>
                                     </div>
                                </div>
                             </div>
                             
                             <div x-show="absenceReason === 'Otro'" class="mt-3">
                                   <textarea x-ref="otherReasonInput"
                                             x-model="otherReasonText"
                                             rows="2"
                                             class="shadow-sm focus:ring-[#22A9C8] focus:border-[#22A9C8] block w-full sm:text-sm border-gray-300 rounded-xl" 
                                             placeholder="Especificar motivo..."></textarea>
                             </div>
                        </div>

                    </div>
                    
                    {{-- Footer Actions --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 rounded-b-2xl">
                        <button type="button" 
                                @click="saveHours()"
                                :disabled="isSaving"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-[#22A9C8] text-base font-medium text-white hover:bg-[#1d91ac] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#22A9C8] sm:ml-3 sm:w-auto sm:text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                             <span x-show="!isSaving">Notificar</span>
                             <span x-show="isSaving" class="flex items-center">
                                 <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                 Guardando...
                             </span>
                        </button>
                        <button type="button" 
                                @click="closeModal()" 
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#22A9C8] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recovery Modal --}}
        <div x-show="isRecoveryModalOpen" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
             
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div x-show="isRecoveryModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="closeRecoveryModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="isRecoveryModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    {{-- Modal Header --}}
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between items-center border-b border-gray-100 rounded-t-2xl">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Recuperar horas
                        </h3>
                         <button @click="closeRecoveryModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        
                        {{-- Section Title --}}
                        <div class="mb-6">
                            <h4 class="text-[#22A9C8] font-bold text-lg mb-1">Registro de recuperación</h4>
                            <p class="text-xs text-gray-500 italic">Registra las horas recuperadas y las actividades realizadas. Recuerda que esto debe estar autorizado por el cliente.</p>
                        </div>

                        {{-- Date Selection --}}
                        <div class="mb-6">
                            <label class="block text-gray-800 font-medium mb-2">Fecha de recuperación</label>
                            <input type="date" x-model="recoveryDate" 
                                   class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22A9C8] focus:border-[#22A9C8] text-sm text-gray-700">
                        </div>

                        {{-- Recovery Hours Redesign --}}
                        <div class="bg-gray-50 rounded-2xl p-6 mb-6 border border-gray-100 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Horas a recuperar</p>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[#22A9C8]"></div>
                                        <p class="text-[11px] text-gray-500 font-medium">
                                            Disponibles: <span class="text-[#22A9C8] font-bold" x-text="missingHours.toFixed(1)"></span>h
                                        </p>
                                    </div>
                                </div>
                                <div class="bg-white px-4 py-2 rounded-xl border border-gray-100 shadow-sm">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-3xl font-black text-gray-900" x-text="recoveryHours.toFixed(1)"></span>
                                        <span class="text-xs font-bold text-gray-400 uppercase">hrs</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <button @click="if(recoveryHours > 0.5) recoveryHours -= 0.5" 
                                        :class="recoveryHours <= 0.5 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-100 active:scale-95 text-gray-600 border-gray-200'"
                                        class="flex-1 h-12 rounded-xl bg-white border flex items-center justify-center transition-all focus:outline-none shadow-sm group">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                                </button>
                                <button @click="if(recoveryHours < missingHours && recoveryHours < 8) recoveryHours += 0.5" 
                                        :class="recoveryHours >= missingHours || recoveryHours >= 8 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-[#22A9C8] hover:text-white active:scale-95 text-[#22A9C8] border-[#22A9C8]/20 bg-[#22A9C8]/5'"
                                        class="flex-1 h-12 rounded-xl border flex items-center justify-center transition-all focus:outline-none shadow-sm group">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div x-show="missingHours <= 0" 
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mb-6 p-4 bg-amber-50 border border-amber-100 rounded-2xl flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-amber-900 font-bold">Sin horas pendientes</p>
                                <p class="text-[10px] text-amber-700 mt-0.5">No tienes horas registradas como ausencia por recuperar en este periodo.</p>
                            </div>
                        </div>

                        {{-- Activities --}}
                        <label class="block text-gray-800 font-medium mb-3">Actividades realizadas</label>
                        
                        {{-- Toggle --}}
                        <div class="flex gap-2 mb-4">
                            <button @click="recoveryDescriptionMode = 'list'" 
                                    class="px-6 py-1.5 rounded-full text-sm font-medium transition-colors border focus:outline-none"
                                    :class="recoveryDescriptionMode === 'list' ? 'bg-[#22A9C8] text-white border-[#22A9C8]' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50'">
                                Lista
                            </button>
                            <button @click="recoveryDescriptionMode = 'text'" 
                                    class="px-6 py-1.5 rounded-full text-sm font-medium transition-colors border focus:outline-none"
                                    :class="recoveryDescriptionMode === 'text' ? 'bg-[#22A9C8] text-white border-[#22A9C8]' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50'">
                                Texto
                            </button>
                        </div>

                        {{-- List Mode --}}
                        <div x-show="recoveryDescriptionMode === 'list'">
                            <div class="bg-gray-50 rounded-lg p-2 flex items-center mb-4 border border-gray-100 focus-within:ring-1 focus-within:ring-[#22A9C8]">
                                <input type="text" x-model="newRecoveryActivity" @keydown.enter.prevent="addRecoveryActivity()" 
                                       placeholder="Escribe aquí la actividad" 
                                       class="bg-transparent border-none focus:ring-0 w-full text-gray-600 text-sm placeholder-gray-400 italic">
                                <button @click="addRecoveryActivity()" class="p-1 text-[#22A9C8] hover:bg-gray-100 rounded-full">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                                </button>
                            </div>
                            
                            <ul class="space-y-3 pl-1 max-h-32 overflow-y-auto">
                                <template x-for="(activity, index) in recoveryActivities" :key="index">
                                    <li class="flex items-start gap-3">
                                        <span class="w-3 h-3 rounded-full bg-[#22A9C8] mt-1.5 flex-shrink-0"></span>
                                        <span class="flex-1 text-sm text-gray-700" x-text="activity"></span>
                                        <button @click="removeRecoveryActivity(index)" class="text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        {{-- Text Mode --}}
                        <div x-show="recoveryDescriptionMode === 'text'">
                            <textarea x-model="recoveryUserComment" rows="3" 
                                      class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-[#22A9C8] focus:border-[#22A9C8] text-sm text-gray-700"
                                      placeholder="Escribe aquí el resumen..."></textarea>
                        </div>

                        {{-- Authorization message removed, professional now sends request --}}
                        <div class="mt-6">
                            <p class="text-xs text-gray-500 italic">Al enviar esta solicitud, la empresa recibirá una notificación para su revisión y aprobación.</p>
                        </div>

                    </div>
                    
                    {{-- Footer Actions --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 rounded-b-2xl">
                        <button type="button" 
                                @click="saveRecoveryHours()"
                                :disabled="isSaving || missingHours <= 0 || recoveryHours <= 0"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-[#22A9C8] text-base font-medium text-white hover:bg-[#1d91ac] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#22A9C8] sm:ml-3 sm:w-auto sm:text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                             <span x-show="!isSaving">Enviar solicitud</span>
                             <span x-show="isSaving" class="flex items-center">
                                 <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                 Guardando...
                             </span>
                        </button>
                        <button type="button" 
                                @click="closeRecoveryModal()" 
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#22A9C8] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
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
                 workedFullDay: '', 
                 hours: 8,
                 absenceHours: 0,
                 absenceReason: null,
                 otherReasonText: '',
                 userComment: '',
                 
                 // New fields
                 descriptionMode: 'list',
                 newActivity: '',
                 activities: [],
                 isSaving: false,

                 // Recovery fields
                 isRecoveryModalOpen: false,
                 recoveryDate: new Date().toISOString().split('T')[0],
                 recoveryHours: 1,
                 recoveryActivities: [],
                 newRecoveryActivity: '',
                 recoveryAuthorized: false,
                 recoveryDescriptionMode: 'list',
                 recoveryUserComment: '',
                 missingHours: {{ $missingHours }},
                 
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
                         const worked = parseFloat(record.hours_worked);
                         if (worked >= 8) {
                             this.workedFullDay = 'yes';
                             this.hours = 8;
                             this.absenceHours = 0;
                         } else {
                             this.workedFullDay = 'no';
                             this.hours = worked;
                             this.absenceHours = 8 - worked;
                         }
                         
                         this.absenceReason = record.absence_reason || null;
                         if (this.absenceReason && !this.absenceOptions.includes(this.absenceReason) && this.absenceReason !== 'Otro') {
                             this.otherReasonText = this.absenceReason;
                             this.absenceReason = 'Otro';
                         }
                     } else {
                          this.workedFullDay = 'yes';
                          this.hours = 8;
                          this.absenceHours = 0;
                          this.absenceReason = null;
                          this.otherReasonText = '';
                          this.userComment = '';
                      }
                      
                      // Initialize comments/activities
                      this.userComment = record ? (record.user_comment || '') : '';
                      this.activities = [];
                      if (this.userComment) {
                          const lines = this.userComment.split('\n').filter(line => line.trim() !== '');
                          // Simple heuristic: if multiline, treat as list. 
                          this.activities = lines;
                      }
                      this.descriptionMode = 'list';
                      
                      this.isModalOpen = true;
                  },

                 closeModal() {
                     this.isModalOpen = false;
                     this.isDropdownOpen = false;
                 },

                 setFullDay(value) {
                     this.workedFullDay = value;
                     if (value === 'yes') {
                         this.hours = 8;
                         this.absenceHours = 0;
                         this.absenceReason = null;
                     } else {
                         if (this.absenceHours === 0) {
                             this.absenceHours = 1;
                             this.hours = 7;
                         }
                     }
                 },

                 incrementAbsence() {
                     if (this.absenceHours < 8) {
                         this.absenceHours += 0.5;
                         this.hours = 8 - this.absenceHours;
                     }
                 },

                 decrementAbsence() {
                     if (this.absenceHours > 0) {
                         this.absenceHours -= 0.5;
                         if (this.absenceHours < 0) this.absenceHours = 0;
                         this.hours = 8 - this.absenceHours;
                     }
                 },
                 
                 formatAbsenceTime() {
                     const hrs = Math.floor(this.absenceHours);
                     const mins = Math.round((this.absenceHours - hrs) * 60);
                     return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
                 },

                 addActivity() {
                     if (this.newActivity.trim() !== '') {
                         this.activities.push(this.newActivity.trim());
                         this.newActivity = '';
                     }
                 },

                 removeActivity(index) {
                     this.activities.splice(index, 1);
                 },
                 
                 formatDate(dateStr) {
                    if(!dateStr) return '';
                    const date = new Date(dateStr + 'T00:00:00');
                    return date.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                 },

                 saveHours() {
                     if (!this.workedFullDay) {
                         alert('Por favor selecciona si trabajaste jornada completa.');
                         return;
                     }

                     if (this.workedFullDay === 'no' && !this.absenceReason) {
                         alert('Por favor selecciona un motivo de ausencia.');
                         return;
                     }

                     this.isSaving = true;

                     // Safety check: ensure hours correspond to absence
                     if (this.workedFullDay === 'no') {
                         this.hours = 8 - this.absenceHours;
                         if (this.hours < 0) this.hours = 0;
                     }

                     let finalReason = this.absenceReason;
                     if (this.absenceReason === 'Otro') {
                         finalReason = this.otherReasonText;
                     }
                     if (this.hours >= 8) {
                        finalReason = null;
                     }
                     
                     let finalComment = '';
                     if (this.workedFullDay) { 
                         if (this.descriptionMode === 'list') {
                             finalComment = this.activities.join('\n');
                         } else {
                             finalComment = this.userComment;
                         }
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
                              absence_hours: this.absenceHours,
                              user_comment: finalComment
                          })
                     })
                     .then(response => {
                         if (!response.ok) {
                             return response.json().then(err => {
                                 throw new Error(err.message || 'Error al guardar');
                             });
                         }
                         return response.json();
                     })
                     .then(data => {
                         this.isSaving = false;
                         if (data.success) {
                             window.location.reload(); 
                         } else {
                             alert(data.message || 'Error al guardar.');
                         }
                     })
                     .catch(error => {
                         this.isSaving = false;
                         console.error('Error:', error);
                         alert(error.message || 'Error de conexión.');
                     });
                 },

                 addRecoveryActivity() {
                     if (this.newRecoveryActivity.trim() !== '') {
                         this.recoveryActivities.push(this.newRecoveryActivity.trim());
                         this.newRecoveryActivity = '';
                     }
                 },

                 removeRecoveryActivity(index) {
                     this.recoveryActivities.splice(index, 1);
                 },

                 openRecoveryModal() {
                     this.isRecoveryModalOpen = true;
                     this.recoveryDate = new Date().toISOString().split('T')[0];
                     this.recoveryHours = this.missingHours > 0 ? (this.missingHours >= 1 ? 1 : this.missingHours) : 0;
                     this.recoveryActivities = [];
                     this.recoveryAuthorized = false;
                     this.recoveryUserComment = '';
                 },

                 closeRecoveryModal() {
                     this.isRecoveryModalOpen = false;
                 },

                 saveRecoveryHours() {
                     if (this.recoveryHours <= 0) {
                         alert('Las horas a recuperar deben ser mayores a 0.');
                         return;
                     }

                     this.isSaving = true;

                     let finalComment = '';
                     if (this.recoveryDescriptionMode === 'list') {
                         finalComment = this.recoveryActivities.join('\n');
                     } else {
                         finalComment = this.recoveryUserComment;
                     }

                     fetch('{{ route('work-hours.store') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'Accept': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                         },
                         body: JSON.stringify({
                              work_date: this.recoveryDate,
                              hours_worked: 0,
                              recovered_hours: this.recoveryHours,
                              recovery_comment: finalComment,
                              user_comment: '[RECUPERACIÓN] ' + finalComment
                          })
                     })
                     .then(response => response.json())
                     .then(data => {
                         this.isSaving = false;
                         if (data.success) {
                             window.location.reload(); 
                         } else {
                             alert(data.message || 'Error al guardar.');
                         }
                     })
                     .catch(error => {
                         this.isSaving = false;
                         alert('Error de conexión.');
                     });
                 }
             }));
        });
    </script>
    @endpush
</x-app-layout>