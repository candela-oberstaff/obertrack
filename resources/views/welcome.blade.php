<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Obertrack | Visibilidad Total</title>
<link rel="icon" type="image/x-icon" href="images/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        oldLogoBlue: '#0976D6',
        brutalYellow: '#FFDE59',
        brutalRed: '#FF5A5F',
        brutalGreen: '#00D4AA',
        brutalPurple: '#9D4EDD'
      },
      keyframes: {
        typing: { 
          '0%': { width: '0ch' }, 
          '30%, 90%': { width: '100%' }, 
          '100%': { width: '0ch' } 
        },
        scroll: { 
          '0%': { transform: 'translateX(0)' }, 
          '100%': { transform: 'translateX(-50%)' } 
        },
        float: { 
          '0%, 100%': { transform: 'translateY(0) rotate(-1deg)' }, 
          '50%': { transform: 'translateY(-10px) rotate(1deg)' } 
        },
        pulseGlow: {
          '0%, 100%': { opacity: 1 },
          '50%': { opacity: 0.7 }
        },
        dash: {
          '0%': { strokeDashoffset: 1000 },
          '100%': { strokeDashoffset: 0 }
        },
        growBar: {
          '0%': { height: '0%' },
          '100%': { height: 'var(--final-height)' }
        },
        growBarSlow: {
          '0%': { height: '0%' },
          '100%': { height: 'var(--final-height)' }
        },
        countUp: {
          '0%': { transform: 'translateY(20px)', opacity: 0 },
          '100%': { transform: 'translateY(0)', opacity: 1 }
        },
        fadeInUp: {
          '0%': { transform: 'translateY(10px)', opacity: 0 },
          '100%': { transform: 'translateY(0)', opacity: 1 }
        },
        bounceIn: {
          '0%, 60%, 75%, 90%, 100%': { 
            animationTimingFunction: 'cubic-bezier(0.215, 0.610, 0.355, 1.000)' 
          },
          '0%': { opacity: 0, transform: 'scale3d(0.3, 0.3, 0.3)' },
          '60%': { opacity: 1, transform: 'scale3d(1.1, 1.1, 1.1)' },
          '75%': { transform: 'scale3d(0.9, 0.9, 0.9)' },
          '90%': { transform: 'scale3d(1.03, 1.03, 1.03)' },
          '100%': { opacity: 1, transform: 'scale3d(1, 1, 1)' }
        },
        slideInRight: {
          '0%': { transform: 'translateX(-10px)', opacity: 0 },
          '100%': { transform: 'translateX(0)', opacity: 1 }
        },
        shimmer: {
          '0%': { backgroundPosition: '-200% center' },
          '100%': { backgroundPosition: '200% center' }
        },
        progressGrow: {
          '0%': { width: '0%' },
          '100%': { width: 'var(--final-width)' }
        },
        progressGrowSlow: {
          '0%': { width: '0%' },
          '100%': { width: 'var(--final-width)' }
        },
        pulseColor: {
          '0%, 100%': { opacity: 0.3, transform: 'scale(1)' },
          '50%': { opacity: 1, transform: 'scale(1.2)' }
        },
        fillArea: {
          '0%': { clipPath: 'inset(100% 0 0 0)' },
          '100%': { clipPath: 'inset(0 0 0 0)' }
        },
        growBarHorizontal: {
          '0%': { width: '0%' },
          '100%': { width: 'var(--final-width)' }
        },
        slideUpStack: {
          '0%': { transform: 'translateY(100%)', opacity: 0 },
          '100%': { transform: 'translateY(0)', opacity: 1 }
        },
        checkmarkAppear: {
          '0%': { transform: 'scale(0)', opacity: 0 },
          '50%': { transform: 'scale(1.2)', opacity: 1 },
          '100%': { transform: 'scale(1)', opacity: 1 }
        },
        documentWave: {
          '0%': { transform: 'translateX(0)' },
          '100%': { transform: 'translateX(-100%)' }
        }
      },
      animation: {
        typing: 'typing 8s steps(40) infinite',
        scroll: 'scroll 25s linear infinite',
        float: 'float 3s ease-in-out infinite',
        pulseGlow: 'pulseGlow 2s ease-in-out infinite',
        dash: 'dash 3s ease-out forwards',
        growBar: 'growBar 1.5s ease-out forwards',
        growBarSlow: 'growBarSlow 2.5s ease-out forwards',
        countUp: 'countUp 0.8s ease-out forwards',
        fadeInUp: 'fadeInUp 0.6s ease-out forwards',
        bounceIn: 'bounceIn 0.8s ease-out forwards',
        slideInRight: 'slideInRight 0.5s ease-out forwards',
        shimmer: 'shimmer 2s infinite linear',
        progressGrow: 'progressGrow 1.2s ease-out forwards',
        progressGrowSlow: 'progressGrowSlow 2s ease-out forwards',
        pulseColor: 'pulseColor 2s ease-in-out infinite',
        fillArea: 'fillArea 2s ease-out forwards',
        growBarHorizontal: 'growBarHorizontal 1.5s ease-out forwards',
        slideUpStack: 'slideUpStack 0.8s ease-out forwards',
        checkmarkAppear: 'checkmarkAppear 0.5s ease-out forwards',
        documentWave: 'documentWave 3s linear infinite'
      }
    }
  }
}
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"/>
<style>
  * {
    box-sizing: border-box;
  }
  
  body {
    background: #FFFFFF;
    overflow-x: hidden;
  }
  
  .brutal-border {
    border: 4px solid #1B1725 !important;
    box-shadow: 8px 8px 0px 0px #1B1725 !important;
  }
  
  .brutal-border-thin {
    border: 2px solid #1B1725 !important;
    box-shadow: 4px 4px 0px 0px #1B1725 !important;
  }
  
  .brutal-button {
    border: 3px solid #1B1725 !important;
    box-shadow: 6px 6px 0px 0px #1B1725 !important;
    transition: all 0.2s ease !important;
  }
  
  .brutal-button:hover {
    transform: translate(3px, 3px) !important;
    box-shadow: 3px 3px 0px 0px #1B1725 !important;
  }
  
  .brutal-card {
    border: 3px solid #1B1725 !important;
    background: #FFFFFF !important;
    box-shadow: 6px 6px 0px 0px #1B1725 !important;
    position: relative;
    overflow: hidden;
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
  }
  
  .brutal-input:focus {
    outline: none;
    box-shadow: 6px 6px 0px 0px #22A9C8 !important;
    transform: translate(-2px, -2px);
  }
  
  .brutal-select {
    border: 2px solid #1B1725 !important;
    background: #FFFFFF url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%231B1725' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat right 0.75rem center !important;
    box-shadow: 4px 4px 0px 0px rgba(27, 23, 37, 0.2) !important;
  }
  
  .typing-container {
    display: inline-flex;
    align-items: center;
  }
  
  .graphic-grid {
    background-image: 
      linear-gradient(to right, rgba(27, 23, 37, 0.05) 1px, transparent 1px),
      linear-gradient(to bottom, rgba(27, 23, 37, 0.05) 1px, transparent 1px);
    background-size: 30px 30px;
  }
  
  .graphic-dots {
    background-image: radial-gradient(circle, rgba(27, 23, 37, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
  }
  
  .graphic-diagonal {
    background: linear-gradient(45deg, transparent 49%, rgba(34, 169, 200, 0.1) 50%, transparent 51%);
    background-size: 40px 40px;
  }
  
  select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2322A9C8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1.2em;
  }
  
  .progress-bar-inner {
    width: 0% !important;
    animation-fill-mode: forwards;
  }

  .progress-bar-inner.efficiency {
    --final-width: 66%;
  }

  
  .metric-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
  }

  .metric-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 8px 8px 0px 0px #1B1725 !important;
    z-index: 10;
  }

  .progress-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
  }

  .progress-card:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 6px 6px 0px 0px #1B1725 !important;
    z-index: 10;
  }

  
  .animate-on-scroll {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.8s ease, transform 0.8s ease;
  }
  
  .animate-on-scroll.visible {
    opacity: 1;
    transform: translateY(0);
  }
  
  
  .dashboard-progress {
    width: 0% !important;
    transition: width 2s ease-out !important;
  }
  
  .dashboard-progress.animate {
    width: var(--final-width) !important;
  }
  
  .pie-circle {
    stroke-dasharray: 220;
    stroke-dashoffset: 220;
    transition: stroke-dashoffset 2s ease-out;
  }
  
  .pie-circle.animate {
    stroke-dashoffset: 33;
  }
  
  .inner-circle {
    opacity: 0;
    transform: scale(0);
    transition: opacity 2s ease, transform 2s ease;
  }
  
  .inner-circle.animate {
    opacity: 1;
    transform: scale(1);
  }
  
  .line-path {
    stroke-dasharray: 500;
    stroke-dashoffset: 500;
    transition: stroke-dashoffset 2s ease-out;
  }
  
  .line-path.animate {
    stroke-dashoffset: 0;
  }
  
  .line-point {
    opacity: 0;
    transform: scale(0);
    transition: opacity 1s ease, transform 1s ease;
  }
  
  .line-point.animate {
    opacity: 1;
    transform: scale(1);
  }
  
  /* Estilos para la nueva gráfica de dona */
  .dona-graph {
    height: 120px;
    width: 100%;
    position: relative;
    margin-top: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .dona-container {
    position: relative;
    width: 100px;
    height: 100px;
  }
  
  .dona-circle {
    fill: none;
    stroke-width: 8;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
    stroke-dasharray: 283; 
    stroke-dashoffset: 283;
    transition: stroke-dashoffset 2s ease-out;
  }
  
  .dona-circle.animate {
    stroke-dashoffset: 57; 
  }
  
  .dona-bg {
    fill: none;
    stroke: #F3F4F6;
    stroke-width: 8;
  }
  
  .dona-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    opacity: 0;
    transition: opacity 2s ease-out;
  }
  
  .dona-center.animate {
    opacity: 1;
  }
  
  .dona-percentage {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1B1725;
  }
  
  .dona-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #666;
  }
  
  /* GRÁFICA PARA CARD 2*/
  .report-stack-graph {
    height: 120px;
    width: 100%;
    position: relative;
    margin-top: 20px;
  }
  
  .report-stack-container {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    gap: 5px;
    padding: 10px 0;
  }
  
  .report-stack-item {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 60px;
    height: 100%;
  }
  
  .stack-column {
    width: 40px;
    border-radius: 4px 4px 0 0;
    background: #F3F4F6;
    border: 2px solid #1B1725;
    border-bottom: none;
    position: relative;
    overflow: hidden;
    height: 0;
    animation: slideUpStack 0.8s ease-out forwards;
  }
  
  .stack-column.column-1 {
    height: 0;
    background: #FFDE59;
    animation-delay: 0.1s;
    animation-fill-mode: forwards;
  }
  
  .stack-column.column-2 {
    height: 0;
    background: #22A9C8;
    animation-delay: 0.3s;
    animation-fill-mode: forwards;
  }
  
  .stack-column.column-3 {
    height: 0;
    background: #00D4AA;
    animation-delay: 0.5s;
    animation-fill-mode: forwards;
  }
  
  .stack-column.column-4 {
    height: 0;
    background: #9D4EDD;
    animation-delay: 0.7s;
    animation-fill-mode: forwards;
  }
  
  .stack-column.animate {
    animation: slideUpStack 0.8s ease-out forwards;
  }
  
  .stack-checkmark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    opacity: 0;
    font-size: 1.2rem;
    color: #1B1725;
    z-index: 2;
  }
  
  .stack-checkmark.animate {
    animation: checkmarkAppear 0.5s ease-out forwards;
    animation-delay: 0.9s;
  }
  
  .stack-label {
    font-size: 0.6rem;
    font-weight: 700;
    margin-top: 5px;
    color: #1B1725;
    text-align: center;
  }
  
  .stack-value {
    font-size: 0.7rem;
    font-weight: 800;
    margin-top: 3px;
    color: #1B1725;
    opacity: 0;
    transform: translateY(5px);
    transition: opacity 0.5s ease, transform 0.5s ease;
  }
  
  .stack-column.animate ~ .stack-value {
    opacity: 1;
    transform: translateY(0);
    transition-delay: 0.8s;
  }
  
  /* Indicadores de automatización */
  .automation-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 15px;
  }
  
  .automation-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #FFDE59;
    margin: 0 3px;
    animation: pulseColor 2s ease-in-out infinite;
  }
  
  .automation-dot:nth-child(2) {
    background: #22A9C8;
    animation-delay: 0.3s;
  }
  
  .automation-dot:nth-child(3) {
    background: #00D4AA;
    animation-delay: 0.6s;
  }
  
  .automation-dot:nth-child(4) {
    background: #9D4EDD;
    animation-delay: 0.9s;
  }
  
  /* Barras de progreso horizontales para los reportes */
  .report-progress-bar {
    height: 6px;
    background: #F3F4F6;
    border: 2px solid #1B1725;
    margin-bottom: 10px;
    overflow: hidden;
  }
  
  .report-progress-fill {
    height: 100%;
    width: 0%;
    animation: growBarHorizontal 1.5s ease-out forwards;
  }
  
  /* Retrasos para animaciones secuenciales */
  .graph-bar.animate.delay-1 { animation-delay: 0.1s !important; }
  .graph-bar.animate.delay-2 { animation-delay: 0.2s !important; }
  .graph-bar.animate.delay-3 { animation-delay: 0.3s !important; }
  
  .line-point.animate.delay-1 { transition-delay: 2s !important; }
  .line-point.animate.delay-2 { transition-delay: 2.2s !important; }
  .line-point.animate.delay-3 { transition-delay: 2.4s !important; }

  /* Clase para reiniciar animaciones */
  .reset-animation {
    animation: none !important;
    transition: none !important;
  }
</style>
</head>
<body class="bg-white font-sans text-brandBlack overflow-x-hidden">
<header class="fixed top-0 left-0 w-full bg-white/95 backdrop-blur-sm shadow-sm z-50
h-16 flex items-center">
<div class="max-w-7xl mx-auto w-full flex justify-between items-center px-5">
<a class="flex items-center gap-3" href="#">
<x-application-logo class="h-10 w-auto object-contain" />
<div class="flex flex-col">
<div class="relative inline-block">
<span class="font-bold text-xl tracking-tight text-oldLogoBlue leading-
none">Obertrack</span>
<span class="absolute -top-1 -right-3 text-[0.4rem] font-bold text-
gray-900">TM</span>
</div>
<span class="text-[0.55rem] font-bold tracking-widest text-gray-500 uppercase
leading-none mt-0.5">Remote Work Tracking</span>
</div>
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
</html>