<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comunicación Masiva') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ 
                activeTab: '{{ request('tab', session('active_tab', 'email')) }}',
                waStatus: 'LOADING',
                waQr: null,
                waError: null,
                isCheckingWa: false,
                waCheckCount: 0,
                async checkWaStatus() {
                    if (this.isCheckingWa || this.activeTab !== 'whatsapp') return;
                    this.isCheckingWa = true;
                    try {
                        let res = await fetch('{{ route('admin.whatsapp.status') }}');
                        if (!res.ok) throw new Error('Status Error');
                        let data = await res.json();
                        
                        this.waStatus = data.status;
                        this.waQr = data.qr;
                        this.waError = null;
                        this.waCheckCount++;
                    } catch (e) { 
                        console.warn('WA Status Error'); 
                        if (this.waCheckCount > 3) this.waError = 'Conexión interrumpida con el servicio.';
                    }
                    finally { this.isCheckingWa = false; }
                },
                async startWaSession(force = false) {
                    this.waStatus = 'STARTING';
                    this.waError = null;
                    this.waCheckCount = 0;
                    try {
                        let url = '{{ route('admin.whatsapp.start') }}';
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
                        <div class="text-gray-900 font-extrabold text-xl border-b border-gray-50 pb-6 mb-8 flex items-center gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            Comunicación por Email
                        </div>

                        <!-- Quill Styles -->
                        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

                        <form action="{{ route('admin.mass-email') }}" method="POST" class="space-y-6" id="massEmailForm">
                            @csrf
                            
                            <!-- Template Loader -->
                            <div class="bg-blue-50/50 p-6 rounded-2xl mb-6 border border-blue-100/50">
                                <label class="block text-xs font-black text-blue-900 uppercase tracking-widest mb-3">Cargar Plantilla (Opcional)</label>
                                <select id="templateSelector" class="block w-full rounded-xl border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-white">
                                    <option value="">-- Seleccionar una plantilla --</option>
                                    @foreach(\App\Models\EmailTemplate::all() as $template)
                                        <option value="{{ $template->id }}" 
                                                {{ request('template_id') == $template->id ? 'selected' : '' }}
                                                data-subject="{{ $template->subject }}" 
                                                data-body="{{ $template->body }}">
                                            {{ $template->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Segmento de Destino</label>
                                    <select name="segment" id="emailSegment" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="red_alerts">Alertas Rojas (Inactivos 2+ días)</option>
                                        <option value="yellow_alerts">Alertas Amarillas (Inactivos 1 día)</option>
                                        <option value="all_professionals">Todos los Profesionales</option>
                                        <option value="all_companies">Todas las Empresas</option>
                                        <option value="individual_professional">Profesional Individual</option>
                                        <option value="individual_company">Empresa Individual</option>
                                    </select>
                                </div>
                                
                                <div id="individual_professional_div" class="hidden">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Seleccionar Profesional</label>
                                    <select name="individual_id" id="individual_professional_select" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" disabled>
                                        <option value="">-- Buscar Profesional --</option>
                                        @foreach($allProfessionals as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="individual_company_div" class="hidden">
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Seleccionar Empresa</label>
                                    <select name="individual_id" id="individual_company_select" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" disabled>
                                        <option value="">-- Buscar Empresa --</option>
                                        @foreach($allCompanies as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? 'Sin nombre' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Asunto del Correo</label>
                                <input type="text" name="subject" id="subject" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Ej: Importante: Actualiza tus horas" value="{{ request('subject') }}">
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

                    <!-- WhatsApp Form -->
                    <div x-show="activeTab === 'whatsapp'" class="p-8" x-cloak>
                        <div class="text-gray-900 font-extrabold text-xl border-b border-gray-50 pb-6 mb-8 flex items-center gap-3">
                            <div class="p-2 bg-green-50 text-green-600 rounded-xl">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
                            </div>
                            Comunicación por WhatsApp
                        </div>

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
                            <form action="{{ route('admin.mass-whatsapp') }}" method="POST" class="space-y-6" id="massWhatsappForm">
                                @csrf
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Segmento de Destino</label>
                                        <select name="segment" id="whatsappSegment" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                            <option value="all_professionals">Todos los Profesionales</option>
                                            <option value="red_alerts">Alertas Rojas</option>
                                            <option value="yellow_alerts">Alertas Amarillas</option>
                                            <option value="all_companies">Todas las Empresas</option>
                                            <option value="individual_professional">Profesional Individual</option>
                                            <option value="individual_company">Empresa Individual</option>
                                        </select>
                                    </div>
                                    
                                    <div id="wa_individual_professional_div" class="hidden">
                                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Seleccionar Profesional</label>
                                        <select name="individual_id" id="wa_individual_professional_select" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" disabled>
                                            <option value="">-- Buscar Profesional --</option>
                                            @foreach($allProfessionals->whereNotNull('phone_number') as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->phone_number }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="wa_individual_company_div" class="hidden">
                                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Seleccionar Empresa</label>
                                        <select name="individual_id" id="wa_individual_company_select" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" disabled>
                                            <option value="">-- Buscar Empresa --</option>
                                            @foreach($allCompanies->whereNotNull('phone_number') as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone_number }})</option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                            <strong>Seguridad Anti-Ban:</strong> Los mensajes se enviarán con un intervalo de 60 segundos entre cada uno para proteger la cuenta.
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
                    <!-- Impact Cards -->
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                        <h3 class="text-gray-900 font-extrabold text-lg mb-6 flex items-center gap-2">
                             <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                             </div>
                             Métricas
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="p-5 bg-gray-50 rounded-2xl flex items-center justify-between border border-gray-100">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Enviados</p>
                                    <p class="text-2xl font-black text-gray-900">{{ number_format($emailStats['total_recipients']) }}</p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-blue-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-1">Profesionales</p>
                                    <p class="text-xl font-black text-blue-900">{{ number_format($emailStats['by_segment']['professionals']) }}</p>
                                </div>
                                <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/50">
                                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Empresas</p>
                                    <p class="text-xl font-black text-indigo-900">{{ number_format($emailStats['by_segment']['companies']) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-50">
                            <p class="text-xs font-bold text-gray-400 italic">Estadísticas basadas en {{ $emailStats['total_sessions'] }} sesiones de envío.</p>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100">
                        <h3 class="text-gray-900 font-extrabold text-lg mb-6 flex items-center gap-2">
                             <div class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                             </div>
                             Mi Historial Reciente
                        </h3>

                        <div class="space-y-6">
                            @forelse($emailStats['recent_logs'] as $log)
                                <div class="relative pl-6 pb-2 border-l-2 border-gray-100 last:border-0 last:pb-0">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-2 border-green-500 shadow-sm"></div>
                                    <p class="text-xs font-black text-gray-900 leading-tight mb-0.5">{{ $log->subject }}</p>
                                    <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                        <span>{{ $log->recipient_count }} destinatarios</span>
                                        <span>•</span>
                                        <span>{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <span class="inline-block mt-2 px-2 py-0.5 bg-gray-100 rounded text-[9px] font-black text-gray-500 uppercase tracking-widest">
                                        {{ str_replace('_', ' ', $log->segment) }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <p class="text-sm font-bold text-gray-300 italic">No hay envíos registrados aún.</p>
                                </div>
                            @endforelse
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

                // Template Loader
                document.getElementById('templateSelector').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        document.getElementById('subject').value = selectedOption.getAttribute('data-subject');
                        quill.root.innerHTML = selectedOption.getAttribute('data-body');
                    }
                });

                // Email Segment Logic
                function setupSegmentLogic(segmentId, profDivId, compDivId, profSelectId, compSelectId) {
                    const segmentSelect = document.getElementById(segmentId);
                    const professionalDiv = document.getElementById(profDivId);
                    const companyDiv = document.getElementById(compDivId);
                    const professionalSelect = document.getElementById(profSelectId);
                    const companySelect = document.getElementById(compSelectId);

                    segmentSelect.addEventListener('change', function() {
                        const value = this.value;
                        professionalDiv.classList.toggle('hidden', value !== 'individual_professional');
                        companyDiv.classList.toggle('hidden', value !== 'individual_company');
                        
                        professionalSelect.disabled = value !== 'individual_professional';
                        companySelect.disabled = value !== 'individual_company';
                        
                        professionalSelect.name = value === 'individual_professional' ? 'individual_id' : 'temp_p';
                        companySelect.name = value === 'individual_company' ? 'individual_id' : 'temp_c';
                    });
                }

                setupSegmentLogic('emailSegment', 'individual_professional_div', 'individual_company_div', 'individual_professional_select', 'individual_company_select');
                setupSegmentLogic('whatsappSegment', 'wa_individual_professional_div', 'wa_individual_company_div', 'wa_individual_professional_select', 'wa_individual_company_select');

                // Init logic if deep linked
                @if(request('individual_id'))
                    const deepSegment = '{{ request('segment') }}';
                    const deepId = '{{ request('individual_id') }}';

                    // Email side
                    const emailSeg = document.getElementById('emailSegment');
                    emailSeg.value = deepSegment;
                    emailSeg.dispatchEvent(new Event('change'));
                    setTimeout(() => {
                        document.getElementById('individual_professional_select').value = deepId;
                        document.getElementById('individual_company_select').value = deepId;
                    }, 100);

                    // WA side
                    const waSeg = document.getElementById('whatsappSegment');
                    waSeg.value = deepSegment;
                    waSeg.dispatchEvent(new Event('change'));
                    setTimeout(() => {
                        document.getElementById('wa_individual_professional_select').value = deepId;
                        document.getElementById('wa_individual_company_select').value = deepId;
                    }, 100);
                @endif

                // Email Form Submission
                document.getElementById('massEmailForm').addEventListener('submit', function(e) {
                    document.getElementById('message').value = quill.root.innerHTML;
                    const btn = document.getElementById('submitBtn');
                    btn.disabled = true;
                    btn.innerHTML = 'Enviando...';
                });

                // WhatsApp Form Submission
                document.getElementById('massWhatsappForm').addEventListener('submit', function(e) {
                    const btn = document.getElementById('submitWhatsappBtn');
                    btn.disabled = true;
                    btn.innerHTML = 'Programando...';
                });

                // Auto-load template if needed
                if (document.getElementById('templateSelector').value) {
                    document.getElementById('templateSelector').dispatchEvent(new Event('change'));
                }
            </script>

            <style>
                [x-cloak] { display: none !important; }
            </style>

        </div>
    </div>
</x-app-layout>
