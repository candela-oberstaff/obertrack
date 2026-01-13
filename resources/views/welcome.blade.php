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
          '100%': { height: '100%' }
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
        pulseColor: {
          '0%, 100%': { opacity: 0.3, transform: 'scale(1)' },
          '50%': { opacity: 1, transform: 'scale(1.2)' }
        }
      },
      animation: {
        typing: 'typing 8s steps(40) infinite',
        scroll: 'scroll 25s linear infinite',
        float: 'float 3s ease-in-out infinite',
        pulseGlow: 'pulseGlow 2s ease-in-out infinite',
        dash: 'dash 3s ease-in-out infinite',
        growBar: 'growBar 1.5s ease-out forwards',
        countUp: 'countUp 0.8s ease-out forwards',
        fadeInUp: 'fadeInUp 0.6s ease-out forwards',
        bounceIn: 'bounceIn 0.8s ease-out forwards',
        slideInRight: 'slideInRight 0.5s ease-out forwards',
        shimmer: 'shimmer 2s infinite linear',
        progressGrow: 'progressGrow 1.2s ease-out forwards',
        pulseColor: 'pulseColor 2s ease-in-out infinite'
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
  
  .bar-graph {
    position: relative;
    height: 100px;
  }
  
  .bar-graph div {
    position: absolute;
    bottom: 0;
    width: 20px;
    background: #22A9C8;
    border: 2px solid #1B1725;
  }
  
  .pie-chart {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: conic-gradient(
      #22A9C8 0% 30%,
      #FFDE59 30% 60%,
      #00D4AA 60% 90%,
      #9D4EDD 90% 100%
    );
    border: 3px solid #1B1725;
  }
  
  .line-graph {
    position: relative;
    height: 80px;
    width: 100%;
  }
  
  .line-graph svg {
    position: absolute;
    width: 100%;
    height: 100%;
  }
  
  .cost-graph {
    height: 120px;
    width: 100%;
    position: relative;
    margin-top: 20px;
  }
  
  .graph-bar {
    position: absolute;
    bottom: 0;
    width: 20px;
    background: #22A9C8;
    border: 2px solid #1B1725;
    animation: growBar 1.5s ease-out forwards;
    transform-origin: bottom;
  }
  
  .audit-graph {
    height: 100px;
    width: 100%;
    position: relative;
    margin-top: 20px;
  }
  
  .talent-graph {
    height: 100px;
    width: 100%;
    position: relative;
    margin-top: 20px;
  }

  /* Animación para las barras de progreso */
  .progress-bar-inner {
    --final-width: 75%;
    animation: progressGrow 1.2s ease-out forwards;
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
        <div class="brutal-card p-1 h-full animate-fadeInUp" style="animation-delay: 0.4s">
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
                <div class="text-2xl font-bold animate-countUp" style="animation-delay: 0.7s">12</div>
                <div class="text-xs text-gray-500 animate-fadeInUp" style="animation-delay: 0.8s">+2 esta semana</div>
              </div>
              
              <div class="brutal-border-thin p-3 animate-fadeInUp metric-card" style="animation-delay: 0.7s">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold">Horas Totales</span>
                 
                </div>
                <div class="text-2xl font-bold animate-countUp" style="animation-delay: 0.8s">122</div>
                <div class="text-xs text-gray-500 animate-fadeInUp" style="animation-delay: 0.9s">+15.4% vs mes anterior</div>
              </div>
              
              <div class="brutal-border-thin p-3 animate-fadeInUp metric-card" style="animation-delay: 0.8s">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold">Tareas</span>
                  
                </div>
                <div class="text-2xl font-bold animate-countUp" style="animation-delay: 0.9s">89</div>
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
                  <div class="h-full bg-brutalGreen progress-bar-inner" style="width: 75%;"></div>
                </div>
              </div>
              
              <div class="brutal-border-thin p-3 animate-fadeInUp progress-card" style="animation-delay: 1.1s">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold animate-fadeInUp" style="animation-delay: 1.2s">Eficiencia</span>
                  <span class="text-xs font-bold text-brandBlue animate-fadeInUp" style="animation-delay: 1.3s">+22%</span>
                </div>
                <div class="h-2 bg-gray-200 brutal-border-thin overflow-hidden">
                  <div class="h-full bg-brandBlue progress-bar-inner efficiency" style="width: 66%;"></div>
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
        <!-- Card 1 - Control de Costos con gráfica -->
        <div class="brutal-card p-8 transition-transform duration-300 hover:scale-105" style="animation-delay: 0.3s">
          <h3 class="text-xl font-extrabold text-brandBlack uppercase mb-3">Control de Costos</h3>
          <p class="text-sm mb-4 font-poppins">Identifica exactamente en qué se invierte el presupuesto y elimina horas muertas.</p>
          
          <!-- Gráfica de barras para costos -->
          <div class="cost-graph">
            <div class="graph-bar left-0 h-3/4" style="animation-delay: 0.1s"></div>
            <div class="graph-bar left-8 h-1/2" style="animation-delay: 0.3s"></div>
            <div class="graph-bar left-16 h-full bg-brutalRed" style="animation-delay: 0.5s"></div>
            <div class="graph-bar left-24 h-2/3" style="animation-delay: 0.7s"></div>
            <div class="graph-bar left-32 h-3/4" style="animation-delay: 0.9s"></div>
            <div class="graph-bar left-40 h-1/2" style="animation-delay: 1.1s"></div>
            <div class="graph-bar left-48 h-5/6" style="animation-delay: 1.3s"></div>
          </div>
          
          <div class="mt-4 flex justify-between text-xs font-bold">
            <span>Ene</span>
            <span>Feb</span>
            <span>Mar</span>
            <span>Abr</span>
            <span>May</span>
            <span>Jun</span>
            <span>Jul</span>
          </div>
        </div>
        
        <!-- Card 2 - Auditorías con gráfica -->
        <div class="brutal-card p-8 transition-transform duration-300 hover:scale-105" style="animation-delay: 0.4s">
          <h3 class="text-xl font-extrabold text-brandBlack uppercase mb-3">Auditorías de Rentabilidad</h3>
          <p class="text-sm mb-4 font-poppins">Exporta informes listos para presentar a clientes o gerencia en un clic.</p>
          
          <!-- Gráfica circular para auditorías -->
          <div class="audit-graph flex items-center justify-center">
            <div class="relative w-20 h-20">
              <div class="absolute inset-0 rounded-full border-3 border-brandBlack"></div>
              <div class="absolute inset-3 bg-brandBlue rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-sm">85%</span>
              </div>
              <div class="absolute inset-0">
                <svg width="80" height="80" viewBox="0 0 80 80">
                  <circle cx="40" cy="40" r="35" fill="none" stroke="#FFDE59" stroke-width="6" 
                          stroke-dasharray="220" stroke-dashoffset="33" transform="rotate(-90 40 40)"/>
                </svg>
              </div>
            </div>
          </div>
          
          <div class="mt-4 text-center text-xs font-bold">
            <span class="text-brandBlue">Eficiencia: 85%</span>
          </div>
        </div>
        
        <!-- Card 3 - Optimización con gráfica -->
        <div class="brutal-card p-8 transition-transform duration-300 hover:scale-105" style="animation-delay: 0.5s">
          <h3 class="text-xl font-extrabold text-brandBlack uppercase mb-3">Optimización de Talento</h3>
          <p class="text-sm mb-4 font-poppins">Asigna la carga de trabajo de forma equitativa y evita el burnout de tu equipo.</p>
          
          <!-- Gráfica de líneas para talento -->
          <div class="talent-graph">
            <svg width="100%" height="100%" viewBox="0 0 200 100" class="brutal-border-thin p-2">
              <polyline points="10,80 40,40 70,60 100,20 130,50 160,10 190,30" 
                       fill="none" 
                       stroke="#9D4EDD" 
                       stroke-width="3"
                       stroke-linecap="round"
                       stroke-linejoin="round"/>
              <circle cx="40" cy="40" r="4" fill="#9D4EDD" stroke="#1B1725" stroke-width="2"/>
              <circle cx="100" cy="20" r="4" fill="#9D4EDD" stroke="#1B1725" stroke-width="2"/>
              <circle cx="160" cy="10" r="4" fill="#9D4EDD" stroke="#1B1725" stroke-width="2"/>
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

    // Animación para gráficas al hacer scroll
    const observerOptions = {
      threshold: 0.2,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const graphs = entry.target.querySelectorAll('.graph-bar');
          graphs.forEach((bar, index) => {
            bar.style.animation = `growBar 1.5s ease-out ${index * 0.2}s forwards`;
          });
        }
      });
    }, observerOptions);

    document.querySelectorAll('.brutal-card').forEach(card => {
      observer.observe(card);
    });

    // Animación de conteo para los números del dashboard
    function animateCountUp() {
      const counters = document.querySelectorAll('.animate-countUp');
      counters.forEach(counter => {
        const target = parseInt(counter.textContent);
        let current = 0;
        const increment = target / 30;
        const timer = setInterval(() => {
          current += increment;
          if (current >= target) {
            counter.textContent = target;
            clearInterval(timer);
          } else {
            counter.textContent = Math.floor(current);
          }
        }, 50);
      });
    }

    // Ejecutar animación de conteo cuando el dashboard sea visible
    const dashboardObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          setTimeout(animateCountUp, 800); // Retardo para que coincida con la animación de entrada
        }
      });
    });

    const dashboard = document.querySelector('.brutal-card');
    if (dashboard) {
      dashboardObserver.observe(dashboard);
    }
  </script>
</body>
</html>