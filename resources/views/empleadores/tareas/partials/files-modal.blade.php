<!-- Files Modal -->
<div 
    x-show="isFilesModalOpen" 
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0"
>
    <!-- Backdrop -->
    <div 
        x-show="isFilesModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 transform transition-all"
        @click="isFilesModalOpen = false"
    >
        <div class="absolute inset-0 bg-gray-600 opacity-50"></div>
    </div>

    <!-- Modal Content -->
    <div 
        x-show="isFilesModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="bg-white rounded-3xl overflow-hidden shadow-xl transform transition-all w-full max-w-4xl max-h-[85vh] flex flex-col pt-0"
        @click.away="isFilesModalOpen = false"
    >
        <!-- Header -->
        <div class="px-6 py-4 flex items-center gap-2 border-b border-gray-100 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" placeholder="Buscar archivos" class="w-full border-none focus:ring-0 text-sm text-gray-600 placeholder-gray-400">
            <button @click="isFilesModalOpen = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Tabs -->
        <div class="px-8 mt-4 flex gap-6 text-sm font-medium border-b border-gray-100 pb-2 shrink-0">
            <button @click="isFilesModalOpen = false; openComments(activeTask.id)" class="text-gray-500 hover:text-gray-700 pb-1">Comentarios</button>
            <button class="text-[#22A9C8] border-b-2 border-[#22A9C8] pb-1">Archivos</button>
        </div>

        <!-- Filter Section -->
        <div class="px-8 py-6 shrink-0">
            <h4 class="text-[#22A9C8] font-medium text-lg mb-4">Filtro de búsqueda</h4>
            <!-- ... Filters (condensed) ... -->
             <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2 text-gray-600">
                    <span class="text-sm">Desde</span>
                    <input type="date" class="bg-gray-50 border-none rounded-full py-2 px-4 text-sm w-36 text-gray-500">
                </div>
                 <div class="flex items-center gap-2 text-gray-600">
                    <span class="text-sm">Hasta</span>
                    <input type="date" class="bg-gray-50 border-none rounded-full py-2 px-4 text-sm w-36 text-gray-500">
                </div>
                <button class="bg-[#22A9C8] hover:bg-[#1B8BA6] text-white font-medium py-2 px-6 rounded-full text-sm transition-colors shadow-sm">
                    Filtrar
                </button>
            </div>
        </div>

        <!-- Files List Header -->
        <div class="px-8 mb-2 flex text-xs font-bold text-[#0D1E4C] shrink-0">
            <div class="w-1/2">Nombre del archivo</div>
            <div class="w-1/4">Tipo</div>
            <div class="w-1/4">Modificado por última vez</div>
        </div>

        <!-- Files List -->
        <div class="flex-1 overflow-y-auto px-8 space-y-2 pb-4 min-h-0">
             <template x-if="activeTask && activeTask.attachments">
                <template x-for="file in activeTask.attachments" :key="file.id">
                    <div class="bg-gray-50 rounded-lg p-4 flex items-center text-sm">
                        <!-- Name -->
                        <div class="w-1/2 flex items-center gap-2 font-medium text-gray-800 italic">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                           <a :href="'/storage/' + file.stored_filename" target="_blank" class="hover:underline" x-text="file.filename"></a>
                        </div>
                        <!-- Type -->
                        <div class="w-1/4 text-gray-600 uppercase font-bold text-xs pl-1">
                             <span x-text="file.filename.split('.').pop()"></span>
                        </div>
                        <!-- Modified -->
                        <div class="w-1/4 text-gray-500 text-xs">
                            El <span x-text="new Date(file.updated_at).toLocaleDateString()"></span> 
                            por <span x-text="file.uploader ? file.uploader.name : 'Desconocido'"></span>
                        </div>
                    </div>
                </template>
            </template>
            <div x-show="!activeTask?.attachments?.length" class="text-center text-gray-500 py-8">
                No hay archivos adjuntos.
            </div>
        </div>

        <!-- Footer / Upload -->
        <div class="p-8 flex justify-center shrink-0 bg-white">
             <!-- Hidden File Input -->
             <input type="file" x-ref="fileInput" class="hidden" @change="uploadFile(activeTask.id, $event.target.files[0])">
             
             <button 
                @click="$refs.fileInput.click()"
                class="border border-[#22A9C8] text-[#0D1E4C] hover:bg-[#22A9C8] hover:text-white font-medium py-2 px-10 rounded-full transition-colors shadow-sm"
            >
                Subir archivo
            </button>
        </div>
    </div>
</div>
