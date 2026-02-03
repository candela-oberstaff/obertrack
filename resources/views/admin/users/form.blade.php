<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($user) ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-6 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al listado
            </a>

            <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-gray-100 p-8">
                <form action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @if(isset($user))
                        @method('PUT')
                    @endif

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required autofocus
                               class="w-full border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-800">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required
                               class="w-full border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-800">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="tipo_usuario" class="block text-sm font-bold text-gray-700 mb-1">Rol de Usuario</label>
                        <select name="tipo_usuario" id="tipo_usuario" required
                                class="w-full border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-800">
                            <option value="">Selecciona un rol</option>
                            <option value="empleador" {{ (old('tipo_usuario', $user->tipo_usuario ?? '') == 'empleador') ? 'selected' : '' }}>Empresa</option>
                            <option value="empleado" {{ (old('tipo_usuario', $user->tipo_usuario ?? '') == 'empleado') ? 'selected' : '' }}>Profesional</option>
                            <option value="superadmin" {{ (old('tipo_usuario', $user->tipo_usuario ?? '') == 'superadmin') ? 'selected' : '' }}>Superadmin</option>
                        </select>
                        @error('tipo_usuario')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Job Title -->
                    <div>
                        <label for="job_title" class="block text-sm font-bold text-gray-700 mb-1">Cargo</label>
                        <input type="text" name="job_title" id="job_title" value="{{ old('job_title', $user->job_title ?? '') }}"
                               class="w-full border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-800">
                        @error('job_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company (Employer) -->
                    <div>
                        <label for="empleador_id" class="block text-sm font-bold text-gray-700 mb-1">Empresa</label>
                        <select name="empleador_id" id="empleador_id"
                                class="w-full border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-800">
                            <option value="">Ninguna</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ (old('empleador_id', $user->empleador_id ?? '') == $company->id) ? 'selected' : '' }}>
                                    {{ $company->company_name ?? $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('empleador_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone_number" class="block text-sm font-bold text-gray-700 mb-1">Teléfono (Opcional)</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number ?? '') }}"
                               class="w-full border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-800">
                        @error('phone_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Section -->
                    <div class="pt-4 border-t border-gray-100">
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-1">
                            {{ isset($user) ? 'Contraseña (Dejar en blanco para mantener la actual)' : 'Contraseña' }}
                        </label>
                        <input type="password" name="password" id="password" {{ isset($user) ? '' : 'required' }}
                               class="w-full border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-800">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" {{ isset($user) ? '' : 'required' }}
                               class="w-full border-gray-200 rounded-lg focus:ring-[#22A9C8] font-medium text-gray-800">
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-8 py-3 bg-[#22A9C8] text-white rounded-full font-bold shadow-lg shadow-[#22A9C8]/20 hover:bg-[#1b8fa8] transition-all transform hover:scale-105">
                            {{ isset($user) ? 'Actualizar Usuario' : 'Crear Usuario' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
