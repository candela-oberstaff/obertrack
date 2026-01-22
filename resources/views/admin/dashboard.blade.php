<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Superadmin - Obertrack') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Admin Navigation Hub -->
            @include('admin.partials.nav')
            
            <!-- Overall Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-4 md:p-6 border-l-4 border-blue-500 relative group cursor-help">
                    <div class="text-[10px] md:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Profesionales</div>
                    <div class="text-xl md:text-2xl font-bold">{{ $stats['total_professionals'] }}</div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                bg-gray-900 text-white text-[10px] rounded-lg
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                whitespace-nowrap z-50 pointer-events-none shadow-xl">
                        Número total de profesionales registrados
                    </div>
                </div>
                <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-4 md:p-6 border-l-4 border-indigo-500 relative group cursor-help">
                    <div class="text-[10px] md:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Empresas</div>
                    <div class="text-xl md:text-2xl font-bold">{{ $stats['total_companies'] }}</div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                bg-gray-900 text-white text-[10px] rounded-lg
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                whitespace-nowrap z-50 pointer-events-none shadow-xl">
                        Número total de empresas registradas
                    </div>
                </div>
                <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-4 md:p-6 border-l-4 border-yellow-500 relative group cursor-help">
                    <div class="text-[10px] md:text-sm font-medium text-gray-500 uppercase tracking-wider">Alertas Amarillas</div>
                    <div class="text-xl md:text-2xl font-bold">{{ $stats['yellow_alerts'] }}</div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                bg-gray-900 text-white text-[10px] rounded-lg
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                whitespace-nowrap z-50 pointer-events-none shadow-xl">
                        Número de profesionales con inactividad de 1 día
                    </div>
                </div>
                <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-4 md:p-6 border-l-4 border-red-500 relative group cursor-help">
                    <div class="text-[10px] md:text-sm font-medium text-gray-500 uppercase tracking-wider">Alertas Rojas</div>
                    <div class="text-xl md:text-2xl font-bold">{{ $stats['red_alerts'] }}</div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2
                                bg-gray-900 text-white text-[10px] rounded-lg
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                whitespace-nowrap z-50 pointer-events-none shadow-xl">
                        Número de profesionales con inactividad de 2 o más días
                    </div>
                </div>
            </div>

            <!-- Professional Monitoring -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 font-bold border-b border-gray-100">
                    Monitoreo de Profesionales
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50 uppercase text-xs font-bold text-gray-500">
                                <th class="px-4 py-3">Nombre</th>
                                <th class="px-4 py-3 hidden sm:table-cell">Última Actividad</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($professionals as $p)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-gray-900">{{ $p['user']->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $p['user']->email }}</div>
                                    </td>
                                    <td class="px-4 py-4 hidden sm:table-cell text-gray-600">
                                        {{ $p['last_registration'] ? \Carbon\Carbon::parse($p['last_registration'])->format('d/m/Y') : 'Nunca' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($p['status'] === 'red')
                                            <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-800 font-bold text-xs uppercase">ROJO (2+ días)</span>
                                        @elseif($p['status'] === 'yellow')
                                            <span class="px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800 font-bold text-xs uppercase">AMARILLO (1 día)</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-800 font-bold text-xs uppercase">Activo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('chat', $p['user']->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors title="Chat Interno">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                            </a>
                                            @if($p['user']->phone_number)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p['user']->phone_number) }}" target="_blank" class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors" title="WhatsApp">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            <a href="mailto:{{ $p['user']->email }}" class="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors" title="Email">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mass Communication & Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Mass Communication Form (Left) -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                    <div class="p-8 text-gray-900 font-extrabold text-xl border-b border-gray-50 flex items-center gap-3">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        Comunicación Masiva
                        <span class="relative group inline-flex items-center ml-1 cursor-help">
    <span class=" rounded-full bg-blue-50 text-blue-500 hover:bg-blue-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"/>
        </svg>
    </span>

    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                px-3 py-2 bg-gray-900 text-white text-[10px]
                rounded-lg opacity-0 group-hover:opacity-100
                transition-opacity duration-200
                whitespace-nowrap z-50 pointer-events-none shadow-xl">
       Envía correos segmentados a profesionales o empresas según su estado o selección específica 
    </div>
</span>
                    </div>
                    <div class="p-8">
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
                                    <select name="segment" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
                                <input type="text" name="subject" id="subject" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Ej: Importante: Actualiza tus horas">
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

            <!-- Scripts Section (Refactored) -->
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

                // Segment Logic
                const segmentSelect = document.querySelector('select[name="segment"]');
                const professionalDiv = document.getElementById('individual_professional_div');
                const companyDiv = document.getElementById('individual_company_div');
                const professionalSelect = document.getElementById('individual_professional_select');
                const companySelect = document.getElementById('individual_company_select');

                segmentSelect.addEventListener('change', function() {
                    const value = this.value;
                    professionalDiv.classList.toggle('hidden', value !== 'individual_professional');
                    companyDiv.classList.toggle('hidden', value !== 'individual_company');
                    
                    professionalSelect.disabled = value !== 'individual_professional';
                    companySelect.disabled = value !== 'individual_company';
                    
                    professionalSelect.name = value === 'individual_professional' ? 'individual_id' : 'temp_p';
                    companySelect.name = value === 'individual_company' ? 'individual_id' : 'temp_c';
                });

                // Form Submission
                document.getElementById('massEmailForm').addEventListener('submit', function(e) {
                    document.getElementById('message').value = quill.root.innerHTML;
                });

                // Auto-load template if needed
                if (document.getElementById('templateSelector').value) {
                    document.getElementById('templateSelector').dispatchEvent(new Event('change'));
                }
            </script>

        </div>
    </div>
</x-app-layout>
