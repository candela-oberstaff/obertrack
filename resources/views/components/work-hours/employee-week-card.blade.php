@props(['summary', 'employeeId', 'weekStart', 'isPending' => false])

@php
    $employee = \App\Models\User::find($employeeId);
@endphp

<div class="mb-8 last:mb-0 pb-8 last:pb-0" x-data="{ 
    selectedDates: [],
    loading: false,
    allPendingDates: {{ json_encode(collect($summary['days'])->where('hours', '>', 0)->where('approved', false)->pluck('date')->values()) }},
    
    toggleDate(date) {
        if (this.selectedDates.includes(date)) {
            this.selectedDates = this.selectedDates.filter(d => d !== date);
        } else {
            this.selectedDates.push(date);
        }
    },

    toggleAll() {
        if (this.selectedDates.length === this.allPendingDates.length) {
            this.selectedDates = [];
        } else {
            this.selectedDates = [...this.allPendingDates];
        }
    },

    async approveSelected(withComment = false) {
        if (this.selectedDates.length === 0) return;
        
        if (withComment) {
            showCommentModal({{ $employeeId }}, this.selectedDates);
            return;
        }

        this.loading = true;
        try {
            const response = await fetch('{{ route('work-hours.approve-days') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    employee_id: {{ $employeeId }},
                    dates: this.selectedDates
                })
            });

            if (response.ok) {
                // Real-time update: Reload or update state
                // For now, reload is safest but we could also update the local state
                location.reload();
            } else {
                alert('Error al aprobar las horas');
                this.loading = false;
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión');
            this.loading = false;
        }
    }
}">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
        <div class="flex items-center gap-3">
            @if($employee)
                <x-user-avatar :user="$employee" size="10" />
            @else
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                    <i class="fas fa-user text-lg"></i>
                </div>
            @endif
            <div>
                <h4 class="text-lg font-bold text-[#0D1E4C]">
                    {{ $summary['name'] }}
                </h4>
                <p class="text-xs text-gray-500 font-medium">{{ $employee->job_title ?? 'Profesional' }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <template x-if="allPendingDates.length > 0">
                <button @click="toggleAll()" class="text-[10px] font-bold text-[#22A9C8] hover:underline mr-2 uppercase tracking-widest">
                    <span x-text="selectedDates.length === allPendingDates.length ? 'Desmarcar todos' : 'Marcar todos'"></span>
                </button>
            </template>
            <span class="px-3 py-1 bg-[#22A9C8]/10 text-[#22A9C8] rounded-full text-xs font-bold whitespace-nowrap">
                {{ $summary['total_hours'] }}/40h semanales
            </span>
            @if($summary['pending_hours'] > 0)
                <span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-xs font-bold whitespace-nowrap">
                    {{ $summary['pending_hours'] }}h pendientes
                </span>
            @else
                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold whitespace-nowrap text-center">
                    Aprobadas
                </span>
            @endif
        </div>
    </div>
    
    <!-- Days Breakdown -->
    <div class="bg-white border rounded-2xl p-6 mb-6 transition-all" 
         :class="selectedDates.length > 0 ? 'border-[#22A9C8] shadow-sm' : 'border-[#22A9C8]/30'">
        <div class="grid grid-cols-5 gap-4">
            @php
                $dayNames = ['Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mié', 'Thu' => 'Jue', 'Fri' => 'Vie'];
            @endphp
            @foreach($summary['days'] as $day)
                @php 
                    $dateStr = $day['date'];
                    $date = Carbon\Carbon::parse($dateStr);
                    $dayName = $dayNames[$date->format('D')] ?? $date->format('D');
                    $isSelectable = !$day['approved'] && $day['hours'] > 0;
                @endphp
                <div class="flex flex-col items-center {{ $isSelectable ? 'cursor-pointer group' : '' }}" 
                     @if($isSelectable) @click="toggleDate('{{ $dateStr }}')" @endif>
                    <span class="text-[10px] uppercase tracking-wider font-bold mb-2 transition-colors"
                          :class="selectedDates.includes('{{ $dateStr }}') ? 'text-[#22A9C8]' : 'text-gray-400'">
                        {{ $dayName }}
                    </span>
                    <div class="w-full flex flex-col items-center gap-1 relative">
                        <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden relative">
                            <div class="h-full transition-all duration-300 {{ $day['approved'] ? 'bg-green-500' : ($day['hours'] > 0 ? 'bg-yellow-400' : 'bg-gray-200') }}" 
                                 :class="selectedDates.includes('{{ $dateStr }}') ? '!bg-[#22A9C8]' : ''"
                                 style="width: {{ $day['hours'] > 0 ? '100%' : '0%' }}"></div>
                        </div>
                        
                        @if($isSelectable)
                            <div class="absolute -top-1 -right-1" x-show="selectedDates.includes('{{ $dateStr }}')" x-transition>
                                <div class="bg-[#22A9C8] text-white rounded-full p-0.5 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-2 w-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        @endif

                        <span class="text-xs font-extrabold transition-colors"
                              :class="selectedDates.includes('{{ $dateStr }}') ? 'text-[#22A9C8]' : '{{ $day['hours'] > 0 ? 'text-[#0D1E4C]' : 'text-gray-300' }}'">
                            {{ $day['hours'] }}h
                        </span>
                        <span class="text-[9px] font-bold transition-colors"
                              :class="selectedDates.includes('{{ $dateStr }}') ? 'text-[#22A9C8]' : '{{ $day['approved'] ? 'text-green-500' : ($day['hours'] > 0 ? 'text-yellow-600' : 'text-gray-400') }}'">
                            {{ $day['approved'] ? 'APROBADO' : ($day['hours'] > 0 ? (isset($day['is_selected']) || false ? 'SELECCIONADO' : 'PENDIENTE') : '-') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Approval Actions -->
    <div class="flex flex-wrap justify-end gap-3 items-center min-h-[44px]">
        <template x-if="selectedDates.length > 0">
            <div class="flex items-center gap-3" x-transition>
                <span class="text-xs font-bold text-[#22A9C8]">
                    <span x-text="selectedDates.length"></span> día(s) seleccionado(s)
                </span>
                <button @click="approveSelected(false)" :disabled="loading"
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2.5 rounded-full text-xs font-extrabold transition duration-300 shadow-sm disabled:opacity-50">
                    <span x-show="!loading">Aprobar seleccionados</span>
                    <span x-show="loading">Procesando...</span>
                </button>
                <button @click="approveSelected(true)" :disabled="loading"
                    class="bg-[#22A9C8] hover:bg-[#1B8BA6] text-white px-6 py-2.5 rounded-full text-xs font-extrabold transition duration-300 shadow-sm disabled:opacity-50">
                    Aprobar con comentarios
                </button>
            </div>
        </template>
        <template x-if="selectedDates.length === 0 && allPendingDates.length > 0">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic" x-transition>
                Selecciona los días que deseas aprobar arriba
            </p>
        </template>
    </div>
</div>
