<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($template) ? __('Editar Plantilla') : __('Nueva Plantilla') }}
        </h2>
    </x-slot>

    <!-- Quill Styles -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Admin Navigation Hub -->
            @include('admin.partials.nav')

            <!-- Breadcrumbs / Back button -->
            <div class="mb-8 flex items-center justify-between">
                <a href="{{ route('admin.email-templates.index') }}" 
                   class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-[#22A9C8] hover:opacity-70 transition-all">
                    <i class="bi bi-arrow-left mr-2 bg-[#22A9C8]/10 p-2 rounded-lg"></i>
                    Volver a la lista
                </a>
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">
                    {{ isset($template) ? 'Paso 2: Edición de contenido' : 'Paso 2: Nueva Configuración' }}
                </h3>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6 md:p-10 border-b border-gray-50 bg-[#F8F9FA]/50">
                    <h3 class="text-xl md:text-2xl font-black text-[#0D1E4C] leading-tight">
                        {{ isset($template) ? 'Editar Plantilla Existente' : 'Crear Nueva Plantilla' }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2 uppercase tracking-wide font-bold">Personaliza el asunto y el cuerpo del mensaje masivo</p>
                </div>

                <div class="p-6 md:p-10">
                    <form action="{{ isset($template) ? route('admin.email-templates.update', $template->id) : route('admin.email-templates.store') }}" method="POST" id="templateForm">
                        @csrf
                        @if(isset($template))
                            @method('PUT')
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            <div class="space-y-2">
                                <label for="title" class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Título Interno</label>
                                <div class="relative group">
                                    <input type="text" name="title" id="title" value="{{ old('title', $template->title ?? '') }}" required 
                                           class="block w-full rounded-xl border-gray-100 bg-gray-50/50 p-4 text-sm font-bold text-gray-700 focus:border-[#22A9C8] focus:ring-0 transition-all placeholder:text-gray-300 shadow-inner"
                                           placeholder="Ej: Recordatorio de Horas Pendientes">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="subject" class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Asunto del Email</label>
                                <div class="relative group">
                                    <input type="text" name="subject" id="subject" value="{{ old('subject', $template->subject ?? '') }}" required 
                                           class="block w-full rounded-xl border-gray-100 bg-gray-50/50 p-4 text-sm font-bold text-gray-700 focus:border-[#22A9C8] focus:ring-0 transition-all placeholder:text-gray-300 shadow-inner"
                                           placeholder="Ej: Acción Requerida: Reporte de la Semana">
                                </div>
                            </div>
                        </div>

                        <div class="mb-10">
                            <label for="editor" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
                                Cuerpo del Mensaje
                                <span class="bg-gray-100 text-gray-400 px-2 py-0.5 rounded text-[8px] font-bold">EDITOR ENRIQUECIDO</span>
                            </label>
                            <!-- Quill Editor Container -->
                            <div class="rounded-2xl border border-gray-100 overflow-hidden shadow-inner">
                                <div id="editor-container" style="height: 500px; border: none;" class="bg-white">
                                    {!! old('body', $template->body ?? '') !!}
                                </div>
                            </div>
                            <input type="hidden" name="body" id="body">
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end items-center gap-4">
                            <a href="{{ route('admin.email-templates.index') }}" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-red-500 transition-all order-2 sm:order-1">Cancelar</a>
                            <button type="button" id="submitBtn" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-10 py-4 bg-[#0D1E4C] text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-[#22A9C8] transition-all shadow-xl active:scale-95 order-1 sm:order-2">
                                <i class="bi bi-cloud-arrow-up-fill mr-3 text-base"></i>
                                {{ isset($template) ? 'Actualizar Plantilla' : 'Guardar y Finalizar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quill Scritps -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],

            [{ 'header': 1 }, { 'header': 2 }],               // custom button values
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
            [{ 'direction': 'rtl' }],                         // text direction

            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

            [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
            [{ 'font': [] }],
            [{ 'align': [] }],

            ['link', 'image', 'video'],                       // link and image, video
            ['clean']                                         // remove formatting button
        ];

        var quill = new Quill('#editor-container', {
            modules: {
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        'image': imageHandler
                    }
                }
            },
            theme: 'snow'
        });

        function imageHandler() {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = async () => {
                var file = input.files[0];
                var formData = new FormData();

                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch('{{ route('admin.email-templates.upload-image') }}', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                         const data = await response.json();
                         const range = quill.getSelection();
                         quill.insertEmbed(range.index, 'image', data.url);
                    } else {
                        console.error('Upload failed');
                        showError('Error al subir la imagen');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showError('Error en la conexión');
                }
            };
        }

        // Form Submission
        document.getElementById('submitBtn').addEventListener('click', function() {
            var html = quill.root.innerHTML;
            document.getElementById('body').value = html;
            document.getElementById('templateForm').submit();
        });
    </script>
</x-app-layout>
