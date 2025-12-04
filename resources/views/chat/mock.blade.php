<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-[calc(100vh-12rem)] flex">
                <!-- Sidebar / Contact List -->
                <div class="w-1/3 border-r border-gray-200 flex flex-col">
                    <!-- Header -->
                    <div class="bg-gray-100 p-4 border-b border-gray-200 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="font-semibold text-gray-700">Chats</span>
                        </div>
                        <div class="flex gap-3 text-gray-500">
                            <button><i class="fas fa-circle-notch"></i></button>
                            <button><i class="fas fa-comment-alt"></i></button>
                            <button><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                    
                    <!-- Search -->
                    <div class="p-2 bg-white border-b border-gray-100">
                        <div class="bg-gray-100 rounded-lg flex items-center px-3 py-2">
                            <i class="fas fa-search text-gray-400 mr-2"></i>
                            <input type="text" placeholder="Buscar o iniciar un nuevo chat" class="bg-transparent border-none focus:ring-0 w-full text-sm text-gray-700 placeholder-gray-500">
                        </div>
                    </div>

                    <!-- Contact List (Mock) -->
                    <div class="flex-1 overflow-y-auto">
                        <!-- Active Contact -->
                        <div class="flex items-center p-3 bg-gray-100 cursor-pointer border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3 flex-shrink-0">
                                S
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline mb-1">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">Soporte Obertrack</h3>
                                    <span class="text-xs text-gray-500">10:42 AM</span>
                                </div>
                                <p class="text-sm text-gray-600 truncate">Hola, ¿en qué podemos ayudarte hoy?</p>
                            </div>
                        </div>

                        <!-- Other Contacts -->
                        <div class="flex items-center p-3 cursor-pointer border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold mr-3 flex-shrink-0">
                                RR
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline mb-1">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">Recursos Humanos</h3>
                                    <span class="text-xs text-gray-500">Ayer</span>
                                </div>
                                <p class="text-sm text-gray-500 truncate">Documentación recibida, gracias.</p>
                            </div>
                        </div>

                        <div class="flex items-center p-3 cursor-pointer border-b border-gray-100 hover:bg-gray-50 transition">
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold mr-3 flex-shrink-0">
                                IT
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline mb-1">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">Soporte Técnico</h3>
                                    <span class="text-xs text-gray-500">Martes</span>
                                </div>
                                <p class="text-sm text-gray-500 truncate">El ticket #402 ha sido resuelto.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="w-2/3 flex flex-col bg-[#efeae2] relative">
                    <!-- Chat Header -->
                    <div class="bg-gray-100 p-4 border-b border-gray-200 flex justify-between items-center z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                S
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Soporte Obertrack</h3>
                                <p class="text-xs text-gray-500">en línea</p>
                            </div>
                        </div>
                        <div class="flex gap-4 text-gray-500">
                            <button><i class="fas fa-search"></i></button>
                            <button><i class="fas fa-paperclip"></i></button>
                            <button><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="flex-1 overflow-y-auto p-8 space-y-4 bg-opacity-50" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');">
                        <!-- Message Received -->
                        <div class="flex justify-start">
                            <div class="bg-white rounded-lg p-3 max-w-md shadow-sm relative">
                                <p class="text-sm text-gray-800">¡Hola {{ auth()->user()->name }}! Bienvenido al sistema de chat de Obertrack.</p>
                                <span class="text-[10px] text-gray-500 block text-right mt-1">10:41 AM</span>
                            </div>
                        </div>

                        <!-- Message Received -->
                        <div class="flex justify-start">
                            <div class="bg-white rounded-lg p-3 max-w-md shadow-sm relative">
                                <p class="text-sm text-gray-800">Este es un chat de demostración (Mock) mientras integramos la API de WhatsApp (Waha).</p>
                                <span class="text-[10px] text-gray-500 block text-right mt-1">10:41 AM</span>
                            </div>
                        </div>

                        <!-- Message Received -->
                        <div class="flex justify-start">
                            <div class="bg-white rounded-lg p-3 max-w-md shadow-sm relative">
                                <p class="text-sm text-gray-800">¿En qué podemos ayudarte hoy?</p>
                                <span class="text-[10px] text-gray-500 block text-right mt-1">10:42 AM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="bg-gray-100 p-3 flex items-center gap-4 z-10">
                        <button class="text-gray-500 hover:text-gray-700">
                            <i class="far fa-smile text-xl"></i>
                        </button>
                        <button class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-plus text-xl"></i>
                        </button>
                        <div class="flex-1 bg-white rounded-lg px-4 py-2">
                            <input type="text" placeholder="Escribe un mensaje" class="w-full border-none focus:ring-0 text-sm bg-transparent" disabled>
                        </div>
                        <button class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-microphone text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
