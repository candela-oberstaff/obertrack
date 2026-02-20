<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('google-forms.manage') }}" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <div>
                            <h2 class="text-2xl font-bold">{{ $formTitle }}</h2>
                            <p class="text-gray-500">Editando formulario</p>
                        </div>
                    </div>
                    
                    <a href="https://docs.google.com/forms/d/{{ $formId }}/edit" target="_blank" class="text-[#22A9C8] hover:underline text-sm">
                        Ver en Google Forms &rarr;
                    </a>
                </div>

                @if(session()->has('success'))
                    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Questions List -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold mb-4">Preguntas Actuales</h3>
                        @forelse($questions as $index => $item)
                            <div class="p-4 border border-gray-200 rounded-xl bg-gray-50">
                                @if(isset($item['questionItem']))
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#22A9C8] text-white flex items-center justify-center text-xs font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item['title'] }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                @if(isset($item['questionItem']['question']['textQuestion']))
                                                    Texto (Párrafo)
                                                @elseif(isset($item['questionItem']['question']['choiceQuestion']))
                                                    Opción Múltiple
                                                @else
                                                    Otro tipo
                                                @endif
                                            </p>
                                            
                                            @if(isset($item['questionItem']['question']['choiceQuestion']))
                                                <ul class="mt-2 space-y-1 ml-1 text-sm text-gray-600">
                                                    @foreach($item['questionItem']['question']['choiceQuestion']['options'] as $option)
                                                        <li class="flex items-center gap-2">
                                                            <div class="w-3 h-3 rounded-full border border-gray-400"></div>
                                                            {{ $option['value'] }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                @elseif(isset($item['title']))
                                     <h4 class="font-medium text-gray-800">{{ $item['title'] }} (Título de sección)</h4>
                                @else
                                    <span class="italic text-gray-400">Elemento sin título</span>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400 border border-dashed border-gray-300 rounded-xl">
                                No hay preguntas todavía. ¡Agrega una!
                            </div>
                        @endforelse
                    </div>

                    <!-- Add Question Form -->
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 h-fit sticky top-6">
                        <h3 class="text-lg font-semibold mb-4">Agregar Pregunta</h3>
                        <form wire:submit.prevent="addQuestion">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título de la pregunta</label>
                                <input type="text" wire:model="newQuestionTitle" class="w-full rounded-lg border-gray-300 focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-50">
                                @error('newQuestionTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de respuesta</label>
                                <select wire:model.live="newQuestionType" class="w-full rounded-lg border-gray-300 focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-50">
                                    <option value="TEXT">Párrafo (Texto largo)</option>
                                    <option value="RADIO">Opción Múltiple</option>
                                </select>
                            </div>

                            @if($newQuestionType === 'RADIO')
                                <div class="mb-4 space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Opciones</label>
                                    @foreach($newQuestionOptions as $index => $option)
                                        <div class="flex gap-2">
                                            <input type="text" wire:model="newQuestionOptions.{{ $index }}" placeholder="Opción {{ $index + 1 }}" class="flex-1 rounded-lg border-gray-300 focus:border-[#22A9C8] focus:ring focus:ring-[#22A9C8] focus:ring-opacity-50 text-sm">
                                            @if(count($newQuestionOptions) > 1)
                                                <button type="button" wire:click="removeOption({{ $index }})" class="text-red-400 hover:text-red-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                    <button type="button" wire:click="addOption" class="text-sm text-[#22A9C8] hover:underline font-medium">
                                        + Agregar otra opción
                                    </button>
                                </div>
                            @endif

                            <button type="submit" class="w-full flex justify-center items-center px-4 py-2 bg-[#22A9C8] text-white text-sm font-semibold rounded-lg hover:bg-[#1B8BA6] transition duration-150 disabled:opacity-50" wire:loading.attr="disabled">
                                <span wire:loading.remove>Agregar Pregunta</span>
                                <span wire:loading>Guardando...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
