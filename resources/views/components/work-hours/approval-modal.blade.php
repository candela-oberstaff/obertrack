\u003cdiv id="commentModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true"\u003e
    \u003cdiv class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"\u003e
        \u003cdiv class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"\u003e\u003c/div\u003e
        \u003cspan class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"\u003e\u0026#8203;\u003c/span\u003e
        \u003cdiv class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"\u003e
            \u003cdiv class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"\u003e
                \u003ch3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title"\u003e
                    Aprobar con comentarios
                \u003c/h3\u003e
                \u003cdiv class="mt-2"\u003e
                    \u003ctextarea id="approvalComment" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Ingrese sus comentarios aquí"\u003e\u003c/textarea\u003e
                \u003c/div\u003e
            \u003c/div\u003e
            \u003cdiv class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"\u003e
                \u003cbutton type="button" onclick="approveWithComment()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"\u003e
                    Aprobar
                \u003c/button\u003e
                \u003cbutton type="button" onclick="closeCommentModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"\u003e
                    Cancelar
                \u003c/button\u003e
            \u003c/div\u003e
        \u003c/div\u003e
    \u003c/div\u003e
\u003c/div\u003e
