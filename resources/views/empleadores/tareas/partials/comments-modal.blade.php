<!-- Comments Modal -->
<div 
    x-show="isCommentsModalOpen" 
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0"
>
    <!-- Backdrop -->
    <div 
        x-show="isCommentsModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 transform transition-all"
        @click="isCommentsModalOpen = false"
    >
        <div class="absolute inset-0 bg-gray-600 opacity-50"></div>
    </div>

    <!-- Modal Content -->
    <div 
        x-show="isCommentsModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="bg-white rounded-3xl overflow-hidden shadow-xl transform transition-all w-full max-w-2xl max-h-[85vh] flex flex-col pt-0"
        @click.away="isCommentsModalOpen = false"
    >
        <!-- Header -->
        <div class="px-6 py-4 flex items-center gap-2 border-b border-gray-100 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" placeholder="Buscar comentarios" class="w-full border-none focus:ring-0 text-sm text-gray-600 placeholder-gray-400">
            <button @click="isCommentsModalOpen = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Tabs -->
        <div class="px-8 mt-4 flex gap-6 text-sm font-medium border-b border-gray-100 pb-2 shrink-0">
            <button class="text-[#22A9C8] border-b-2 border-[#22A9C8] pb-1">Comentarios</button>
            <button @click="isCommentsModalOpen = false; openFiles(activeTask.id)" class="text-gray-500 hover:text-gray-700 pb-1">Archivos</button>
        </div>

        <!-- Filter Section -->
        <div class="px-8 py-6 shrink-0">
            <h4 class="text-[#22A9C8] font-medium text-lg mb-4">Filtro de búsqueda</h4>
            <!-- ... Filters (kept condensed) ... -->
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

        <!-- Comments List -->
        <div class="flex-1 overflow-y-auto px-8 space-y-4 min-h-0">
            <template x-if="activeTask && activeTask.comments">
                <template x-for="comment in activeTask.comments" :key="comment.id">
                    <div class="bg-gray-50 rounded-2xl p-4 flex justify-between items-start">
                        <p class="text-gray-700 text-sm italic" x-text="comment.content"></p>
                        <div class="flex items-center gap-4 ml-4 shrink-0">
                            <span class="text-gray-800 text-sm font-medium" x-text="new Date(comment.created_at).toLocaleDateString()"></span>
                           <div class="flex items-center gap-2">
                                <img :src="comment.user.avatar ? (comment.user.avatar.startsWith('http') ? comment.user.avatar : '{{ asset('avatars') }}/' + (comment.user.avatar.includes('/') ? comment.user.avatar.split('/').pop() : comment.user.avatar)) : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(comment.user.name) + '&color=FFFFFF&background=22A9C8'" 
                                     class="w-6 h-6 rounded-full border border-gray-200 object-cover"
                                     x-on:error="$el.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(comment.user.name) + '&color=FFFFFF&background=22A9C8'">
                            </div>
                        </div>
                    </div>
                </template>
            </template>
             <div x-show="!activeTask?.comments?.length" class="text-center text-gray-500 py-8">
                No hay comentarios aún.
            </div>
        </div>

        <!-- Footer / Add Comment -->
        <div class="p-8 shrink-0 bg-white" x-data="{ 
            isAddingComment: false, 
            commentText: '', 
            isSubmitting: false
        }">
             <div x-show="!isAddingComment" class="flex justify-center">
                 <button 
                    @click="isAddingComment = true; $nextTick(() => $refs.commentInput.focus())"
                    class="border border-[#22A9C8] text-[#0D1E4C] hover:bg-[#22A9C8] hover:text-white font-medium py-2 px-10 rounded-full transition-colors shadow-sm"
                >
                    Añadir comentario
                </button>
             </div>

             <form x-show="isAddingComment" 
                   @submit.prevent="
                        isSubmitting = true;
                        if (await submitComment(activeTask.id, commentText)) {
                            isAddingComment = false;
                            commentText = '';
                        }
                        isSubmitting = false;
                   " 
                   class="flex flex-col gap-4"
             >
                 <textarea 
                    x-ref="commentInput"
                    x-model="commentText" 
                    class="w-full bg-gray-50 border-none rounded-lg p-3 text-sm focus:ring-2 focus:ring-[#22A9C8]" 
                    placeholder="Escribe tu comentario..." 
                    rows="3" 
                    required
                    :disabled="isSubmitting"
                 ></textarea>
                 <div class="flex justify-end gap-2">
                     <button type="button" @click="isAddingComment = false" class="text-gray-500 hover:text-gray-700 text-sm font-medium px-4 py-2" :disabled="isSubmitting">Cancelar</button>
                     <button type="submit" class="bg-[#22A9C8] text-white hover:bg-[#1B8BA6] font-medium py-2 px-6 rounded-full text-sm transition-colors shadow-sm flex items-center gap-2" :disabled="isSubmitting">
                        <span x-show="isSubmitting" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                        <span x-text="isSubmitting ? 'Enviando...' : 'Enviar'"></span>
                     </button>
                 </div>
             </form>
        </div>
    </div>
</div>
