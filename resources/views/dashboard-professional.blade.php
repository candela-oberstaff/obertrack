<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    ¡Hola, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="text-gray-600 mt-2">
                    @if(auth()->user()->is_manager)
                        Bienvenido a tu panel de Manager
                    @else
                        Aquí está tu resumen de actividades
                    @endif
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Pending Tasks -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Tareas Pendientes</p>
                            <p class="text-4xl font-bold mt-2">
                                {{ auth()->user()->assignedTasks()->where('completed', false)->count() }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Hours This Month (Amber/Orange Light Gradient matching Help Card) -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 shadow-lg border border-amber-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-700 text-sm font-medium">Horas Este Mes</p>
                            <p class="text-4xl font-bold mt-2 text-gray-900">
                                {{ auth()->user()->workHours()
                                    ->whereYear('work_date', now()->year)
                                    ->whereMonth('work_date', now()->month)
                                    ->sum('hours_worked') ?? 0 }}
                            </p>
                        </div>
                        <div class="bg-amber-200 rounded-full p-4">
                            <svg class="w-8 h-8 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed Tasks (Green) -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Tareas Completadas</p>
                            <p class="text-4xl font-bold mt-2">
                                {{ auth()->user()->assignedTasks()
                                    ->where('completed', true)
                                    ->whereYear('updated_at', now()->year)
                                    ->whereMonth('updated_at', now()->month)
                                    ->count() }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column: Tasks -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Recent Tasks -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Mis Tareas Recientes</h2>
                            <a href="{{ route('empleados.tasks.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Ver todas →
                            </a>
                        </div>
                        
                        @php
                            $recentTasks = auth()->user()->assignedTasks()->latest()->take(5)->get();
                        @endphp

                        @forelse($recentTasks as $task)
                            <div class="flex items-start gap-4 p-4 hover:bg-gray-50 rounded-lg transition mb-3">
                                <div class="flex-shrink-0 mt-1">
                                    @if($task->completed)
                                        <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 {{ $task->completed ? 'line-through' : '' }}">
                                        {{ $task->title }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Vence: {{ $task->end_date->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $task->completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $task->completed ? 'Completada' : 'Pendiente' }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No tienes tareas asignadas</p>
                            </div>
                        @endforelse
                    </div>

                    @if(auth()->user()->is_manager)
                    <!-- Manager: Team Overview -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl shadow-lg p-6 border border-indigo-200">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Panel de Manager</h2>
                        <p class="text-gray-600 mb-4">Como manager, puedes asignar tareas a tu equipo</p>
                        <a href="{{ route('empleadores.tareas-asignadas') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-full hover:bg-indigo-700 transition">
                            Gestionar Equipo
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Right Column: Quick Actions & Info -->
                <div class="space-y-6">
                    <!-- Quick Hour Registration -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Registro Rápido</h3>
                        <div class="space-y-3">
                            <a href="{{ route('empleado.registrar-horas') }}" class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                                Registrar Horas
                            </a>
                            
                            <div x-data="{ showModal: false }">
                                <button @click="showModal = true" class="block w-full bg-white border-2 border-blue-600 text-blue-600 text-center py-3 rounded-lg hover:bg-blue-50 transition font-medium">
                                    Reportar Actividad
                                </button>

                                <!-- Modal -->
                                <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>

                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                            <form action="{{ route('empleador.crear-tarea') }}" method="POST" class="p-6">
                                                @csrf
                                                <input type="hidden" name="employee_id" value="{{ auth()->id() }}">
                                                <input type="hidden" name="visible_para" value="{{ auth()->id() }}">
                                                
                                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                                    Reportar Nueva Actividad
                                                </h3>

                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                                                        <input type="text" name="title" id="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                    </div>

                                                    <div>
                                                        <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                                                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                                                            <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                        </div>
                                                        <div>
                                                            <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha Fin</label>
                                                            <input type="date" name="end_date" id="end_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="priority" class="block text-sm font-medium text-gray-700">Prioridad</label>
                                                        <select name="priority" id="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                            <option value="low">Baja</option>
                                                            <option value="medium" selected>Media</option>
                                                            <option value="high">Alta</option>
                                                            <option value="urgent">Urgente</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                                                        Guardar
                                                    </button>
                                                    <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                                        Cancelar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Quick Links -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Accesos Rápidos</h3>
                        <div class="space-y-3">
                            <a href="{{ route('empleados.tasks.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Mis Tareas</p>
                                    <p class="text-xs text-gray-500">Ver todas</p>
                                </div>
                            </a>

                            <a href="{{ route('chat') }}" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Mensajes</p>
                                    <p class="text-xs text-gray-500">Chat</p>
                                </div>
                            </a>

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Mi Perfil</p>
                                    <p class="text-xs text-gray-500">Configuración</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg p-6 border border-amber-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">¿Necesitas ayuda?</h3>
                        <p class="text-sm text-gray-600 mb-4">Revisa nuestros recursos o contacta a soporte</p>
                        <a href="#" class="text-sm text-amber-700 font-medium hover:text-amber-800">
                            Ver recursos →
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <x-layout.footer />

</x-app-layout>
