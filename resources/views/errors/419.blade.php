<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesión Expirada - Obertrack</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
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
        <!-- Slightly scaled down GIF -->
        <div class="mb-6 transform hover:scale-105 transition-transform duration-500">
            <img src="https://i.gifer.com/3cVf.gif" alt="Mascota Feliz" class="w-48 h-48 md:w-60 md:h-60 object-contain">
        </div>
        
        <!-- Adjusted Heading -->
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 tracking-tight">
            ¡Un pequeño descanso!
        </h1>
        
        <!-- Reassuring Message -->
        <p class="text-base md:text-lg text-gray-500 max-w-lg mb-8 leading-relaxed px-4">
            Parece que esta página se tomó un respiro por seguridad (la sesión expiró). 
            <span class="block mt-1 font-medium text-gray-600">No hay de qué preocuparse, ¡solo recarga y seguimos!</span>
        </p>
        
        <!-- Primary Action -->
        <div class="w-full max-w-xs flex flex-col items-center gap-4">
            <button onclick="window.location.reload()" 
                class="w-full bg-[#22A9C8] hover:bg-[#1b8a9e] text-white text-lg font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 shadow-xl active:scale-95 flex items-center justify-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Recargar Página
            </button>
            
            <a href="/" class="text-gray-400 hover:text-[#22A9C8] font-medium transition-colors text-xs">
                Volver al inicio
            </a>
        </div>

        <!-- Subtle footer message -->
        <div class="mt-8 opacity-20 pointer-events-none">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Obertrack</p>
        </div>
    </div>
</body>
</html>
