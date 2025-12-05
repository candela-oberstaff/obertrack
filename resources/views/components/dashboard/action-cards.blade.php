<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <!-- Card 1: Ver tareas -->
    <div class="bg-gray-100 rounded-2xl p-6 flex flex-col justify-between h-48 transition-transform hover:scale-105 duration-300 border border-transparent hover:border-[#0976D6] shadow-md">
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Ver tareas del profesional</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Monitorea el progreso de las asignaciones.</p>
        </div>
        <div>
            @if(auth()->user()->tipo_usuario === 'empleador')
                <a href="{{ route('empleadores.tareas-asignadas') }}" class="inline-block bg-[#1E293B] text-white text-sm font-medium px-6 py-2 rounded-full hover:bg-[#0976D6] transition-colors">
                    Ver tareas
                </a>
            @else
                <a href="{{ route('empleados.crear-tarea') }}" class="inline-block bg-[#1E293B] text-white text-sm font-medium px-6 py-2 rounded-full hover:bg-[#0976D6] transition-colors">
                    Ver tareas
                </a>
            @endif
        </div>
    </div>

    <!-- Card 2: Mensajes -->
    <div class="bg-gray-100 rounded-2xl p-6 flex flex-col justify-between h-48 transition-transform hover:scale-105 duration-300 border border-transparent hover:border-[#0976D6] shadow-md">
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Mensajes</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Inicia una conversación a través del chat.</p>
        </div>
        <div>
            <a href="{{ route('chat') }}" class="inline-block bg-[#1E293B] text-white text-sm font-medium px-6 py-2 rounded-full hover:bg-[#0976D6] transition-colors">
                Ir a mensajes
            </a>
        </div>
    </div>

    <!-- Card 3: Monitoreo de horas -->
    <div class="bg-gray-100 rounded-2xl p-6 flex flex-col justify-between h-48 transition-transform hover:scale-105 duration-300 border border-transparent hover:border-[#0976D6] shadow-md">
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Monitoreo de horas</h3>
            <p class="text-gray-600 text-sm leading-relaxed">Revisa el registro de horas de los profesionales.</p>
        </div>
        <div>
            @if(auth()->user()->tipo_usuario === 'empleador')
                <a href="{{ route('empleadores.tareas-asignadas') }}" class="inline-block bg-[#1E293B] text-white text-sm font-medium px-6 py-2 rounded-full hover:bg-[#0976D6] transition-colors">
                    Ver resumen
                </a>
            @else
                <a href="{{ route('empleado.registrar-horas') }}" class="inline-block bg-[#1E293B] text-white text-sm font-medium px-6 py-2 rounded-full hover:bg-[#0976D6] transition-colors">
                    Ver resumen
                </a>
            @endif
        </div>
    </div>
</div>
