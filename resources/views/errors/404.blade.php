<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página no encontrada - Obertrack</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #fff;
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
        <!-- GIF Animation -->
        <div class="mb-6 transform hover:scale-105 transition-transform duration-500">
            <img src="https://i.gifer.com/Vp3M.gif" alt="Página no encontrada" class="w-48 h-48 md:w-60 md:h-60 object-contain">
        </div>
        
        <!-- Adjusted Heading -->
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 tracking-tight">
            ¡Ups! Te has perdido
        </h1>
        
        <!-- Reassuring Message -->
        <p class="text-base md:text-lg text-gray-500 max-w-lg mb-8 leading-relaxed px-4">
            Parece que esta página no vive aquí o se fue de vacaciones. 
            <span class="block mt-1 font-medium text-gray-600">No hay de qué preocuparse, ¡te llevamos de vuelta!</span>
        </p>
        
        <!-- Primary Action -->
        <div class="w-full max-w-xs flex flex-col items-center gap-4">
            <a href="/" 
                class="w-full bg-[#22A9C8] hover:bg-[#1b8a9e] text-white text-lg font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-105 shadow-xl active:scale-95 flex items-center justify-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Volver al Inicio
            </a>
        </div>

        <!-- Subtle footer message -->
        <div class="mt-8 opacity-20 pointer-events-none">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Obertrack</p>
        </div>
    </div>
</body>
</html>
