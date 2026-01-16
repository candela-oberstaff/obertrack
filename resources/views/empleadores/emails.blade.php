<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comunicación Masiva') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Mass Communication Form (Left) -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                    <div class="p-8 text-gray-900 font-extrabold text-xl border-b border-gray-50 flex items-center gap-3">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        Redactar Nuevo Correo
                    </div>
                    <div class="p-8">
                        <!-- Quill Styles -->
                        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

                        <form action="{{ route('empleador.mass-email') }}" method="POST" class="space-y-6" id="massEmailForm" enctype="multipart/form-data">
                            @csrf
                            
                            @if(session('success'))
                                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Destinatarios</label>
                                    <select name="recipient_id" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">Todo el equipo ({{ $allProfessionals->count() }} profesionales)</option>
                                        @foreach($allProfessionals as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Deja en blanco para enviar a todos.</p>
                                </div>
                                
                                <div class="relative">
                                     <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Adjuntar Archivos</label>
                                     <input type="file" name="attachments[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                                     <p class="mt-1 text-xs text-gray-400">PDF, Imágenes, Word, Excel (Max 10MB)</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Asunto del Correo</label>
                                <input type="text" name="subject" required class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Ej: Importante: Actualiza tus horas">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Cuerpo del Mensaje</label>
                                <!-- Quill Editor -->
                                <div class="rounded-xl overflow-hidden border border-gray-200">
                                    <div id="editor-container" style="height: 350px; background: white; border: none;"></div>
                                </div>
                                <input type="hidden" name="message" id="message">
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" id="submitBtn" class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-200 active:scale-95">
                                    <span>Enviar Comunicación</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Stats Sidebar (Right) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                        <h3 class="text-gray-900 font-extrabold text-lg mb-6 flex items-center gap-2">
                             <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                             </div>
                             Información
                        </h3>
                        
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Utiliza esta herramienta para enviar comunicados oficiales a tu equipo de trabajo. Los correos se enviarán individualmente a cada destinatario seleccionado.
                            </p>
                            
                            <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100">
                                <h4 class="text-xs font-black text-yellow-600 uppercase tracking-widest mb-2">Recomendaciones</h4>
                                <ul class="list-disc list-inside text-xs text-yellow-700 space-y-1">
                                    <li>Sé claro y conciso en el asunto.</li>
                                    <li>Utiliza el formato para resaltar puntos clave.</li>
                                    <li>Verifica los archivos adjuntos antes de enviar.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scripts Section -->
            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
            <script>
                var quill = new Quill('#editor-container', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'header': [1, 2, false] }],
                            [{ 'color': [] }],
                            ['link', 'clean']
                        ]
                    }
                });

                // Form Submission
                document.getElementById('massEmailForm').addEventListener('submit', function(e) {
                    let htmlContent = quill.root.innerHTML;
                    // Generic validation for empty editor
                    if (quill.getText().trim().length === 0 && htmlContent.indexOf('<img') === -1) {
                         alert('Por favor escribe un mensaje.');
                         e.preventDefault();
                         return;
                    }
                    document.getElementById('message').value = htmlContent;
                    
                    // Button state
                    const btn = document.getElementById('submitBtn');
                    btn.disabled = true;
                    btn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enviando...
                    `;
                });
            </script>

        </div>
    </div>
</x-app-layout>
