<div class="py-12" x-data="{ 
    copyLink() {
        navigator.clipboard.writeText('{{ $responderUri }}');
        alert('¡Enlace copiado!');
    }
}">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('google-forms.manage') }}" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <div>
                            <h2 class="text-2xl font-bold">{{ $formTitle }}</h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded uppercase">Google Forms</span>
                                @if($responderUri)
                                    <button @click="copyLink()" class="text-xs text-gray-500 hover:text-[#22A9C8] flex items-center gap-1 transition-colors">
                                        <i class="fa fa-link"></i>
                                        Copiar enlace público
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <a href="https://docs.google.com/forms/d/{{ $formId }}/edit" target="_blank" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition duration-150 flex items-center gap-2">
                            <i class="fa fa-external-link text-xs"></i>
                            Ver en Google
                        </a>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex border-b border-gray-200 mb-8">
                    <button wire:click="setTab('preguntas')" 
                            class="px-6 py-3 text-sm font-bold transition-colors border-b-2 {{ $currentTab === 'preguntas' ? 'border-[#22A9C8] text-[#22A9C8]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Preguntas
                    </button>
                    <button wire:click="setTab('respuestas')" 
                            class="px-6 py-3 text-sm font-bold transition-colors border-b-2 {{ $currentTab === 'respuestas' ? 'border-[#22A9C8] text-[#22A9C8]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Respuestas
                        @if($responses['total'] > 0)
                            <span class="ml-2 px-1.5 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded-full">{{ $responses['total'] }}</span>
                        @endif
                    </button>
                </div>

                @if(session()->has('success'))
                    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl flex items-center gap-3">
                        <i class="fa fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-xl flex items-center gap-3">
                        <i class="fa fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($currentTab === 'preguntas')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Questions List -->
                        <div class="lg:col-span-2 space-y-4">
                            @forelse($questions as $index => $item)
                                <div class="p-5 border border-gray-200 rounded-2xl bg-white shadow-sm hover:shadow-md transition-shadow">
                                    @if(isset($item['type']))
                                        @if($item['type'] === 'QUESTION')
                                            <div class="flex items-start gap-4">
                                                <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-blue-50 text-[#22A9C8] flex items-center justify-center text-sm font-bold">
                                                    {{ $index + 1 }}
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-bold text-gray-900">{{ $item['title'] }}</p>
                                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mt-1">
                                                        @if(isset($item['questionItem']['question']['textQuestion']))
                                                            <i class="fa fa-paragraph mr-1"></i> Texto largo
                                                        @elseif(isset($item['questionItem']['question']['choiceQuestion']))
                                                            <i class="fa fa-dot-circle mr-1"></i> Opción múltiple
                                                        @endif
                                                    </p>
                                                    
                                                    @if(isset($item['questionItem']['question']['choiceQuestion']))
                                                        <ul class="mt-3 space-y-2">
                                                            @foreach($item['questionItem']['question']['choiceQuestion']['options'] as $option)
                                                                <li class="flex items-center gap-3 text-sm text-gray-600 bg-gray-50 p-2 rounded-lg">
                                                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300"></div>
                                                                    {{ $option['value'] }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($item['type'] === 'IMAGE')
                                            <div class="space-y-3">
                                                <div class="flex items-center gap-2 text-gray-400">
                                                    <i class="fa fa-image"></i>
                                                    <span class="text-xs font-bold uppercase">{{ $item['title'] ?: 'Imagen' }}</span>
                                                </div>
                                                <img src="{{ $item['image']['contentUri'] }}" alt="{{ $item['image']['altText'] ?? '' }}" class="rounded-xl w-full max-h-96 object-contain bg-gray-50 border border-gray-100">
                                            </div>
                                        @elseif($item['type'] === 'VIDEO')
                                            <div class="space-y-3">
                                                <div class="flex items-center gap-2 text-gray-400">
                                                    <i class="fa fa-play-circle"></i>
                                                    <span class="text-xs font-bold uppercase">{{ $item['title'] ?: 'Video' }}</span>
                                                </div>
                                                <div class="aspect-video w-full rounded-xl overflow-hidden border border-gray-100 bg-black">
                                                    @php
                                                        $videoId = '';
                                                        $url = $item['video']['youtubeUri'];
                                                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                                                            $videoId = $match[1];
                                                        }
                                                    @endphp
                                                    @if($videoId)
                                                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-500 text-xs text-center p-4">
                                                            No se pudo cargar la vista previa del video
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-gray-500 italic">{{ $item['title'] ?? 'Elemento sin título' }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="py-20 text-center bg-gray-50 border border-dashed border-gray-200 rounded-3xl">
                                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-gray-300 text-2xl">
                                        <i class="fa fa-plus"></i>
                                    </div>
                                    <h4 class="text-gray-900 font-bold">Sin contenido</h4>
                                    <p class="text-gray-500 text-sm mt-1">Comienza agregando preguntas o multimedia.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Sidebar Actions -->
                        <div class="space-y-6">
                            <!-- Add Question -->
                            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm sticky top-6">
                                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fa fa-plus-circle text-[#22A9C8]"></i>
                                    Agregar Pregunta
                                </h3>
                                <form wire:submit.prevent="addQuestion" class="space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Título</label>
                                        <input type="text" wire:model="newQuestionTitle" placeholder="¿Cómo te llamas?" class="w-full rounded-xl border-gray-200 focus:border-[#22A9C8] focus:ring-0 text-sm">
                                        @error('newQuestionTitle') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Tipo</label>
                                        <select wire:model.live="newQuestionType" class="w-full rounded-xl border-gray-200 focus:border-[#22A9C8] text-sm">
                                            <option value="TEXT">Texto largo</option>
                                            <option value="RADIO">Opción múltiple</option>
                                        </select>
                                    </div>

                                    @if($newQuestionType === 'RADIO')
                                        <div class="space-y-2">
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Opciones</label>
                                            @foreach($newQuestionOptions as $index => $option)
                                                <div class="flex gap-2">
                                                    <input type="text" wire:model="newQuestionOptions.{{ $index }}" placeholder="Opción {{ $index + 1 }}" class="flex-1 rounded-xl border-gray-200 focus:border-[#22A9C8] text-xs">
                                                    @if(count($newQuestionOptions) > 1)
                                                        <button type="button" wire:click="removeOption({{ $index }})" class="text-gray-400 hover:text-red-500">
                                                            <i class="fa fa-times-circle"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                            <button type="button" wire:click="addOption" class="text-[10px] text-[#22A9C8] hover:underline font-bold uppercase">
                                                + Agregar opción
                                            </button>
                                        </div>
                                    @endif

                                    <button type="submit" class="w-full py-3 bg-[#22A9C8] text-white text-xs font-bold rounded-xl hover:bg-[#1B8BA6] transition shadow-lg shadow-blue-100 flex items-center justify-center gap-2" wire:loading.attr="disabled">
                                        <span wire:loading.remove>Guardar pregunta</span>
                                        <span wire:loading><i class="fa fa-spinner fa-spin"></i> Guardando...</span>
                                    </button>
                                </form>

                                <div class="my-6 h-px bg-gray-100"></div>

                                <!-- Add Media -->
                                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fa fa-camera-retro text-[#22A9C8]"></i>
                                    Multimedia
                                </h3>
                                <form wire:submit.prevent="addMedia" class="space-y-4">
                                    <div class="flex bg-gray-50 p-1 rounded-xl mb-4">
                                        <button type="button" @click="$wire.mediaType = 'IMAGE'" :class="$wire.mediaType === 'IMAGE' ? 'bg-white shadow-sm' : ''" class="flex-1 py-1.5 text-[10px] font-bold rounded-lg transition-all">IMAGEN</button>
                                        <button type="button" @click="$wire.mediaType = 'VIDEO'" :class="$wire.mediaType === 'VIDEO' ? 'bg-white shadow-sm' : ''" class="flex-1 py-1.5 text-[10px] font-bold rounded-lg transition-all">VIDEO</button>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Título / Pie</label>
                                        <input type="text" wire:model="mediaTitle" placeholder="Ej: Mapa de la oficina" class="w-full rounded-xl border-gray-200 focus:border-[#22A9C8] text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">URL (Imagen o YouTube)</label>
                                        <input type="text" wire:model="mediaUri" placeholder="https://..." class="w-full rounded-xl border-gray-200 focus:border-[#22A9C8] text-sm">
                                    </div>

                                    <button type="submit" class="w-full py-3 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2">
                                        Agregar elemento
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Responses Tab -->
                    <div class="space-y-8">
                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Respuestas</p>
                                <p class="text-4xl font-extrabold text-[#0D1E4C]">{{ $responses['total'] }}</p>
                            </div>
                            <!-- Future metrics like Completion Rate could go here -->
                        </div>

                        <!-- Individual Responses -->
                        @if($responses['total'] > 0)
                            <div class="overflow-x-auto bg-white rounded-3xl border border-gray-200 shadow-sm">
                                <table class="w-full text-left bg-white">
                                    <thead>
                                        <tr class="bg-gray-50 border-b border-gray-200">
                                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Fecha</th>
                                            @foreach($questions as $q)
                                                @if(isset($q['type']) && $q['type'] === 'QUESTION')
                                                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">{{ $q['title'] }}</th>
                                                @endif
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($responses['responses'] as $resp)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($resp['createTime'])->format('d/m/Y H:i') }}
                                                </td>
                                                @foreach($questions as $q)
                                                    @if(isset($q['type']) && $q['type'] === 'QUESTION')
                                                        <td class="px-6 py-4 text-sm text-gray-600">
                                                            {{ $resp['answers'][$q['itemId']] ?? '-' }}
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-20 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                                <p class="text-gray-500">Aún no se han recibido respuestas para este formulario.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
