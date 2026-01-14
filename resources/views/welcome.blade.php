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

  /* Estilos para hover en métricas */
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

  /* ANIMACIONES NUEVAS - ESTILOS SIMPLIFICADOS */
  .animate-on-scroll {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.8s ease, transform 0.8s ease;
  }
  
  .animate-on-scroll.visible {
    opacity: 1;
    transform: translateY(0);
  }
  
  /* Estilos específicos para animaciones de gráficas */
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
    stroke-dasharray: 283; /* Circunferencia del círculo (2πr) */
    stroke-dashoffset: 283;
    transition: stroke-dashoffset 2s ease-out;
  }
  
  .dona-circle.animate {
    stroke-dashoffset: 57; /* 80% del círculo (283 * 0.2) */
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
  
  /* NUEVA GRÁFICA DE FICHAS APILADAS PARA CARD 2 - REPORTES AUTOMATIZADOS */
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
<body class="font-sans text-brandBlack overflow-x-hidden graphic-grid">
  <!-- Header -->
  <header class="fixed top-0 left-0 w-full bg-white z-50 h-16 flex items-center brutal-border-thin">
    <div class="max-w-7xl mx-auto w-full flex justify-between items-center px-5">
      <a class="flex items-center gap-3" href="#">
       <img 
          src="images/logoNuevo.png" 
          alt="Obertrack Logo" 
          class="h-16 w-auto object-contain animate-fadeInUp"
          style="animation-delay: 0.1s"
        >
      </a>
      
      <!-- Botones con estilo neobrutalista -->
      <div class="hidden lg:flex gap-4 items-center">
        <a href="{{ url('/register') }}" class="px-4 py-2 bg-white text-brandBlack font-medium text-sm brutal-button animate-fadeInUp" style="animation-delay: 0.2s">
          Registrarse
        </a>
        <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-brandBlue text-white font-medium text-sm brutal-button animate-fadeInUp" style="animation-delay: 0.3s">
          Iniciar sesión
        </a>
      </div>
    </div>
  </header>

  <!-- Hero Section con Dashboard Gráfico -->
  <section class="pt-28 pb-16 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-5 grid lg:grid-cols-2 gap-12 items-center relative z-10">
      <div class="text-left">
        <h1 class="text-3xl md:text-5xl font-extrabold text-brandBlack uppercase leading-tight mb-6 animate-fadeInUp" style="animation-delay: 0.1s">
          Maximiza la rentabilidad de tu equipo remoto <br>
          <span class="typing-container text-brandBlue">
            <span class="overflow-hidden whitespace-nowrap border-r-4 border-brandBlue pr-1 animate-typing">con visibilidad total</span>
          </span>
        </h1>
        <p class="text-gray-600 text-lg mb-8 max-w-lg font-poppins animate-fadeInUp" style="animation-delay: 0.2s">
          Centraliza el control de tiempos, tareas y rendimiento para que tomes decisiones basadas <strong class="font-black">en datos, no en suposiciones.</strong>
        </p>
        <a href="/dashboard" class="inline-flex items-center px-8 py-3 bg-brandBlue text-white font-bold text-lg brutal-button animate-bounceIn" style="animation-delay: 0.3s">
          COMIENZA AHORA <i class="bi bi-rocket-takeoff-fill ml-3"></i>
        </a>
      </div>
      
      <div class="relative">
        <!-- Dashboard gráfico-->
        <div class="brutal-card p-1 h-full animate-fadeInUp dashboard-container" style="animation-delay: 0.4s">
          <div class="bg-white p-6 h-full flex flex-col">
            <div class="flex justify-between items-center mb-6 animate-slideInRight" style="animation-delay: 0.5s">
              <div class="flex items-center gap-3">
                <div class="flex space-x-1">
                  <div class="w-4 h-4 bg-brutalRed rounded-full animate-pulse" style="animation-delay: 0.1s"></div>
                  <div class="w-4 h-4 bg-brutalYellow rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                  <div class="w-4 h-4 bg-brutalGreen rounded-full animate-pulse" style="animation-delay: 0.3s"></div>
                </div>
                <div class="text-base font-bold">DASHBOARD</div>
              </div>
              <div class="text-xs font-bold text-brutalGreen bg-brutalGreen/10 px-2 py-1 rounded brutal-border-thin">
                CONECTADO
              </div>
            </div>
            
            <!-- Fila superior de métricas -->
            <div class="grid grid-cols-3 gap-4 mb-6">
              <div class="brutal-border-thin p-3 animate-fadeInUp metric-card" style="animation-delay: 0.6s">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold">Profesionales Activos</span>
                </div>
                <div class="text-2xl font-bold count-up" data-target="12">0</div>
                <div class="text-xs text-gray-500 animate-fadeInUp" style="animation-delay: 0.8s">+2 esta semana</div>
              </div>
              
              <div class="brutal-border-thin p-3 animate-fadeInUp metric-card" style="animation-delay: 0.7s">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold">Horas Totales</span>
                </div>
                <div class="text-2xl font-bold count-up" data-target="122">0</div>
                <div class="text-xs text-gray-500 animate-fadeInUp" style="animation-delay: 0.9s">+15.4% vs mes anterior</div>
              </div>
              
              <div class="brutal-border-thin p-3 animate-fadeInUp metric-card" style="animation-delay: 0.8s">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold">Tareas</span>
                </div>
                <div class="text-2xl font-bold count-up" data-target="89">0</div>
                <div class="text-xs text-gray-500 animate-fadeInUp" style="animation-delay: 1.0s">Completadas</div>
              </div>
            </div>
            
            <!-- Gráfica principal -->
            <div class="mb-6">
           
            </div>
            
            <!-- Mini gráficas de progreso -->
            <div class="grid grid-cols-2 gap-4 mt-auto">
              <div class="brutal-border-thin p-3 animate-fadeInUp progress-card" style="animation-delay: 1.0s">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold animate-fadeInUp" style="animation-delay: 1.1s">Productividad</span>
                  <span class="text-xs font-bold text-brutalGreen animate-fadeInUp" style="animation-delay: 1.2s">+34%</span>
                </div>
                <div class="h-2 bg-gray-200 brutal-border-thin overflow-hidden">
                  <div class="h-full bg-brutalGreen progress-bar-inner dashboard-progress productivity-bar" style="--final-width: 75%;"></div>
                </div>
              </div>
              
              <div class="brutal-border-thin p-3 animate-fadeInUp progress-card" style="animation-delay: 1.1s">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold animate-fadeInUp" style="animation-delay: 1.2s">Eficiencia</span>
                  <span class="text-xs font-bold text-brandBlue animate-fadeInUp" style="animation-delay: 1.3s">+22%</span>
                </div>
                <div class="h-2 bg-gray-200 brutal-border-thin overflow-hidden">
                  <div class="h-full bg-brandBlue progress-bar-inner dashboard-progress efficiency-bar" style="--final-width: 66%;"></div>
                </div>
              </div>
            </div>
            
            <!-- Indicadores de estado -->
            <div class="grid grid-cols-3 gap-3 mt-4">
              <div class="flex items-center gap-2 animate-fadeInUp" style="animation-delay: 1.3s">
                <div class="w-3 h-3 bg-brutalGreen rounded-full animate-pulse"></div>
                <span class="text-xs font-medium">Online</span>
              </div>
              <div class="flex items-center gap-2 animate-fadeInUp" style="animation-delay: 1.4s">
                <div class="w-3 h-3 bg-brutalYellow rounded-full animate-pulse"></div>
                <span class="text-xs font-medium">Pendiente</span>
              </div>
              <div class="flex items-center gap-2 animate-fadeInUp" style="animation-delay: 1.5s">
                <div class="w-3 h-3 bg-brutalRed rounded-full animate-pulse"></div>
                <span class="text-xs font-medium">Inactivo</span>
              </div>
            </div>
          </div>
        </div>
   
        </div>
      </div>
    </div>
  </section>

  <!-- Section con Elementos Gráficos -->
  <section class="py-12 bg-white graphic-dots">
    <div class="max-w-6xl mx-auto px-5">
      <!-- Título centrado antes de las cards -->
      <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-extrabold text-brandBlack uppercase mb-4 animate-fadeInUp">
          Transforma tu gestión remota
        </h2>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto font-poppins animate-fadeInUp" style="animation-delay: 0.2s">
          Soluciones diseñadas para maximizar la productividad y rentabilidad de tu equipo
        </p>
      </div>
      
      <div class="grid md:grid-cols-3 gap-6">
        <!-- Card 1 - Control de Costos con gráfica de dona -->
        <div class="brutal-card p-8 transition-transform duration-300 hover:scale-105 cost-card" style="animation-delay: 0.3s">
          <h3 class="text-xl font-extrabold text-brandBlack uppercase mb-3">Control de Costos</h3>
          <p class="text-sm mb-4 font-poppins">Identifica exactamente en qué se invierte el presupuesto y elimina horas muertas.</p>
          
          <!-- Gráfica de dona para control de costos -->
          <div class="dona-graph">
            <div class="dona-container">
              <svg width="100" height="100" viewBox="0 0 100 100">
                <circle class="dona-bg" cx="50" cy="50" r="45" />
                <circle class="dona-circle cost-dona-circle" cx="50" cy="50" r="45" stroke="#FF5A5F" />
              </svg>
              <div class="dona-center cost-dona-center">
                <div class="dona-percentage">80%</div>
                <div class="dona-label">Ahorro</div>
              </div>
            </div>
          </div>
          
          <div class="mt-4 text-center text-xs font-bold">
            <span class="text-brutalRed">Reducción de costos operativos</span>
          </div>
        </div>
        
        <!-- Card 2 - Reportes Automatizados CON NUEVA GRÁFICA DE FICHAS APILADAS -->
        <div class="brutal-card p-8 transition-transform duration-300 hover:scale-105 report-card" style="animation-delay: 0.4s">
          <h3 class="text-xl font-extrabold text-brandBlack uppercase mb-3">Reportes Automatizados</h3>
          <p class="text-sm mb-4 font-poppins">Genera informes detallados de productividad y rendimiento en un clic.</p>
          
          <!-- NUEVA Gráfica de fichas apiladas -->
          <div class="report-stack-graph">
            <div class="report-stack-container">
              <!-- Columna 1 - Reportes de Tiempo -->
              <div class="report-stack-item">
                <div class="stack-column column-1"></div>
                <div class="stack-checkmark"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stack-label">Tiempo</div>
                <div class="stack-value">95%</div>
              </div>
              
              <!-- Columna 2 - Reportes de Costos -->
              <div class="report-stack-item">
                <div class="stack-column column-2"></div>
                <div class="stack-checkmark"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stack-label">Costos</div>
                <div class="stack-value">88%</div>
              </div>
              
              <!-- Columna 3 - Reportes de Productividad -->
              <div class="report-stack-item">
                <div class="stack-column column-3"></div>
                <div class="stack-checkmark"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stack-label">Product.</div>
                <div class="stack-value">76%</div>
              </div>
              
              <!-- Columna 4 - Reportes de Rentabilidad -->
              <div class="report-stack-item">
                <div class="stack-column column-4"></div>
                <div class="stack-checkmark"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stack-label">Rentab.</div>
                <div class="stack-value">92%</div>
              </div>
            </div>
            
           
          </div>
          
        
          
          <!-- Estadísticas de automatización -->
          <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="brutal-border-thin p-2 text-center">
              <div class="text-xs font-bold text-brutalGreen">+95%</div>
              <div class="text-[0.6rem]">Automatización</div>
            </div>
            <div class="brutal-border-thin p-2 text-center">
              <div class="text-xs font-bold text-brandBlue">-60%</div>
              <div class="text-[0.6rem]">Tiempo manual</div>
            </div>
          </div>
        </div>
        
        <!-- Card 3 - Optimización de Talento con gráfica de líneas -->
        <div class="brutal-card p-8 transition-transform duration-300 hover:scale-105 talent-card" style="animation-delay: 0.5s">
          <h3 class="text-xl font-extrabold text-brandBlack uppercase mb-3">Optimización de Talento</h3>
          <p class="text-sm mb-4 font-poppins">Asigna la carga de trabajo de forma equitativa y evita el burnout de tu equipo.</p>
          
          <!-- Gráfica de líneas para talento -->
          <div class="talent-graph">
            <svg width="100%" height="100%" viewBox="0 0 200 100" class="brutal-border-thin p-2">
              <polyline class="line-path talent-line-path" points="10,80 40,40 70,60 100,20 130,50 160,10 190,30" 
                       fill="none" 
                       stroke="#9D4EDD" 
                       stroke-width="3"
                       stroke-linecap="round"
                       stroke-linejoin="round"/>
              <circle class="line-point delay-1 talent-point" cx="40" cy="40" r="4" fill="#9D4EDD" stroke="#1B1725" stroke-width="2"/>
              <circle class="line-point delay-2 talent-point" cx="100" cy="20" r="4" fill="#9D4EDD" stroke="#1B1725" stroke-width="2"/>
              <circle class="line-point delay-3 talent-point" cx="160" cy="10" r="4" fill="#9D4EDD" stroke="#1B1725" stroke-width="2"/>
            </svg>
          </div>
          
          <div class="mt-4 text-center text-xs font-bold">
            <span class="text-brutalPurple">Optimización: +32%</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Banner -->
  <div class="py-8 bg-white border-y border-gray-100 overflow-hidden graphic-diagonal">
    <div class="flex animate-scroll whitespace-nowrap">
      <div class="flex items-center gap-20 px-10">
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <i class="bi bi-file-earmark-excel text-green-600"></i>
          </div>
          EXCEL
        </span>
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <!-- Logo de Asana -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#F06A6A">
              <path d="M18.78 12.653c-2.882 0-5.22 2.336-5.22 5.22 0 2.882 2.338 5.22 5.22 5.22 2.883 0 5.22-2.338 5.22-5.22 0-2.884-2.337-5.22-5.22-5.22zm-13.56 0c-2.883 0-5.22 2.336-5.22 5.22 0 2.882 2.337 5.22 5.22 5.22 2.882 0 5.22-2.338 5.22-5.22 0-2.884-2.338-5.22-5.22-5.22zm6.78-6.78c-2.883 0-5.22 2.337-5.22 5.22 0 2.882 2.337 5.22 5.22 5.22 2.882 0 5.22-2.338 5.22-5.22 0-2.883-2.338-5.22-5.22-5.22z"/>
            </svg>
          </div>
          ASANA
        </span>
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <i class="bi bi-slack text-purple-500"></i>
          </div>
          SLACK
        </span>
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <i class="bi bi-stopwatch text-red-500"></i>
          </div>
          TIME DOCTOR
        </span>
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <i class="bi bi-trello text-blue-500"></i>
          </div>
          TRELLO
        </span>
      </div>
      <div class="flex items-center gap-20 px-10">
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <i class="bi bi-file-earmark-excel text-green-600"></i>
          </div>
          EXCEL
        </span>
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <!-- Logo de Asana -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#F06A6A">
              <path d="M18.78 12.653c-2.882 0-5.22 2.336-5.22 5.22 0 2.882 2.338 5.22 5.22 5.22 2.883 0 5.22-2.338 5.22-5.22 0-2.884-2.337-5.22-5.22-5.22zm-13.56 0c-2.883 0-5.22 2.336-5.22 5.22 0 2.882 2.337 5.22 5.22 5.22 2.882 0 5.22-2.338 5.22-5.22 0-2.884-2.338-5.22-5.22-5.22zm6.78-6.78c-2.883 0-5.22 2.337-5.22 5.22 0 2.882 2.337 5.22 5.22 5.22 2.882 0 5.22-2.338 5.22-5.22 0-2.883-2.338-5.22-5.22-5.22z"/>
            </svg>
          </div>
          ASANA
        </span>
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <i class="bi bi-slack text-purple-500"></i>
          </div>
          SLACK
        </span>
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <i class="bi bi-stopwatch text-red-500"></i>
          </div>
          TIME DOCTOR
        </span>
        <span class="text-gray-400 font-bold text-2xl flex items-center gap-3">
          <div class="w-10 h-10 bg-white flex items-center justify-center brutal-border-thin">
            <i class="bi bi-trello text-blue-500"></i>
          </div>
          TRELLO
        </span>
      </div>
    </div>
  </div>

  <!-- Contacto -->
  <section class="py-12 bg-white relative">
    <div class="max-w-6xl mx-auto px-5">
      <div class="grid lg:grid-cols-[1fr_1.4fr] gap-8 items-start">
        <div class="space-y-6">
          <div>
            <div class="inline-block px-3 py-1 bg-brutalYellow mb-3 brutal-border-thin">
              <span class="text-brandBlack font-extrabold tracking-widest uppercase text-xs">Contáctanos</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-brandBlack uppercase leading-tight">
              ¿Listo para dar el siguiente paso?
            </h2>
          </div>
          
          <div class="space-y-4">
            <p class="text-xl font-bold text-brandBlack leading-snug font-poppins">
              Toma el control total de tu operación hoy mismo.
            </p>
            
            <!-- Contenedor gráfico para el texto -->
            <div class="brutal-card p-5 bg-white">
              <div class="flex items-start gap-4">
                <p class="text-gray-600 text-base leading-relaxed font-poppins italic">
                  "Cuéntanos lo que necesitas y te responderemos con una propuesta clara, efectiva y pensada para tu equipo."
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-xl brutal-border">
          <form id="webhookForm" class="space-y-4">
            <input type="text" name="nombre" placeholder="NOMBRE COMPLETO" 
                   class="w-full bg-white rounded-lg px-4 py-3 brutal-input text-sm transition-all font-medium" required>
            
            <div class="grid md:grid-cols-2 gap-4">
              <input type="email" name="email" placeholder="EMAIL CORPORATIVO" 
                     class="w-full bg-white rounded-lg px-4 py-3 brutal-input text-sm font-medium" required>
              <input type="text" name="empresa" placeholder="EMPRESA"
                     class="w-full bg-white rounded-lg px-4 py-3 brutal-input text-sm font-medium">
            </div>
            
            <div class="grid md:grid-cols-2 gap-4">
              <select name="tamano_equipo" 
                      class="w-full bg-white rounded-lg px-4 py-3 brutal-select text-sm cursor-pointer font-medium">
                <option value="">TAMAÑO DE EQUIPO</option>
                <option>1–10 INTEGRANTES</option>
                <option>11–50 INTEGRANTES</option>
                <option>51–200 INTEGRANTES</option>
                <option>MÁS DE 200</option>
              </select>
              
              <select name="herramientas" 
                      class="w-full bg-white rounded-lg px-4 py-3 brutal-select text-sm cursor-pointer font-medium">
                <option value="">HERRAMIENTAS ACTUALES</option>
                <option>PLANILLAS/EXCEL</option>
                <option>ASANA/SLACK/TIME DOCTOR</option>
                <option>NINGUNA/BUSCO MI PRIMERA HERRAMIENTA</option>
              </select>
            </div>
            
            <textarea name="mensaje" rows="3" placeholder="¿CÓMO PODEMOS AYUDARTE?"
                      class="w-full bg-white rounded-lg px-4 py-3 brutal-input text-sm transition-all font-medium" required></textarea>
            
            <div class="pt-2">
              <button type="submit" class="w-full bg-brandBlue text-white py-3 rounded-lg font-bold text-base brutal-button flex items-center justify-center gap-2">
                ENVIAR MENSAJE <i class="bi bi-send-fill text-xs"></i>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Status Modal -->
  <div id="statusModal" class="fixed inset-0 bg-brandBlack/80 flex items-center justify-center hidden z-[60]">
    <div class="bg-white p-6 rounded-xl shadow-2xl max-w-sm w-full text-center relative brutal-border">
      <div class="w-16 h-16 mx-auto mb-4 bg-brutalGreen/20 rounded-full flex items-center justify-center brutal-border-thin">
        <i class="bi bi-check-lg text-brutalGreen text-2xl"></i>
      </div>
      <span id="statusModalText" class="text-lg font-bold">Enviando...</span>
      <button id="closeStatusModal" class="absolute top-3 right-4 text-2xl font-bold">&times;</button>
      <div class="mt-4">
        <div class="h-1 w-full bg-gray-200 brutal-border-thin">
          <div class="h-full bg-brandBlue animate-dash"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="py-8 bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-5">
      <div class="flex justify-center items-center">
        <div class="text-gray-600 text-sm font-poppins">
          © 2026 Obertrack. Todos los derechos reservados.
        </div>
      </div>
    </div>
  </footer>

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

    document.getElementById('closeStatusModal').onclick = () => {
      modal.classList.add('hidden');
    };

    // SISTEMA DE ANIMACIONES POR SCROLL MEJORADO
    function initScrollAnimations() {
      // Elementos del dashboard
      const dashboardProgressBars = document.querySelectorAll('.dashboard-progress');
      const countUpElements = document.querySelectorAll('.count-up');
      const dashboardContainer = document.querySelector('.dashboard-container');
      
      // Elementos de las cards
      const costCard = document.querySelector('.cost-card');
      const reportCard = document.querySelector('.report-card');
      const talentCard = document.querySelector('.talent-card');
      
      // Elementos específicos de animación
      const costDonaCircle = document.querySelector('.cost-dona-circle');
      const costDonaCenter = document.querySelector('.cost-dona-center');
      const stackColumns = document.querySelectorAll('.stack-column');
      const stackCheckmarks = document.querySelectorAll('.stack-checkmark');
      const automationDots = document.querySelectorAll('.automation-dot');
      const talentLinePath = document.querySelector('.talent-line-path');
      const talentPoints = document.querySelectorAll('.talent-point');
      
      // Variables para control de animaciones
      let dashboardAnimated = false;
      let costCardAnimated = false;
      let reportCardAnimated = false;
      let talentCardAnimated = false;
      
      // Timers para animaciones de conteo
      const countTimers = new Map();
      
      // Función para animar el conteo de números
      function animateCountUp(element) {
        // Limpiar timer anterior si existe
        if (countTimers.has(element)) {
          clearInterval(countTimers.get(element));
          countTimers.delete(element);
        }
        
        const target = parseInt(element.getAttribute('data-target'));
        const duration = 1500; // 1.5 segundos
        const step = target / (duration / 16); // 60fps
        let current = 0;
        
        element.textContent = '0';
        
        const timer = setInterval(() => {
          current += step;
          if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
            countTimers.delete(element);
          } else {
            element.textContent = Math.floor(current);
          }
        }, 16);
        
        countTimers.set(element, timer);
      }
      
      // Función para reiniciar animaciones del dashboard
      function resetDashboardAnimations() {
        // Quitar clases de animación del dashboard
        dashboardProgressBars.forEach(bar => {
          bar.classList.remove('animate');
          // Forzar reflow para reiniciar animación
          void bar.offsetWidth;
        });
        
        // Detener y reiniciar animación de conteo
        countUpElements.forEach(element => {
          element.textContent = '0';
        });
        
        // Limpiar todos los timers
        countTimers.forEach(timer => {
          clearInterval(timer);
        });
        countTimers.clear();
        
        dashboardAnimated = false;
      }
      
      // Función para reiniciar animaciones de las cards
      function resetCardAnimations() {
        // Quitar animaciones de la card de costos (gráfica de dona)
        if (costDonaCircle) {
          costDonaCircle.classList.remove('animate');
          void costDonaCircle.offsetWidth;
        }
        
        if (costDonaCenter) {
          costDonaCenter.classList.remove('animate');
          void costDonaCenter.offsetWidth;
        }
        
        // Quitar animaciones de la card de reportes (nueva gráfica de fichas apiladas)
        stackColumns.forEach(column => {
          column.classList.remove('animate');
          void column.offsetWidth;
        });
        
        stackCheckmarks.forEach(checkmark => {
          checkmark.classList.remove('animate');
          void checkmark.offsetWidth;
        });
        
        // Detener animación de puntos de automatización
        automationDots.forEach(dot => {
          dot.style.animation = 'none';
          void dot.offsetWidth;
        });
        
        // Quitar animaciones de la card de talentos
        if (talentLinePath) {
          talentLinePath.classList.remove('animate');
          void talentLinePath.offsetWidth;
        }
        
        talentPoints.forEach(point => {
          point.classList.remove('animate');
          void point.offsetWidth;
        });
        
        // Resetear flags de las cards
        costCardAnimated = false;
        reportCardAnimated = false;
        talentCardAnimated = false;
      }
      
      // Función para verificar si un elemento está en el viewport
      function isElementInViewport(el) {
        if (!el) return false;
        const rect = el.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        return (
          rect.top <= windowHeight * 0.8 && // 80% del viewport
          rect.bottom >= windowHeight * 0.2 // 20% del viewport
        );
      }
      
      // Función para animar el dashboard (se comporta como las cards)
      function animateDashboard() {
        const isInViewport = isElementInViewport(dashboardContainer);
        
        if (isInViewport && !dashboardAnimated) {
          // Animar barras de progreso
          dashboardProgressBars.forEach(bar => {
            bar.classList.add('animate');
          });
          
          // Animar conteo de números
          countUpElements.forEach(element => {
            animateCountUp(element);
          });
          
          dashboardAnimated = true;
        } else if (!isInViewport && dashboardAnimated) {
          // Reiniciar animaciones del dashboard
          resetDashboardAnimations();
        }
      }
      
      // Función para animar la card de control de costos
      function animateCostCard() {
        const isInViewport = isElementInViewport(costCard);
        
        if (isInViewport && !costCardAnimated) {
          if (costDonaCircle) costDonaCircle.classList.add('animate');
          if (costDonaCenter) costDonaCenter.classList.add('animate');
          costCardAnimated = true;
        } else if (!isInViewport && costCardAnimated) {
          if (costDonaCircle) {
            costDonaCircle.classList.remove('animate');
            void costDonaCircle.offsetWidth;
          }
          if (costDonaCenter) {
            costDonaCenter.classList.remove('animate');
            void costDonaCenter.offsetWidth;
          }
          costCardAnimated = false;
        }
      }
      
      // Función para animar la card de reportes (nueva gráfica de fichas apiladas)
      function animateReportCard() {
        const isInViewport = isElementInViewport(reportCard);
        
        if (isInViewport && !reportCardAnimated) {
          // Animar columnas de fichas apiladas
          stackColumns.forEach(column => {
            column.classList.add('animate');
          });
          
          // Animar checkmarks
          setTimeout(() => {
            stackCheckmarks.forEach(checkmark => {
              checkmark.classList.add('animate');
            });
          }, 800);
          
          // Activar animación de puntos de automatización
          automationDots.forEach(dot => {
            dot.style.animation = 'pulseColor 2s ease-in-out infinite';
          });
          
          reportCardAnimated = true;
        } else if (!isInViewport && reportCardAnimated) {
          // Quitar animaciones de columnas
          stackColumns.forEach(column => {
            column.classList.remove('animate');
            void column.offsetWidth;
          });
          
          // Quitar animaciones de checkmarks
          stackCheckmarks.forEach(checkmark => {
            checkmark.classList.remove('animate');
            void checkmark.offsetWidth;
          });
          
          // Detener animación de puntos de automatización
          automationDots.forEach(dot => {
            dot.style.animation = 'none';
          });
          
          reportCardAnimated = false;
        }
      }
      
      // Función para animar la card de talentos
      function animateTalentCard() {
        const isInViewport = isElementInViewport(talentCard);
        
        if (isInViewport && !talentCardAnimated) {
          if (talentLinePath) talentLinePath.classList.add('animate');
          talentPoints.forEach(point => {
            point.classList.add('animate');
          });
          talentCardAnimated = true;
        } else if (!isInViewport && talentCardAnimated) {
          if (talentLinePath) {
            talentLinePath.classList.remove('animate');
            void talentLinePath.offsetWidth;
          }
          talentPoints.forEach(point => {
            point.classList.remove('animate');
            void point.offsetWidth;
          });
          talentCardAnimated = false;
        }
      }
      
      // Función principal de animación
      function animateAll() {
        animateDashboard();
        animateCostCard();
        animateReportCard();
        animateTalentCard();
      }
      
      // Ejecutar al cargar y al hacer scroll
      window.addEventListener('load', animateAll);
      window.addEventListener('scroll', animateAll);
      
      // Inicializar animaciones
      setTimeout(animateAll, 100);
    }
    
    // Inicializar animaciones al cargar la página
    document.addEventListener('DOMContentLoaded', initScrollAnimations);
  </script>
</body>
</html>