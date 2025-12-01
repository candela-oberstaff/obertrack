<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Obertrack</title>
        <meta name="description" content="" />
        <link rel="shortcut icon" href="./assets/logo/logo1.png" type="image/x-icon" />

        <!-- Open Graph / Facebook -->
        <meta property="og:title" content="Title of the project" />
        <meta property="og:description" content="" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://github.com/arquidev8" />
        <meta property="og:image" content="" />

        <link rel="stylesheet" href="../../tailwind-css/tailwind-runtime.css" />
        <link rel="stylesheet" href="css/index.css" />
        <script src="https://cdn.tailwindcss.com"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.tailwindcss.com"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body class="flex min-h-screen flex-col bg-white">
        <header class="fixed top-0 z-20 flex h-16 w-full items-center justify-between px-5 bg-white shadow-md">
            <a class="h-20 w-auto p-1" href="">
                <img src="https://i.postimg.cc/JhdTT8GV/Obertrack-2-removebg-preview.png" alt="logo" class="h-full w-auto object-contain" />
            </a>
            <nav class="hidden lg:flex space-x-4">
                <!-- <a class="text-gray-600 hover:text-indigo-600" href="#">Inicio</a>
                <a class="text-gray-600 hover:text-indigo-600" href="#pricing">Precios</a>
                <a class="text-gray-600 hover:text-indigo-600" href="#">Características</a>
                <a class="text-gray-600 hover:text-indigo-600" href="#">Contacto</a> -->
            </nav>
            <div class="flex items-center space-x-4">
                <a href="{{ url('/dashboard') }}" aria-label="signup" class="hidden sm:inline-flex rounded-full bg-indigo-500 px-4 py-2 text-white transition duration-300 hover:bg-indigo-700">
                    <span>Acceder al dashboard</span>
                    <i class="bi bi-arrow-right ml-2"></i>
                </a>
                <button class="text-indigo-600 text-3xl lg:hidden" onclick="toggleMenu()" aria-label="menu" id="menu-btn">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="fixed inset-0 bg-white z-30 transform translate-x-full transition-transform duration-300 ease-in-out lg:hidden">
            <div class="flex flex-col h-full justify-center items-center space-y-8 relative">
                <button onclick="toggleMenu()" class="absolute top-4 right-4 text-3xl text-indigo-600">
                    <i class="bi bi-x"></i>
                </button>
                <!-- <a class="text-2xl text-gray-600 hover:text-indigo-600" href="#">Inicio</a>
                <a class="text-2xl text-gray-600 hover:text-indigo-600" href="#pricing">Precios</a>
                <a class="text-2xl text-gray-600 hover:text-indigo-600" href="#">Características</a>
                <a class="text-2xl text-gray-600 hover:text-indigo-600" href="#">Contacto</a> -->
                <a href="{{ url('/dashboard') }}" class="rounded-full bg-indigo-500 px-6 py-3 text-white transition duration-300 hover:bg-indigo-700">
                    Acceder al dashboard
                </a>
            </div>
        </div>

        <main class="flex flex-grow items-center justify-center px-4 py-16 bg-gradient-to-br from-indigo-500 to-blue-500 mt-16">
            <div class="max-w-6xl mx-auto text-center text-white">
            <h1 class="mb-6 text-4xl sm:text-5xl md:text-6xl font-bold uppercase leading-tight lg:text-7xl text-white [text-shadow:_0_4px_8px_rgba(0,0,0,0.3),_0_6px_20px_rgba(0,0,0,0.2)] transition-all duration-300 hover:scale-105">
    <span class="block">OBERTRACK</span>
</h1>
<p class="mb-8 text-lg sm:text-xl lg:text-2xl text-white [text-shadow:_0_2px_4px_rgba(0,0,0,0.2)] transition-all duration-300 hover:translate-y-[-2px]">
    Descubre un mundo de posibilidades y conecta con tu equipo como nunca antes.
</p>
<a href="/dashboard" class="inline-block rounded-full bg-white px-8 py-4 text-lg font-semibold text-indigo-600 transition-all duration-300 hover:bg-opacity-90 hover:scale-105 hover:translate-y-[-2px] shadow-[0_0_20px_rgba(255,255,255,0.5),0_0_30px_rgba(255,255,255,0.3)] hover:shadow-[0_0_25px_rgba(255,255,255,0.6),0_0_40px_rgba(255,255,255,0.4)]">
    Comienza ahora
</a>

                <div class="mt-16 flex justify-center">
                    <div class="relative w-full max-w-2xl">
                        <img src="./ob1.png" alt="dashboard" class="rounded-2xl shadow-2xl w-full h-auto object-cover" />
                    </div>
                </div>

                <div class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="flex flex-col items-center">
                        <i class="bi bi-clock text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Registra Horas</h3>
                        <p class="text-gray-200">Lleva un control preciso del tiempo dedicado a cada tarea y proyecto.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <i class="bi bi-file-earmark-text text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Crea Reportes</h3>
                        <p class="text-gray-200">Genera informes detallados para analizar la productividad y el progreso.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <i class="bi bi-people text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Gestiona Profesionales</h3>
                        <p class="text-gray-200">Administra eficientemente tu equipo y optimiza la asignación de recursos.</p>
                    </div>
                </div>
            </div>
        </main>

        <script>
            function toggleMenu() {
                const menu = document.getElementById('mobile-menu');
                menu.classList.toggle('translate-x-full');
            }
        </script>
    </body>
</html>