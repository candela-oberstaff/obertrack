<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Hemos enviado un código de verificación a tu correo electrónico. Ingrésalo a continuación para continuar. El código es válido por 15 minutos.') }}
    </div>

    <form method="POST" action="{{ route('password.verify-code') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
            <x-input-label for="code" :value="__('Código de Verificación')" />
            <x-text-input id="code" class="block mt-1 w-full text-center text-2xl tracking-widest" type="text" name="code" required autofocus maxlength="6" placeholder="000000" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verificar Código') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
