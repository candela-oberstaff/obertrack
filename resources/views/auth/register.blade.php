<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Obertrack</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Alpine JS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
          theme: {
            extend: {
              fontFamily: { 
                sans: ['Space Grotesk', 'sans-serif'],
                poppins: ['Poppins', 'sans-serif']
              },
              colors: {
                brandBlue: '#22A9C8',
                brandBlueDark: '#0D5C7D',
                brandBlack: '#1B1725',
                brandGray: '#F3F4F6',
                brutalYellow: '#FFDE59',
                brutalRed: '#FF5A5F',
                brutalGreen: '#00D4AA',
                brutalPurple: '#9D4EDD'
              }
            }
          }
        }
    </script>
    <style>
        body { font-family: 'Space Grotesk', sans-serif; }
        
        .graphic-grid {
            background-image: 
              linear-gradient(to right, rgba(27, 23, 37, 0.05) 1px, transparent 1px),
              linear-gradient(to bottom, rgba(27, 23, 37, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            background-color: #FFFFFF;
        }

        .brutal-card {
            border: 3px solid #1B1725 !important;
            background: #FFFFFF !important;
            box-shadow: 6px 6px 0px 0px #1B1725 !important;
            position: relative;
        }

        .brutal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: #22A9C8;
        }

        .brutal-input {
            border: 2px solid #1B1725 !important;
            background: #FFFFFF !important;
            box-shadow: 4px 4px 0px 0px rgba(27, 23, 37, 0.2) !important;
            transition: all 0.2s ease !important;
            border-radius: 0.5rem;
        }

        .brutal-input:focus {
            outline: none;
            box-shadow: 6px 6px 0px 0px #22A9C8 !important;
            transform: translate(-2px, -2px);
        }

        .brutal-button {
            border: 3px solid #1B1725 !important;
            box-shadow: 4px 4px 0px 0px #1B1725 !important;
            transition: all 0.2s ease !important;
        }

        .brutal-button:hover {
            transform: translate(2px, 2px) !important;
            box-shadow: 2px 2px 0px 0px #1B1725 !important;
        }

        .brutal-select {
            border: 2px solid #1B1725 !important;
            background: #FFFFFF url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%231B1725' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat right 0.75rem center !important;
            box-shadow: 4px 4px 0px 0px rgba(27, 23, 37, 0.2) !important;
            transition: all 0.2s ease !important;
            border-radius: 0.5rem;
            appearance: none;
        }

        .brutal-select:focus {
            outline: none;
            box-shadow: 6px 6px 0px 0px #22A9C8 !important;
            transform: translate(-2px, -2px);
        }

        .brutal-select option {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body class="min-h-screen graphic-grid flex items-center justify-center py-8 px-4">

    <!-- Card Container -->
    <div class="w-full max-w-sm brutal-card rounded-xl p-8">
        
        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <x-application-logo class="block h-16 w-auto" />
        </div>

        <h2 class="text-2xl font-extrabold text-center text-brandBlack mb-6 uppercase tracking-tight">
            Regístrate
        </h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Nombre</label>
                <input id="name" name="name" type="text" placeholder="Nombre y apellido" required autofocus
                       class="w-full brutal-input py-2.5 px-4 text-brandBlack placeholder-gray-400"
                       value="{{ old('name') }}">
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs font-bold text-brutalRed" />
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Email</label>
                <input id="email" name="email" type="email" placeholder="ejemplo@obertrack.com" required
                       class="w-full brutal-input py-2.5 px-4 text-brandBlack placeholder-gray-400"
                       value="{{ old('email') }}">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-bold text-brutalRed" />
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Contraseña</label>
                <input id="password" name="password" type="password" placeholder="••••••••" required
                       class="w-full brutal-input py-2.5 px-4 text-brandBlack placeholder-gray-400">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-bold text-brutalRed" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Confirmar Contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" placeholder="••••••••" required
                       class="w-full brutal-input py-2.5 px-4 text-brandBlack placeholder-gray-400">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs font-bold text-brutalRed" />
            </div>

            <!-- Phone & Country (Shared) -->
            <div x-data="{
                prefixes: [
                    { name: 'Argentina', code: '54', flag: '🇦🇷' },
                    { name: 'España', code: '34', flag: '🇪🇸' },
                    { name: 'Venezuela', code: '58', flag: '🇻🇪' },
                    { name: 'Puerto Rico', code: '1787', flag: '🇵🇷' },
                    { name: 'Puerto Rico (Alt)', code: '1939', flag: '🇵🇷' },
                    { name: 'Colombia', code: '57', flag: '🇨🇴' },
                    { name: 'México', code: '52', flag: '🇲🇽' },
                    { name: 'Chile', code: '56', flag: '🇨🇱' },
                    { name: 'Uruguay', code: '598', flag: '🇺🇾' },
                    { name: 'Ecuador', code: '593', flag: '🇪🇨' },
                    { name: 'Perú', code: '51', flag: '🇵🇪' },
                    { name: 'EE.UU.', code: '1', flag: '🇺🇸' },
                    { name: 'Bolivia', code: '591', flag: '🇧🇴' },
                    { name: 'Paraguay', code: '595', flag: '🇵🇾' },
                    { name: 'Rep. Dominicana', code: '1', flag: '🇩🇴' },
                    { name: 'Otro', code: '', flag: '🌐' }
                ],
                selectedPrefix: '{{ old('selected_prefix', '') }}',
                localNumber: '{{ old('local_number', '') }}',
                fullNumber: '{{ old('phone_number', '') }}',
                init() {
                    if (this.fullNumber && !this.selectedPrefix) {
                        let cleanFull = this.fullNumber.replace('+', '').trim();
                        let sortedPrefixes = [...this.prefixes]
                            .filter(p => p.code !== '')
                            .sort((a,b) => b.code.length - a.code.length);
                        for (let p of sortedPrefixes) {
                            if (cleanFull.startsWith(p.code)) {
                                this.selectedPrefix = p.code;
                                this.localNumber = cleanFull.substring(p.code.length);
                                return;
                            }
                        }
                    }
                },
                updateFull() {
                    let cleanLocal = this.localNumber.replace(/\D/g, '');
                    this.fullNumber = this.selectedPrefix ? '+' + this.selectedPrefix + cleanLocal : cleanLocal;
                }
            }" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Teléfono</label>
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <select x-model="selectedPrefix" @change="updateFull()" class="w-full brutal-select py-2.5 px-3 text-brandBlack text-xs pr-8">
                                <option value="">País</option>
                                <template x-for="p in prefixes" :key="p.name + p.code">
                                    <option :value="p.code" x-text="p.flag + ' +' + p.code"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex-1">
                            <input type="text" x-model="localNumber" @input="updateFull()" placeholder="Número" class="w-full brutal-input py-2.5 px-4 text-brandBlack placeholder-gray-400">
                        </div>
                    </div>
                    <input type="hidden" name="phone_number" :value="fullNumber">
                    <input type="hidden" name="selected_prefix" :value="selectedPrefix">
                    <input type="hidden" name="local_number" :value="localNumber">
                    <p class="mt-1 text-[10px] text-gray-500 ml-1">Formato: <span x-text="fullNumber || 'No ingresado'"></span></p>
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2 text-xs font-bold text-brutalRed" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">País de Residencia</label>
                    <input id="country" name="country" type="text" placeholder="Venezuela, España, etc."
                           class="w-full brutal-input py-2.5 px-4 text-brandBlack placeholder-gray-400"
                           value="{{ old('country') }}" required>
                    <x-input-error :messages="$errors->get('country')" class="mt-2 text-xs font-bold text-brutalRed" />
                </div>
            </div>

            <!-- User Type -->
            <div>
                <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Tipo de Usuario</label>
                <select id="tipo_usuario" name="tipo_usuario" required
                        class="w-full brutal-select py-2.5 px-4 text-brandBlack">
                    <option value="" disabled selected>Selecciona una opción</option>
                    <option value="empleador">Empresa</option>
                    <option value="empleado">Profesional</option>
                </select>
                <x-input-error :messages="$errors->get('tipo_usuario')" class="mt-2 text-xs font-bold text-brutalRed" />
            </div>
            
            <!-- Job Title (Hidden by default) -->
            <div id="job_title_container" class="hidden">
                <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Cargo / Profesión</label>
                <input id="job_title" name="job_title" type="text" placeholder="Ej: Desarrollador, Diseñador..."
                       class="w-full brutal-input py-2.5 px-4 text-brandBlack placeholder-gray-400"
                       value="{{ old('job_title') }}">
                <x-input-error :messages="$errors->get('job_title')" class="mt-2 text-xs font-bold text-brutalRed" />
            </div>

            <!-- Company Specific Fields (Hidden by default) -->
            <div id="company_fields_container" class="hidden space-y-4">
                <div>
                    <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Nombre de Empresa</label>
                    <input id="company_name" name="company_name" type="text" placeholder="Tu empresa S.A."
                           class="w-full brutal-input py-2.5 px-4 text-brandBlack placeholder-gray-400"
                           value="{{ old('company_name') }}">
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2 text-xs font-bold text-brutalRed" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Contacto Relacionado</label>
                    <input id="related_contact" name="related_contact" type="text" placeholder="Nombre del contacto"
                           class="w-full brutal-input py-2.5 px-4 text-brandBlack placeholder-gray-400"
                           value="{{ old('related_contact') }}">
                    <x-input-error :messages="$errors->get('related_contact')" class="mt-2 text-xs font-bold text-brutalRed" />
                </div>
            </div>

            <!-- Employer Selection (Hidden by default) -->
            <div id="empleado_por_id_container" class="hidden">
                <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Selecciona tu Empresa</label>
                <select name="empleado_por_id" id="empleado_por_id"
                        class="w-full brutal-select py-2.5 px-4 text-brandBlack">
                    <option value="">Selecciona tu empresa</option>
                    @foreach ($empleadores as $empleadorId => $nombreEmpleador)
                        <option value="{{ $empleadorId }}">{{ $nombreEmpleador }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('empleado_por_id')" class="mt-2 text-xs font-bold text-brutalRed" />
            </div>

            <!-- Messages -->
            <p id="msg_empleado" class="hidden text-xs text-gray-600 text-center transition-all duration-300 font-poppins">
                Recibirás notificaciones por WhatsApp relacionadas con tus tareas.
            </p>
            <p id="msg_empleador" class="hidden text-xs text-gray-600 text-center transition-all duration-300 font-poppins">
                Recibirás notificaciones por WhatsApp relacionadas con tus profesionales.
            </p>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-brandBlue text-white font-extrabold uppercase tracking-wide py-3 px-4 rounded-lg brutal-button">
                    Registrarse
                </button>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600 font-medium">
                    ¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-brandBlue hover:text-brandBlueDark font-bold underline decoration-2">Inicia sesión</a>
                </p>
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
                    $('#company_fields_container').slideUp(200);
                    $('#msg_empleado').removeClass('hidden');
                    $('#msg_empleador').addClass('hidden');
                } else if (selectedValue === 'empleador') {
                    $('#empleado_por_id_container').slideUp(200);
                    $('#job_title_container').slideUp(200);
                    $('#company_fields_container').slideDown(200);
                    $('#msg_empleado').addClass('hidden');
                    $('#msg_empleador').removeClass('hidden');
                } else {
                    $('#empleado_por_id_container').slideUp(200);
                    $('#job_title_container').slideUp(200);
                    $('#company_fields_container').slideUp(200);
                    $('#msg_empleado').addClass('hidden');
                    $('#msg_empleador').addClass('hidden');
                }
            });
            // Trigger change on load if value is pre-selected
            var currentVal = $('#tipo_usuario').val();
            if(currentVal === 'empleado') {
                 $('#empleado_por_id_container').show();
                 $('#job_title_container').show();
                 $('#company_fields_container').hide();
                 $('#msg_empleado').removeClass('hidden');
                 $('#msg_empleador').addClass('hidden');
            } else if(currentVal === 'empleador') {
                 $('#empleado_por_id_container').hide();
                 $('#job_title_container').hide();
                 $('#company_fields_container').show();
                 $('#msg_empleado').addClass('hidden');
                 $('#msg_empleador').removeClass('hidden');
            }
        });
    </script>
</body>
</html>