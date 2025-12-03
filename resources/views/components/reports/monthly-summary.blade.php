@props(['empleadosInfo', 'currentMonth', 'totalApprovedHours'])

\u003cdiv class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-900 shadow-2xl rounded-xl overflow-hidden mb-8 p-4 sm:p-8 md:p-12 lg:p-10"\u003e
    \u003ch2 class="text-2xl sm:text-3xl font-extrabold text-gray-800 dark:text-gray-100 mb-6 text-center"\u003e
        Resumen de {{ $currentMonth-\u003etranslatedFormat('F Y') }}
    \u003c/h2\u003e

    \u003cdiv class="space-y-6"\u003e
        @foreach($empleadosInfo as $empleado)
        \u003cdiv x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl"\u003e
            \u003cdiv @click="open = !open" class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 p-4 cursor-pointer"\u003e
                \u003ch3 class="text-lg sm:text-xl font-bold text-white flex flex-wrap items-center justify-between"\u003e
                    \u003cspan class="flex items-center mb-2 sm:mb-0"\u003e
                        \u003csvg class="h-6 w-6 mr-2" fill="currentColor" viewBox="0 0 20 20"\u003e
                            \u003cpath fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/\u003e
                        \u003c/svg\u003e
                        {{ $empleado['name'] }}
                    \u003c/span\u003e
                    \u003cspan class="text-sm bg-white bg-opacity-20 px-3 py-1 rounded-full"\u003e
                        {{ number_format($empleado['totalApprovedHours'], 2) }} horas aprobadas
                    \u003c/span\u003e
                \u003c/h3\u003e
            \u003c/div\u003e

            \u003cdiv x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="p-4"\u003e
                \u003cdiv class="mb-6"\u003e
                    \u003ch4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3"\u003eResumen de Semanas\u003c/h4\u003e
                    \u003cdiv class="flex flex-wrap items-center gap-2"\u003e
                        @foreach($empleado['approvedWeeks'] as $index =\u003e $week)
                            \u003cdiv class="w-10 h-10 rounded-full flex items-center justify-center {{ $week['approved'] ? 'bg-green-500' : 'bg-yellow-500' }} text-white" title="Semana {{ $index + 1 }}: {{ $week['start'] }} - {{ $week['end'] }}"\u003e
                                \u003cspan class="text-xs font-bold"\u003eS{{ $index + 1 }}\u003c/span\u003e
                            \u003c/div\u003e
                        @endforeach
                    \u003c/div\u003e
                \u003c/div\u003e

                @php
                    $allApproved = collect($empleado['approvedWeeks'])-\u003eevery(fn($week) =\u003e $week['approved']);
                    $canDownload = $allApproved && $empleado['totalApprovedHours'] \u003e= 160;
                @endphp

                \u003cdiv class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-inner"\u003e
                    \u003cdiv class="flex items-center mb-4"\u003e
                        \u003cinput type="checkbox" id="certifyHours_{{ $empleado['id'] }}" class="form-checkbox h-5 w-5 text-blue-600 transition duration-150 ease-in-out"\u003e
                        \u003clabel for="certifyHours_{{ $empleado['id'] }}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300"\u003e
                            Certifico que las horas son correctas y autorizo el pago
                        \u003c/label\u003e
                    \u003c/div\u003e
                    \u003cbutton
                        onclick="downloadReport({{ $empleado['id'] }}, '{{ $empleado['name'] }}', {{ $totalApprovedHours }}, '{{ $currentMonth-\u003eformat('Y-m') }}')"
                        class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white {{ $canDownload ? 'bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500' : 'bg-gray-400 cursor-not-allowed' }} transition ease-in-out duration-150"
                        {{ $canDownload ? '' : 'disabled' }}
                    \u003e
                        \u003csvg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20"\u003e
                            \u003cpath fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/\u003e
                        \u003c/svg\u003e
                        Descargar Reporte Mensual
                    \u003c/button\u003e
                \u003c/div\u003e
            \u003c/div\u003e
        \u003c/div\u003e
        @endforeach
    \u003c/div\u003e
\u003c/div\u003e
