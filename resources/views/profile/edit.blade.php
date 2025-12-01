<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if(Auth::user()->tipo_usuario === 'empleador')
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Mis Profesionales') }}
                        </h2>
                        <table class="min-w-full divide-y divide-gray-200 mt-4">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                                @foreach($empleados as $empleado)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $empleado->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $empleado->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $empleado->is_manager ? 'Manager' : 'Empleado' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($empleado->is_manager)
                                                <form action="{{ route('profile.degradar-manager', $empleado) }}" method="POST" class="mb-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Degradar de Manager</button>
                                                </form>
                                            @else
                                                <form action="{{ route('profile.promover-manager', $empleado) }}" method="POST" class="mb-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Promover a Manager</button>
                                                </form>
                                            @endif

                                            @if($empleado->is_manager)
                                                <form action="{{ route('profile.toggle-superadmin', $empleado) }}" method="POST" class="mb-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-purple-600 hover:text-purple-900">
                                                        {{ $empleado->is_superadmin ? 'Quitar SuperAdmin' : 'Hacer SuperAdmin' }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('profile.eliminar-empleado', $empleado) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este empleado?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>