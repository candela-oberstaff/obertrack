<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden transform hover:scale-[1.01] transition-transform duration-300 relative">
            
            <!-- Decorative Background Patterns -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-green-100 opacity-50 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 rounded-full bg-blue-100 opacity-50 blur-3xl"></div>

            <div class="relative p-8 sm:p-12 text-center">
                
                <!-- Fun Icon/Graphic Area -->
                <div class="mb-8 relative inline-block">
                    <div class="absolute inset-0 bg-green-200 rounded-full animate-ping opacity-25"></div>
                    <div class="w-32 h-32 rounded-full flex items-center justify-center shadow-lg mx-auto relative z-10" style="background: linear-gradient(135deg, #4ade80 0%, #25D366 100%);">
                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                    </div>
                </div>

                <!-- Text Content -->
                <h1 class="text-4xl font-black text-gray-800 mb-4 tracking-tight">
                    ¡WhatsApp está en el horno! 🍕
                </h1>
                
                <p class="text-lg text-gray-600 mb-8 max-w-lg mx-auto leading-relaxed">
                    <span class="font-bold text-green-600">Muy pronto</span> podrás chatear con tus contactos directamente desde aquí sin despeinarte.
                </p>

                <!-- Progress Bar Mockup -->
                <div class="max-w-xs mx-auto mb-8">
                    <div class="flex justify-between text-xs font-bold text-gray-500 mb-1">
                        <span>Conectando...</span>
                        <span>98%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-emerald-500 h-3 rounded-full animate-pulse" style="width: 98%"></div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-center gap-4">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-xl shadow-sm text-white bg-gray-800 hover:bg-gray-700 transition-all transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Volver al Dashboard
                    </a>
                </div>

                <!-- Footer Note -->
                <p class="mt-8 text-xs text-gray-400 font-medium uppercase tracking-widest">
                    Prometemos que valdrá la pena la espera 😉
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
