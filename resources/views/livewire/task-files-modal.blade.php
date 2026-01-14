<div x-data="{ isModalOpen: @entangle('isOpen') }"
     x-show="isModalOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
     style="display: none;">
    
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-4xl p-8 max-h-[90vh] flex flex-col relative"
         @click.away="isModalOpen = false">
        
        {{-- Close Button --}}
        <button @click="isModalOpen = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 z-10">
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

        {{-- Filters (Visual only) --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-primary mb-4">Filtro de búsqueda</h3>
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center text-gray-600">
                    <span class="mr-2">Archivos subidos desde</span>
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

        {{-- Success/Error Messages --}}
        @if (session()->has('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @error('newFile')
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ $message }}
            </div>
        @enderror

        {{-- Table Headers --}}
        <div class="grid grid-cols-12 gap-4 px-4 py-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
            <div class="col-span-5">Nombre del archivo</div>
            <div class="col-span-2">Tipo / Tamaño</div>
            <div class="col-span-5">Modificado el</div>
        </div>

        {{-- Content List --}}
        <div class="flex-1 overflow-y-auto mb-8 pr-2 custom-scrollbar">
            @if(isset($files) && $files->count() > 0)
                <div class="space-y-2">
                    @foreach($files as $file)
                        <div wire:key="attachment-{{ $file->id }}" class="group bg-gray-50 hover:bg-gray-100 rounded-lg p-4 grid grid-cols-12 gap-4 items-center transition duration-200">
                            <div class="col-span-5 text-gray-800 text-sm font-medium italic truncate flex items-center">
                                <svg class="w-5 h-5 mr-2 {{ $file->file_icon }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 3.414L15.586 7 18 10v6a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                </svg>
                                <a href="{{ route('tasks.attachments.download', $file) }}" class="hover:underline">
                                    {{ $file->filename }}
                                </a>
                            </div>
                            <div class="col-span-2 text-gray-600 text-xs flex flex-col uppercase">
                                <span class="font-bold">{{ $file->file_type }}</span>
                                <span class="text-[10px] lowercase text-gray-400 font-normal italic">({{ $file->file_size_human }})</span>
                            </div>
                            <div class="col-span-5 text-gray-500 text-sm italic">
                                El {{ \Carbon\Carbon::parse($file->created_at)->format('d-m-Y') }} a las {{ \Carbon\Carbon::parse($file->created_at)->format('h:i a') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-500">
                    No hay archivos adjuntos en esta tarea.
                </div>
            @endif
        </div>

        {{-- Footer Action --}}
        <div class="flex justify-center flex-col items-center">
             <div wire:loading wire:target="newFile" class="mb-4">
                <div class="flex items-center space-x-2 text-primary">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium">Subiendo archivo...</span>
                </div>
             </div>
             <label wire:loading.remove wire:target="newFile" class="cursor-pointer border border-primary text-primary hover:bg-primary hover:text-white px-8 py-2 rounded-full font-medium transition duration-300 block text-center">
                Subir archivo
                <input type="file" wire:model="newFile" class="hidden">
            </label>
        </div>

    </div>
</div>
