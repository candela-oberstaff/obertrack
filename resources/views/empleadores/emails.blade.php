<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comunicación Masiva') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                       <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ 
                activeTab: '{{ session('active_tab', 'email') }}',
                recipientId: '{{ request('recipient_id', '') }}',
                waStatus: 'LOADING',
                waQr: null,
                waError: null,
                isCheckingWa: false,
                waCheckCount: 0,
                async checkWaStatus() {
                    if (this.isCheckingWa || this.activeTab !== 'whatsapp') return;
                    this.isCheckingWa = true;
                    try {
                        let res = await fetch('{{ route('empleador.whatsapp.status') }}');
                        if (!res.ok) throw new Error('Status Error');
                        let data = await res.json();
                        
                        // If status transitions to SCAN_QR_CODE, reset counter or just update
                        this.waStatus = data.status;
                        this.waQr = data.qr;
                        this.waError = null;
                        this.waCheckCount++;
                    } catch (e) { 
                        console.warn('WA Status Error'); 
                        // Only show error after some retries
                        if (this.waCheckCount > 3) this.waError = 'Conexión interrumpida con el servicio.';
                    }
                    finally { this.isCheckingWa = false; }
                },
                async startWaSession(force = false) {
                    this.waStatus = 'STARTING';
                    this.waError = null;
                    this.waCheckCount = 0;
                    try {
                        let url = '{{ route('empleador.whatsapp.start') }}';
                        if (force) url += '?force=true';
                        
                        let res = await fetch(url, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        let data = await res.json();
                        if (data.error) {
                            this.waError = data.error;
                            this.waStatus = 'STOPPED';
                        }
                    } catch (e) { 
                        this.waError = 'Error al iniciar sesión. Verifica tu conexión.';
                        this.waStatus = 'STOPPED';
                    }
                }
            }" x-init="setInterval(() => checkWaStatus(), 2000); checkWaStatus();">
                <!-- Mass Communication Form (Left) -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-100 px-8 pt-6">
                        <button 
                            @click="activeTab = 'email'"
                            :class="activeTab === 'email' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-400 hover:text-gray-600'"
                            class="px-6 py-3 font-black uppercase text-xs tracking-widest transition-all"
                        >
                            Email
                        </button>
                        <button 
                            @click="activeTab = 'whatsapp'"
                            :class="activeTab === 'whatsapp' ? 'border-b-2 border-green-500 text-green-500' : 'text-gray-400 hover:text-gray-600'"
                            class="px-6 py-3 font-black uppercase text-xs tracking-widest transition-all flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
                            WhatsApp
                        </button>
                    </div>

                    <!-- Email Form -->
                    <div x-show="activeTab === 'email'" class="p-8">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <h2 class="text-gray-900 font-extrabold text-xl">Redactar Correo Masivo</h2>
                        </div>

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
                                    <select name="recipient_id" x-model="recipientId" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
                                <div class="rounded-xl overflow-hidden border border-gray-200">
                                    <div id="editor-container" style="height: 350px; background: white; border: none;"></div>
                                </div>
                                <input type="hidden" name="message" id="message">
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" id="submitBtn" class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-200 active:scale-95">
                                    <span>Enviar Correo</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- WhatsApp Form -->
                    <div x-show="activeTab === 'whatsapp'" class="p-8" x-cloak>
                        <!-- WhatsApp Connection Status -->
                        <div class="mb-10 bg-gray-50 p-6 rounded-3xl border border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-xl shadow-sm">
                                        <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Estado de Conexión</p>
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full" :class="{
                                                'bg-green-500 animate-pulse': waStatus === 'WORKING',
                                                'bg-yellow-500 animate-pulse': waStatus === 'SCAN_QR_CODE' || waStatus === 'STARTING',
                                                'bg-red-500': waStatus === 'STOPPED' || waStatus === 'FAILED',
                                                'bg-gray-300': waStatus === 'LOADING'
                                            }"></span>
                                            <span class="text-sm font-black uppercase tracking-tighter text-gray-700" x-text="
                                                waStatus === 'WORKING' ? 'Conectado' : 
                                                (waStatus === 'SCAN_QR_CODE' ? 'Esperando Escaneo' : 
                                                (waStatus === 'STARTING' ? 'Iniciando...' : 
                                                (waStatus === 'STOPPED' ? 'Desconectado' : 'Cargando...')))
                                            "></span>
                                        </div>
                                    </div>
                                </div>

                                <button 
                                    x-show="waStatus === 'STOPPED' || waStatus === 'FAILED' || (waStatus === 'STARTING' && waCheckCount > 5)"
                                    @click="startWaSession(waStatus === 'STARTING')"
                                    class="text-[10px] font-black uppercase tracking-widest bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all shadow-sm shadow-green-100"
                                >
                                    <span x-text="waStatus === 'STARTING' ? 'Forzar Reinicio' : 'Conectar WhatsApp'"></span>
                                </button>
                            </div>

                            <!-- QR Code Section -->
                            <div x-show="waStatus === 'SCAN_QR_CODE'" class="flex flex-col items-center py-4 bg-white rounded-2xl border border-gray-100 animate-in fade-in zoom-in duration-300">
                                <div class="mb-4">
                                    <template x-if="waQr">
                                        <div class="p-2 bg-white border-2 border-dashed border-gray-100 rounded-xl">
                                            <img :src="waQr" class="w-48 h-48 object-contain mx-auto" alt="WhatsApp QR Code">
                                        </div>
                                    </template>
                                    <template x-if="!waQr">
                                        <div class="w-48 h-48 flex items-center justify-center">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                                        </div>
                                    </template>
                                </div>
                                <p class="text-xs text-gray-500 text-center max-w-xs px-4">
                                    Escanea este código con tu WhatsApp (Configuración > Dispositivos vinculados) para poder enviar mensajes masivos.
                                </p>
                            </div>

                            <div x-show="waStatus === 'WORKING'" class="bg-green-50/50 p-4 rounded-2xl border border-green-100 flex items-center gap-3">
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-green-600 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <p class="text-xs text-green-700 font-medium">WhatsApp está listo para enviar comunicaciones masivas.</p>
                            </div>

                            <!-- Error Message -->
                            <div x-show="waError" class="mt-4 p-4 bg-red-50 text-red-700 rounded-2xl border border-red-100 animate-in slide-in-from-top-2 duration-300">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <div>
                                        <p class="text-xs font-bold mb-1">Problema de conexión</p>
                                        <p class="text-[10px] leading-relaxed" x-text="waError"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form restricted by status -->
                        <div :class="waStatus !== 'WORKING' ? 'opacity-40 pointer-events-none grayscale' : ''">
                            <div class="mb-6 flex items-center gap-3">
                                <div class="p-2 bg-green-50 text-green-600 rounded-xl">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
                                </div>
                                <h2 class="text-gray-900 font-extrabold text-xl">Mensaje de WhatsApp Masivo</h2>
                            </div>

                            <form action="{{ route('empleador.mass-whatsapp') }}" method="POST" class="space-y-6" id="massWhatsappForm">
                                @csrf
                                
                                @if(session('success'))
                                    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                                        <p class="text-sm text-green-700 font-bold">{{ session('success') }}</p>
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                                        <p class="text-sm text-red-700 font-bold">{{ session('error') }}</p>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Destinatarios</label>
                                    <select name="recipient_id" x-model="recipientId" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                        <option value="">Todo el equipo ({{ $allProfessionals->whereNotNull('phone_number')->count() }} con WhatsApp)</option>
                                        @foreach($allProfessionals as $p)
                                            @if($p->phone_number)
                                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->phone_number }})</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Solo se muestran profesionales con teléfono registrado.</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Mensaje</label>
                                    <textarea name="message" rows="6" required class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Escribe tu mensaje aquí..."></textarea>
                                    <p class="mt-2 text-[10px] text-gray-400 font-medium">Puedes usar *negrita*, _cursiva_ o ~tachado~ como en WhatsApp.</p>
                                </div>

                                <div class="bg-blue-50/50 rounded-xl p-4 border border-blue-100">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <p class="text-[11px] text-blue-700 leading-relaxed font-medium">
                                            <strong>Seguridad Anti-Ban:</strong> Para proteger tu cuenta, los mensajes se enviarán con un intervalo de 60 segundos entre cada uno. Recibirás una notificación cuando el proceso comience.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex justify-end pt-4">
                                    <button type="submit" id="submitWhatsappBtn" class="inline-flex items-center gap-2 bg-green-500 text-white px-8 py-3 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-green-600 transition shadow-lg shadow-green-200 active:scale-95">
                                        <span>Programar WhatsApp</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </div>
                            </form>
                        </div>
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
                            <template x-if="activeTab === 'email'">
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Utiliza esta herramienta para enviar comunicados oficiales por correo electrónico. Ideal para mensajes largos, archivos adjuntos y formato enriquecido.
                                </p>
                            </template>

                            <template x-if="activeTab === 'whatsapp'">
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Ideal para avisos rápidos y urgentes. El mensaje llegará directamente al WhatsApp del profesional.
                                </p>
                            </template>
                            
                            <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100">
                                <h4 class="text-xs font-black text-yellow-600 uppercase tracking-widest mb-2">Recomendaciones</h4>
                                <ul class="list-disc list-inside text-xs text-yellow-700 space-y-1">
                                    <template x-if="activeTab === 'email'">
                                        <li>Sé claro y conciso en el asunto.</li>
                                    </template>
                                    <template x-if="activeTab === 'whatsapp'">
                                        <li>Evita el exceso de mensajes para no ser reportado.</li>
                                    </template>
                                    <li>Verifica los destinatarios antes de enviar.</li>
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

                // Email Form Submission
                document.getElementById('massEmailForm').addEventListener('submit', function(e) {
                    let htmlContent = quill.root.innerHTML;
                    if (quill.getText().trim().length === 0 && htmlContent.indexOf('<img') === -1) {
                         alert('Por favor escribe un mensaje.');
                         e.preventDefault();
                         return;
                    }
                    document.getElementById('message').value = htmlContent;
                    
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

                // WhatsApp Form Submission
                document.getElementById('massWhatsappForm').addEventListener('submit', function(e) {
                    const btn = document.getElementById('submitWhatsappBtn');
                    btn.disabled = true;
                    btn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Programando...
                    `;
                });
            </script>

            <style>
                [x-cloak] { display: none !important; }
            </style>

        </div>
    </div>
</x-app-layout>
