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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 md:p-6 border-l-4 border-blue-500">
                    <div class="text-[10px] md:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Profesionales</div>
                    <div class="text-xl md:text-2xl font-bold">{{ $stats['total_professionals'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 md:p-6 border-l-4 border-indigo-500">
                    <div class="text-[10px] md:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Empresas</div>
                    <div class="text-xl md:text-2xl font-bold">{{ $stats['total_companies'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 md:p-6 border-l-4 border-yellow-500">
                    <div class="text-[10px] md:text-sm font-medium text-gray-500 uppercase tracking-wider">Alertas Amarillas</div>
                    <div class="text-xl md:text-2xl font-bold">{{ $stats['yellow_alerts'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 md:p-6 border-l-4 border-red-500">
                    <div class="text-[10px] md:text-sm font-medium text-gray-500 uppercase tracking-wider">Alertas Rojas</div>
                    <div class="text-xl md:text-2xl font-bold">{{ $stats['red_alerts'] }}</div>
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

            <!-- Mass Communication Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 font-bold border-b border-gray-100">
                    Comunicación Masiva
                </div>
                <div class="p-6">
                    <!-- Quill Styles -->
                    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

                    <form action="{{ route('admin.mass-email') }}" method="POST" class="space-y-4 max-w-2xl" id="massEmailForm">
                        @csrf
                        
                        <!-- Template Loader -->
                        <div class="bg-blue-50 p-4 rounded-md mb-4 border border-blue-100">
                             <label class="block text-sm font-bold text-blue-800 mb-2">Cargar Plantilla (Opcional)</label>
                             <select id="templateSelector" class="block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Segmento</label>
                            <select name="segment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="red_alerts">Alertas Rojas (Inactivos 2+ días)</option>
                                <option value="yellow_alerts">Alertas Amarillas (Inactivos 1 día)</option>
                                <option value="all_professionals">Todos los Profesionales</option>
                                <option value="all_companies">Todas las Empresas</option>
                                <option value="individual_professional">Profesional Individual</option>
                                <option value="individual_company">Empresa Individual</option>
                            </select>
                        </div>
                        
                        <!-- Individual Professional Dropdown -->
                        <div id="individual_professional_div" class="hidden">
                            <label class="block text-sm font-medium text-gray-700">Seleccionar Profesional</label>
                            <select name="individual_id" id="individual_professional_select" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" disabled>
                                <option value="">-- Buscar Profesional --</option>
                                @foreach($allProfessionals as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Individual Company Dropdown -->
                        <div id="individual_company_div" class="hidden">
                            <label class="block text-sm font-medium text-gray-700">Seleccionar Empresa</label>
                            <select name="individual_id" id="individual_company_select" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" disabled>
                                <option value="">-- Buscar Empresa --</option>
                                @foreach($allCompanies as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? 'Sin nombre' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asunto</label>
                            <input type="text" name="subject" id="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mensaje</label>
                            <!-- Quill Editor -->
                            <div id="editor-container" style="height: 300px; background: white;"></div>
                            <input type="hidden" name="message" id="message">
                        </div>
                        <button type="submit" id="submitBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                            Enviar Emails
                        </button>
                    </form>

                    <!-- Quill Scripts & Logic -->
                    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
                    <script>
                        var quill = new Quill('#editor-container', {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    ['bold', 'italic', 'underline', 'strike'],
                                    ['blockquote', 'code-block'],
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                    [{ 'color': [] }, { 'background': [] }],
                                    [{ 'align': [] }],
                                    ['link', 'image'],
                                    ['clean']
                                ]
                            }
                        });

                        // Template Loader Logic
                        document.getElementById('templateSelector').addEventListener('change', function() {
                            var selectedOption = this.options[this.selectedIndex];
                            if (selectedOption.value) {
                                var subject = selectedOption.getAttribute('data-subject');
                                var body = selectedOption.getAttribute('data-body');

                                document.getElementById('subject').value = subject;
                                quill.root.innerHTML = body;
                            }
                        });


                        // Segment Selector Logic
                        const segmentSelect = document.querySelector('select[name="segment"]');
                        const professionalDiv = document.getElementById('individual_professional_div');
                        const companyDiv = document.getElementById('individual_company_div');
                        const professionalSelect = document.getElementById('individual_professional_select');
                        const companySelect = document.getElementById('individual_company_select');

                        segmentSelect.addEventListener('change', function() {
                            const value = this.value;
                            
                            // Reset
                            professionalDiv.classList.add('hidden');
                            companyDiv.classList.add('hidden');
                            professionalSelect.disabled = true;
                            companySelect.disabled = true;
                            professionalSelect.name = 'temp_ignore'; // Prevent sending
                            companySelect.name = 'temp_ignore'; 

                            if (value === 'individual_professional') {
                                professionalDiv.classList.remove('hidden');
                                professionalSelect.disabled = false;
                                professionalSelect.name = 'individual_id';
                            } else if (value === 'individual_company') {
                                companyDiv.classList.remove('hidden');
                                companySelect.disabled = false;
                                companySelect.name = 'individual_id';
                            }
                        });


                        // Trigger change on load if value exists
                        if (document.getElementById('templateSelector').value) {
                             document.getElementById('templateSelector').dispatchEvent(new Event('change'));
                             // Scroll to form to show user what happened
                             document.getElementById('massEmailForm').scrollIntoView({ behavior: 'smooth' });
                        }

                        // Form Submission
                        document.getElementById('submitBtn').addEventListener('click', function(e) {
                            e.preventDefault();
                            var html = quill.root.innerHTML;
                            document.getElementById('message').value = html;
                            document.getElementById('massEmailForm').submit();
                        });
                    </script>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
