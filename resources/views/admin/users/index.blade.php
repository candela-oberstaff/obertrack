<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Usuarios - Admin') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen" x-data="userManagement()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @include('admin.partials.nav')

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded-r-xl shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-bold rounded-r-xl shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-gray-100">
                <div class="p-4 md:p-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900">Base de Datos de Usuarios</h3>
                        <p class="text-xs text-gray-500 mt-1">Administra todos los usuarios de la plataforma</p>
                    </div>
                    
                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-[#22A9C8] text-white rounded-full text-sm font-bold hover:bg-[#1b8fa8] transition-colors shadow-lg shadow-[#22A9C8]/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Crear Nuevo Usuario
                    </a>
                </div>

                <!-- Filters -->
                <div class="p-4 bg-gray-50/50 border-b border-gray-100">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end w-full">
                        <div class="w-full md:flex-1">
                            <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Buscar</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o email..." class="w-full pl-9 pr-4 py-2 text-sm border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-600 bg-white">
                            </div>
                        </div>
                        <div class="w-full md:w-48">
                            <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Rol</label>
                            <select name="role" class="w-full py-2 text-sm border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-600 bg-white">
                                <option value="">Todos</option>
                                <option value="empleado" {{ request('role') == 'empleado' ? 'selected' : '' }}>Profesional</option>
                                <option value="empleador" {{ request('role') == 'empleador' ? 'selected' : '' }}>Empresa</option>
                                <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                            </select>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg text-sm font-bold hover:bg-gray-700 transition-colors h-[38px]">
                            Filtrar
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50 uppercase text-[10px] font-bold text-gray-400 tracking-widest">
                                <th class="px-4 md:px-8 py-4">Usuario</th>
                                <th class="px-4 md:px-8 py-4 hidden sm:table-cell">Rol</th>
                                <th class="px-4 md:px-8 py-4 hidden md:table-cell">Contacto</th>
                                <th class="px-4 md:px-8 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 md:px-8 py-4">
                                        <div class="flex items-center gap-4">
                                            <x-user-avatar :user="$user" size="10" class="md:w-10 md:h-10 shadow-sm border border-gray-100" />
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                                <div class="text-[11px] text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-8 py-4 hidden sm:table-cell">
                                        @if($user->tipo_usuario === 'superadmin')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                                Superadmin
                                            </span>
                                        @elseif($user->tipo_usuario === 'empleador')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                Empresa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                                Profesional
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 md:px-8 py-4 hidden md:table-cell">
                                        <span class="text-gray-500 text-xs">{{ $user->phone_number ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 md:px-8 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-gray-400 hover:text-[#22A9C8] transition-colors rounded-full hover:bg-[#22A9C8]/10" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            
                                            <button @click="openDeleteModal({{ $user->id }}, '{{ $user->name }}')" class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded-full hover:bg-red-50" title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-12 text-center text-gray-500">
                                        No se encontraron usuarios
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-8 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div x-show="isDeleteModalOpen" 
             style="display: none;"
             class="fixed inset-0 z-[100] overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
             
            <!-- Backdrop -->
            <div x-show="isDeleteModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"></div>

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Panel -->
                <div x-show="isDeleteModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
                     @click.away="closeDeleteModal()">
                    
                    <form :action="'/admin/users/' + deletingUserId" method="POST">
                        @csrf
                        @method('DELETE')
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Eliminar Usuario
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            ¿Estás seguro de que deseas eliminar a <span class="font-bold text-gray-800" x-text="deletingUserName"></span>?
                                            Esta acción no se puede deshacer.
                                        </p>
                                        
                                        <!-- Reassignment Section -->
                                        <div class="mt-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <label for="reassign_to" class="block text-sm font-medium text-gray-700 mb-2">
                                                Reasignar tareas a:
                                            </label>
                                            <select name="reassign_to" id="reassign_to" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-50">
                                                <option value="">-- Seleccionar Usuario --</option>
                                                <option value="{{ auth()->id() }}">Mí mismo (Admin)</option>
                                                @foreach($potentialAssignees as $assignee)
                                                    @if($assignee->id !== auth()->id())
                                                        <option value="{{ $assignee->id }}" x-show="deletingUserId != {{ $assignee->id }}">
                                                            {{ $assignee->name }} ({{ $assignee->empresa ?? ($assignee->tipo_usuario == 'superadmin' ? 'Admin' : 'Empresa') }})
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <p class="text-xs text-gray-500 mt-2">
                                                Si el usuario tiene tareas creadas, deben ser reasignadas para evitar errores.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Eliminar y Reasignar
                            </button>
                            <button type="button" @click="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine Data -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userManagement', () => ({
                isDeleteModalOpen: false,
                deletingUserId: null,
                deletingUserName: '',

                openDeleteModal(userId, userName) {
                    this.deletingUserId = userId;
                    this.deletingUserName = userName;
                    this.isDeleteModalOpen = true;
                },

                closeDeleteModal() {
                    this.isDeleteModalOpen = false;
                    setTimeout(() => {
                        this.deletingUserId = null;
                        this.deletingUserName = '';
                    }, 300);
                }
            }))
        })
    </script>
</x-app-layout>
