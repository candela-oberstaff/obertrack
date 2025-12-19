<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Obertrack - Remote Work Tracking Solution</title>
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

        <meta name="description" content="Obertrack: Effortless remote work tracking and task management for distributed teams. Boost productivity and streamline your workflow.">
        <meta name="keywords" content="remote work, task tracking, productivity, time management, project management">


        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-white">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Intro.js logic
                window.startTour = function() {
                    const intro = introJs();
                    const route = window.location.pathname;
                    
                    // Define steps based on current route
                    let steps = [];

                    // Common: Welcome step
                    steps.push({
                        title: 'Bienvenido a Obertrack',
                        intro: 'Te daremos un recorrido rápido por las funciones principales de esta sección.'
                    });

                    // Route-specific steps
                    if (route.includes('/dashboard') && !route.includes('/empleador')) {
                        // Employee Dashboard
                        steps.push({
                            element: document.querySelector('#dashboard-stats-cards'),
                            title: 'Resumen de Actividades',
                            intro: 'Aquí podés ver tus tareas pendientes, horas registradas y tareas completadas del mes.'
                        });
                        steps.push({
                            element: document.querySelector('#dashboard-latest-tasks'),
                            title: 'Últimas Tareas',
                            intro: 'Esta tabla muestra tus tareas más recientes. Hacé click en cualquier fila para ver los detalles completos.'
                        });
                        steps.push({
                            element: document.querySelector('#dashboard-latest-comments'),
                            title: 'Últimos Comentarios',
                            intro: 'Acá aparecen los comentarios más recientes en tus tareas.'
                        });
                    } else if (route.includes('/empleador/dashboard')) {
                        // Employer Dashboard
                        steps.push({
                            element: document.querySelector('#employer-stats-cards'),
                            title: 'Monitoreo de Horas',
                            intro: 'Estas tarjetas muestran el progreso de horas registradas por cada profesional.'
                        });
                        steps.push({
                            element: document.querySelector('#employer-calendar'),
                            title: 'Calendario Interactivo',
                            intro: 'Hacé click en cualquier día para ver las horas registradas por tu equipo.'
                        });
                    } else if (route.includes('/empleador/tareas')) {
                        // Employer Tasks
                        steps.push({
                            element: document.querySelector('#team-tasks-section'),
                            title: 'Asignaciones en Equipo',
                            intro: 'Aquí podés gestionar las tareas asignadas a todo el equipo.'
                        });
                        steps.push({
                            element: document.querySelector('#create-team-task-btn'),
                            title: 'Crear Tarea de Equipo',
                            intro: 'Hacé click aquí para crear una nueva tarea para todo el equipo.'
                        });
                        steps.push({
                            element: document.querySelector('#individual-tasks-section'),
                            title: 'Asignaciones Individuales',
                            intro: 'Más abajo encontrarás las tareas asignadas individualmente a cada profesional.'
                        });
                    } else if (route.includes('/reportes')) {
                        // Reportes
                        steps.push({
                            element: document.querySelector('#reportes-professionals-list'),
                            title: 'Lista de Profesionales',
                            intro: 'Aquí podés ver todos los profesionales registrados con sus estadísticas semanales.'
                        });
                        const firstCard = document.querySelector('.reportes-professional-card');
                        if (firstCard) {
                            steps.push({
                                element: firstCard,
                                title: 'Tarjeta de Profesional',
                                intro: 'Cada tarjeta muestra el promedio de horas semanales, ausencias y tareas incompletas del profesional.'
                            });
                        }
                        const reportButton = document.querySelector('#reportes-view-button');
                        if (reportButton) {
                            steps.push({
                                element: reportButton,
                                title: 'Ver Reporte Completo',
                                intro: 'Hacé click en "Ver reporte completo" para ver el detalle de horas y descargar PDFs.'
                            });
                        }
                    } else if (route.includes('/chat')) {
                        // Chat
                        steps.push({
                            element: document.querySelector('#chat-contacts-sidebar'),
                            title: 'Lista de Contactos',
                            intro: 'Aquí aparecen todos tus contactos disponibles para chatear.'
                        });
                        const searchBar = document.querySelector('#chat-search-bar');
                        if (searchBar) {
                            steps.push({
                                element: searchBar,
                                title: 'Buscar Contactos',
                                intro: 'Usá esta barra para buscar rápidamente un contacto específico.'
                            });
                        }
                        const contactsList = document.querySelector('#chat-contacts-list');
                        if (contactsList) {
                            steps.push({
                                element: contactsList,
                                title: 'Seleccionar Contacto',
                                intro: 'Hacé click en cualquier contacto para abrir la conversación.'
                            });
                        }
                        const messagesArea = document.querySelector('#chat-messages-area');
                        if (messagesArea) {
                            steps.push({
                                element: messagesArea,
                                title: 'Área de Mensajes',
                                intro: 'Aquí verás el historial completo de la conversación.'
                            });
                        }
                        const messageInput = document.querySelector('#chat-message-input');
                        if (messageInput) {
                            steps.push({
                                element: messageInput,
                                title: 'Enviar Mensajes',
                                intro: 'Escribí tu mensaje aquí y podés adjuntar archivos usando el ícono de clip.'
                            });
                        }
                    } else if (route.includes('/empleado/registrar-horas')) {
                        // Registrar Horas
                        steps.push({
                            element: document.querySelector('#registrar-horas-summary'),
                            title: 'Resumen de Horas',
                            intro: 'Aquí podés ver el total de horas registradas del mes y tu progreso hacia las 160 horas objetivo.'
                        });
                        const monthNav = document.querySelector('#registrar-horas-month-nav');
                        if (monthNav) {
                            steps.push({
                                element: monthNav,
                                title: 'Navegación de Meses',
                                intro: 'Usá las flechas para moverte entre diferentes meses y ver tu historial.'
                            });
                        }
                        const calendar = document.querySelector('#registrar-horas-calendar');
                        if (calendar) {
                            steps.push({
                                element: calendar,
                                title: 'Calendario de Registro',
                                intro: 'Hacé click en "Registrar horas" en cada día para agregar tus horas trabajadas.'
                            });
                        }
                    } else if (route.includes('/profile')) {
                        // Profile / Configuración
                        steps.push({
                            element: document.querySelector('#profile-personal-info'),
                            title: 'Información Personal',
                            intro: 'Aquí podés ver y editar tu información personal como nombre y empresa.'
                        });
                        const accountConfig = document.querySelector('#profile-account-config');
                        if (accountConfig) {
                            steps.push({
                                element: accountConfig,
                                title: 'Configuración de Cuenta',
                                intro: 'Gestioná tu correo electrónico y contraseña desde esta sección.'
                            });
                        }
                        const professionalsList = document.querySelector('#profile-professionals-list');
                        if (professionalsList) {
                            steps.push({
                                element: professionalsList,
                                title: 'Profesionales Registrados',
                                intro: 'Como empleador, podés ver, promover y gestionar a todos tus profesionales desde aquí.'
                            });
                        }
                        const dangerZone = document.querySelector('#profile-danger-zone');
                        if (dangerZone) {
                            steps.push({
                                element: dangerZone,
                                title: 'Zona Peligrosa',
                                intro: 'Desde aquí podés eliminar permanentemente tu cuenta. Esta acción no se puede deshacer.'
                            });
                        }
                    }

                    // Common: Navigation
                    if (document.querySelector('nav')) {
                        steps.push({
                            element: document.querySelector('nav'),
                            title: 'Navegación',
                            intro: 'Usá este menú para moverte entre Dashboard, Tareas y Chat.'
                        });
                    }

                    // Common: Chat notification (if exists)
                    const chatNotif = document.querySelector('[href*="chat"]');
                    if (chatNotif) {
                        steps.push({
                            element: chatNotif,
                            title: 'Chat',
                            intro: 'Comunicate con tu equipo en tiempo real desde aquí.'
                        });
                    }

                    // Common: Profile
                    const profileDropdown = document.querySelector('.relative.ml-3');
                    if (profileDropdown) {
                        steps.push({
                            element: profileDropdown,
                            title: 'Perfil',
                            intro: 'Acá podés gestionar tu cuenta, reiniciar este tour, y cerrar sesión.'
                        });
                    }

                    // Filter out steps where elements don't exist
                    const validSteps = steps.filter(step => {
                        if (step.element) {
                            return step.element !== null;
                        }
                        return true; // Steps without elements (intro only) are always valid
                    });

                    intro.setOptions({
                        steps: validSteps,
                        nextLabel: 'Siguiente',
                        prevLabel: 'Anterior',
                        doneLabel: 'Listo',
                        exitOnOverlayClick: false,
                        showProgress: true,
                        showBullets: false
                    });

                    intro.oncomplete(function() {
                        markTourAsCompleted();
                    });

                    intro.onexit(function() {
                        markTourAsCompleted();
                    });

                    intro.start();
                };

                function markTourAsCompleted() {
                    fetch('{{ route('tour.complete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(response => {
                        console.log('Tour marked as completed');
                    }).catch(error => {
                        console.error('Error marking tour as completed', error);
                    });
                }

                @auth
                    @if(is_null(Auth::user()->tour_completed_at))
                        // Start tour automatically if not completed
                        // Use a small timeout to ensure DOM is ready and animations finished
                        setTimeout(() => {
                            window.startTour();
                        }, 1000);
                    @endif
                @endauth
            });
        </script>
    </body>
</html>
