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

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
          integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="flex min-h-screen flex-col bg-white">

<!-- HEADER / MENÚ -->
<header class="fixed top-0 z-20 flex h-16 w-full items-center justify-between px-5 bg-white shadow-md">
    <a class="h-20 w-auto p-1 flex items-center gap-3" href="">
        <x-application-logo class="h-16 w-auto object-contain" />
        <div class="flex flex-col">
            <div class="relative inline-block">
                <span class="font-bold text-2xl tracking-tight text-[#0976D6] leading-none">Obertrack</span>
                <span class="absolute -top-1 -right-3 text-[0.5rem] font-bold text-gray-900">TM</span>
            </div>
            <span class="text-[0.6rem] font-bold tracking-widest text-gray-500 uppercase leading-none mt-0.5">
                Remote Work Tracking
            </span>
        </div>
    </a>

    <nav class="hidden lg:flex space-x-4"></nav>

    <div class="flex items-center space-x-2">
        <a href="{{ url('/register') }}"
           class="rounded-full border-2 border-[#22A9C8] px-4 py-2 text-black bg-white hover:bg-blue-50 transition">
            Registrarse
        </a>
        <a href="{{ url('/dashboard') }}"
           class="rounded-full bg-[#22A9C8] px-4 py-2 text-white hover:bg-[#0D5C7D] transition">
            Iniciar sesión
        </a>
        <button class="text-blue-600 text-3xl lg:hidden" onclick="toggleMenu()" aria-label="menu">
            <i class="bi bi-list"></i>
        </button>
    </div>
</header>

<!-- Mobile menu -->
<div id="mobile-menu"
     class="fixed inset-0 bg-white z-30 transform translate-x-full transition-transform duration-300 ease-in-out lg:hidden">
    <div class="flex flex-col h-full justify-center items-center space-y-4 relative">
        <button onclick="toggleMenu()" class="absolute top-4 right-4 text-3xl text-blue-600">
            <i class="bi bi-x"></i>
        </button>
        <a href="{{ url('/register') }}"
           class="rounded-full border-2 border-[#22A9C8] px-6 py-3 text-black bg-white hover:bg-blue-50 transition">
            Registrarse
        </a>
        <a href="{{ url('/dashboard') }}"
           class="rounded-full bg-[#22A9C8] px-6 py-3 text-white hover:bg-[#0D5C7D] transition">
            Iniciar sesión
        </a>
    </div>
</div>

<!-- WRAPPER HERO CON IMAGEN + DEGRADADO RADIAL -->
<div class="relative mt-16 overflow-hidden">

    <!-- Imagen de fondo (solo hero, desde header hasta botón) -->
    <div class="absolute top-0 left-0 w-full h-[400px] bg-cover bg-center"
         style="background-image: url('{{ asset('images/fondo-hero.png') }}'); opacity:0.5; transform: scaleY(-1); z-index:0;">
    </div>

    <!--  Degradado radial extendido más grande -->
 <div class="absolute inset-0"
         style="
            background: radial-gradient(
                circle at center bottom, /* punto fuerte en medio-abajo */
                rgba(35, 181, 214, 0.55) 0%,  /* azul menos intenso */
                rgba(34, 169, 200, 0.45) 20%, /* azul del medio más visible */
                rgba(34, 169, 200, 0.25) 50%, 
                rgba(255, 255, 255, 0.0) 85% /* se difumina hacia los bordes */
            );
            z-index:1;
         ">
    </div>

    <!-- CONTENIDO -->
    <div class="relative z-10" >

        <main class="flex flex-col flex-grow px-4 py-16">
            <div class="max-w-6xl mx-auto text-center">

<div class="inline-block mb-8 px-4 py-1 rounded-full border-2 border-[#22A9C8] text-sm">
    <span class="font-bold text-[#22A9C8]">Control y Calidad</span> 
    <span class="font-bold text-[#23272A]">para equipos modernos </span> 
</div>

                <h1 class="mb-6 text-2xl sm:text-3xl md:text-5xl font-bold uppercase leading-tight lg:text-5xl text-[#23272A]">
                    Gestión inteligente para equipos que necesitan claridad y resultados
                </h1>

                <p class="mb-8 text-lg sm:text-xl lg:text-2xl text-[#485156]">
                    Unifica tus procesos, registra horas, genera reportes y administra talento con datos en tiempo real.
                </p>

                <a href="/dashboard"
                   class="inline-block rounded-lg bg-[#23272A] px-12 py-2 text-lg font-semibold text-white transition-all duration-300 hover:scale-105">
                    Comienza ahora
                </a>

                <!-- Grid de tres elementos -->
                <div class="mt-16 grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="flex flex-col items-center bg-[#FFFFFF] rounded-lg p-10 shadow-lg">
                        <i class="bi bi-calendar4-event text-6xl mb-4 text-[#09122C] border-2 border-[#22A9C8] rounded-lg p-4"></i>
                        <h3 class="text-xl font-bold mb-2 text-[#09122C]">Registra Horas</h3>
                        <p class="text-[#09122C] text-center">
                            Lleva un control preciso del tiempo dedicado a cada tarea y proyecto.
                        </p>
                    </div>

                    <div class="flex flex-col items-center bg-[#FFFFFF] rounded-lg p-10 shadow-lg">
                        <i class="bi bi-file-earmark-text text-6xl mb-4 text-[#09122C] border-2 border-[#22A9C8] rounded-lg p-4"></i>
                        <h3 class="text-xl font-bold mb-2 text-[#09122C]">Crea Reportes</h3>
                        <p class="text-[#09122C] text-center">
                            Genera informes detallados para analizar la productividad y el progreso.
                        </p>
                    </div>

                    <div class="flex flex-col items-center bg-[#FFFFFF] rounded-lg p-10 shadow-lg">
                        <i class="bi bi-person-check text-6xl mb-4 text-[#09122C] border-2 border-[#22A9C8] rounded-lg p-4"></i>
                        <h3 class="text-xl font-bold mb-2 text-[#09122C]">Gestiona Profesionales</h3>
                        <p class="text-[#09122C] text-center">
                            Administra eficientemente tu equipo y optimiza la asignación de recursos.
                        </p>
                    </div>
                </div>

                <!-- Elemento debajo del grid -->
                <div class="mt-8 flex flex-col items-center bg-[#FFFFFF] rounded-lg p-10 shadow-lg max-w-6xl mx-auto">
                    <i class="bi bi-gear text-6xl mb-4 text-[#09122C] border-2 border-[#22A9C8] rounded-lg p-4"></i>
                    <h3 class="text-xl font-bold mb-2 text-[#09122C] text-center">
                        Centraliza tus procesos y toma decisiones con datos reales, en tiempo real
                    </h3>
                    <p class="text-[#09122C] text-center">
                        Completa la función de reportes y gestión, apelando al valor estratégico.
                    </p>
                </div>
            </div>
        </main>

    </div>
</div>

<!-- CONTACTO -->
<section class="bg-white py-16">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 px-4">
        <div class="flex flex-col justify-between h-full">
            <h2 class="text-4xl font-bold tracking-tight text-[#22A9C8] mb-6">CONTÁCTANOS</h2>
            <p class="text-[17px] mb-6 text-[#09122C]">¿Tienes preguntas o necesitas una solución a medida?</p>
            <p class="text-[17px] mb-6 text-[#09122C]">
                Estamos aquí para ayudarte. Cuéntanos lo que necesitas y te responderemos con una propuesta clara,
                efectiva y pensada para tu equipo.
            </p>
            <p class="text-[19px] text-[#09122C] font-bold">
                Nos comprometemos a brindarte
                <span class="font-bold text-[#22A9C8]">una respuesta de valor</span>
            </p>
        </div>

        <form id="webhookForm" class="flex flex-col gap-4">
            <div class="flex gap-4">
                <input type="text" name="nombre" placeholder="Nombre"
                       class="flex-1 border rounded-lg px-4 py-2 border-[#22A9C8]" required />
                <input type="text" name="apellidos" placeholder="Apellido"
                       class="flex-1 border rounded-lg px-4 py-2 border-[#22A9C8]" required />
            </div>

            <input type="email" name="email" placeholder="Email"
                   class="border rounded-lg px-4 py-2 border-[#22A9C8]" required />

            <input type="text" name="empresa" placeholder="Empresa"
                   class="border rounded-lg px-4 py-2 border-[#22A9C8]" />

            <textarea name="mensaje" placeholder="Mensaje" rows="4"
                      class="border rounded-lg px-4 py-2 border-[#22A9C8]" required></textarea>

            <button type="submit"
                    class="rounded-full bg-[#22A9C8] text-white px-6 py-3 hover:bg-[#0D5C7D]">
                Enviar mensaje
            </button>
        </form>

        <div id="statusModal"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg relative max-w-sm w-full text-center">
                <span id="statusModalText" class="text-lg font-semibold">Enviando...</span>
                <button id="closeStatusModal"
                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 font-bold">
                    &times;
                </button>
            </div>
        </div>
    </div>
</section>

<x-layout.footer />



    <script>
function initContacto() {
  (function() {
    const WEBHOOK_URL = 'https://n8n.obertrack.com/webhook-test/obertrack';   

    const form = document.getElementById('webhookForm');
    const statusModal = document.getElementById('statusModal');
    const statusModalText = document.getElementById('statusModalText');
    const closeStatusModal = document.getElementById('closeStatusModal');

    if (!form || !statusModal || !statusModalText || !closeStatusModal) return;

    form.addEventListener('submit', async function(event) {
      event.preventDefault();

      statusModalText.textContent = 'Enviando...';
      statusModal.classList.remove('hidden'); // muestra el modal
      document.body.style.overflow = 'hidden';

      const data = {
        nombre: form.nombre.value,
        apellidos: form.apellidos.value,
        email: form.email.value,
        empresa: form.empresa.value,
        mensaje: form.mensaje.value
      };

      try {
        const response = await fetch(WEBHOOK_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });

        if (response.ok) {
          statusModalText.textContent = 'Datos enviados exitosamente';
          form.reset();
        } else {
          statusModalText.textContent = 'Error al enviar los datos. Código ' + response.status;
        }
      } catch (error) {
        statusModalText.textContent = 'Error de red o en la URL del Webhook: ' + error.message;
        console.error('Error:', error);
      }
    });

    // Cerrar modal
    closeStatusModal.addEventListener('click', () => {
      statusModal.classList.add('hidden');
      document.body.style.overflow = '';
    });

    // Cerrar al hacer clic fuera del contenido
    statusModal.addEventListener('click', (e) => {
      if (e.target === statusModal) {
        statusModal.classList.add('hidden');
        document.body.style.overflow = '';
      }
    });
  })();
}

initContacto(); // ejecutar la función
</script>




        <script>
            function toggleMenu() {
                const menu = document.getElementById('mobile-menu');
                menu.classList.toggle('translate-x-full');
            }
        </script>

</body>

</html>