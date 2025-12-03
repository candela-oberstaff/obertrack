<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Obertrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                    },
                    colors: {
                        'brand-purple': '#1e1b4b',
                        'brand-indigo': '#3b82f6',
                        'brand-light': '#ffffff',
                    }
                }
            }
        }
    </script>
    <style>
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        @keyframes gradient {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 15s ease infinite;
        }
        @keyframes blob {
            0% {transform: translate(0px, 0px) scale(1);}
            33% {transform: translate(30px, -50px) scale(1.1);}
            66% {transform: translate(-20px, 20px) scale(0.9);}
            100% {transform: translate(0px, 0px) scale(1);}
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
    </style>
</head>
<body class="font-sans bg-brand-purple text-brand-light min-h-screen">
    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 animate-gradient bg-gradient-to-br from-brand-purple via-brand-indigo to-brand-light opacity-50"></div>
        
        <div id="particles-js" class="absolute inset-0"></div>

        <div class="relative z-10 w-full max-w-md">
            <div class="glassmorphism p-10 rounded-3xl shadow-2xl space-y-6">
                <div class="text-center">
                    <h2 class="text-3xl font-black mb-2 tracking-tight">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-indigo to-brand-light">
                            Registro
                        </span>
                    </h2>
                    <p class="text-lg text-white opacity-80">Crea tu cuenta en Obertrack</p>
                </div>
                
                <form class="space-y-4" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-brand-light">Nombre</label>
                        <input id="name" name="name" type="text" required autofocus autocomplete="name"
                               class="mt-1 block w-full px-3 py-2 bg-white bg-opacity-10 border border-brand-light border-opacity-20 rounded-md text-brand-light placeholder-brand-light placeholder-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-indigo focus:border-transparent"
                               value="{{ old('name') }}">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-brand-light">Email</label>
                        <input id="email" name="email" type="email" required autocomplete="username"
                               class="mt-1 block w-full px-3 py-2 bg-white bg-opacity-10 border border-brand-light border-opacity-20 rounded-md text-brand-light placeholder-brand-light placeholder-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-indigo focus:border-transparent"
                               value="{{ old('email') }}">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-brand-light">Contraseña</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                               class="mt-1 block w-full px-3 py-2 bg-white bg-opacity-10 border border-brand-light border-opacity-20 rounded-md text-brand-light placeholder-brand-light placeholder-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-indigo focus:border-transparent">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-brand-light">Confirmar Contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                               class="mt-1 block w-full px-3 py-2 bg-white bg-opacity-10 border border-brand-light border-opacity-20 rounded-md text-brand-light placeholder-brand-light placeholder-opacity-50 focus:outline-none focus:ring-2 focus:ring-brand-indigo focus:border-transparent">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div>
                        <label for="tipo_usuario" class="block text-sm font-medium text-brand-light">Tipo de Usuario</label>
                        <select id="tipo_usuario" name="tipo_usuario" required
                                class="mt-1 block w-full px-3 py-2 bg-white text-black font-sans bg-opacity-10 border border-brand-light border-opacity-20 rounded-md  focus:outline-none focus:ring-2 focus:ring-brand-indigo focus:border-transparent">
                            <option class="" value="">Seleccione...</option>
                            <option value="empleador">Empresa</option>
                            <option value="empleado">Profesional</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo_usuario')" class="mt-2" />
                    </div>

                    <div id="empleado_por_id_container" class="hidden">
                        <label for="empleado_por_id" id="empleado_por_id_label" class="block text-sm font-medium text-brand-light">Seleccionar Empresa</label>
                        <select name="empleado_por_id" id="empleado_por_id"
                                class="mt-1 block w-full px-3 py-2 bg-white text-black bg-opacity-10 border border-brand-light border-opacity-20 rounded-md  focus:outline-none focus:ring-2 focus:ring-brand-indigo focus:border-transparent">
                            <option value="">Seleccione una Empresa</option>
                            @foreach ($empleadores as $empleadorId => $nombreEmpleador)
                                <option value="{{ $empleadorId }}">{{ $nombreEmpleador }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <a class="text-sm text-brand-light hover:text-brand-indigo transition duration-150 ease-in-out" href="{{ route('login') }}">
                            ¿Ya tienes una cuenta?
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-brand-indigo text-white font-semibold rounded-md hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-brand-indigo focus:ring-offset-2 focus:ring-offset-brand-purple transition duration-150 ease-in-out">
                            Registrarse
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-indigo rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 right-0 w-72 h-72 bg-brand-purple rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div> -->
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": {"value": 80},
                "color": {"value": "#ffffff"},
                "shape": {"type": "circle"},
                "opacity": {"value": 0.5, "random": true},
                "size": {"value": 3, "random": true},
                "move": {"enable": true, "speed": 1, "direction": "none", "random": true, "out_mode": "out"}
            },
            "interactivity": {
                "events": {
                    "onhover": {"enable": true, "mode": "repulse"},
                    "onclick": {"enable": true, "mode": "push"}
                }
            }
        });

        $(document).ready(function() {
            $('#tipo_usuario').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'empleado' || selectedValue === '') {
                    $('#empleado_por_id_container').show();
                } else {
                    $('#empleado_por_id_container').hide();
                }
            });
            $('#tipo_usuario').trigger('change');
        });

        // Animaciones con GSAP
        gsap.from(".glassmorphism", {duration: 1, y: 50, opacity: 0, ease: "power3.out"});
        gsap.from("h2", {duration: 1, y: 20, opacity: 0, ease: "power3.out", delay: 0.3});
        gsap.from("p", {duration: 1, y: 20, opacity: 0, ease: "power3.out", delay: 0.5});
        gsap.from("input, select", {duration: 1, y: 20, opacity: 0, ease: "power3.out", delay: 0.7, stagger: 0.1});
        gsap.from("button", {duration: 1, y: 20, opacity: 0, ease: "power3.out", delay: 1});
    </script>
</body>
</html>