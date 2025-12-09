<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-[#0F172A] leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
            
            <!-- System Messages -->
            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">Tu perfil ha sido actualizado.</span>
                </div>
            @endif
            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">Tu contraseña ha sido actualizada.</span>
                </div>
            @endif

            <!-- INFORMATION SECTION (Personal Data Only) -->
            <div x-data="{ openProfileModal: false }">
                <h3 class="text-[#0976D6] font-medium text-lg mb-6">Información registrada</h3>
                
                <div class="border border-[#0976D6] rounded-xl p-8 bg-white relative">
                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Nombre y Apellido</label>
                            <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                {{ $user->name }}
                            </div>
                        </div>

                        <!-- Company (Static) -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Empresa</label>
                            <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                {{ $user->email }} <!-- Placeholder/Dual-purpose field as per design -->
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-center">
                        <button @click="openProfileModal = true" class="bg-[#0976D6] hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-full transition duration-150">
                            Editar información
                        </button>
                    </div>
                </div>

                <!-- PROFILE UPDATE MODAL -->
                <div x-show="openProfileModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Backdrop -->
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openProfileModal = false">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <!-- Modal Panel -->
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg leading-6 font-bold text-gray-900">
                                        Edita tu información personal
                                    </h3>
                                    <button @click="openProfileModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                                
                                <form method="post" action="{{ route('profile.update') }}" id="updateProfileForm" class="space-y-4">
                                    @csrf
                                    @method('patch')
                                    
                                    <!-- Visible Fields -->
                                    <div>
                                        <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nombre y Apellido</label>
                                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full bg-[#F3F4F6] border-none rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-3 px-4">
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>

                                    <div>
                                        <label for="company_dummy" class="block text-sm font-bold text-gray-700 mb-1">Empresa</label>
                                        <input type="text" id="company_dummy" value="{{ $user->email }}" readonly class="mt-1 block w-full bg-[#F3F4F6] border-none rounded-md shadow-sm text-gray-500 sm:text-sm py-3 px-4 cursor-not-allowed">
                                    </div>

                                    <!-- Hidden Required Field -->
                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                </form>
                            </div>
                            <div class="bg-white px-4 py-3 sm:px-6 sm:flex sm:flex-row justify-center gap-4 mb-4">
                                <button type="button" class="w-full inline-flex justify-center rounded-full border border-blue-500 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:text-sm min-w-[120px]" @click="openProfileModal = false">
                                    Descartar
                                </button>
                                <button type="submit" form="updateProfileForm" class="w-full inline-flex justify-center rounded-full border border-transparent shadow-sm px-4 py-2 bg-[#0976D6] text-base font-medium text-white hover:bg-blue-700 sm:w-auto sm:text-sm min-w-[150px]">
                                    Guardar cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCOUNT CONFIGURATION SECTION (Security: Email & Password) -->
            <div x-data="{ openEmailModal: false, openPasswordModal: false }">
                <h3 class="text-[#0F172A] font-extrabold text-2xl mb-2">Configuración de la cuenta</h3>
                <h4 class="text-[#0976D6] font-medium text-lg mb-6">Configuración de acceso</h4>

                <div class="border border-[#0976D6] rounded-xl p-8 bg-white">
                    <div class="space-y-6">
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Correo electrónico</label>
                            <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                {{ $user->email }}
                            </div>
                            <div class="mt-4 flex justify-center">
                                <button @click="openEmailModal = true" class="bg-[#0976D6] hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-full transition duration-150 text-sm">
                                    Cambiar correo electrónico
                                </button>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="pt-4 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-900 mb-2">Contraseña</label>
                            <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full tracking-widest">
                                **********
                            </div>
                            <div class="mt-4 text-center">
                                <span class="text-sm text-gray-900 font-bold">¿Olvidaste tu contraseña?</span>
                                <button @click="openPasswordModal = true" class="text-sm text-[#0976D6] font-bold hover:underline ml-1">
                                    Crea una nueva
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EMAIL UPDATE MODAL -->
                <div x-show="openEmailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openEmailModal = false">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white p-8">
                                <div class="flex justify-between items-start mb-6">
                                    <h3 class="text-xl font-bold text-gray-900">
                                        Cambio de correo electrónico
                                    </h3>
                                    <button @click="openEmailModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                                
                                <form method="post" action="{{ route('profile.update') }}" id="updateEmailForm" class="space-y-6">
                                    @csrf
                                    @method('patch')
                                    
                                    <div>
                                        <label for="email" class="block text-sm text-gray-600 mb-2">Introduce el nuevo correo electrónico</label>
                                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full bg-[#F3F4F6] border-none rounded-lg px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#0976D6] transition" required>
                                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                    </div>
                                    
                                    <input type="hidden" name="name" value="{{ $user->name }}">

                                    <div class="flex flex-row justify-center gap-4 pt-2">
                                        <button type="button" class="w-full sm:w-auto px-8 py-2.5 rounded-full border border-[#0976D6] text-[#0976D6] font-medium bg-white hover:bg-gray-50 focus:outline-none transition duration-150 min-w-[140px] text-center" @click="openEmailModal = false">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="w-full sm:w-auto px-8 py-2.5 rounded-full bg-[#0976D6] text-white font-medium hover:bg-blue-700 focus:outline-none transition duration-150 min-w-[140px] text-center">
                                            Confirmar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASSWORD UPDATE MODAL -->
                <div x-data="{ 
                    step: {{ $errors->has('code') || $errors->has('password') ? 2 : 1 }}, 
                    sending: false, 
                    errorMessage: '',
                    async sendCode() {
                        this.sending = true;
                        this.errorMessage = '';
                        try {
                            const response = await fetch('{{ route('profile.send-password-code') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            
                            if (response.ok) {
                                this.step = 2;
                            } else {
                                this.errorMessage = 'Hubo un error al enviar el código. Inténtalo de nuevo.';
                            }
                        } catch (error) {
                            this.errorMessage = 'Error de conexión.';
                        } finally {
                            this.sending = false;
                        }
                    }
                }" 
                x-show="openPasswordModal" 
                x-init="@if($errors->has('code') || $errors->has('password')) openPasswordModal = true; @endif"
                style="display: none;" 
                class="fixed inset-0 z-50 overflow-y-auto" 
                x-cloak>
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openPasswordModal = false">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white p-8">
                                <div class="flex justify-between items-start mb-6">
                                    <h3 class="text-xl font-bold text-gray-900">
                                        Cambio de contraseña
                                    </h3>
                                    <button @click="openPasswordModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                                
                                <!-- Step 1: Send Code -->
                                <div x-show="step === 1">
                                    <p class="text-gray-600 mb-6">
                                        Para tu seguridad, enviaremos un código de verificación a tu correo electrónico registrado ({{ substr($user->email, 0, 3) }}***{{ substr($user->email, strpos($user->email, '@')) }}).
                                    </p>
                                    
                                    <div x-show="errorMessage" class="mb-4 text-red-600 text-sm" x-text="errorMessage"></div>

                                    <div class="flex flex-row justify-center gap-4 pt-2">
                                        <button type="button" class="w-full sm:w-auto px-8 py-2.5 rounded-full border border-[#0976D6] text-[#0976D6] font-medium bg-white hover:bg-gray-50 focus:outline-none transition duration-150 min-w-[140px] text-center" @click="openPasswordModal = false">
                                            Cancelar
                                        </button>
                                        <button type="button" @click="sendCode()" :disabled="sending" class="w-full sm:w-auto px-8 py-2.5 rounded-full bg-[#0976D6] text-white font-medium hover:bg-blue-700 focus:outline-none transition duration-150 min-w-[140px] text-center disabled:opacity-50">
                                            <span x-show="!sending">Enviar código</span>
                                            <span x-show="sending">Enviando...</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 2: Verify and Change -->
                                <div x-show="step === 2">
                                    <form method="post" action="{{ route('profile.update-password-with-code') }}" id="updatePasswordForm" class="space-y-6">
                                        @csrf
                                        @method('put')
                                        
                                        <div>
                                            <label for="code" class="block text-sm text-gray-600 mb-2">Código de verificación</label>
                                            <input type="text" name="code" id="code" class="w-full bg-[#F3F4F6] border-none rounded-lg px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#0976D6] transition" placeholder="123456" required>
                                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label for="password" class="block text-sm text-gray-600 mb-2">Introduce la nueva contraseña</label>
                                            <input type="password" name="password" id="password" class="w-full bg-[#F3F4F6] border-none rounded-lg px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#0976D6] transition" required>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label for="password_confirmation" class="block text-sm text-gray-600 mb-2">Confirma la nueva contraseña</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full bg-[#F3F4F6] border-none rounded-lg px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#0976D6] transition" required>
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                        </div>

                                        <div class="flex flex-row justify-center gap-4 pt-2">
                                            <button type="button" class="w-full sm:w-auto px-8 py-2.5 rounded-full border border-[#0976D6] text-[#0976D6] font-medium bg-white hover:bg-gray-50 focus:outline-none transition duration-150 min-w-[140px] text-center" @click="openPasswordModal = false; step = 1">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="w-full sm:w-auto px-8 py-2.5 rounded-full bg-[#0976D6] text-white font-medium hover:bg-blue-700 focus:outline-none transition duration-150 min-w-[140px] text-center">
                                                Cambiar contraseña
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professionals Section -->

            <!-- Professionals Section -->
            @if(Auth::user()->tipo_usuario === 'empleador')
                <div x-data="{ openDeleteModal: false, openPromoteModal: false, selectedUser: null, actionUrl: '' }">
                    <h3 class="text-[#0976D6] font-medium text-lg mb-6">Profesionales registrados</h3>

                    <div class="bg-[#F3F4F6] rounded-xl p-6">
                        <!-- Header Row -->
                        <div class="grid grid-cols-12 gap-4 mb-4 px-4 text-sm font-bold text-black">
                            <div class="col-span-3">Nombre</div>
                            <div class="col-span-4">Email</div>
                            <div class="col-span-2">Roll</div>
                            <div class="col-span-3 text-center">Acciones</div>
                        </div>

                        <!-- List Items -->
                        <div class="space-y-3">
                            @foreach($empleados as $empleado)
                                <div class="bg-white rounded-lg border border-[#0976D6] shadow-sm p-4 grid grid-cols-12 gap-4 items-center">
                                    <div class="col-span-3 font-medium text-gray-900">{{ $empleado->name }}</div>
                                    <div class="col-span-4 text-gray-600 text-sm truncate">{{ $empleado->email }}</div>
                                    <div class="col-span-2 text-gray-900">{{ $empleado->is_manager ? 'Gerente' : 'Profesional' }}</div>
                                    <div class="col-span-3 flex justify-end items-center gap-3">
                                        @if(!$empleado->is_manager)
                                            <button 
                                                @click="selectedUser = '{{ $empleado->name }}'; actionUrl = '{{ route('profile.promover-manager', $empleado) }}'; openPromoteModal = true"
                                                class="text-[#0976D6] hover:underline text-sm font-medium">
                                                Promover a manager
                                            </button>
                                        @else
                                            <button 
                                                @click="selectedUser = '{{ $empleado->name }}'; actionUrl = '{{ route('profile.degradar-manager', $empleado) }}'; openPromoteModal = true"
                                                class="text-orange-600 hover:underline text-sm font-medium">
                                                Degradar
                                            </button>
                                        @endif
                                        
                                        <button 
                                            @click="selectedUser = '{{ $empleado->name }}'; actionUrl = '{{ route('profile.eliminar-empleado', $empleado) }}'; openDeleteModal = true"
                                            class="bg-[#EF4444] hover:bg-red-700 text-white text-xs font-bold py-1.5 px-4 rounded transition">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div x-show="openDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openDeleteModal = false">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                                <div class="absolute top-4 right-4">
                                    <button @click="openDeleteModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-center mt-4">
                                    <h3 class="text-xl font-bold text-gray-900 mb-8">
                                        ¿Estas seguro de que quieres<br>eliminar a este profesional?
                                    </h3>
                                    <div class="flex justify-center gap-4">
                                        <button @click="openDeleteModal = false" class="w-32 rounded-full border border-[#0976D6] py-2 text-[#0976D6] font-medium hover:bg-blue-50 transition">
                                            Cancelar
                                        </button>
                                        <form :action="actionUrl" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-32 rounded-full bg-[#0976D6] py-2 text-white font-medium hover:bg-blue-700 transition">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Promote Confirmation Modal -->
                    <div x-show="openPromoteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openPromoteModal = false">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                                <div class="absolute top-4 right-4">
                                    <button @click="openPromoteModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-center mt-4">
                                    <h3 class="text-xl font-bold text-gray-900 mb-8">
                                        ¿Estas seguro de que quieres<br>promover a este profesional?
                                    </h3>
                                    <div class="flex justify-center gap-4">
                                        <button @click="openPromoteModal = false" class="w-32 rounded-full border border-[#0976D6] py-2 text-[#0976D6] font-medium hover:bg-blue-50 transition">
                                            Cancelar
                                        </button>
                                        <form :action="actionUrl" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="w-32 rounded-full bg-[#0976D6] py-2 text-white font-medium hover:bg-blue-700 transition">
                                                Promover
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-app-layout>