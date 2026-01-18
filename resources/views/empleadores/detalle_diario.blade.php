<x-app-layout>
    <div class="py-8 bg-white min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs / Back Link -->
            <div class="mb-8">
                <a href="{{ route('empleador.dashboard') }}" class="inline-flex items-center gap-2 text-[#22A9C8] font-bold hover:gap-3 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Volver al Monitoreo</span>
                </a>
            </div>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-end gap-4 mb-12">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-black text-[#1E293B] mb-2">Detalles del día</h2>
                    <div class="flex items-center gap-3">
                        <span class="bg-[#22A9C8] text-white px-4 py-1 rounded-full font-bold text-lg">
                            {{ $targetDate->translatedFormat('d') }}
                        </span>
                        <p class="text-xl text-gray-500 font-medium italic">
                            {{ ucfirst($targetDate->translatedFormat('F, Y')) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Professionals Grid -->
            <div class="grid grid-cols-1 gap-8">
                @foreach($empleados as $employee)
                    @php
                        $record = $dayRecords->where('user_id', $employee->id)->first();
                        $empDayTasks = $dayTasks->filter(function($t) use ($employee) {
                            return $t->created_by == $employee->id || $t->assignees->contains('id', $employee->id);
                        });
                    @endphp
                    
                    <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-xl shadow-gray-100/50 relative overflow-hidden group hover:border-[#22A9C8]/30 transition-all duration-500">
                        <!-- Background Decorative Element -->
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-gray-50 rounded-full group-hover:bg-[#22A9C8]/5 transition-colors duration-500"></div>

                        <div class="relative z-10 flex flex-col lg:flex-row gap-8">
                            
                            <!-- Left: Profile Info -->
                            <div class="lg:w-1/4">
                                <div class="flex flex-col items-center lg:items-start text-center lg:text-left">
                                    <div class="w-24 h-24 rounded-[28px] overflow-hidden border-4 border-white shadow-lg mb-4 bg-gray-100">
                                        <img src="{{ $employee->avatar ? (str_starts_with($employee->avatar, 'http') ? $employee->avatar : asset('avatars/' . $employee->avatar)) : 'https://ui-avatars.com/api/?name='.urlencode($employee->name).'&color=FFFFFF&background=22A9C8' }}" 
                                             alt="{{ $employee->name }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <h3 class="text-xl font-black text-[#1E293B] leading-tight mb-1">{{ $employee->name }}</h3>
                                    <p class="text-sm font-bold text-[#22A9C8] mb-6 uppercase tracking-wider tracking-widest">{{ $employee->job_title ?: 'Colaborador' }}</p>

                                    @if($record)
                                        <div class="inline-flex items-center px-4 py-2 rounded-2xl bg-green-50 text-green-600 border border-green-100 mb-4">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            <span class="text-xs font-black uppercase">Jornada Registrada</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-4 py-2 rounded-2xl bg-red-50 text-red-600 border border-red-100 mb-4">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span class="text-xs font-black uppercase">Sin Registro</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Details -->
                            <div class="lg:w-3/4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    
                                    <!-- Column 1: Record Details -->
                                    <div class="space-y-6">
                                        @if($record)
                                            <!-- Stats -->
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Horas</p>
                                                    <p class="text-xl font-black text-gray-900">{{ round($record->hours_worked) }}h</p>
                                                </div>
                                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Estado</p>
                                                    <p class="text-xl font-black text-gray-900 {{ $record->approved ? 'text-green-600' : 'text-orange-500' }}">
                                                        {{ $record->approved ? 'Aprobado' : 'Pendiente' }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if($record->absence_reason)
                                                <div class="bg-red-50 border border-red-100 rounded-2xl p-5">
                                                    <div class="flex items-center gap-2 mb-2 text-red-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        <span class="text-[10px] font-black uppercase tracking-widest">Motivo de Ausencia</span>
                                                    </div>
                                                    <p class="text-sm text-gray-800 font-bold ml-6">{{ $record->absence_reason }}</p>
                                                </div>
                                            @endif
                                            
                                            @php
                                                $comment = $record->user_comment;
                                                $activities = [];
                                                $summary = '';
                                                
                                                if ($comment) {
                                                    if (str_contains($comment, 'Resumen adicional:')) {
                                                        $parts = explode('Resumen adicional:', $comment);
                                                        $activities = array_filter(explode("\n", trim($parts[0])), fn($a) => !empty(trim($a)));
                                                        $summary = trim($parts[1]);
                                                    } elseif (str_contains($comment, "\n")) {
                                                        $activities = array_filter(explode("\n", trim($comment)), fn($a) => !empty(trim($a)));
                                                    } else {
                                                        $summary = $comment;
                                                    }
                                                }
                                            @endphp

                                            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden h-full min-h-[200px]">
                                                <div class="bg-gray-50/50 px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Actividades Realizadas</span>
                                                </div>
                                                <div class="p-6 max-h-60 overflow-y-auto">
                                                    @if(count($activities) > 0)
                                                        <ul class="space-y-4">
                                                            @foreach($activities as $activity)
                                                                <li class="flex items-start gap-3">
                                                                    <div class="w-1.5 h-1.5 rounded-full bg-[#22A9C8] mt-1.5 flex-shrink-0"></div>
                                                                    <span class="text-sm text-gray-600 leading-relaxed font-medium">{{ $activity }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                    
                                                    @if($summary)
                                                        <div class="{{ count($activities) > 0 ? 'mt-6 pt-6 border-t border-gray-50' : '' }}">
                                                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Resumen Adicional</p>
                                                            <p class="text-sm text-gray-600 leading-relaxed font-medium whitespace-pre-line break-words">{{ $summary }}</p>
                                                        </div>
                                                    @endif

                                                    @if(count($activities) === 0 && !$summary)
                                                        <p class="text-sm text-gray-400 italic text-center py-4">No se registraron detalles de actividades.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="h-full flex flex-col items-center justify-center py-12 bg-gray-50/30 rounded-[28px] border-2 border-dashed border-gray-100">
                                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-4 opacity-50">
                                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <p class="text-gray-300 font-black uppercase tracking-widest text-[10px]">Sin registro de jornada</p>
                                            </div>
                                        @endif

                                        {{-- Recovery Hours Section --}}
                                        @php
                                            $employeeRecoveries = $dayRecoveries->where('user_id', $employee->id);
                                        @endphp
                                        
                                        @if($employeeRecoveries->count() > 0)
                                            <div class="mt-8 pt-8 border-t border-gray-100">
                                                <div class="flex items-center gap-2 mb-6 text-blue-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span class="text-xs font-black uppercase tracking-widest">Recuperación de Horas</span>
                                                </div>

                                                @foreach($employeeRecoveries as $recovery)
                                                    <div class="bg-blue-50/30 border border-blue-100 rounded-2xl p-6 mb-4 last:mb-0 shadow-sm">
                                                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                                            <div class="flex items-center gap-3">
                                                                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-black">{{ $recovery->hours_recovered }} hs</span>
                                                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $recovery->approved ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                                                    {{ $recovery->approved ? 'Aprobado' : 'Pendiente' }}
                                                                </span>
                                                            </div>
                                                            @if(!$recovery->approved)
                                                                <div class="flex gap-2">
                                                                    <form action="{{ route('recovery.update-status', $recovery->id) }}" method="POST" class="inline">
                                                                        @csrf
                                                                        <input type="hidden" name="approved" value="0">
                                                                        <button type="submit" onclick="return confirm('¿Rechazar esta solicitud?')" class="bg-white border border-red-100 text-red-600 px-4 py-2 rounded-lg text-xs font-black uppercase hover:bg-red-50 transition-all">Rechazar</button>
                                                                    </form>
                                                                    <form action="{{ route('recovery.update-status', $recovery->id) }}" method="POST" class="inline">
                                                                        @csrf
                                                                        <input type="hidden" name="approved" value="1">
                                                                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-black uppercase hover:bg-blue-700 transition-all">Aprobar</button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="bg-white/50 rounded-xl p-4 border border-blue-50">
                                                            <p class="text-[10px] font-black uppercase tracking-widest text-blue-400 mb-2">Actividades de recuperación</p>
                                                            <p class="text-sm text-gray-700 font-medium whitespace-pre-line break-words">{{ $recovery->activities }}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Column 2: Tasks Log -->
                                    <div class="space-y-6">
                                        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden h-full flex flex-col">
                                            <div class="bg-gray-50/50 px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Tareas Asignadas</span>
                                            </div>
                                            <div class="p-6 flex-grow">
                                                @forelse($empDayTasks as $task)
                                                    <div class="flex items-start justify-between gap-4 mb-4 last:mb-0 pb-4 last:pb-0 border-b last:border-0 border-gray-50 group/task">
                                                        <div class="flex-grow">
                                                            <p class="text-sm font-bold text-gray-800 mb-1 group-hover/task:text-[#22A9C8] transition-colors">{{ $task->title }}</p>
                                                            <p class="text-[9px] font-black uppercase text-gray-400 tracking-wider">
                                                                Desde: {{ $task->start_date->format('d/m') }} - Hasta: {{ $task->end_date->format('d/m') }}
                                                            </p>
                                                        </div>
                                                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider flex-shrink-0 {{ $task->completed ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                                            {{ $task->completed ? 'Completada' : 'En progreso' }}
                                                        </span>
                                                    </div>
                                                @empty
                                                    <div class="h-full flex flex-col items-center justify-center py-4 bg-gray-50/20 rounded-xl">
                                                        <p class="text-sm text-gray-400 italic">No hay tareas asignadas para este día.</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        @if($record && $record->approval_comment)
                                            <div class="bg-[#22A9C8]/5 border border-[#22A9C8]/10 rounded-2xl p-6">
                                                <div class="flex items-center gap-2 mb-3 text-[#22A9C8]">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                                    <span class="text-[10px] font-black uppercase tracking-widest leading-none">Tu Feedback</span>
                                                </div>
                                                <p class="text-sm text-gray-700 font-medium italic break-words">"{{ $record->approval_comment }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
