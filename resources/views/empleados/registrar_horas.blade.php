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
                                                <button type="submit" class="mt-1 w-full bg-blue-500 text-white text-xs p-1 rounded">Guardar</button>
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
                                                <button type="submit" class="mt-1 w-full bg-blue-500 text-white text-xs p-1 rounded">Guardar</button>
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
                                                <button type="submit" class="mt-1 w-full bg-blue-500 text-white text-xs p-1 rounded">Guardar</button>
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
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-4 text-center">{{ $currentMonth->format('F Y') }}</h2>
                    <div class="grid grid-cols-7 gap-2 sm:gap-4">
                        @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $day)
                            <div class="font-bold text-center text-sm sm:text-base">{{ $day }}</div>
                        @endforeach
                        @php
                            $currentDate = now();
                            $totalHours = 0;
                            $allApproved = true;
                        @endphp
                        @foreach ($calendar as $week)
                            @foreach ($week as $day)
                                <div class="p-2 border {{ $day['inMonth'] ? 'bg-white' : 'bg-gray-100' }} rounded-lg shadow-sm">
                                    <div class="text-sm font-bold text-center">{{ $day['date']->format('d') }}</div>
                                    @if ($day['inMonth'] && !$day['date']->isWeekend())
                                        @if (isset($day['workHours']))
                                            <div class="text-sm hours-display text-center flex items-center justify-center">
                                                {{ $day['workHours']->hours_worked }} hrs
                                                @if ($day['workHours']->approved_at)
                                                    <svg class="w-4 h-4 text-green-500 ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                @else
                                                    @php $allApproved = false; @endphp
                                                @endif
                                            </div>
                                            @php
                                                $totalHours += $day['workHours']->hours_worked;
                                            @endphp
                                        @elseif ($day['date']->lte($currentDate))
                                            <form action="{{ route('work-hours.store') }}" method="POST" class="hours-form">
                                                @csrf
                                                <input type="hidden" name="work_date" value="{{ $day['date']->format('Y-m-d') }}">
                                                <input type="number" name="hours_worked" step="0.5" min="0" max="24" class="w-full text-xs p-1 border rounded" placeholder="Horas">
                                                <button type="submit" class="mt-1 w-full bg-blue-500 text-white text-xs p-1 rounded hover:bg-blue-600 transition duration-300">Guardar</button>
                                            </form>
                                            @php $allApproved = false; @endphp
                                        @else
                                            <div class="text-sm text-gray-400 text-center">Futuro</div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                    
                    <div class="mt-6 text-center">
                        <button id="approveButton" class="bg-green-500 text-white px-6 py-2 rounded-full text-lg font-semibold shadow-md hover:bg-green-600 transition duration-300 {{ ($totalHours >= 160 && !$allApproved) ? '' : 'opacity-50 cursor-not-allowed' }}" {{ ($totalHours >= 160 && !$allApproved) ? '' : 'disabled' }}>
                            Aprobar horas del mes
                        </button>
                    </div>
                    
                    <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden p-12" style="z-index: 50;">
                        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 text-center mb-4">Aprobar horas del mes</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-md text-gray-600 mb-4">
                                        ¿Estás seguro de que quieres aprobar todas las horas registradas este mes?
                                    </p>
                                    <p class="text-md text-gray-600 mb-4">
                                        Por la presente certifico que las horas indicadas aquí representan la totalidad de las horas facturables por los servicios profesionales que he prestado durante el período especificado. También certifico que dichas horas son correctas y que, en caso de no haber cumplido todas las horas correspondientes a los servicios profesionales contratados en beneficio del Cliente, se deducirá la cantidad proporcional de mi pago según lo establecido en el Acuerdo de Servicios entre Oberstaff y mi persona.
                                    </p>
                                    @if(!auth()->user()->signature)
                                    <div class="mt-4">
                                        <label for="signature" class="block text-sm font-medium text-gray-700 mb-2">Firma digital:</label>
                                        <input type="text" id="signature" name="signature" class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Escribe tu nombre completo">
                                    </div>
                                    @else
                                    <div class="mt-4 p-4 bg-gray-100 rounded-md">
                                        <p class="text-sm text-gray-700 font-semibold mb-2">Tu firma digital:</p>
                                        <p class="text-lg text-blue-600 font-bold">{{ auth()->user()->signature->signature }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="items-center px-4 py-3 text-center">
                                    <button id="confirmApproval" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 mr-2 transition duration-300" {{ auth()->user()->signature ? '' : 'disabled' }}>
                                        Confirmar
                                    </button>
                                    <button id="cancelApproval" class="px-4 py-2 bg-gray-300 text-black text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition duration-300">
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
        const approvalModal = document.getElementById('approvalModal');
        const confirmApproval = document.getElementById('confirmApproval');
        const cancelApproval = document.getElementById('cancelApproval');
        const signatureInput = document.getElementById('signature');
        const totalHours = {{ $totalHours }};
        const allApproved = {{ $allApproved ? 'true' : 'false' }};

        function updateApproveButton() {
            approveButton.disabled = totalHours < 160 || allApproved;
            approveButton.classList.toggle('opacity-50', totalHours < 160 || allApproved);
            approveButton.classList.toggle('cursor-not-allowed', totalHours < 160 || allApproved);
        }

        updateApproveButton();

        approveButton.addEventListener('click', function() {
            if (!this.disabled) {
                approvalModal.classList.remove('hidden');
            }
        });

        cancelApproval.addEventListener('click', function() {
            approvalModal.classList.add('hidden');
            if (signatureInput) signatureInput.value = '';
            if (!{{ auth()->user()->signature ? 'true' : 'false' }}) {
                confirmApproval.disabled = true;
            }
        });

        if (signatureInput) {
            signatureInput.addEventListener('input', function() {
                confirmApproval.disabled = this.value.trim() === '';
            });
        }

        confirmApproval.addEventListener('click', function() {
            if ((signatureInput && signatureInput.value.trim() !== '') || {{ auth()->user()->signature ? 'true' : 'false' }}) {
                // Enviar la aprobación al servidor
                fetch('{{ route("work-hours.approve") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        signature: signatureInput ? signatureInput.value : null,
                        month: '{{ $currentMonth->format("Y-m") }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Horas aprobadas con éxito.');
                        location.reload(); // Recargar la página para mostrar las horas aprobadas
                    } else {
                        alert('Error al aprobar las horas: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al aprobar las horas.');
                });

                approvalModal.classList.add('hidden');
                if (signatureInput) signatureInput.value = '';
                confirmApproval.disabled = true;
            }
        });

        window.addEventListener('click', function(event) {
            if (event.target === approvalModal) {
                approvalModal.classList.add('hidden');
                if (signatureInput) signatureInput.value = '';
                if (!{{ auth()->user()->signature ? 'true' : 'false' }}) {
                    confirmApproval.disabled = true;
                }
            }
        });
    });
    </script>

</x-app-layout>