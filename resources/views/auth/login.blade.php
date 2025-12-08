<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Obertrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .bg-gradient-brand {
            background: linear-gradient(180deg, #FFFFFF 0%, #E0F2FE 40%, #3B82F6 100%);
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
            padding: 12px 16px;
            color: #6B7280; /* Text-gray-500 */
        }
        .form-input-custom::placeholder {
            color: #D1D5DB; /* Gray-300 */
        }
        .form-input-custom:focus {
            ring: 2px;
            ring-color: #3B82F6;
        }
        .btn-google {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid #3B82F6;
            color: #1e1b4b;
            transition: all 0.3s;
        }
        .btn-google:hover {
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body class="h-full bg-gradient-brand flex items-center justify-center p-4">

    <!-- Card Container -->
    <div class="w-full max-w-sm glass-card rounded-3xl p-8">
        
        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <x-application-logo class="block h-12 w-auto fill-current text-gray-800 mb-2" />
            <div class="text-center">
                <span class="font-bold text-2xl tracking-tight text-gray-900 leading-none">Obertrack</span>
                <span class="text-[0.6rem] font-bold tracking-widest text-gray-500 uppercase leading-none block mt-1">REMOTE WORK TRACKING</span>
            </div>
        </div>

        <h2 class="text-xl font-bold text-center text-[#1e1b4b] mb-6">
            Inicia sesión en Obertrack
        </h2>

        <!-- Google Login Button -->
        <div class="mb-6">
            <a href="{{ route('login.google') }}" class="btn-google w-full flex items-center justify-center py-2.5 px-4 rounded-full font-medium text-sm group transition-all duration-200 border border-blue-200 hover:border-blue-400 bg-white hover:bg-blue-50">
                <svg class="h-5 w-5 mr-3" aria-hidden="true" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
               Inicia sesión con Google
            </a>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <input id="email" name="email" type="email" placeholder="Email" required autofocus
                       class="w-full form-input-custom focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm"
                       value="{{ old('email') }}">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
            </div>

            <!-- Password -->
            <div>
                <input id="password" name="password" type="password" placeholder="Contraseña" required
                       class="w-full form-input-custom focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
            </div>

            <!-- Remember Me -->
             <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                    <span class="ml-2 text-sm text-gray-600 font-medium">Recuérdame</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2 text-center">
                <button type="submit" class="bg-[#1D71B8] hover:bg-blue-700 text-white font-bold py-2.5 px-10 rounded-full transition duration-300 shadow-lg w-auto inline-block text-sm">
                    Iniciar sesión
                </button>
            </div>

            <!-- Forgot Password -->
            <div class="text-center mt-4">
                <p class="text-xs text-gray-600 font-medium">
                    ¿Olvidaste tu contraseña? 
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-blue-600 hover:text-blue-800 underline">Recupérala</a>
                    @endif
                </p>
                 <p class="text-xs text-gray-600 font-medium mt-2">
                    ¿No tienes una cuenta? <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 underline">Regístrate aquí</a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>
