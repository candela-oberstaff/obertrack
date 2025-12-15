{{-- <x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-4">{{ $currentMonth->format('F Y') }}</h2>
                    <div class="grid grid-cols-7 gap-2">
                        <div class="font-bold">Lun</div>
                        <div class="font-bold">Mar</div>
                        <div class="font-bold">Mié</div>
                        <div class="font-bold">Jue</div>
                        <div class="font-bold">Vie</div>
                        <div class="font-bold">Sáb</div>
                        <div class="font-bold">Dom</div>
                        @foreach ($calendar as $week)
                            @foreach ($week as $day)
                                <div class="p-2 border {{ $day['inMonth'] ? 'bg-white' : 'bg-gray-100' }}">
                                    <div class="text-sm font-bold">{{ $day['date']->format('d') }}</div>
                                    @if ($day['inMonth'] && !$day['date']->isWeekend())
                                        @if (isset($day['workHours']))
                                            <div class="text-sm">{{ $day['workHours']->hours_worked }} hrs</div>
                                        @else
                                            <form action="{{ route('work-hours.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="work_date" value="{{ $day['date']->format('Y-m-d') }}">
                                                <input type="number" name="hours_worked" step="0.5" min="0" max="24" class="w-full text-xs p-1 border rounded" placeholder="Horas">
                                                <button type="submit" class="mt-1 w-full bg-primary text-white text-xs p-1 rounded">Guardar</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}




{{-- 

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-4">{{ $currentMonth->format('F Y') }}</h2>
                    <div class="grid grid-cols-7 gap-2">
                        <div class="font-bold">Lun</div>
                        <div class="font-bold">Mar</div>
                        <div class="font-bold">Mié</div>
                        <div class="font-bold">Jue</div>
                        <div class="font-bold">Vie</div>
                        <div class="font-bold">Sáb</div>
                        <div class="font-bold">Dom</div>
                        @php
                            $totalHours = 0;
                            $currentDate = now();
                        @endphp
                        @foreach ($calendar as $week)
                            @foreach ($week as $day)
                                <div class="p-2 border {{ $day['inMonth'] ? 'bg-white' : 'bg-gray-100' }}">
                                    <div class="text-sm font-bold">{{ $day['date']->format('d') }}</div>
                                    @if ($day['inMonth'] && !$day['date']->isWeekend())
                                        @if (isset($day['workHours']))
                                            <div class="text-sm hours-display">{{ $day['workHours']->hours_worked }} hrs</div>
                                            @php $totalHours += $day['workHours']->hours_worked; @endphp
                                        @elseif ($day['date']->lte($currentDate))
                                            <form action="{{ route('work-hours.store') }}" method="POST" class="hours-form">
                                                @csrf
                                                <input type="hidden" name="work_date" value="{{ $day['date']->format('Y-m-d') }}">
                                                <input type="number" name="hours_worked" step="0.5" min="0" max="24" class="w-full text-xs p-1 border rounded" placeholder="Horas">
                                                <button type="submit" class="mt-1 w-full bg-primary text-white text-xs p-1 rounded">Guardar</button>
                                            </form>
                                        @else
                                            <div class="text-sm text-gray-400">Futuro</div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                    
                    <button id="approveButton" class="mt-4 bg-green-500 text-white px-4 py-2 rounded opacity-50 cursor-not-allowed" disabled>
                        Aprobar horas del mes
                    </button>
                    
                    <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3 text-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Aprobar horas del mes</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500">
                                        ¿Estás seguro de que quieres aprobar todas las horas registradas este mes?
                                    </p>
                                </div>
                                <div class="items-center px-4 py-3">
                                    <button id="confirmApproval" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                                        Confirmar
                                    </button>
                                    <button id="cancelApproval" class="mt-3 px-4 py-2 bg-gray-300 text-black text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    


<script>
document.addEventListener('DOMContentLoaded', function() {
    const approveButton = document.getElementById('approveButton');
    const modal = document.getElementById('approvalModal');
    const confirmButton = document.getElementById('confirmApproval');
    const cancelButton = document.getElementById('cancelApproval');

    let totalHours = {{ $totalHours }}; // Inicializa con el valor del servidor
     console.log('Total hours:', totalHours);

   function updateApproveButton(totalHours) {
    console.log('Updating approve button. Total hours:', totalHours); // Agrega este log para depuración
    const approveButton = document.getElementById('approveButton');
    if (totalHours >= 160) {
        approveButton.classList.remove('opacity-50', 'cursor-not-allowed');
        approveButton.classList.add('hover:bg-green-600');
        approveButton.disabled = false;
        console.log('Button enabled');
    } else {
        approveButton.classList.add('opacity-50', 'cursor-not-allowed');
        approveButton.classList.remove('hover:bg-green-600');
        approveButton.disabled = true;
        console.log('Button disabled');
    }
}

    // Llamar a updateApproveButton al cargar la página
        updateApproveButton(totalHours);


    // Usar delegación de eventos para los envíos de formulario
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('hours-form')) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar la UI para mostrar las horas guardadas
                    const formContainer = form.parentElement;
                    const hours = formData.get('hours_worked');
                    formContainer.innerHTML = `<div class="text-sm hours-display">${hours} hrs</div>`;
                    
                    // Actualizar el total de horas
                    totalHours += parseFloat(hours);
                    
                    // Actualizar el botón de aprobación
                    updateApproveButton();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar las horas');
            });
        }
    });


            approveButton.addEventListener('click', () => {
                if (!approveButton.disabled) {
                    modal.classList.remove('hidden');
                }
            });

            cancelButton.addEventListener('click', () => {
                modal.classList.add('hidden');
            });

            confirmButton.addEventListener('click', () => {
                fetch('{{ route("work-hours.approve-month") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        month: '{{ $currentMonth->format("Y-m") }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Horas aprobadas con éxito');
                        location.reload();
                    } else {
                        alert('Error al aprobar las horas');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al aprobar las horas');
                });

                modal.classList.add('hidden');
            });
        });
    </script>

</x-app-layout> --}}







{{-- 

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-4">{{ $currentMonth->format('F Y') }}</h2>
                    <div class="grid grid-cols-7 gap-2">
                        <div class="font-bold">Lun</div>
                        <div class="font-bold">Mar</div>
                        <div class="font-bold">Mié</div>
                        <div class="font-bold">Jue</div>
                        <div class="font-bold">Vie</div>
                        <div class="font-bold">Sáb</div>
                        <div class="font-bold">Dom</div>
                        @php
                            $currentDate = now();
                        @endphp
                        @foreach ($calendar as $week)
                            @foreach ($week as $day)
                                <div class="p-2 border {{ $day['inMonth'] ? 'bg-white' : 'bg-gray-100' }}">
                                    <div class="text-sm font-bold">{{ $day['date']->format('d') }}</div>
                                    @if ($day['inMonth'] && !$day['date']->isWeekend())
                                        @if (isset($day['workHours']))
                                            <div class="text-sm hours-display">{{ $day['workHours']->hours_worked }} hrs</div>
                                        @elseif ($day['date']->lte($currentDate))
                                            <form action="{{ route('work-hours.store') }}" method="POST" class="hours-form">
                                                @csrf
                                                <input type="hidden" name="work_date" value="{{ $day['date']->format('Y-m-d') }}">
                                                <input type="number" name="hours_worked" step="0.5" min="0" max="24" class="w-full text-xs p-1 border rounded" placeholder="Horas">
                                                <button type="submit" class="mt-1 w-full bg-primary text-white text-xs p-1 rounded">Guardar</button>
                                            </form>
                                        @else
                                            <div class="text-sm text-gray-400">Futuro</div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                    
                    <button id="approveButton" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">
                        Aprobar horas del mes
                    </button>
                
    <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Aprobar horas del mes</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        ¿Estás seguro de que quieres aprobar todas las horas registradas este mes?
                    </p>
                    <p class="mt-4 text-sm text-gray-500">
                        Por la presente certifico que las horas indicadas aquí representan la totalidad de las horas facturables por los servicios profesionales que he prestado durante el período especificado. También certifico que dichas horas son correctas y que, en caso de no haber cumplido todas las horas correspondientes a los servicios profesionales contratados en beneficio del Cliente, se deducirá la cantidad proporcional de mi pago según lo establecido en el Acuerdo de Servicios entre Oberstaff y mi persona.
                    </p>
                    <div class="mt-4">
                        <label for="signature" class="block text-sm font-medium text-gray-700">Firma digital:</label>
                        <input type="text" id="signature" name="signature" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Escribe tu nombre completo">
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmApproval" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300" disabled>
                        Confirmar
                    </button>
                    <button id="cancelApproval" class="mt-3 px-4 py-2 bg-gray-300 text-black text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const approveButton = document.getElementById('approveButton');
        const approvalModal = document.getElementById('approvalModal');
        const confirmApproval = document.getElementById('confirmApproval');
        const cancelApproval = document.getElementById('cancelApproval');
        const signatureInput = document.getElementById('signature');

        // Abrir el modal al hacer clic en el botón de aprobar
        approveButton.addEventListener('click', function() {
            approvalModal.classList.remove('hidden');
        });

        // Cerrar el modal al hacer clic en cancelar
        cancelApproval.addEventListener('click', function() {
            approvalModal.classList.add('hidden');
            signatureInput.value = ''; // Limpiar la firma
            confirmApproval.disabled = true; // Deshabilitar el botón de confirmar
        });

        // Habilitar/deshabilitar el botón de confirmar basado en la firma
        signatureInput.addEventListener('input', function() {
            confirmApproval.disabled = this.value.trim() === '';
        });

        // Aquí puedes agregar la lógica para confirmar la aprobación
        confirmApproval.addEventListener('click', function() {
            if (signatureInput.value.trim() !== '') {
                // Lógica para confirmar la aprobación
                // Por ejemplo, enviar un formulario o hacer una llamada AJAX
                approvalModal.classList.add('hidden');
                alert('Horas aprobadas con éxito. Firma: ' + signatureInput.value);
                signatureInput.value = ''; // Limpiar la firma
                confirmApproval.disabled = true; // Deshabilitar el botón de confirmar
            }
        });

        // Cerrar el modal si se hace clic fuera de él
        window.addEventListener('click', function(event) {
            if (event.target === approvalModal) {
                approvalModal.classList.add('hidden');
                signatureInput.value = ''; // Limpiar la firma
                confirmApproval.disabled = true; // Deshabilitar el botón de confirmar
            }
        });
    });
    </script>

</x-app-layout> --}}




<x-app-layout>
    <div class="py-8 bg-white min-h-screen">
        @php
            $currentDate = now();
            $allApproved = true;
            foreach ($calendar as $week) {
                foreach ($week as $day) {
                    if (isset($day['workHours']) && !$day['workHours']->approved_at) {
                        $allApproved = false;
                        break 2;
                    }
                }
            }
        @endphp
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-8 px-4 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Registro de horas</h1>
                <p class="text-primary font-medium text-sm">Total de horas trabajadas hasta el momento</p>
            </div>

            {{-- Summary Card --}}
            <div class="bg-gray-50 rounded-2xl p-6 mb-12 flex flex-col md:flex-row items-center justify-between mx-4 sm:mx-0">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-xl font-bold text-gray-900">Tu registro de horas</h2>
                    <p class="text-gray-500 text-sm">
                        |{{ $currentMonth->copy()->startOfMonth()->format('M d') }} - {{ $currentMonth->copy()->endOfMonth()->format('M d') }}|
                    </p>
                </div>

                {{-- Progress Circle --}}
                <div class="relative w-32 h-32 flex items-center justify-center">
                    @php
                        $targetHours = 160;
                        $percentage = min(($totalHours / $targetHours) * 100, 100);
                        $circumference = 2 * 3.14159 * 40; // r=40
                        $strokeDasharray = $circumference;
                        $strokeDashoffset = $circumference - ($percentage / 100) * $circumference;
                    @endphp
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="50%" cy="50%" r="40" stroke="#E5E7EB" stroke-width="8" fill="transparent" />
                        <circle cx="50%" cy="50%" r="40" stroke="#0066CC" stroke-width="8" fill="transparent" 
                                stroke-dasharray="{{ $strokeDasharray }}" 
                                stroke-dashoffset="{{ $strokeDashoffset }}" 
                                stroke-linecap="round" />
                    </svg>
                    <span class="absolute text-3xl font-bold text-gray-900">{{ (int)$totalHours }}</span>
                </div>

                <div class="text-right mt-4 md:mt-0">
                    <p class="text-gray-400 text-sm">{{ (int)$totalHours }} de {{ $targetHours }} horas registradas</p>
                    <p class="text-gray-400 text-sm text-right">actualmente</p>
                    @if($totalHours < $targetHours || !$allApproved)
                        <p class="text-red-500 text-xs italic mt-2 text-right">Tus horas siguen pendientes de aprobación</p>
                    @endif
                </div>
            </div>

            {{-- Calendar Section --}}
            <div class="mx-4 sm:mx-0">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Registro actual</h3>
                
                <div class="border border-blue-400 rounded-3xl p-6 bg-white min-h-[600px]">
                    
                    {{-- Month Navigator --}}
                    <div class="flex items-center gap-4 mb-8">
                        <a href="{{ route('empleado.registrar-horas', ['month' => $currentMonth->copy()->subMonth()->format('Y-m-d')]) }}" class="bg-[#0066CC] text-white p-1 rounded-md text-xs hover:bg-primary-hover">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <h2 class="text-xl font-bold text-gray-900">{{ $currentMonth->format('F Y') }}</h2>
                        <a href="{{ route('empleado.registrar-horas', ['month' => $currentMonth->copy()->addMonth()->format('Y-m-d')]) }}" class="bg-[#0066CC] text-white p-1 rounded-md text-xs hover:bg-primary-hover">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- Grid Headers --}}
                    <div class="grid grid-cols-7 gap-4 mb-4 text-center">
                        <div class="font-bold text-gray-700">Lunes</div>
                        <div class="font-bold text-gray-700">Martes</div>
                        <div class="font-bold text-gray-700">Miércoles</div>
                        <div class="font-bold text-gray-700">Jueves</div>
                        <div class="font-bold text-gray-700">Viernes</div>
                        <div class="font-bold text-gray-700">Sábado</div>
                        <div class="font-bold text-gray-700">Domingo</div>
                    </div>

                    {{-- Calendar Grid --}}
                    <div class="grid grid-cols-7 gap-4">
                        @foreach ($calendar as $week)
                            @foreach ($week as $day)
                                @php
                                    $isToday = $day['date']->isToday();
                                    $isWeekend = $day['date']->isWeekend();
                                    $hasHours = isset($day['workHours']);
                                @endphp
                                <div class="bg-gray-50 rounded-xl p-4 min-h-[140px] flex flex-col items-center justify-between {{ $day['inMonth'] ? '' : 'opacity-50' }}">
                                    
                                    {{-- Date Pill --}}
                                    @if($day['inMonth'])
                                        <div class="bg-[#0066CC] text-white rounded-full px-6 py-1 text-sm font-bold mb-2">
                                            {{ $day['date']->format('d') }}
                                        </div>
                                    @endif

                                    @if ($day['inMonth'] && !$isWeekend)
                                        @if ($hasHours)
                                            <div class="flex flex-col items-center gap-1 w-full">
                                                <div class="flex items-center gap-2 bg-pink-100 rounded-full px-3 py-1 w-full justify-center">
                                                     {{-- Use User Avatar Component --}}
                                                    <x-user-avatar :user="auth()->user()" size="6" classes="border-2 border-white" />
                                                    <span class="text-sm font-bold text-gray-700">{{ $day['workHours']->hours_worked }} horas</span>
                                                </div>
                                            </div>
                                        @elseif (true)
                                            <form action="{{ route('work-hours.store') }}" method="POST" class="hours-form w-full">
                                                @csrf
                                                <input type="hidden" name="work_date" value="{{ $day['date']->format('Y-m-d') }}">
                                                <input type="hidden" name="hours_worked" value="8">
                                                <button type="submit" class="w-full flex items-center justify-center gap-1 bg-white border border-gray-200 shadow-sm rounded-lg py-2 px-1 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                                                    <span class="text-primary font-bold">+</span> Registrar horas
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                </div>
                            @endforeach
                        @endforeach
                    </div>

                </div>
            </div>

                </div>
            </div>

        </div>
    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hour Registration Logic (Event Delegation)
        document.addEventListener('submit', function(e) {
            if (e.target.classList.contains('hours-form')) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                const button = form.querySelector('button');
                const originalText = button.innerHTML;
                
                button.disabled = true;
                button.innerHTML = '...';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload to update total hours chart and UI correctly
                        location.reload(); 
                    } else {
                        alert(data.message || 'Error al guardar');
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
            }
        });
    });
    </script>

</x-app-layout>