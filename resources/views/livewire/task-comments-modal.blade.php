<div x-data="{ open: @entangle('isOpen') }"
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
     style="display: none;">
    
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-4xl p-8 max-h-[90vh] flex flex-col relative"
         @click.away="open = false">
        
        {{-- Close Button --}}
        <button @click="open = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 z-10">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Header Search --}}
        <div class="relative mb-6 mr-12">
            <input type="text" 
                   wire:model.live.debounce.300ms="search"
                   placeholder="Buscar comentarios" 
                   class="w-full bg-gray-100 border-none rounded-full py-3 pl-12 pr-4 text-gray-700 focus:ring-2 focus:ring-primary focus:bg-white transition duration-300">
            <svg class="w-6 h-6 text-gray-500 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        {{-- Tabs Removed --}}
        
        {{-- Filters (Visual only as per request/mockup) --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-primary mb-4">Filtro de búsqueda</h3>
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center text-gray-600">
                    <span class="mr-2">Comentarios hechos desde</span>
                    <div class="relative">
                        <input type="date" wire:model="dateFrom" class="bg-gray-100 rounded-lg py-2 px-3 text-sm border-none focus:ring-primary text-gray-500">
                    </div>
                </div>
                <div class="flex items-center text-gray-600">
                    <span class="mr-2">Hasta</span>
                    <div class="relative">
                        <input type="date" wire:model="dateTo" class="bg-gray-100 rounded-lg py-2 px-3 text-sm border-none focus:ring-primary text-gray-500">
                    </div>
                </div>
                <button wire:click="$refresh" class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-full font-medium transition duration-300 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtrar
                </button>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto mb-8 pr-2 custom-scrollbar">
            @if(isset($comments) && $comments->count() > 0)
                <div class="space-y-4">
                    @foreach($comments as $comment)
                        <div class="bg-gray-50 p-4 rounded-xl flex justify-between items-start">
                            <div class="pr-4">
                                <p class="text-gray-800 text-sm leading-relaxed italic">
                                    {{ $comment->content }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-4 min-w-max">
                                <span class="text-gray-500 text-sm font-medium">
                                    {{ \Carbon\Carbon::parse($comment->created_at)->format('Y.m.d') }}
                                </span>
                                <div class="flex items-center text-gray-700 font-bold text-sm">
                                    {{-- User Icon --}}
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $comment->user->name ?? 'Usuario' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-500">
                    No hay comentarios en esta tarea.
                </div>
            @endif

            {{-- New Comment Form (Inline) --}}
            <div x-data="{ showForm: false }" class="mt-4">
                <button @click="showForm = !showForm" x-show="!showForm" class="hidden"></button>
            </div>
        </div>

        {{-- Footer Action --}}
        <div class="flex justify-center" x-data="{ showInput: false }">
             <div x-show="!showInput" class="w-full flex justify-center">
                <button @click="showInput = true" class="border border-primary text-primary hover:bg-primary hover:text-white px-8 py-2 rounded-full font-medium transition duration-300">
                    Añadir comentario
                </button>
            </div>

            <div x-show="showInput" class="w-full" style="display: none;">
                <form wire:submit.prevent="addComment" class="flex gap-2">
                    <input type="text" wire:model="newCommentContent" class="flex-1 rounded-lg border-gray-300 focus:ring-primary focus:border-primary" placeholder="Escribe tu comentario...">
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">Enviar</button>
                    <button type="button" @click="showInput = false" class="text-gray-500 hover:text-gray-700 px-3">Cancelar</button>
                </form>
            </div>
        </div>

    </div>
</div>
