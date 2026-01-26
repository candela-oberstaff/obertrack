<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($template) ? __('Editar Plantilla') : __('Nueva Plantilla') }}
        </h2>
    </x-slot>

    <!-- Quill Styles -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6">
                 <a href="{{ route('admin.email-templates.index') }}" class="text-blue-600 hover:underline">&larr; Volver a la lista</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ isset($template) ? route('admin.email-templates.update', $template->id) : route('admin.email-templates.store') }}" method="POST" id="templateForm">
                        @csrf
                        @if(isset($template))
                            @method('PUT')
                        @endif

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Título de la Plantilla (Identificador interno)</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $template->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-gray-700">Asunto del Email</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject', $template->subject ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div class="mb-6">
                            <label for="editor" class="block text-sm font-medium text-gray-700 mb-2">Cuerpo del Mensaje</label>
                            <!-- Quill Editor Container -->
                            <div id="editor-container" style="height: 400px;">
                                {!! old('body', $template->body ?? '') !!}
                            </div>
                            <input type="hidden" name="body" id="body">
                        </div>

                        <div class="flex justify-end">
                            <button type="button" id="submitBtn" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                                {{ isset($template) ? 'Actualizar Plantilla' : 'Guardar Plantilla' }}
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
