<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Obertrack</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
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

        .btn-google {
            border: 2px solid #1B1725 !important;
            background: #FFFFFF !important;
            box-shadow: 4px 4px 0px 0px rgba(27, 23, 37, 0.2) !important;
            transition: all 0.2s ease !important;
        }
        
        .btn-google:hover {
            box-shadow: 6px 6px 0px 0px #1B1725 !important;
            transform: translate(-2px, -2px) !important;
        }

        .brutal-checkbox {
            border: 2px solid #1B1725 !important;
            background: #FFFFFF !important;
        }

        .brutal-checkbox:checked {
            background-color: #22A9C8 !important;
            border-color: #1B1725 !important;
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
            Inicia sesión
        </h2>

        <!-- Google Login Button -->
        <div class="mb-6">
            <a href="{{ route('login.google') }}" class="btn-google w-full flex items-center justify-center py-2.5 px-4 rounded-lg font-bold text-sm text-brandBlack">
                <svg class="h-5 w-5 mr-3" aria-hidden="true" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Continuar con Google
            </a>
        </div>

        <div class="relative flex py-2 items-center mb-6">
            <div class="flex-grow border-t-2 border-gray-300"></div>
            <span class="flex-shrink-0 mx-4 text-gray-500 text-xs font-bold uppercase">O con tu email</span>
            <div class="flex-grow border-t-2 border-gray-300"></div>
        </div>

        @if (session('error'))
            <div class="bg-brutalRed/10 border-2 border-brutalRed text-brutalRed px-4 py-3 rounded-lg relative mb-4 font-bold text-sm" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-brandBlack uppercase mb-1 ml-1">Email</label>
                <input id="email" name="email" type="email" placeholder="ejemplo@obertrack.com" required autofocus
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

            <!-- Remember Me -->
             <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="brutal-checkbox rounded w-4 h-4" name="remember">
                    <span class="ml-2 text-sm text-gray-600 font-bold">Recuérdame</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-brandBlue hover:text-brandBlueDark font-bold underline decoration-2">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full bg-brandBlue text-white font-extrabold uppercase tracking-wide py-3 px-4 rounded-lg brutal-button">
                    Iniciar sesión
                </button>
            </div>

            <!-- Register Link -->
            <div class="text-center mt-6">
                 <p class="text-sm text-gray-600 font-medium">
                    ¿No tienes una cuenta? <a href="{{ route('register') }}" class="text-brandBlue hover:text-brandBlueDark font-bold underline decoration-2">Regístrate aquí</a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>
