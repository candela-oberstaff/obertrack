<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Welcome Section -->
            <div class="mb-10">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Bienvenido, <span class="text-[#0976D6]">{{ Auth::user()->name }}</span>
                </h1>
                <p class="text-xl text-gray-800 font-medium">¿Qué te gustaría hacer hoy?</p>
            </div>

            <!-- Action Cards -->
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

            <!-- Resource Center -->
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-[#0976D6] mb-8">Centro de recursos</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Left Column: Video (Smaller - 5 cols) -->
                    <div class="lg:col-span-5">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Visión corporativa</h3>
                        <div class="rounded-2xl overflow-hidden shadow-lg relative group cursor-pointer border border-transparent hover:border-[#0976D6]">
                            <div class="aspect-w-16 aspect-h-9 bg-gray-900">
                                <iframe class="w-full h-56 object-cover" src="https://www.youtube.com/embed/Pg0diJ_Smc8?si=orxQt_AIsD7-KAkT" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                                <p class="text-white text-sm font-medium">Conoce los servicios de outsourcing y reclutamiento remoto de Oberstaff</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Tools (Larger - 7 cols) -->
                    <div class="lg:col-span-7">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Herramientas escenciales</h3>
                        <div class="grid grid-cols-3 gap-6">
                            <!-- Time Doctor -->
                            <a href="https://www.timedoctor.com" target="_blank" class="bg-gray-100 rounded-2xl p-8 flex flex-col items-center justify-center text-center h-56 hover:shadow-md transition-shadow border border-transparent hover:border-[#0976D6]">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm">
                                    <img src="https://www.timedoctor.com/favicon.ico" alt="Time Doctor" class="w-10 h-10">
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">Time Doctor</h4>
                                <p class="text-sm text-gray-500">Gestión de tiempo y productividad</p>
                            </a>

                            <!-- Asana -->
                            <a href="https://asana.com" target="_blank" class="bg-gray-100 rounded-2xl p-8 flex flex-col items-center justify-center text-center h-56 hover:shadow-md transition-shadow border border-transparent hover:border-[#0976D6]">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm">
                                    <img src="https://asana.com/favicon.ico" alt="Asana" class="w-10 h-10">
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">Asana</h4>
                                <p class="text-sm text-gray-500">Gestión de proyectos y tareas</p>
                            </a>

                            <!-- Slack -->
                            <a href="https://slack.com" target="_blank" class="bg-gray-100 rounded-2xl p-8 flex flex-col items-center justify-center text-center h-56 hover:shadow-md transition-shadow border border-transparent hover:border-[#0976D6]">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm">
                                    <img src="https://a.slack-edge.com/80588/marketing/img/meta/favicon-32.png" alt="Slack" class="w-10 h-10">
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">Slack</h4>
                                <p class="text-sm text-gray-500">Comunicación de equipo</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Banner -->
            <div class="border-2 border-[#0976D6] rounded-2xl p-12 text-center mb-16">
                <h3 class="text-xl font-medium text-gray-800 mb-6 max-w-3xl mx-auto">
                    ¿Buscas un plan de telefonía que te ayude a mantenerte conectado con tus clientes? Adquiere nuestros servicios y optimiza tu comunicación de manera efectiva.
                </h3>
                <div class="flex justify-center items-center gap-2">
                    <div class="bg-[#0066FF] p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                        </svg>
                    </div>
                    <span class="text-4xl font-bold text-[#1E293B] tracking-tight">obervoice</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-100 py-8 text-center">
        <p class="text-gray-500 text-sm flex items-center justify-center gap-1">
            <span class="font-bold">©</span> 2025 Obertrack. Todos los derechos reservados
        </p>
    </footer>
</x-app-layout>
