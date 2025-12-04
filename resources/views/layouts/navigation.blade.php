<nav x-data="{ open: false }" class="bg-gray-100 border-b border-gray-200">
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
            <div class="hidden space-x-10 sm:flex items-center justify-center flex-1">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                    Dashboard
                </a>
                
                
                @if(auth()->user()->tipo_usuario === 'empleador')
                    <a href="{{ route('empleadores.tareas-asignadas') }}" class="inline-flex items-center px-6 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleadores.tareas-asignadas') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Monitoreo de horas
                    </a>
                @elseif(auth()->user()->is_manager)
                    <a href="{{ route('empleado.registrar-horas') }}" class="inline-flex items-center px-6 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleado.registrar-horas') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        <span class="flex items-center gap-2">
                            Mis horas
                            <span class="px-2 py-0.5 bg-blue-500 text-white text-xs rounded-full">Manager</span>
                        </span>
                    </a>
                @else
                    <a href="{{ route('empleado.registrar-horas') }}" class="inline-flex items-center px-6 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleado.registrar-horas') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Registrar horas
                    </a>
                @endif


                <a href="{{ route('chat') }}" class="inline-flex items-center px-6 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('chatify') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                    Chat
                </a>
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
                        ->where('approved', false)
                        ->exists();
                    
                    if ($totalPendingHours && $empleados->count() > 0) {
                        // Get detailed breakdown by employee
                        $workHoursSummary = [];
                        foreach ($empleados as $empleado) {
                            $pendingHours = \App\Models\WorkHours::where('user_id', $empleado->id)
                                ->where('approved', false)
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
                    $recentTasks = \App\Models\Task::where('visible_para', auth()->id())
                        ->where('completed', false)
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
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if(auth()->user()->tipo_usuario === 'empleador')
                <x-responsive-nav-link :href="route('empleadores.tareas-asignadas')" :active="request()->routeIs('empleadores.tareas-asignadas')">
                    Monitoreo de horas
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('empleado.registrar-horas')" :active="request()->routeIs('empleado.registrar-horas')">
                    {{ auth()->user()->is_manager ? 'Mis horas' : 'Registrar horas' }}
                </x-responsive-nav-link>
            @endif
            
            <x-responsive-nav-link :href="route('chat')" :active="request()->routeIs('chatify')">
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
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
