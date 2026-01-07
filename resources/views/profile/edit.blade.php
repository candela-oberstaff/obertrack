<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-[#0F172A] leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
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

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Aviso:</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if (auth()->user()->tipo_usuario === 'empleado' && (empty($user->phone_number) || empty($user->location)))
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700 font-bold">
                                Perfil incompleto
                            </p>
                            <p class="text-sm text-amber-700">
                                Debes completar tu <strong>teléfono</strong> y <strong>ubicación</strong> para que se habilite la opción de registrar horas.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- INFORMATION SECTION (Personal Data Only) -->
            <div id="profile-personal-info" x-data="{ openProfileModal: false }">
                <h3 class="text-[#22A9C8] font-medium text-lg mb-6">Información registrada</h3>
                
                <div class="border border-[#22A9C8] rounded-xl p-8 bg-white relative">
                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Nombre y Apellido</label>
                            <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                {{ $user->name }}
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Correo electrónico</label>
                            <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                {{ $user->email }}
                            </div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Teléfono</label>
                            <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                {{ $user->phone_number ?? 'No registrado' }}
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Ubicación</label>
                            <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                {{ $user->location ?? 'No registrado' }}
                            </div>
                        </div>

                        @if($user->tipo_usuario === 'empleador')
                            <!-- Company Name -->
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Nombre de Empresa</label>
                                <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                    {{ $user->company_name ?? 'No registrado' }}
                                </div>
                            </div>

                            <!-- Related Contact -->
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Contacto Relacionado</label>
                                <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                    {{ $user->related_contact ?? 'No registrado' }}
                                </div>
                            </div>

                            <!-- Country -->
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">País</label>
                                <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                    {{ $user->country ?? 'No registrado' }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 flex justify-center">
                        <button @click="openProfileModal = true" class="bg-[#22A9C8] hover:bg-primary-hover text-white font-medium py-2.5 px-6 rounded-full transition duration-150">
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
                                
                                <form method="post" action="{{ route('profile.update') }}" id="updateProfileForm" class="space-y-4" enctype="multipart/form-data">
                                    @csrf
                                    @method('patch')
                                    
                                    <!-- Avatar Upload -->
                                    <div class="flex flex-col items-center mb-6">
                                        <div class="relative group">
                                            <x-user-avatar :user="$user" size="24" classes="border-4 border-primary/20 shadow-lg" />
                                            <label for="avatar" class="absolute bottom-0 right-0 bg-[#22A9C8] text-white p-2 rounded-full cursor-pointer shadow-md hover:bg-primary-hover transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </label>
                                            <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">Haz clic en la cámara para cambiar tu foto</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                                    </div>
                                    
                                    <!-- Visible Fields -->
                                    <div>
                                        <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nombre y Apellido</label>
                                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full bg-[#F3F4F6] border-none rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm py-3 px-4">
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>

                                    <!-- Phone -->
                                    <div>
                                        <label for="phone_number" class="block text-sm font-bold text-gray-700 mb-1">Teléfono</label>
                                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="mt-1 block w-full bg-[#F3F4F6] border-none rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm py-3 px-4">
                                        <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                                    </div>

                                    <!-- Location -->
                                    <div>
                                        <label for="location" class="block text-sm font-bold text-gray-700 mb-1">Ubicación</label>
                                        <input type="text" name="location" id="location" value="{{ old('location', $user->location) }}" class="mt-1 block w-full bg-[#F3F4F6] border-none rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm py-3 px-4">
                                        <x-input-error class="mt-2" :messages="$errors->get('location')" />
                                    </div>

                                    @if($user->tipo_usuario === 'empleador')
                                        <!-- Company Name -->
                                        <div>
                                            <label for="company_name" class="block text-sm font-bold text-gray-700 mb-1">Nombre de Empresa</label>
                                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $user->company_name) }}" class="mt-1 block w-full bg-[#F3F4F6] border-none rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm py-3 px-4">
                                            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                                        </div>

                                        <!-- Related Contact -->
                                        <div>
                                            <label for="related_contact" class="block text-sm font-bold text-gray-700 mb-1">Contacto Relacionado</label>
                                            <input type="text" name="related_contact" id="related_contact" value="{{ old('related_contact', $user->related_contact) }}" class="mt-1 block w-full bg-[#F3F4F6] border-none rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm py-3 px-4">
                                            <x-input-error class="mt-2" :messages="$errors->get('related_contact')" />
                                        </div>

                                        <!-- Country -->
                                        <div>
                                            <label for="country" class="block text-sm font-bold text-gray-700 mb-1">País</label>
                                            <input type="text" name="country" id="country" value="{{ old('country', $user->country) }}" class="mt-1 block w-full bg-[#F3F4F6] border-none rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm py-3 px-4">
                                            <x-input-error class="mt-2" :messages="$errors->get('country')" />
                                        </div>
                                    @endif

                                    <!-- Hidden Required Field -->
                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                </form>
                            </div>
                            <div class="bg-white px-4 py-3 sm:px-6 sm:flex sm:flex-row justify-center gap-4 mb-4">
                                <button type="button" class="w-full inline-flex justify-center rounded-full border border-primary shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:text-sm min-w-[120px]" @click="openProfileModal = false">
                                    Descartar
                                </button>
                                <button type="submit" form="updateProfileForm" class="w-full inline-flex justify-center rounded-full border border-transparent shadow-sm px-4 py-2 bg-[#22A9C8] text-base font-medium text-white hover:bg-primary-hover sm:w-auto sm:text-sm min-w-[150px]">
                                    Guardar cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCOUNT CONFIGURATION SECTION (Security: Email & Password) -->
            <div id="profile-account-config" x-data="{ openEmailModal: false, openPasswordModal: false }">
                <h3 class="text-[#0F172A] font-extrabold text-2xl mb-2">Configuración de la cuenta</h3>
                <h4 class="text-[#22A9C8] font-medium text-lg mb-6">Configuración de acceso</h4>

                <div class="border border-[#22A9C8] rounded-xl p-8 bg-white">
                    <div class="space-y-6">
                        
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Correo electrónico</label>
                            <div class="bg-[#F3F4F6] text-gray-700 rounded-lg p-3 w-full">
                                {{ $user->email }}
                            </div>
                            <div class="mt-4 flex justify-center">
                                <button @click="openEmailModal = true" class="bg-[#22A9C8] hover:bg-primary-hover text-white font-medium py-2 px-6 rounded-full transition duration-150 text-sm">
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
                                <button @click="openPasswordModal = true" class="text-sm text-[#22A9C8] font-bold hover:underline ml-1">
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
                                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full bg-[#F3F4F6] border-none rounded-lg px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#22A9C8] transition" required>
                                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                    </div>
                                    
                                    <input type="hidden" name="name" value="{{ $user->name }}">

                                    <div class="flex flex-row justify-center gap-4 pt-2">
                                        <button type="button" class="w-full sm:w-auto px-8 py-2.5 rounded-full border border-[#22A9C8] text-[#22A9C8] font-medium bg-white hover:bg-gray-50 focus:outline-none transition duration-150 min-w-[140px] text-center" @click="openEmailModal = false">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="w-full sm:w-auto px-8 py-2.5 rounded-full bg-[#22A9C8] text-white font-medium hover:bg-primary-hover focus:outline-none transition duration-150 min-w-[140px] text-center">
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
                                        <button type="button" class="w-full sm:w-auto px-8 py-2.5 rounded-full border border-[#22A9C8] text-[#22A9C8] font-medium bg-white hover:bg-gray-50 focus:outline-none transition duration-150 min-w-[140px] text-center" @click="openPasswordModal = false">
                                            Cancelar
                                        </button>
                                        <button type="button" @click="sendCode()" :disabled="sending" class="w-full sm:w-auto px-8 py-2.5 rounded-full bg-[#22A9C8] text-white font-medium hover:bg-primary-hover focus:outline-none transition duration-150 min-w-[140px] text-center disabled:opacity-50">
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
                                            <input type="text" name="code" id="code" class="w-full bg-[#F3F4F6] border-none rounded-lg px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#22A9C8] transition" placeholder="123456" required>
                                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label for="password" class="block text-sm text-gray-600 mb-2">Introduce la nueva contraseña</label>
                                            <input type="password" name="password" id="password" class="w-full bg-[#F3F4F6] border-none rounded-lg px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#22A9C8] transition" required>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>

                                        <div>
                                            <label for="password_confirmation" class="block text-sm text-gray-600 mb-2">Confirma la nueva contraseña</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full bg-[#F3F4F6] border-none rounded-lg px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#22A9C8] transition" required>
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                        </div>

                                        <div class="flex flex-row justify-center gap-4 pt-2">
                                            <button type="button" class="w-full sm:w-auto px-8 py-2.5 rounded-full border border-[#22A9C8] text-[#22A9C8] font-medium bg-white hover:bg-gray-50 focus:outline-none transition duration-150 min-w-[140px] text-center" @click="openPasswordModal = false; step = 1">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="w-full sm:w-auto px-8 py-2.5 rounded-full bg-[#22A9C8] text-white font-medium hover:bg-primary-hover focus:outline-none transition duration-150 min-w-[140px] text-center">
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
                <div id="profile-professionals-list" x-data="{ openDeleteModal: false, openPromoteModal: false, selectedUser: null, actionUrl: '' }">
                    <h3 class="text-[#22A9C8] font-medium text-lg mb-6">Profesionales registrados</h3>

                    <div class="bg-[#F3F4F6] rounded-xl p-6">
                        <!-- Header Row -->
                        <div class="hidden md:grid grid-cols-12 gap-4 mb-4 px-4 text-sm font-bold text-black">
                            <div class="col-span-3">Nombre</div>
                            <div class="col-span-4">Email</div>
                            <div class="col-span-2">Roll</div>
                            <div class="col-span-3 text-center">Acciones</div>
                        </div>

                        <!-- List Items -->
                        <div class="space-y-3">
                            @foreach($empleados as $empleado)
                                <div class="bg-white rounded-lg border border-[#22A9C8] shadow-sm p-4 flex flex-col md:grid md:grid-cols-12 gap-2 md:gap-4 items-start md:items-center">
                                    <div class="w-full md:col-span-3 font-medium text-gray-900">
                                        <span class="md:hidden text-xs text-gray-500 block">Nombre</span>
                                        {{ $empleado->name }}
                                    </div>
                                    <div class="w-full md:col-span-4 text-gray-600 text-sm truncate">
                                        <span class="md:hidden text-xs text-gray-500 block">Email</span>
                                        {{ $empleado->email }}
                                    </div>
                                    <div class="w-full md:col-span-2 text-gray-900">
                                        <span class="md:hidden text-xs text-gray-500 block">Rol</span>
                                        {{ $empleado->is_manager ? 'Manager' : 'Profesional' }}
                                    </div>
                                    <div class="w-full md:col-span-3 flex justify-start md:justify-end items-center gap-3 mt-2 md:mt-0">
                                        @if(!$empleado->is_manager)
                                            <button 
                                                @click="selectedUser = '{{ $empleado->name }}'; actionUrl = '{{ route('profile.promover-manager', $empleado) }}'; openPromoteModal = true"
                                                class="text-[#22A9C8] hover:underline text-sm font-medium">
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
                                        <button @click="openDeleteModal = false" class="w-32 rounded-full border border-[#22A9C8] py-2 text-[#22A9C8] font-medium hover:bg-blue-50 transition">
                                            Cancelar
                                        </button>
                                        <form :action="actionUrl" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-32 rounded-full bg-[#22A9C8] py-2 text-white font-medium hover:bg-primary-hover transition">
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
                                        <button @click="openPromoteModal = false" class="w-32 rounded-full border border-[#22A9C8] py-2 text-[#22A9C8] font-medium hover:bg-blue-50 transition">
                                            Cancelar
                                        </button>
                                        <form :action="actionUrl" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="w-32 rounded-full bg-[#22A9C8] py-2 text-white font-medium hover:bg-primary-hover transition">
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

            <!-- DANGER ZONE SECTION -->
            <div id="profile-danger-zone" x-data="{ openDeleteAccountModal: false }">
                <h3 class="text-[#22A9C8] font-medium text-lg mb-6">Zona peligrosa</h3>
                
                <div class="bg-[#F3F4F6] rounded-xl p-8">
                    <div class="space-y-4">
                        <h4 class="text-xl font-bold text-gray-900">Eliminar cuenta</h4>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Una vez que se elimine tu cuenta, todos sus recursos y datos serán eliminados permanentemente. Antes de eliminar tu cuenta, por favor descarga cualquier dato o información que desees conservar.
                        </p>
                        <div class="pt-4">
                            <button 
                                @click="openDeleteAccountModal = true" 
                                class="bg-[#EF4444] hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg transition duration-150">
                                Eliminar cuenta
                            </button>
                        </div>
                    </div>
                </div>

                <!-- DELETE ACCOUNT CONFIRMATION MODAL -->
                <div x-show="openDeleteAccountModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Backdrop -->
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openDeleteAccountModal = false">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <!-- Modal Panel -->
                        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                            <div class="bg-white px-6 pt-6 pb-4">
                                <div class="text-center">
                                    <h3 class="text-lg font-bold text-gray-900 mb-6">
                                        ¿Estás seguro de que deseas<br>eliminar tu cuenta?
                                    </h3>
                                    
                                    <form method="post" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                                        @csrf
                                        @method('delete')
                                        
                                        <div class="flex justify-center gap-3 mt-6 pb-2">
                                            <button 
                                                type="button" 
                                                @click="openDeleteAccountModal = false" 
                                                class="w-32 rounded-lg border-2 border-gray-300 py-2 text-gray-700 font-medium hover:bg-gray-50 transition">
                                                Cancelar
                                            </button>
                                            <button 
                                                type="submit" 
                                                class="w-32 rounded-lg bg-[#EF4444] py-2 text-white font-medium hover:bg-red-700 transition">
                                                Eliminar cuenta
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>