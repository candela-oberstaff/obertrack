<div id="commentModal" class="fixed inset-0 z-[60] overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 transition-opacity" aria-hidden="true" onclick="closeCommentModal()"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-8 pt-8 pb-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-[#22A9C8]/10 flex items-center justify-center text-[#22A9C8]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#0D1E4C]" id="modal-title">
                        Aprobar con comentarios
                    </h3>
                </div>
                <div class="mt-2">
                    <label for="approvalComment" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tu mensaje para el profesional</label>
                    <textarea id="approvalComment" rows="4" 
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-[#22A9C8] transition-all" 
                        placeholder="Escribe aquí tus observaciones o feedback..."></textarea>
                </div>
            </div>
            <div class="bg-gray-50 px-8 py-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" onclick="approveWithComment()" 
                    class="w-full sm:w-auto inline-flex justify-center rounded-full border border-transparent shadow-sm px-8 py-2.5 bg-[#22A9C8] text-sm font-bold text-white hover:bg-[#1B8BA6] focus:outline-none transition-all">
                    Aprobar ahora
                </button>
                <button type="button" onclick="closeCommentModal()" 
                    class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-full border border-gray-200 shadow-sm px-8 py-2.5 bg-white text-sm font-bold text-gray-500 hover:bg-gray-50 focus:outline-none transition-all">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
