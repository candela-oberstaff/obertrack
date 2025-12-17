<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 relative">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <x-application-logo class="block h-10 w-auto fill-current text-gray-800" />
                    <div class="flex flex-col">
                        <div class="relative inline-block">
                            <span class="font-bold text-2xl tracking-tight text-gray-900 leading-none">Obertrack</span>
                            <span class="absolute -top-1 -right-3 text-[0.5rem] font-bold text-gray-900">TM</span>
                        </div>
                        <span class="text-[0.6rem] font-bold tracking-widest text-gray-500 uppercase leading-none mt-0.5">Remote Work Tracking</span>
                    </div>
                </a>
            </div>

            <!-- Centered Navigation Links -->
            <div class="hidden space-x-4 sm:flex items-center justify-center flex-1">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                    Dashboard
                </a>
                
                
                @if(auth()->user()->tipo_usuario === 'empleador')
                    <a href="{{ route('empleador.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleador.dashboard') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Monitoreo de horas
                    </a>
                    <a href="{{ route('empleador.tareas.index') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleador.tareas.*') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Tareas
                    </a>
                    <a href="{{ route('reportes.index') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('reportes.*') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Reportes
                    </a>
                @elseif(auth()->user()->is_manager)
                    <a href="{{ route('empleado.registrar-horas') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleado.registrar-horas') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        <span class="flex items-center gap-2">
                            Mis horas
                            <span class="px-2 py-0.5 bg-primary text-white text-xs rounded-full">Manager</span>
                        </span>
                    </a>
                    <a href="{{ route('empleador.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleador.dashboard') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Monitoreo
                    </a>
                    <a href="{{ route('empleados.tasks.index') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleados.tasks.*') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Tareas
                    </a>

                @else
                    <a href="{{ route('empleado.registrar-horas') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleado.registrar-horas') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Registrar horas
                    </a>
                    <a href="{{ route('empleados.tasks.index') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleados.tasks.*') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Tareas
                    </a>
                @endif


                <livewire:chat-notification />


            </div>

            <!-- Notification Bell for Employers (Pending Work Hours) -->
            @if(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager)
                @php
                    $empleados = auth()->user()->tipo_usuario === 'empleador' 
                        ? \App\Models\User::where('empleador_id', auth()->id())->get()
                        : \App\Models\User::where('empleador_id', auth()->user()->empleador_id)->get();
                    
                    $pendingWeeks = [];
                    
                    // Check ALL pending hours (not just by week)
                    $totalPendingHours = \App\Models\WorkHours::whereIn('user_id', $empleados->pluck('id'))
                        ->whereRaw('approved IS FALSE')
                        ->exists();
                    
                    if ($totalPendingHours && $empleados->count() > 0) {
                        // Get detailed breakdown by employee
                        $workHoursSummary = [];
                        foreach ($empleados as $empleado) {
                            $pendingHours = \App\Models\WorkHours::where('user_id', $empleado->id)
                                ->whereRaw('approved IS FALSE')
                                ->sum('hours_worked');
                            
                            if ($pendingHours > 0) {
                                $workHoursSummary[$empleado->id] = [
                                    'name' => $empleado->name,
                                    'pending_hours' => $pendingHours,
                                ];
                            }
                        }
                        
                        if (!empty($workHoursSummary)) {
                            $pendingWeeks[] = [
                                'start' => \Illuminate\Support\Carbon::now()->subWeek(),
                                'end' => \Illuminate\Support\Carbon::now(),
                                'summary' => $workHoursSummary
                            ];
                        }
                    }
                    
                    $pendingCount = count($pendingWeeks);
                @endphp
                <x-notifications.employer-bell :pendingCount="$pendingCount" :pendingWeeks="$pendingWeeks" />
            @endif

            <!-- Notification Bell (for employees) -->
            @if(auth()->user()->tipo_usuario === 'empleado')
                @php
                    $recentTasks = \App\Models\Task::whereHas('assignees', function($q) {
                            $q->where('user_id', auth()->id());
                        })
                        ->whereRaw('completed IS FALSE')
                        ->where('created_at', '>=', now()->subDays(7))
                        ->whereDoesntHave('readBy', function ($query) {
                            $query->where('user_id', auth()->id());
                        })
                        ->with('createdBy')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                    $unreadCount = $recentTasks->count();
                @endphp
                <x-notifications.bell :unreadCount="$unreadCount" :recentTasks="$recentTasks" />
            @endif

            <!-- User Menu -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <span class="mr-3 text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            <x-user-avatar :user="Auth::user()" size="10" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Configuración
                        </x-dropdown-link>
                        
                        <x-dropdown-link href="#" onclick="event.preventDefault(); window.startTour();">
                            Ayuda / Tour
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="$dispatch('toggle-mobile-menu')" class="inline-flex items-center justify-center p-2 rounded-md text-[#22A9C8] hover:text-[#1B8BA6] hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-[#1B8BA6] transition duration-150 ease-in-out">
                    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="5" cy="5" r="2" />
                        <circle cx="12" cy="5" r="2" />
                        <circle cx="19" cy="5" r="2" />
                        <circle cx="5" cy="12" r="2" />
                        <circle cx="12" cy="12" r="2" />
                        <circle cx="19" cy="12" r="2" />
                        <circle cx="5" cy="19" r="2" />
                        <circle cx="12" cy="19" r="2" />
                        <circle cx="19" cy="19" r="2" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden absolute top-full left-0 w-full z-50 bg-white shadow-lg border-b border-gray-200" @toggle-mobile-menu.window="open = ! open">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if(auth()->user()->tipo_usuario === 'empleador')
                <x-responsive-nav-link :href="route('empleador.dashboard')" :active="request()->routeIs('empleador.dashboard')">
                    Monitoreo de horas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('empleador.tareas.index')" :active="request()->routeIs('empleador.tareas.*')">
                    Tareas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reportes.index')" :active="request()->routeIs('reportes.*')">
                    Reportes
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('empleado.registrar-horas')" :active="request()->routeIs('empleado.registrar-horas')">
                    {{ auth()->user()->is_manager ? 'Mis horas' : 'Registrar horas' }}
                </x-responsive-nav-link>

                @if(auth()->user()->is_manager)
                    <x-responsive-nav-link :href="route('empleador.dashboard')" :active="request()->routeIs('empleador.dashboard')">
                        Monitoreo
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('empleados.tasks.index')" :active="request()->routeIs('empleados.tasks.*')">
                    Tareas
                </x-responsive-nav-link>
                

            @endif
            
            <x-responsive-nav-link :href="route('chat')" :active="request()->routeIs('chat')">
                {{ __('Chat') }}
            </x-responsive-nav-link>


        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Configuración
                </x-responsive-nav-link>

                <x-responsive-nav-link href="#" onclick="event.preventDefault(); window.startTour();">
                    Ayuda / Tour
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Cerrar sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
