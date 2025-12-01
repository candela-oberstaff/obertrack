<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Experiencia Inmersiva</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.net.min.js"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .custom-shape-divider-bottom-1628127602 {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }
        .custom-shape-divider-bottom-1628127602 svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 150px;
        }
        .custom-shape-divider-bottom-1628127602 .shape-fill {
            fill: #FFFFFF;
        }

    </style>

    <link rel="stylesheet" href="../../tailwind-css/tailwind-runtime.css" />
    <link rel="stylesheet" href="css/index.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <style>
        
        .bg-gradient-light {
            background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.5) 100%);
        }
    </style>
    <body class="h-screen bg-gradient-to-r from-white via-blue-200 to-purple-200">
    <div class="tw-absolute tw-top-0 tw-flex tw-h-[150px] tw-w-full">
        <div class="header-gradient tw-h-full tw-w-full"></div>
    </div>

    <header class="lgtw-max-w-lg:tw-justify-around tw-max-w-lg:tw-px-4 tw-max-w-lg:tw-mr-auto tw-absolute tw-top-0 tw-z-20 tw-flex tw-h-[60px] tw-w-full tw-bg-opacity-0 tw-px-[5%] tw-text-black">
        <a class="tw-h-[100px] tw-w-[100px] tw-p-[4px]" href="">
            <img src="https://i.postimg.cc/JhdTT8GV/Obertrack-2-removebg-preview.png" alt="logo" class="tw-object tw-h-full tw-w-[280px]" />
        </a>
    </header>
</head>

<body>
<section class="tw-relative tw-flex tw-min-h-[100vh] tw-w-full tw-max-w-[100vw] tw-flex-col tw-overflow-hidden max-lg:tw-p-4 max-md:tw-mt-[50px]">
    <div class="tw-flex tw-h-full tw-min-h-[100vh] tw-w-full tw-flex-col tw-place-content-center tw-gap-6 tw-p-[5%] max-xl:tw-place-items-center">
        <div class="tw-flex tw-flex-col tw-place-content-center tw-items-center">
            <div class="tw-text-center tw-text-7xl tw-font-semibold tw-uppercase tw-leading-[80px] max-lg:tw-text-4xl max-md:tw-leading-snug">
                <span class="tw-text-primary tw-font-black tw-text-2xl">OBERTRACK</span>
            </div>
            <div class="tw-mt-10 tw-max-w-[450px] tw-p-2 tw-text-center max-lg:tw-max-w-full">
                Inicia sesión para acceder a tu cuenta
            </div>

            <form class="tw-mt-8 tw-space-y-6 tw-w-full tw-max-w-md" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="tw-space-y-4">
                    <div class="tw-relative">
                        <input id="email" name="email" type="email" required
                               class="tw-w-full tw-px-4 tw-py-3 tw-bg-white tw-bg-opacity-10 tw-rounded-lg tw-text-black tw-placeholder-gray-500 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-primary tw-transition tw-duration-200"
                               placeholder="Correo electrónico">
                    </div>
                    <div class="tw-relative">
                        <input id="password" name="password" type="password" required
                               class="tw-w-full tw-px-4 tw-py-3 tw-bg-white tw-bg-opacity-10 tw-rounded-lg tw-text-black tw-placeholder-gray-500 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-primary tw-transition tw-duration-200"
                               placeholder="Contraseña">
                    </div>
                </div>

                <div class="tw-flex tw-items-center tw-justify-between tw-text-sm">
                    <div class="tw-flex tw-items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                               class="tw-h-4 tw-w-4 tw-text-primary focus:tw-ring-primary tw-border-gray-300 tw-rounded">
                        <label for="remember_me" class="tw-ml-2 tw-block tw-text-gray-700">
                            Recordarme
                        </label>
                    </div>
                </div>

                <button type="submit"
                        class="tw-w-full tw-py-3 tw-px-4 tw-bg-primary hover:tw-bg-primary-dark tw-text-white tw-font-bold tw-rounded-lg tw-shadow-md hover:tw-shadow-lg tw-transition tw-duration-300">
                    Iniciar sesión
                </button>
            </form>

            <div class="tw-mt-6 tw-text-center tw-text-sm tw-text-gray-600">
                ¿No tienes una cuenta?
                <a href="#" class="tw-font-medium tw-text-primary hover:tw-text-primary-dark tw-transition tw-duration-150 tw-ease-in-out">
                    Regístrate aquí
                </a>
            </div>
        </div>
    </div>
</section>
</body>


</html>





