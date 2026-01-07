<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Obertrack | Visibilidad Total</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?
family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
theme: {
extend: {
fontFamily: { sans: ['Poppins', 'sans-serif'] },
colors: {
brandBlue: '#22A9C8',
brandBlueDark: '#0D5C7D',
brandBlack: '#1B1725',
brandGray: '#F3F4F6',
oldLogoBlue: '#0976D6',
},
keyframes: {
typing: { '0%': { width: '0ch' }, '30%, 90%': { width: '100%' }, '100%': { width: '0ch' } },
scroll: { '0%': { transform: 'translateX(0)' }, '100%': { transform: 'translateX(-50%)' } },
float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform:
'translateY(-10px)' } }
},
animation: {
typing: 'typing 8s steps(40) infinite',
scroll: 'scroll 25s linear infinite',
float: 'float 3s ease-in-out infinite',
}
}
}
}
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/
font/bootstrap-icons.min.css"/>
<style>
.typing-container { display: inline-flex; align-items: center; }
select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg
xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'
stroke='%2322A9C8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-
width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-
repeat; background-position: right 0.75rem center; background-size: 1.2em; }
</style>
</head>
<body class="bg-white font-sans text-brandBlack overflow-x-hidden">
<header class="fixed top-0 left-0 w-full bg-white/95 backdrop-blur-sm shadow-sm z-50
h-16 flex items-center">
<div class="max-w-7xl mx-auto w-full flex justify-between items-center px-5">
                <a class="flex items-center" href="#">
                    <x-application-logo class="h-16 w-auto" />
                </a>
<div class="hidden lg:flex gap-4 items-center">
<a href="{{ url('/register') }}" class="px-4 py-2 border-2 border-brandBlue text-
brandBlack rounded-full hover:bg-blue-50 transition font-medium text-
sm">Registrarse</a>
<a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-full bg-brandBlue text-
white hover:bg-brandBlueDark transition font-medium text-sm">Iniciar sesión</a>
</div>
</div>
</header>
<section class="pt-32 pb-16 bg-white">
<div class="max-w-7xl mx-auto px-5 grid lg:grid-cols-2 gap-12 items-center">
<div class="text-left">
<h1 class="text-3xl md:text-5xl font-extrabold text-brandBlack uppercase leading-
tight mb-6">
Maximiza la rentabilidad de tu equipo remoto <br>
<span class="typing-container text-brandBlue">
<span class="overflow-hidden whitespace-nowrap border-r-4 border-brandBlue
pr-1 animate-typing">con visibilidad total</span>
</span>
</h1>
<p class="text-gray-600 text-lg mb-8 max-w-lg">
Centraliza el control de tiempos, tareas y rendimiento para que tomes decisiones
basadas <strong>en datos, no en suposiciones.</strong>
</p>
<a href="/dashboard" class="inline-flex items-center px-10 py-4 rounded-2xl bg-brandBlue text-white font-bold text-xl hover:bg-brandBlueDark transition-all duration-300 hover:scale-105 shadow-xl">
    COMIENZA AHORA <i class="bi bi-rocket-takeoff-fill ml-3"></i>
</a>
</div>
<div class="relative">
<div class="relative z-10 bg-white rounded-2xl shadow-2xl border border-gray-100
overflow-hidden">
<img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?
auto=format&fit=crop&w=800&q=80" alt="Dashboard" class="w-full h-auto opacity-90">
</div>
<div class="absolute -top-6 -right-6 bg-white p-4 rounded-xl shadow-lg border
border-gray-50 animate-float z-20">
<div class="flex items-center gap-3">
<div class="bg-green-100 p-2 rounded-full text-green-600"><i class="bi bi-graph-up-
arrow"></i></div>
<div><p class="text-[0.6rem] text-gray-400 font-bold
uppercase">Productividad</p><p class="font-bold text-brandBlack">+15.4%</p></div>
</div>
</div>
<div class="absolute bottom-10 -left-10 bg-white p-4 rounded-xl shadow-lg border
border-gray-50 animate-float z-20" style="animation-delay: 1.5s">
<div class="flex items-center gap-3">
<div class="bg-brandBlue/10 p-2 rounded-full text-brandBlue"><i class="bi bi-
person-check-fill"></i></div>
<div><p class="text-[0.6rem] text-gray-400 font-bold uppercase">Estado</p><p
class="font-bold text-brandBlack">Activo</p></div>
</div>
</div>
</div>
</div>
</section>
<section class="py-16 bg-white">
<div class="max-w-6xl mx-auto px-5 grid md:grid-cols-3 gap-10">
<div class="bg-brandGray rounded-3xl p-10 shadow-lg hover:shadow-2xl transform
hover:-translate-y-2 transition duration-500 border-b-4 border-brandBlue">
<i class="bi bi-cash-coin text-4xl text-brandBlack mb-4 block"></i>
<h3 class="text-xl font-bold text-brandBlack mb-3">Control de Costos</h3>
<p class="text-sm">Identifica en qué se invierte tu presupuesto y elimina horas
muertas.</p>
</div>
<div class="bg-brandGray rounded-3xl p-10 shadow-lg hover:shadow-2xl transform
hover:-translate-y-2 transition duration-500 border-b-4 border-brandBlue">
<i class="bi bi-file-earmark-text text-4xl text-brandBlack mb-4 block"></i>
<h3 class="text-xl font-bold text-brandBlack mb-3">Auditorías de Rentabilidad</h3>
<p class="text-sm">Exporta informes listos para presentar a clientes o gerencia en un
clic.</p>
</div>
<div class="bg-brandGray rounded-3xl p-10 shadow-lg hover:shadow-2xl transform
hover:-translate-y-2 transition duration-500 border-b-4 border-brandBlue">
<i class="bi bi-person-check text-4xl text-brandBlack mb-4 block"></i>
<h3 class="text-xl font-bold text-brandBlack mb-3">Optimización de Talento</h3>
<p class="text-sm">Asigna la carga de trabajo de forma equitativa y evita el burnout
de tu equipo.</p>
</div>
</div>
</section>
<div class="py-12 bg-white border-y border-gray-100 overflow-hidden">
<div class="flex animate-scroll whitespace-nowrap">
<div class="flex items-center gap-20 px-10">
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
file-earmark-excel"></i> EXCEL</span>
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
asana"></i> ASANA</span>
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
slack"></i> SLACK</span>
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
stopwatch"></i> TIME DOCTOR</span>
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
trello"></i> TRELLO</span>
</div>
<div class="flex items-center gap-20 px-10">
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
file-earmark-excel"></i> EXCEL</span>
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
asana"></i> ASANA</span>
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
slack"></i> SLACK</span>
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
stopwatch"></i> TIME DOCTOR</span>
<span class="text-gray-400 font-bold text-2xl flex items-center gap-3"><i class="bi bi-
trello"></i> TRELLO</span>
</div>
</div>
</div>
<section class="py-20 bg-white">
<div class="max-w-6xl mx-auto px-5">
<div class="grid lg:grid-cols-[1fr_1.4fr] gap-12 items-center">
<div class="space-y-6">
<div>
<span class="text-brandBlue font-extrabold tracking-widest uppercase text-
sm">Contáctanos</span>
<h2 class="text-4xl md:text-5xl font-extrabold text-brandBlack mt-3 leading-tight
uppercase">
¿Listo para dar el siguiente paso?
</h2>
</div>
<div class="space-y-5">
<p class="text-2xl font-bold text-brandBlack leading-snug">
Toma el control total de tu operación hoy mismo.
</p>
<p class="text-gray-500 text-lg leading-relaxed max-w-md border-l-4 border-
brandBlue pl-6 italic">
Cuéntanos lo que necesitas y te responderemos con una propuesta clara, efectiva
y pensada para tu equipo.
</p>
</div>
<div class="flex flex-wrap gap-6 pt-2">
<div class="flex items-center gap-3 text-sm font-semibold text-brandBlue">
<div class="bg-brandBlue/10 p-2 rounded-full"><i class="bi bi-shield-lock-fill text-
xl"></i></div>
Datos 100% Seguros
</div>
<div class="flex items-center gap-3 text-sm font-semibold text-brandBlue">
<div class="bg-brandBlue/10 p-2 rounded-full"><i class="bi bi-lightning-charge-fill
text-xl"></i></div>
Respuesta en < 24h
</div>
</div>
</div>
<div class="bg-brandGray rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-
gray-100">
<form id="webhookForm" class="space-y-4">
<input type="text" name="nombre" placeholder="Nombre completo" class="w-full
bg-white rounded-xl px-5 py-4 border border-transparent focus:border-brandBlue
outline-none focus:ring-4 focus:ring-brandBlue/10 text-base transition-all shadow-sm"
required>
<div class="grid md:grid-cols-2 gap-4">
<input type="email" name="email" placeholder="Email corporativo" class="w-full
bg-white rounded-xl px-5 py-4 border border-transparent focus:border-brandBlue
outline-none focus:ring-4 focus:ring-brandBlue/10 text-base shadow-sm" required>
<input type="text" name="empresa" placeholder="Nombre de la Empresa"
class="w-full bg-white rounded-xl px-5 py-4 border border-transparent focus:border-
brandBlue outline-none focus:ring-4 focus:ring-brandBlue/10 text-base shadow-sm">
</div>
<div class="grid md:grid-cols-2 gap-4">
<select name="tamano_equipo" class="w-full bg-white rounded-xl px-5 py-4
border border-transparent focus:border-brandBlue outline-none focus:ring-4
focus:ring-brandBlue/10 text-base shadow-sm cursor-pointer">
<option value="">Tamaño de equipo</option>
<option>1–10 integrantes</option>
<option>11–50 integrantes</option>
<option>51–200 integrantes</option>
<option>Más de 200</option>
</select>
<select name="herramientas" class="w-full bg-white rounded-xl px-5 py-4 border
border-transparent focus:border-brandBlue outline-none focus:ring-4 focus:ring-
brandBlue/10 text-base shadow-sm cursor-pointer">
<option value="">Herramientas actuales</option>
<option>Planillas/Excel</option>
<option>Asana/Slack/Time Doctor</option>
<option>Ninguna/Busco mi primera herramienta</option>
</select>
</div>
<textarea name="mensaje" rows="3" placeholder="¿Cómo podemos ayudarte?"
class="w-full bg-white rounded-xl px-5 py-4 border border-transparent focus:border-
brandBlue outline-none focus:ring-4 focus:ring-brandBlue/10 text-base transition-all
shadow-sm" required></textarea>
<div class="pt-2">
<button type="submit" class="w-full bg-brandBlue text-white py-5 rounded-2xl
font-bold text-xl hover:bg-brandBlueDark transition-all duration-300 shadow-lg
hover:shadow-brandBlue/30 flex items-center justify-center gap-3 transform hover:-
translate-y-1">
ENVIAR MENSAJE <i class="bi bi-send-fill text-sm"></i>
</button>
</div>
</form>
</div>
</div> </div>
</section>
<div id="statusModal" class="fixed inset-0 bg-brandBlack/80 flex items-center justify-
center hidden z-[60]">
<div class="bg-white p-8 rounded-2xl shadow-2xl max-w-sm w-full text-center
relative">
<span id="statusModalText" class="text-lg font-bold">Enviando...</span>
<button id="closeStatusModal" class="absolute top-3 right-4 text-2xl font-
bold">&times;</button>
</div>
</div>
<x-layout.footer />
<script>
const form = document.getElementById('webhookForm');
const modal = document.getElementById('statusModal');
const modalText = document.getElementById('statusModalText');
form.addEventListener('submit', async (e) => {
e.preventDefault();
modal.classList.remove('hidden');
modalText.textContent = "Enviando...";
const data = Object.fromEntries(new FormData(form));
try {
const resp = await fetch('https://n8n.obertrack.com/webhook-test/obertrack', {
method: 'POST',
headers: { 'Content-Type': 'application/json' },
body: JSON.stringify(data)
});
modalText.textContent = resp.ok ? "¡Mensaje enviado con éxito!" : "Error al enviar.";
if(resp.ok) form.reset();
} catch {
modalText.textContent = "Error de conexión.";
}
});
document.getElementById('closeStatusModal').onclick = () =>
modal.classList.add('hidden');
</script>
</body>