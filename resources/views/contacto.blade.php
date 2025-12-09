<x-app-layout>
    <div class="bg-white min-h-screen flex flex-col">
        <!-- Main Content -->
        <div class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start">
                
                <!-- Left Column: Text -->
                <div class="space-y-8">
                    <h1 class="text-5xl font-bold text-[#0976D6] uppercase tracking-wide">Contáctanos</h1>
                    
                    <div class="space-y-4 text-gray-800">
                        <h2 class="text-2xl font-bold text-[#0F172A]">
                            ¿Tienes preguntas o necesitas una<br>solución a medida?
                        </h2>
                        
                        <p class="text-lg leading-relaxed text-gray-600">
                            Estamos aquí para ayudarte. Cuéntanos lo que necesitas y te responderemos con una propuesta clara, efectiva y pensada para tu equipo.
                        </p>
                    </div>

                    <div class="pt-4">
                        <p class="text-xl font-bold italic text-[#0F172A]">
                            Nos comprometemos a brindarte<br>una respuesta de valor
                        </p>
                    </div>
                </div>

                <!-- Right Column: Form -->
                <div>
                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <input type="text" name="name" placeholder="Nombre y Apellido" class="w-full rounded-lg border-[#0976D6] border-2 px-4 py-3 placeholder-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-200 transition outline-none" required>
                            </div>
                            <!-- Email -->
                            <div>
                                <input type="email" name="email" placeholder="Email" class="w-full rounded-lg border-[#0976D6] border-2 px-4 py-3 placeholder-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-200 transition outline-none" required>
                            </div>
                        </div>

                        <!-- Company -->
                        <div>
                            <input type="text" name="company" placeholder="Empresa" class="w-full rounded-lg border-[#0976D6] border-2 px-4 py-3 placeholder-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-200 transition outline-none">
                        </div>

                        <!-- Message -->
                        <div>
                            <textarea name="message" rows="6" placeholder="Mensaje" class="w-full rounded-lg border-[#0976D6] border-2 px-4 py-3 placeholder-gray-300 focus:border-blue-600 focus:ring focus:ring-blue-200 transition outline-none resize-none" required></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" class="w-full bg-[#0F172A] text-white font-medium rounded-full py-3.5 px-6 hover:bg-gray-800 transition duration-300 shadow-lg">
                                Enviar mensaje
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer (Custom for this page or matching design) -->
        <footer class="bg-[#F3F4F6] py-8 mt-auto">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-gray-600 text-sm">
                    &copy; 2025 Obertrack. Todos los derechos reservados
                </p>
            </div>
        </footer>
    </div>
</x-app-layout>
