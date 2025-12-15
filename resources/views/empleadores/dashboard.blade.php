<x-app-layout>
    <div class="py-8 bg-white min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div class="flex items-center gap-4">
                     <h2 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B]">Monitoreo de horas</h2>
                </div>
            </div>

            <!-- Subtitle -->
            <h3 class="text-[#22A9C8] font-medium text-base mb-6">Horas totales registradas por los profesionales</h3>
            
            <!-- Employee Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
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
                        <div class="w-full text-center mb-8 mt-6">
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
                            <p class="text-gray-500 text-[10px] leading-tight max-w-[180px] mx-auto">
                                {{ round($summary['total_hours']) }} de {{ $summary['target_hours'] }} horas registradas actualmente (Dec 1 - Dec 12)
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

                <!-- Calendar Grid -->
                <div class="overflow-x-auto pb-4">
                    <div class="min-w-[1000px] border border-[#22A9C8] rounded-xl p-6 bg-white">
                        <!-- Headers -->
                         <div class="grid grid-cols-7 gap-4 mb-4">
                            @foreach(['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dayName)
                                <div class="text-center font-bold text-gray-700 text-sm">{{ $dayName }}</div>
                            @endforeach
                         </div>

                        <!-- Days -->
                        <div class="grid grid-cols-7 gap-4">
                            @foreach($calendar as $day)
                                <div class="min-h-[180px] bg-[#F8F9FA] rounded-2xl p-3 flex flex-col items-center {{ !$day['is_current_month'] ? 'opacity-0' : '' }}">
                                    @if($day['is_current_month'])
                                        <!-- Date Badge -->
                                        <div class="mb-3">
                                            <div class="bg-[#22A9C8] text-white w-12 h-6 rounded-full flex items-center justify-center text-xs font-bold shadow-sm">
                                                {{ str_pad($day['day'], 2, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                        
                                        <!-- Events -->
                                        <div class="space-y-2 w-full px-1">
                                            @foreach($day['employees'] as $emp)
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-[10px] text-white font-bold uppercase {{ $emp['color_class'] }}">
                                                        {{ $emp['initials'] }}
                                                    </div>
                                                    <span class="text-xs font-medium text-gray-600 whitespace-nowrap">
                                                        {{ round($emp['hours']) }} horas
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
