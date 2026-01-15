{{-- Task Details Modal (Unified) --}}
<template x-teleport="body">
    <div x-show="isDetailsModalOpen" 
         class="fixed inset-0 z-[9999] overflow-y-auto" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="isDetailsModalOpen = false"></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl flex flex-col max-h-[90vh]"
                 @click.stop>
                
                <!-- Header & Tabs -->
                <div class="bg-gray-50 flex-shrink-0 border-b border-gray-100">
                    <div class="px-6 py-4 sm:px-8 flex justify-between items-start">
                        <div>
                             <h3 class="text-xl font-bold leading-6 text-[#0D1E4C]" x-text="selectedTask?.title"></h3>
                             <p class="mt-1 text-sm text-gray-500" x-text="'Creada por: ' + (selectedTask?.createdBy?.name || 'Sistema')"></p>
                        </div>
                        <button type="button" @click="isDetailsModalOpen = false" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Tabs Navigation -->
                    <div class="px-6 sm:px-8 flex space-x-6 border-t border-gray-100 mt-2">
                        <button @click="currentTab = 'details'" 
                                class="py-3 text-sm font-bold border-b-2 transition-colors duration-200"
                                :class="currentTab === 'details' ? 'border-[#22A9C8] text-[#22A9C8]' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Detalles
                        </button>
                        <button @click="currentTab = 'comments'" 
                                class="py-3 text-sm font-bold border-b-2 transition-colors duration-200 flex items-center gap-2"
                                :class="currentTab === 'comments' ? 'border-[#22A9C8] text-[#22A9C8]' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Comentarios
                            <span class="bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs" x-text="selectedTask?.comments?.length || 0"></span>
                        </button>
                        <button @click="currentTab = 'files'" 
                                class="py-3 text-sm font-bold border-b-2 transition-colors duration-200 flex items-center gap-2"
                                :class="currentTab === 'files' ? 'border-[#22A9C8] text-[#22A9C8]' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Archivos
                            <span class="bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs" x-text="selectedTask?.attachments?.length || 0"></span>
                        </button>
                    </div>
                </div>

                <!-- Body Content -->
                <div class="px-6 py-6 sm:px-8 overflow-y-auto flex-1">
                    
                    <!-- TAB: DETAILS -->
                    <div x-show="currentTab === 'details'" class="space-y-6">
                        <!-- Description -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Descripción</h4>
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <p class="text-sm text-gray-600 whitespace-pre-line" x-text="selectedTask?.description || 'Sin descripción'"></p>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Prioridad</span>
                                <p class="text-sm font-bold capitalize" 
                                   :class="{
                                        'text-red-500': selectedTask?.priority === 'high' || selectedTask?.priority === 'urgent',
                                        'text-yellow-500': selectedTask?.priority === 'medium',
                                        'text-[#22A9C8]': selectedTask?.priority === 'low'
                                   }"
                                   x-text="selectedTask?.priority === 'low' ? 'Baja' : (selectedTask?.priority === 'medium' ? 'Media' : (selectedTask?.priority === 'high' ? 'Alta' : 'Urgente'))"></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-2xl">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Fecha Límite</span>
                                <p class="text-sm font-bold text-red-500" x-text="formatDate(selectedTask?.end_date)"></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-2xl col-span-2">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Estado / Asignados</span>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <template x-if="selectedTask?.completed">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 uppercase tracking-wider">Completada</span>
                                        </template>
                                        <template x-if="!selectedTask?.completed">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                                                  :class="new Date(selectedTask?.end_date) < new Date() ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'"
                                                  x-text="new Date(selectedTask?.end_date) < new Date() ? 'Vencida' : 'Pendiente'">
                                            </span>
                                        </template>
                                    </div>
                                    <div class="flex -space-x-2">
                                         <template x-for="assignee in selectedTask?.assignees" :key="assignee.id">
                                            <img :src="assignee.avatar ? (assignee.avatar.startsWith('http') ? assignee.avatar : '/storage/' + assignee.avatar) : 'https://ui-avatars.com/api/?name='+encodeURIComponent(assignee.name)+'&color=FFFFFF&background=22A9C8'" 
                                                 :title="assignee.name"
                                                 class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 object-cover">
                                         </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: COMMENTS -->
                    <div x-show="currentTab === 'comments'" class="space-y-6">
                        <!-- Comment List -->
                         <div class="space-y-4 max-h-[40vh] overflow-y-auto pr-2">
                            <template x-for="comment in selectedTask?.comments" :key="comment.id">
                                <div class="flex gap-3 group">
                                    <div class="flex-shrink-0">
                                        <img :src="comment.user?.avatar ? (comment.user.avatar.startsWith('http') ? comment.user.avatar : '/storage/' + comment.user.avatar) : 'https://ui-avatars.com/api/?name='+encodeURIComponent(comment.user?.name || 'U')+'&color=FFFFFF&background=22A9C8'" 
                                             class="w-8 h-8 rounded-full bg-gray-200">
                                    </div>
                                    <div class="flex-1 bg-gray-50 rounded-2xl rounded-tl-none p-3 relative hover:bg-gray-100 transition-colors">
                                        
                                        <!-- Header -->
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-xs font-bold text-[#0D1E4C]" x-text="comment.user?.name || 'Usuario'"></span>
                                            <span class="text-[10px] text-gray-400" x-text="new Date(comment.created_at).toLocaleDateString() + ' ' + new Date(comment.created_at).toLocaleTimeString().slice(0,5)"></span>
                                        </div>
                                        
                                        <!-- Content -->
                                        <template x-if="editingCommentId !== comment.id">
                                            <p class="text-sm text-gray-700 whitespace-pre-wrap leading-snug" x-text="comment.content"></p>
                                        </template>

                                        <!-- Edit Mode -->
                                        <template x-if="editingCommentId === comment.id">
                                            <div class="mt-2">
                                                <textarea x-model="editCommentContent" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#22A9C8] focus:border-[#22A9C8]" rows="2"></textarea>
                                                <div class="flex justify-end gap-2 mt-2">
                                                    <button @click="editingCommentId = null" class="text-xs text-gray-500 hover:text-gray-700">Cancelar</button>
                                                    <button @click="updateComment(comment.id)" class="text-xs bg-[#22A9C8] text-white px-3 py-1 rounded-full hover:bg-[#1B8BA6]">Guardar</button>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Actions (Only for owner) -->
                                        <template x-if="currentUser.id === comment.user.id && editingCommentId !== comment.id">
                                            <div class="absolute right-2 bottom-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                                <button @click="startEditingComment(comment)" class="text-gray-400 hover:text-[#22A9C8]" title="Editar">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                </button>
                                                <button @click="confirmDeleteComment(comment.id)" class="text-gray-400 hover:text-red-500" title="Eliminar">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!selectedTask?.comments || selectedTask.comments.length === 0">
                                <div class="text-center py-8">
                                    <p class="text-gray-400 text-sm italic">No hay comentarios aún.</p>
                                </div>
                            </template>
                        </div>

                        <!-- Add Comment Form -->
                        <div class="bg-white border-t border-gray-100 pt-4 -mx-6 px-6 sm:-mx-8 sm:px-8 mt-auto sticky bottom-0">
                            <div class="flex gap-3">
                                <div class="flex-shrink-0 pt-1">
                                    <img :src="currentUser.avatar ? (currentUser.avatar.startsWith('http') ? currentUser.avatar : '/storage/' + currentUser.avatar) : 'https://ui-avatars.com/api/?name='+encodeURIComponent(currentUser.name)+'&color=FFFFFF&background=22A9C8'" 
                                             class="w-8 h-8 rounded-full bg-gray-200">
                                </div>
                                <div class="flex-1">
                                    <textarea x-model="newCommentText" 
                                              placeholder="Escribe un comentario..." 
                                              class="w-full text-sm border-gray-200 rounded-xl focus:ring-[#22A9C8] focus:border-[#22A9C8] resize-none py-3"
                                              rows="2"></textarea>
                                    <div class="flex justify-end mt-2">
                                        <button @click="submitComment()" 
                                                class="bg-[#22A9C8] hover:bg-[#1B8BA6] text-white text-xs font-bold py-2 px-6 rounded-full transition-colors disabled:opacity-50"
                                                :disabled="!newCommentText.trim() || isSubmittingComment">
                                            <span x-show="!isSubmittingComment">Enviar</span>
                                            <span x-show="isSubmittingComment">Enviando...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: FILES -->
                    <div x-show="currentTab === 'files'" class="space-y-6">
                        <!-- Upload Area -->
                        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-[#22A9C8] hover:bg-blue-50 transition-colors cursor-pointer relative group">
                            <input type="file" @change="uploadFile($event.target.files[0])" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                            <div x-show="!isUploadingFile">
                                <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-[#22A9C8] transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600 font-medium">Click para subir archivo</p>
                                <p class="mt-1 text-xs text-gray-400">PNG, JPG, PDF up to 10MB</p>
                            </div>
                            <div x-show="isUploadingFile" class="text-[#22A9C8]">
                                <svg class="animate-spin mx-auto h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="mt-2 text-sm font-medium">Subiendo...</p>
                            </div>
                        </div>

                        <!-- File List -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Archivos adjuntos</h4>
                            <template x-for="attachment in selectedTask?.attachments" :key="attachment.id">
                                <div class="flex items-center p-3 bg-gray-50 rounded-xl group hover:bg-gray-100 transition-colors border border-transparent hover:border-gray-200">
                                    
                                    <!-- Icon -->
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-white flex items-center justify-center text-gray-400 shadow-sm overflow-hidden">
                                        <!-- Image Preview if applicable -->
                                        <template x-if="['jpg','jpeg','png','gif','webp'].includes(attachment.filename.split('.').pop().toLowerCase())">
                                            <img :src="'/tasks/attachments/' + attachment.id + '/download'" class="w-full h-full object-cover">
                                        </template>
                                        <!-- Default Icon -->
                                        <template x-if="!['jpg','jpeg','png','gif','webp'].includes(attachment.filename.split('.').pop().toLowerCase())">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </template>
                                    </div>

                                    <!-- Info -->
                                    <div class="ml-3 flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-700 truncate" x-text="attachment.filename"></p>
                                        <div class="flex text-[10px] text-gray-400 gap-2">
                                            <span x-text="(attachment.file_size / 1024).toFixed(1) + ' KB'"></span>•
                                            <span x-text="new Date(attachment.created_at).toLocaleDateString()"></span>•
                                            <span x-text="attachment.uploader?.name || 'Usuario'"></span>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a :href="'/tasks/attachments/' + attachment.id + '/download'" 
                                           class="p-1.5 text-gray-400 hover:text-[#22A9C8] transition-colors rounded-lg hover:bg-white" 
                                           title="Descargar" @click.stop>
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                        
                                        <!-- Delete (Only uploader) -->
                                        <template x-if="currentUser.id === attachment.uploaded_by">
                                            <button @click="confirmDeleteFile(attachment.id)" 
                                                    class="p-1.5 text-gray-400 hover:text-red-500 transition-colors rounded-lg hover:bg-white"
                                                    title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!selectedTask?.attachments || selectedTask.attachments.length === 0">
                                <div class="text-center py-8">
                                    <p class="text-gray-400 text-sm italic">No hay archivos adjuntos.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Confirmation Modal Overlay -->
        <div x-show="deleteConfirmation.isOpen" 
             style="display: none;"
             class="absolute inset-0 z-[10000] flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm"
             x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center transform transition-all" @click.stop>
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-500 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">¿Estás seguro?</h3>
                <p class="text-sm text-gray-500 mb-6">Esta acción no se puede deshacer. El elemento será eliminado permanentemente.</p>
                <div class="flex gap-3 justify-center">
                    <button @click="deleteConfirmation.isOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                    <button @click="performDelete()" class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">Eliminar</button>
                </div>
            </div>
        </div>

    </div>
</template>
