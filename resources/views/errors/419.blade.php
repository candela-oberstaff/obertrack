<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesión Expirada - Obertrack</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
        }
        .mascot-bounce {
            animation: bounce 3s infinite ease-in-out;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-white md:bg-gray-50 overflow-hidden">
    <div class="w-full max-w-2xl flex flex-col items-center justify-center text-center">
        <div class="mb-6 transform hover:scale-105 transition-transform duration-500">
            <img src="{{ asset('images/logo.png') }}" alt="Obertrack Logo" class="h-24 md:h-32 object-contain">
        </div>
        

        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 tracking-tight">
            ¡Tu sesión ha expirado!
        </h1>
        

        <p class="text-base md:text-lg text-gray-500 max-w-lg mb-8 leading-relaxed px-4">
            Por seguridad, tu sesión se tomó un respiro. 
            <span class="block mt-1 font-medium text-gray-600">No te preocupes, ¡vuelve a iniciar sesión y continuemos!</span>
        </p>
        

        <div class="w-full max-w-xs flex flex-col items-center gap-4">
            <button onclick="window.location.href='/login'" 
                class="w-full bg-[#22A9C8] hover:bg-[#1b8a9e] text-white text-lg font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 shadow-xl active:scale-95 flex items-center justify-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Iniciar Sesión
            </button>
            
            <a href="/" class="text-gray-400 hover:text-[#22A9C8] font-medium transition-colors text-xs">
                Volver al inicio
            </a>
        </div>


        <div class="mt-8 opacity-20 pointer-events-none">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Obertrack</p>
        </div>
    </div>
</body>
</html>
