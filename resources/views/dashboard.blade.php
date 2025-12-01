<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <div class="bg-gray-100 min-h-screen">
        <div class="flex">
            <!-- Menú lateral fijo -->
            <aside class="w-64 bg-white h-screen fixed left-0 top-0 overflow-y-auto shadow-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Menú</h3>
                    <nav class="dark:text-black">
                        @if (auth()->user()->tipo_usuario == 'empleado')
                            <a href="{{ route('empleados.crear-tarea') }}"
                               class="flex items-center text-gray-700 hover:bg-indigo-50 p-3 rounded-md transition duration-300 ease-in-out mb-2">
                                <span class="material-icons-outlined mr-3 text-indigo-600">assignment</span>
                                {{ __('Crear Tarea') }}
                            </a>
                            @if(auth()->user()->is_manager)
                                <a href="{{ route('manager.tasks.index') }}"
                                   class="flex items-center text-gray-700 hover:bg-indigo-50 p-3 rounded-md transition duration-300 ease-in-out mb-2">
                                    <span class="material-icons-outlined mr-3 text-indigo-600">supervisor_account</span>
                                    {{ __('Asignar tareas a mi equipo') }}
                                </a>
                            @endif
                        @elseif (auth()->user()->tipo_usuario == 'empleador')
                            <a href="{{ route('empleadores.tareas-asignadas') }}"
                               class="flex items-center text-gray-700 hover:bg-indigo-50 p-3 rounded-md transition duration-300 ease-in-out mb-2">
                                <span class="material-icons-outlined mr-3 text-indigo-600">assignment_turned_in</span>
                                {{ __('Registro de Horas') }}
                            </a>
                        @endif
                        <a href="/profile" class="flex items-center text-gray-700 hover:bg-indigo-50 p-3 rounded-md transition duration-300 ease-in-out mb-2">
                            <span class="material-icons-outlined mr-3 text-indigo-600">person</span>
                            Perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="flex items-center text-gray-700 hover:bg-indigo-50 p-3 rounded-md transition duration-300 ease-in-out">
                                <span class="material-icons-outlined mr-3 text-indigo-600">logout</span>
                                {{ __('Cerrar Sesión') }}
                            </a>
                        </form>
                    </nav>
                </div>
            </aside>

            <!-- Contenido principal -->
            <main class="flex-1 ml-64 p-8">
                <!-- Cabecera hero -->
                @if (auth()->user()->tipo_usuario == 'empleado')
                <header class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white rounded-lg shadow-lg mb-8 p-8">
                    <h1 class="text-4xl font-bold mb-4">Bienvenido, {{ $nombreUsuario }}</h1>
                    <p class="text-xl mb-6">¿Qué te gustaría hacer hoy?</p>
                    <div class="flex space-x-4">
                        <a href="{{ route('empleados.crear-tarea') }}" class="bg-white text-indigo-600 font-semibold px-6 py-3 rounded-full hover:bg-indigo-50 transition duration-300">Ver tareas</a>
                        <a href="/chat" class="bg-transparent border-2 border-white text-white font-semibold px-6 py-3 rounded-full hover:bg-white hover:text-indigo-600 transition duration-300">Mensajes</a>
                    </div>
                </header>
                @elseif (auth()->user()->tipo_usuario == 'empleador')
                <header class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white rounded-lg shadow-lg mb-8 p-8">
                    <h1 class="text-4xl font-bold mb-4">Bienvenido, {{ $nombreUsuario }}</h1>
                    <p class="text-xl mb-6">¿Qué te gustaría hacer hoy?</p>
                    <div class="flex space-x-4">
                        <a href="{{ route('empleadores.tareas-asignadas') }}" class="bg-white text-indigo-600 font-semibold px-6 py-3 rounded-full hover:bg-indigo-50 transition duration-300">Ver tareas</a>
                        <a href="/chat" class="bg-transparent border-2 border-white text-white font-semibold px-6 py-3 rounded-full hover:bg-white hover:text-indigo-600 transition duration-300">Mensajes</a>
                    </div>
                </header>
                @endif

                <!-- Tarjetas de contenido -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @if (auth()->user()->tipo_usuario == 'empleado')
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-xl" data-aos="fade-up">
                            <div class="p-8 bg-gradient-to-r from-indigo-600 to-blue-500">
                                <h2 class="text-3xl font-bold text-white mb-4">Registrar Horas</h2>
                                <p class="text-gray-100 text-lg mb-6">Registra tus horas diariamente.</p>
                                <a href="{{ route('empleados.crear-tarea') }}" class="inline-block bg-white text-indigo-600 font-semibold px-6 py-3 rounded-md hover:bg-green-50 transition duration-300">Registrar Horas</a>
                            </div>
                        </div>
                        @if(auth()->user()->is_manager)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-xl" data-aos="fade-up" data-aos-delay="100">
                                <div class="p-8 bg-purple-600">
                                    <h2 class="text-3xl font-bold text-white mb-4">Tareas del Equipo</h2>
                                    <p class="text-gray-100 text-lg mb-6">Gestiona las tareas de tu equipo.</p>
                                    <a href="{{ route('manager.tasks.index') }}" class="inline-block bg-white text-purple-600 font-semibold px-6 py-3 rounded-md hover:bg-purple-50 transition duration-300">Ver Tareas Asignadas</a>
                                </div>
                            </div>
                        @endif
                    @elseif (auth()->user()->tipo_usuario == 'empleador')
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-xl" data-aos="fade-up">
                            <div class="p-8 bg-gradient-to-r from-indigo-600 to-blue-500">
                                <h2 class="text-3xl font-bold text-white mb-4">Registro de Horas</h2>
                                <p class="text-gray-100 text-lg mb-6">Revisa el registro de horas de los profesionales.</p>
                                <a href="{{ route('empleadores.tareas-asignadas') }}" class="inline-block bg-white text-blue-600 font-semibold px-6 py-3 rounded-md hover:bg-blue-50 transition duration-300">Ver Resumen</a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Centro de Recursos -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mt-8 transition-all duration-300 hover:shadow-xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-6">Centro de Recursos</h2>

                        <div class="mb-8">
                            <h3 class="text-2xl font-semibold text-gray-700 mb-4">Visión Corporativa</h3>
                            <div class="aspect-w-16 aspect-h-9">
                                <iframe class="w-full h-64 rounded-lg shadow-sm" src="https://www.youtube.com/embed/Pg0diJ_Smc8?si=orxQt_AIsD7-KAkT" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-2xl font-semibold text-gray-700 mb-4">Herramientas Esenciales</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <a href="https://www.timedoctor.com" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center p-6 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors duration-300 ease-in-out border border-indigo-200">
                                    <img src="https://www.timedoctor.com/favicon.ico" alt="Time Doctor" class="w-16 h-16 mb-4">
                                    <h4 class="text-lg font-medium text-gray-900">Time Doctor</h4>
                                    <p class="text-sm text-gray-600 text-center mt-2">Gestión de tiempo y productividad</p>
                                </a>
                                <a href="https://asana.com" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-300 ease-in-out border border-green-200">
                                    <img src="https://asana.com/favicon.ico" alt="Asana" class="w-16 h-16 mb-4">
                                    <h4 class="text-lg font-medium text-gray-900">Asana</h4>
                                    <p class="text-sm text-gray-600 text-center mt-2">Gestión de proyectos y tareas</p>
                                </a>
                                <a href="https://slack.com" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center p-6 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-300 ease-in-out border border-purple-200">
                                    <img src="https://slack.com/favicon.ico" alt="Slack" class="w-16 h-16 mb-4">
                                    <h4 class="text-lg font-medium text-gray-900">Slack</h4>
                                    <p class="text-sm text-gray-600 text-center mt-2">Comunicación de equipo</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        AOS.init({
            duration: 1000,
            once: true,
        });
    </script>
</x-app-layout>
