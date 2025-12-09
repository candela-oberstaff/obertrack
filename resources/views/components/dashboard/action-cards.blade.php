<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <!-- Card 1: Tasks -->
    <div class="bg-gray-100 rounded-2xl p-6 flex flex-col justify-between h-48 transition-transform hover:scale-105 duration-300 border border-transparent hover:border-[#0976D6] shadow-md">
        <div>
            @if(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager)
                <h3 class="text-lg font-bold text-gray-900 mb-2">Asignar tareas</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Crea y asigna tareas a tus profesionales.</p>
            @else
                <h3 class="text-lg font-bold text-gray-900 mb-2">Mis tareas</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Revisa tus tareas asignadas.</p>
            @endif
        </div>
        <div>
            @if(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager)
                <a href="{{ route('empleadores.tareas-asignadas') }}" class="inline-block bg-[#1E293B] text-white text-sm font-medium px-6 py-2 rounded-full hover:bg-[#0976D6] transition-colors">
                    Asignar tareas
                </a>
            @else
                <a href="{{ route('empleados.tasks.index') }}" class="inline-block bg-[#1E293B] text-white text-sm font-medium px-6 py-2 rounded-full hover:bg-[#0976D6] transition-colors">
                    Ver mis tareas
                </a>
            @endif
        </div>
    </div>

    <!-- Card 2: Messages -->
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

    <!-- Card 3: Activities/Hours -->
    <div class="bg-gray-100 rounded-2xl p-6 flex flex-col justify-between h-48 transition-transform hover:scale-105 duration-300 border border-transparent hover:border-[#0976D6] shadow-md">
        <div>
            @if(auth()->user()->tipo_usuario === 'empleador')
                <h3 class="text-lg font-bold text-gray-900 mb-2">Monitoreo de horas</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Revisa el registro de horas de los profesionales.</p>
            @else
                <h3 class="text-lg font-bold text-gray-900 mb-2">Reportar actividades</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Registra tus horas y actividades diarias.</p>
            @endif
        </div>
        <div>
            @if(auth()->user()->tipo_usuario === 'empleador')
                <a href="{{ route('empleador.dashboard') }}" class="inline-block bg-[#1E293B] text-white text-sm font-medium px-6 py-2 rounded-full hover:bg-[#0976D6] transition-colors">
                    Ver resumen
                </a>
            @else
                <a href="{{ route('empleado.registrar-horas') }}" class="inline-block bg-[#1E293B] text-white text-sm font-medium px-6 py-2 rounded-full hover:bg-[#0976D6] transition-colors">
                    Registrar
                </a>
            @endif
        </div>
    </div>
</div>
