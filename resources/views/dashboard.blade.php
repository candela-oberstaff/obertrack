<x-app-layout>
    <div class="py-12 bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Welcome Section -->
            <x-dashboard.welcome-section />

            <!-- Action Cards -->
            <x-dashboard.action-cards />

            <!-- Resource Center -->
            <x-dashboard.resource-center />

            <!-- Bottom Banner -->
            <x-dashboard.bottom-banner />

        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-100 py-8 text-center">
        <p class="text-gray-500 text-sm flex items-center justify-center gap-1">
            <span class="font-bold">©</span> 2025 Obertrack. Todos los derechos reservados
        </p>
    </footer>
</x-app-layout>
