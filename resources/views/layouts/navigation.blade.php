<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 relative">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <x-application-logo class="block h-12 w-auto" />
                </a>
            </div>

            <!-- Centered Navigation Links -->
            <div class="hidden space-x-4 sm:flex items-center justify-center flex-1">
                @if(auth()->user()->tipo_usuario !== 'empleador')
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Dashboard
                    </a>
                @endif
                
                
                @if(auth()->user()->tipo_usuario === 'empleador')
                    <a href="{{ route('empleador.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleador.dashboard') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('empleadores.tareas-asignadas') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleadores.tareas-asignadas') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Tareas
                    </a>
                    <a href="{{ route('reportes.index') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('reportes.*') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Reportes
                    </a>

                @elseif(auth()->user()->is_manager)
                    <a href="{{ route('empleado.registrar-horas') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleado.registrar-horas') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Mis horas
                    </a>
                    <a href="{{ route('empleador.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleador.dashboard') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Monitoreo
                    </a>
                    <a href="{{ route('empleadores.tareas-asignadas') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleadores.tareas-asignadas') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Asignar tareas
                    </a>
                    <a href="{{ route('empleados.tasks.index') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleados.tasks.*') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Mis tareas
                    </a>


                @else
                    <a href="{{ route('empleado.registrar-horas') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleado.registrar-horas') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Registrar jornada
                    </a>
                    <a href="{{ route('empleados.tasks.index') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('empleados.tasks.*') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Tareas asignadas
                    </a>
                @endif

                @if(auth()->user()->is_superadmin)
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('admin.dashboard') ? 'bg-white border border-gray-300 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Administración
                    </a>
                @endif

                {{-- Communications Dropdown for Employers/Managers/Superadmins --}}
                @if(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager || auth()->user()->is_superadmin)
                    <div class="hidden sm:flex sm:items-center sm:ms-2">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-500 hover:text-gray-900 focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('chat') || request()->routeIs('empleador.emails.*') || request()->routeIs('admin.mass-email.*') ? 'bg-white border-gray-300 text-gray-900 shadow-sm' : '' }}">
                                    <div>Comunicaciones</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('chat')">
                                    {{ __('Chat') }}
                                </x-dropdown-link>
                                
                                @if(auth()->user()->is_superadmin)
                                    <x-dropdown-link :href="route('admin.mass-email.show')">
                                        {{ __('Mensajes Masivos') }}
                                    </x-dropdown-link>
                                @elseif(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager)
                                    <x-dropdown-link :href="route('empleador.emails.create')">
                                        {{ __('Mensajes Masivos') }}
                                    </x-dropdown-link>
                                @endif
                                
                                @if(auth()->user()->is_manager)
                                    <x-dropdown-link :href="route('whatsapp.chat')">
                                        {{ __('WhatsApp') }}
                                    </x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif


                {{-- Cache buster: Force recompile --}}

                <livewire:chat-notification />


            </div>

            <!-- WhatsApp Icon (Right Side) -->
            @if(auth()->user()->tipo_usuario === 'empleado' && !auth()->user()->is_manager)
            <a href="{{ route('whatsapp.chat') }}" class="inline-flex items-center px-2 py-2 rounded-full transition duration-150 ease-in-out hover:bg-gray-100 {{ request()->routeIs('whatsapp.chat') ? '' : 'text-[#25D366]' }}" title="WhatsApp">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </a>
            @endif

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
                    
                    // Check for pending recoveries
                    $totalPendingRecoveries = \App\Models\WorkHours::whereIn('user_id', $empleados->pluck('id'))
                        ->where('recovered_hours', '>', 0)
                        ->whereRaw('recovery_approved IS FALSE')
                        ->exists();
                    
                    if (($totalPendingHours || $totalPendingRecoveries) && $empleados->count() > 0) {
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
                        
                        // Get recovery requests
                        $recoveryRequests = \App\Models\WorkHours::whereIn('user_id', $empleados->pluck('id'))
                            ->where('recovered_hours', '>', 0)
                            ->whereRaw('recovery_approved IS NULL')
                            ->with('user')
                            ->get();
                        
                        if (!empty($workHoursSummary) || $recoveryRequests->count() > 0) {
                            $pendingWeeks[] = [
                                'start' => \Illuminate\Support\Carbon::now()->subWeek(),
                                'end' => \Illuminate\Support\Carbon::now(),
                                'summary' => $workHoursSummary,
                                'recovery_requests' => $recoveryRequests
                            ];
                        }
                    }
                    
                    // Count both pending hours and recovery requests
                    $pendingCount = 0;
                    foreach ($pendingWeeks as $week) {
                        // Only count employees who actually have pending hours in this week
                        $pendingCount += collect($week['summary'])->filter(fn($emp) => $emp['pending_hours'] > 0)->count();
                        
                        // Add recovery requests if present
                        if (isset($week['recovery_requests'])) {
                            $pendingCount += $week['recovery_requests']->count();
                        }
                    }
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
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->tipo_usuario !== 'empleador')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif
            
            @if(auth()->user()->role === 'empleado')
            <x-responsive-nav-link :href="route('empleado.registrar-horas')" :active="request()->routeIs('empleado.registrar-horas')">
                {{ __('Notificar tareas') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('empleados.tasks.index')" :active="request()->routeIs('empleados.tasks.index')">
                {{ __('Tareas asignadas') }}
            </x-responsive-nav-link>
        @endif        @if(auth()->user()->tipo_usuario === 'empleador')
                <x-responsive-nav-link :href="route('empleador.dashboard')" :active="request()->routeIs('empleador.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('empleadores.tareas-asignadas')" :active="request()->routeIs('empleadores.tareas-asignadas')">
                    Tareas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reportes.index')" :active="request()->routeIs('reportes.*')">
                    Reportes
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('empleado.registrar-horas')" :active="request()->routeIs('empleado.registrar-horas')">
                    {{ auth()->user()->is_manager ? 'Mis horas' : 'Registro de jornada' }}
                </x-responsive-nav-link>

                @if(auth()->user()->is_manager)
                    <x-responsive-nav-link :href="route('empleador.dashboard')" :active="request()->routeIs('empleador.dashboard')">
                        Monitoreo
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('empleadores.tareas-asignadas')" :active="request()->routeIs('empleadores.tareas-asignadas')">
                        Asignar tareas
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('empleados.tasks.index')" :active="request()->routeIs('empleados.tasks.index')">
                        Mis tareas
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('empleadores.tareas-asignadas')" :active="request()->routeIs('empleadores.tareas-asignadas')">
                        Tareas asignadas
                    </x-responsive-nav-link>
                @endif
                

            @endif
            
                {{-- Communications Links for Employers/Managers/Superadmins --}}
                @if(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager || auth()->user()->is_superadmin)
                    <div class="border-t border-gray-100 mt-2 pt-2">
                        <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Comunicaciones
                        </div>
                        <x-responsive-nav-link :href="route('chat')" :active="request()->routeIs('chat')">
                            {{ __('Chat') }}
                        </x-responsive-nav-link>
                        
                        @if(auth()->user()->is_superadmin)
                            <x-responsive-nav-link :href="route('admin.mass-email.show')" :active="request()->routeIs('admin.mass-email.show')">
                                {{ __('Mensajes Masivos') }}
                            </x-responsive-nav-link>
                        @elseif(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager)
                            <x-responsive-nav-link :href="route('empleador.emails.create')" :active="request()->routeIs('empleador.emails.create')">
                                {{ __('Mensajes Masivos') }}
                            </x-responsive-nav-link>
                        @endif

                        @if(auth()->user()->is_manager)
                            <x-responsive-nav-link :href="route('whatsapp.chat')" :active="request()->routeIs('whatsapp.chat')">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <span>WhatsApp</span>
                                </div>
                            </x-responsive-nav-link>
                        @endif
                    </div>
                @else
                    {{-- Standard Chat Link for Employees --}}
                    <x-responsive-nav-link :href="route('chat')" :active="request()->routeIs('chat')">
                        {{ __('Chat') }}
                    </x-responsive-nav-link>
                @endif




            @if(auth()->user()->tipo_usuario === 'empleado' && !auth()->user()->is_manager)
                <x-responsive-nav-link :href="route('whatsapp.chat')" :active="request()->routeIs('whatsapp.chat')">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </div>
                </x-responsive-nav-link>
            @endif


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
