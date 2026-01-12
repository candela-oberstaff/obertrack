<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                <div class="flex items-center gap-6">
                    <a href="{{ route('reportes.index') }}" class="w-10 h-10 bg-[#22A9C8] text-white rounded-lg flex items-center justify-center hover:opacity-90 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-5xl font-extrabold text-[#1a202c] leading-tight">
                            {{ $professional->name }}
                        </h1>
                        <p class="text-[#22A9C8] text-lg font-medium">{{ $professional->job_title ?? 'Profesional' }}</p>
                    </div>
                </div>
            </div>

            <!-- Week Navigator & Action Buttons -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-10">
                <div class="flex items-center gap-4 text-[#22A9C8] font-bold">
                    <a href="{{ route('reportes.show', ['user' => $professional->id, 'week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" class="hover:opacity-75">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <span class="text-sm">Semana del {{ $weekStart->format('Y-m-d') }} al {{ $weekEnd->format('Y-m-d') }}</span>
                    <a href="{{ route('reportes.show', ['user' => $professional->id, 'week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" class="hover:opacity-75">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <div class="flex flex-col items-end gap-3" x-data="{ sendEmail: false }">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" x-model="sendEmail" class="sr-only">
                            <div class="w-10 h-5 bg-gray-200 rounded-full shadow-inner transition-colors" :class="sendEmail ? 'bg-[#22A9C8]' : 'bg-gray-200'"></div>
                            <div class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full shadow transition-transform" :class="sendEmail ? 'translate-x-5' : 'translate-x-0'"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider group-hover:text-[#22A9C8] transition-colors">Enviar también por email</span>
                    </label>

                    <div class="flex gap-4">
                        <a :href="'{{ route('reportes.download.weekly', ['user' => $professional->id, 'week' => $weekStart->format('Y-m-d')]) }}' + (sendEmail ? '&send_email=1' : '')" 
                           class="px-6 py-2 border-2 border-[#22A9C8] text-[#0D1E4C] text-sm font-bold rounded-full hover:bg-gray-50 transition-all">
                            Descargar reporte semanal
                        </a>
                        <a :href="'{{ route('reportes.download.monthly', ['user' => $professional->id, 'month' => $weekStart->format('Y-m-d')]) }}' + (sendEmail ? '?send_email=1' : '')" 
                           class="px-6 py-2 bg-[#22A9C8] text-white text-sm font-bold rounded-full hover:opacity-90 transition-all shadow-sm">
                            Descargar reporte mensual
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                
                <!-- Average Week Hours -->
                <div class="bg-white rounded-3xl border-2 border-[#22A9C8] p-6 relative flex flex-col items-center">
                    <h3 class="text-lg font-bold text-[#1a202c] text-center mb-2 leading-tight">Promedio de horas semanal</h3>
                    <span class="text-7xl font-black text-[#1a202c] leading-none mb-4">{{ $registeredHours }}</span>
                    
                    <div class="w-full space-y-1 mt-auto">
                        @foreach($dailyHours as $day)
                            <div class="flex justify-between items-center text-xs font-medium text-[#1a202c]">
                                <span>{{ $day['day'] }}:</span>
                                <span>{{ $day['hours'] > 0 ? $day['hours'] . ' horas' : $day['status'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Absences -->
                <div class="bg-[#F3F4F6] rounded-3xl p-6 flex flex-col items-center">
                    <h3 class="text-xl font-bold text-[#1a202c] text-center mb-2 px-4">Ausencias registradas</h3>
                    <span class="text-7xl font-black text-[#1a202c] leading-none mb-6">{{ $absences }}</span>
                    
                    <div class="mt-auto w-full text-center">
                        @if($absences > 0)
                            @foreach($dailyHours as $day)
                                @if($day['status'] === 'Ausente')
                                    <p class="text-xs font-bold text-[#1a202c]">{{ $day['day'] }} {{ $day['date'] }}</p>
                                @endif
                            @endforeach
                        @else
                            <p class="text-xs font-medium text-gray-400">Sin ausencias registradas</p>
                        @endif
                    </div>
                </div>

                <!-- Incomplete Tasks -->
                <div class="bg-white rounded-3xl border-2 border-[#22A9C8] p-6 flex flex-col items-center">
                    <h3 class="text-xl font-bold text-[#1a202c] text-center mb-2 px-4">Registro de tareas incompletas</h3>
                    <span class="text-7xl font-black text-[#1a202c] leading-none mb-6">{{ $incompleteTasks }}</span>
                    
                    <div class="mt-auto w-full text-center">
                        <p class="text-xs font-medium text-gray-300">
                            @if($incompleteTasks > 0)
                                Hay tareas pendientes de revisión
                            @else
                                Todas las tareas han sido completadas con éxito
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-gray-50/50 rounded-[2.5rem] p-8 md:p-12 border border-gray-100 shadow-sm relative overflow-hidden">
                <!-- Background Decorative Pattern -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#22A9C8]/5 rounded-full -mr-20 -mt-20 blur-3xl"></div>
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 relative z-10">
                    <div>
                        <h3 class="text-3xl font-black text-[#1E293B]">Bitácora de comentarios</h3>
                        <p class="text-sm text-gray-500 font-medium">Cronología completa de la comunicación durante el periodo</p>
                    </div>
                    <div class="bg-white px-4 py-2 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="text-[10px] font-black text-[#22A9C8] uppercase tracking-widest block mb-0.5">Periodo Actual</span>
                        <span class="text-xs font-bold text-gray-700">{{ $weekStart->format('d/m/Y') }} — {{ $weekEnd->format('d/m/Y') }}</span>
                    </div>
                </div>
                
                <div class="space-y-12 relative z-10">
                    @php
                        $groupedComments = $allComments->groupBy(function($item) {
                            return \Carbon\Carbon::parse($item->work_date)->format('Y-m-d');
                        })->sortKeysDesc();
                    @endphp

                    @forelse($groupedComments as $date => $dayRecords)
                        <div class="relative">
                            <!-- Date Indicator -->
                            <div class="flex items-center gap-4 mb-6">
                                <div class="h-px flex-grow bg-gray-200"></div>
                                <div class="bg-white border-2 border-gray-100 px-6 py-1.5 rounded-full shadow-sm">
                                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">
                                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d \d\e F') }}
                                    </span>
                                </div>
                                <div class="h-px flex-grow bg-gray-200"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($dayRecords as $record)
                                    @php
                                        // Professional Comment Parsing
                                        $pComment = $record->user_comment;
                                        $pActivities = [];
                                        $pSummary = '';
                                        if ($pComment) {
                                            if (str_contains($pComment, 'Resumen adicional:')) {
                                                $parts = explode('Resumen adicional:', $pComment);
                                                $pActivities = array_filter(explode("\n", trim($parts[0])), fn($a) => !empty(trim($a)));
                                                $pSummary = trim($parts[1]);
                                            } elseif (str_contains($pComment, "\n")) {
                                                $pActivities = array_filter(explode("\n", trim($pComment)), fn($a) => !empty(trim($a)));
                                            } else {
                                                $pSummary = $pComment;
                                            }
                                        }

                                        // Employer Feedback Parsing
                                        $eComment = $record->approval_comment;
                                        $eActivities = [];
                                        $eSummary = '';
                                        if ($eComment) {
                                            if (str_contains($eComment, "\n")) {
                                                $eActivities = array_filter(explode("\n", trim($eComment)), fn($a) => !empty(trim($a)));
                                            } else {
                                                $eSummary = $eComment;
                                            }
                                        }
                                    @endphp

                                    @if($pComment)
                                        <div class="group h-full">
                                            <div class="h-full bg-white rounded-3xl p-6 border border-gray-100 shadow-lg shadow-gray-200/40 hover:shadow-xl hover:border-[#22A9C8]/20 transition-all duration-300">
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-2xl bg-gray-100 flex items-center justify-center text-[#1E293B]">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    </div>
                                                    <div>
                                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Profesional</span>
                                                        <span class="text-sm font-bold text-gray-800">{{ $professional->name }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="space-y-4 pl-4 border-l-2 border-gray-100">
                                                    @if(count($pActivities) > 0)
                                                        <ul class="space-y-2">
                                                            @foreach($pActivities as $activity)
                                                                <li class="flex items-start gap-2">
                                                                    <div class="w-1.5 h-1.5 rounded-full bg-[#22A9C8] mt-1.5 flex-shrink-0"></div>
                                                                    <span class="text-sm text-gray-600 font-medium leading-tight">{{ $activity }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif

                                                    @if($pSummary)
                                                        <div class="{{ count($pActivities) > 0 ? 'mt-3 pt-3 border-t border-gray-50' : '' }}">
                                                            <p class="text-xs text-gray-700 font-semibold leading-relaxed">
                                                                {{ $pSummary }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($eComment)
                                        <div class="group h-full">
                                            <div class="h-full bg-white rounded-3xl p-6 border border-[#22A9C8]/10 shadow-lg shadow-[#22A9C8]/5 hover:shadow-xl hover:border-[#22A9C8]/30 transition-all duration-300">
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-2xl bg-[#22A9C8]/10 flex items-center justify-center text-[#22A9C8]">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                    </div>
                                                    <div>
                                                        <span class="text-[10px] font-black text-[#22A9C8] uppercase tracking-widest block">Feedback Cliente</span>
                                                        <span class="text-sm font-bold text-gray-800">{{ $professional->empleador->company_name ?? $professional->empleador->name ?? 'Empresa' }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="space-y-4 pl-4 border-l-2 border-[#22A9C8]/10 font-medium italic">
                                                    @if(count($eActivities) > 0)
                                                        <ul class="space-y-2">
                                                            @foreach($eActivities as $activity)
                                                                <li class="flex items-start gap-2">
                                                                    <div class="w-1.5 h-1.5 rounded-full bg-[#22A9C8] mt-1.5 flex-shrink-0 opacity-50"></div>
                                                                    <span class="text-sm text-gray-600 leading-tight">"{{ ltrim($activity, '"') }}"</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif

                                                    @if($eSummary)
                                                        <p class="text-sm text-gray-600 leading-relaxed">
                                                            "{{ $eSummary }}"
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-20 bg-white/50 rounded-[2rem] border-2 border-dashed border-gray-100">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="text-gray-400 font-black uppercase tracking-widest text-xs">No hay registros este periodo</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
