<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Obertrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .bg-gradient-brand {
            background: linear-gradient(180deg, #FFFFFF 0%, #E0F7FA 40%, #22A9C8 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }
        .form-input-custom {
            background: white;
            border: none;
            border-radius: 8px;
            padding: 10px 14px; /* Reduced vertical padding */
            font-size: 0.95rem;
            color: #6B7280; /* Text-gray-500 */
        }
        .form-input-custom::placeholder {
            color: #D1D5DB; /* Gray-300 */
        }
        .form-input-custom:focus {
            ring: 2px;
            ring-color: #22A9C8;
        }
        /* Custom scrollbar handling for modal if needed on small screens */
        .glass-card {
            max-height: 95vh;
            overflow-y: auto;
        }
    </style>
</head>
<body class="h-full bg-gradient-brand flex items-center justify-center p-4">

    <!-- Reduced padding p-6 sm:p-8 and max-w-sm -->
    <div class="w-full max-w-sm glass-card rounded-3xl p-6 sm:p-8">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-4">
            <x-application-logo class="block h-10 w-auto fill-current text-gray-800 mb-1" />
            <div class="text-center">
                <span class="font-bold text-xl tracking-tight text-gray-900 leading-none">Obertrack</span>
                <span class="text-[0.5rem] font-bold tracking-widest text-gray-500 uppercase leading-none block mt-0.5">REMOTE WORK TRACKING</span>
            </div>
        </div>

        <h2 class="text-xl font-bold text-center text-[#1e1b4b] mb-4">
            Regístrate en Obertrack
        </h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-3">
            @csrf

            <!-- Name -->
            <div>
                <input id="name" name="name" type="text" placeholder="Nombre y apellido" required autofocus
                       class="w-full form-input-custom focus:outline-none focus:ring-2 focus:ring-primary transition shadow-sm"
                       value="{{ old('name') }}">
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
            </div>

            <!-- Email -->
            <div>
                <input id="email" name="email" type="email" placeholder="Email" required
                       class="w-full form-input-custom focus:outline-none focus:ring-2 focus:ring-primary transition shadow-sm"
                       value="{{ old('email') }}">
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
            </div>

            <!-- Password -->
            <div>
                <input id="password" name="password" type="password" placeholder="Contraseña" required
                       class="w-full form-input-custom focus:outline-none focus:ring-2 focus:ring-primary transition shadow-sm">
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            <!-- Confirm Password -->
            <div>
                <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirma la contraseña" required
                       class="w-full form-input-custom focus:outline-none focus:ring-2 focus:ring-primary transition shadow-sm">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
            </div>

            <!-- User Type -->
            <div>
                <select id="tipo_usuario" name="tipo_usuario" required
                        class="w-full form-input-custom focus:outline-none focus:ring-2 focus:ring-primary transition shadow-sm text-gray-600 appearance-none">
                    <option value="" disabled selected>Tipo de usuario</option>
                    <option value="empleador">Empresa</option>
                    <option value="empleado">Profesional</option>
                </select>
                <x-input-error :messages="$errors->get('tipo_usuario')" class="mt-1 text-xs" />
            </div>
            
            <!-- Job Title (Hidden by default) -->
            <div id="job_title_container" class="hidden">
                 <input id="job_title" name="job_title" type="text" placeholder="Cargo / Profesión"
                       class="w-full form-input-custom focus:outline-none focus:ring-2 focus:ring-primary transition shadow-sm"
                       value="{{ old('job_title') }}">
                <x-input-error :messages="$errors->get('job_title')" class="mt-1 text-xs" />
            </div>

            <!-- Employer Selection (Hidden by default) -->
            <div id="empleado_por_id_container" class="hidden">
                 <select name="empleado_por_id" id="empleado_por_id"
                        class="w-full form-input-custom focus:outline-none focus:ring-2 focus:ring-primary transition shadow-sm text-gray-600 appearance-none">
                    <option value="">Selecciona tu empresa</option>
                    @foreach ($empleadores as $empleadorId => $nombreEmpleador)
                        <option value="{{ $empleadorId }}">{{ $nombreEmpleador }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('empleado_por_id')" class="mt-1 text-xs" />
            </div>

            <!-- Footer & Button -->
            <div class="pt-2 text-center">
                <p class="text-xs font-semibold text-gray-600 mb-4">
                    ¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-primary hover:text-primary-hover underline">Inicia sesión</a>
                </p>

                <button type="submit" class="bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-10 rounded-full transition duration-300 shadow-lg w-auto inline-block text-sm">
                    Registrarse
                </button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#tipo_usuario').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'empleado') {
                    $('#empleado_por_id_container').slideDown(200);
                    $('#job_title_container').slideDown(200);
                } else {
                    $('#empleado_por_id_container').slideUp(200);
                    $('#job_title_container').slideUp(200);
                }
            });
            // Trigger change on load if value is pre-selected
            var currentVal = $('#tipo_usuario').val();
            if(currentVal === 'empleado') {
                 $('#empleado_por_id_container').show();
                 $('#job_title_container').show();
            }
        });
    </script>
</body>
</html>