{{-- Task Details Modal (Any.do Style) --}}
<div>
        <div x-show="isDetailsModalOpen" 
             class="fixed inset-0 z-[9999] overflow-hidden" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="if(isDetailsModalOpen) closeModal()">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

            <!-- Modal Panel -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-6xl h-[85vh] bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row"
                     @click.stop>
                     
                    <!-- LEFT COLUMN: Task Details (65%) -->
                    <div class="flex-1 flex flex-col overflow-y-auto bg-white relative">
                        
                        <!-- Header Actions -->
                        <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm px-8 py-6 flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <!-- Mark Complete Circle -->

                                
                                <span class="text-xs font-semibold tracking-wider text-gray-500 uppercase" x-text="selectedTask?.project?.name || 'Tareas'"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Edit/Delete Actions (Only for authorized users) -->
                                <template x-if="selectedTask && (currentUser.id == selectedTask.created_by || currentUser.tipo_usuario == 'empleador' || currentUser.is_superadmin) && !isEditingTask">
                                    <div class="flex items-center gap-1">
                                        <button @click="startEditingTask()" class="p-2 text-gray-400 hover:text-[#22A9C8] hover:bg-gray-100 rounded-full transition-colors" title="Editar">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        <button type="button" @click="confirmDeleteTask()" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="px-8 pb-10 space-y-8">
                            
                            <!-- Task Title -->
                            <div x-show="!isEditingTask">
                                <h1 class="text-3xl font-bold text-gray-900 leading-tight break-words" x-text="selectedTask?.title"></h1>
                            </div>
                            <!-- Edit Title -->
                            <div x-show="isEditingTask">
                                <input type="text" x-model="editTaskData.title" class="text-3xl font-bold text-gray-900 leading-tight w-full border-0 border-b-2 border-gray-200 focus:border-[#22A9C8] focus:ring-0 px-0 py-2 bg-transparent placeholder-gray-300" placeholder="Task Title">
                            </div>

                            <!-- Meta Data Row -->
                            <div class="flex flex-wrap items-center gap-4">
                                <!-- Assignees -->
                                <div class="flex items-center -space-x-2">
                                    <template x-for="assignee in selectedTask?.assignees" :key="assignee.id">
                                        <div class="relative z-0 group cursor-help flex items-center gap-2 pr-4 bg-white border border-gray-100 rounded-full pl-1 py-1 shadow-sm hover:shadow-md transition-all">
                                            <img :src="assignee.avatar ? (assignee.avatar.startsWith('http') ? assignee.avatar : '/avatars/' + assignee.avatar) : 'https://ui-avatars.com/api/?name='+encodeURIComponent(assignee.name)+'&color=FFFFFF&background=22A9C8'" 
                                                 class="w-8 h-8 rounded-full border-2 border-white shadow-sm"
                                                 :title="assignee.name">
                                            <span class="text-sm font-medium text-gray-700 max-w-[120px] truncate" x-text="assignee.name"></span>
                                        </div>
                                    </template>
                                    <button x-show="isEditingTask" class="w-8 h-8 rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 hover:text-[#22A9C8] hover:border-[#22A9C8] bg-white transition-colors" title="Add Assignee (Use edit mode below)">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                    </button>
                                </div>

                                <!-- Due Date Pipeline -->
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-full border border-gray-200 text-sm font-medium text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span x-text="selectedTask?.end_date ? formatDate(selectedTask.end_date) : 'Sin fecha'"></span>
                                </div>

                                <!-- Priority Tag -->
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-full border border-gray-200 text-sm font-medium capitalize"
                                     :class="{
                                        'text-red-500 bg-red-50 border-red-100': selectedTask?.priority === 'high' || selectedTask?.priority === 'urgent',
                                        'text-yellow-600 bg-yellow-50 border-yellow-100': selectedTask?.priority === 'medium',
                                        'text-blue-500 bg-blue-50 border-blue-100': selectedTask?.priority === 'low'
                                     }">
                                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-8a2 2 0 012-2h14a2 2 0 012 2v8l-6-5-6 5z" /></svg>
                                     <span x-text="selectedTask?.priority || 'Normal'"></span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Descripción</h4>
                                <div x-show="!isEditingTask" class="text-gray-700 leading-relaxed text-sm whitespace-pre-line break-all">
                                    <p x-text="selectedTask?.description || 'Añade una descripción...'"></p>
                                </div>
                                <div x-show="isEditingTask">
                                    <textarea x-model="editTaskData.description" rows="6" class="w-full bg-gray-50 border-gray-200 rounded-xl py-3 px-4 text-sm focus:ring-[#22A9C8] focus:border-[#22A9C8] transition-all resize-none" placeholder="Task description..."></textarea>
                                </div>
                            </div>

                            <!-- Additional Edit Fields (Hidden unless editing) -->
                            <div x-show="isEditingTask" class="grid grid-cols-2 gap-6 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 mb-2 block">Fecha Límite</label>
                                    <input type="date" x-model="editTaskData.end_date" class="w-full rounded-lg border-gray-200 text-sm focus:border-[#22A9C8] focus:ring-[#22A9C8]">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 mb-2 block">Prioridad</label>
                                    <select x-model="editTaskData.priority" class="w-full rounded-lg border-gray-200 text-sm focus:border-[#22A9C8] focus:ring-[#22A9C8]">
                                        <option value="low">Baja</option>
                                        <option value="medium">Media</option>
                                        <option value="high">Alta</option>
                                        <option value="urgent">Urgente</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="text-xs font-semibold text-gray-500 mb-2 block">Asignados</label>
                                    <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto">
                                        @if(isset($employees) && count($employees) > 0)
                                            @foreach($employees as $employee)
                                                <label class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-[#22A9C8] transition-colors">
                                                    <input type="checkbox" value="{{ $employee->id }}" x-model="editTaskData.assignees" class="rounded text-[#22A9C8] focus:ring-[#22A9C8]">
                                                    <span class="text-sm font-medium text-gray-700">{{ $employee->name }}</span>
                                                </label>
                                            @endforeach
                                        @else
                                            <span class="text-sm text-gray-400">No employees found.</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-span-2 flex justify-end gap-3 pt-2">
                                    <button @click="isEditingTask = false" class="px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">Cancelar</button>
                                    <button @click="saveTask()" 
                                            :disabled="isSavingTask"
                                            class="px-6 py-2 text-sm font-bold text-white bg-[#22A9C8] hover:bg-[#1B8BA6] rounded-lg shadow-sm transition-colors flex items-center gap-2"
                                            :class="{'opacity-50 cursor-not-allowed': isSavingTask}">
                                        <span x-show="isSavingTask" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                        <span x-text="isSavingTask ? 'Guardando...' : 'Guardar Cambios'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Attachments Section -->
                            <div class="pt-6 border-t border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                        Archivos
                                    </h4>
                                    <!-- Upload Button -->
                                    <label class="cursor-pointer text-[#22A9C8] hover:text-[#1B8BA6] text-sm font-bold flex items-center gap-1 transition-colors">
                                        <input type="file" @change="uploadFile($event.target.files[0])" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                        <svg x-show="!isUploadingFile" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        <svg x-show="isUploadingFile" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Añadir archivo</span>
                                    </label>
                                </div>
                                
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                     <template x-for="attachment in selectedTask?.attachments" :key="attachment.id">
                                        <div class="relative group bg-gray-50 rounded-xl border border-gray-100 overflow-hidden hover:border-gray-300 transition-all">
                                            <!-- Preview -->
                                            <div class="h-24 bg-gray-100 flex items-center justify-center relative">
                                                <template x-if="['jpg','jpeg','png','gif','webp'].includes(attachment.filename.split('.').pop().toLowerCase())">
                                                    <img :src="'/tasks/attachments/' + attachment.id + '/download'" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!['jpg','jpeg','png','gif','webp'].includes(attachment.filename.split('.').pop().toLowerCase())">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                </template>
                                                
                                                <!-- Overlay Actions -->
                                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                                    <a :href="'/tasks/attachments/' + attachment.id + '/download'" class="p-1.5 bg-white text-gray-700 rounded-lg hover:text-[#22A9C8]" title="Descargar" @click.stop>
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                    </a>
                                                    <button @click="deleteFile(attachment.id)" class="p-1.5 bg-white text-red-500 rounded-lg hover:bg-red-50" title="Eliminar">
                                                        <template x-if="deletingFileId === attachment.id">
                                                            <span class="animate-spin h-4 w-4 border-2 border-red-500 border-t-transparent rounded-full block"></span>
                                                        </template>
                                                        <template x-if="deletingFileId !== attachment.id">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </template>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="px-3 py-2">
                                                <p class="text-xs font-bold text-gray-700 break-all" x-text="attachment.filename"></p>
                                                <p class="text-[10px] text-gray-400" x-text="(attachment.size / 1024).toFixed(1) + ' KB'"></p>
                                            </div>
                                        </div>
                                     </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Activity & Chat (35%) -->
                    <div class="w-full md:w-[380px] bg-gray-50 flex flex-col border-l border-gray-100 overflow-x-hidden">
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white md:bg-gray-50">
                            <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Actividad</h3>
                            <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Chat List -->
                        <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-4">
                            <!-- Empty State -->
                             <template x-if="!selectedTask?.comments || selectedTask.comments.length === 0">
                                <div class="h-full flex flex-col items-center justify-center text-center opacity-50 pb-10">
                                    <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">No hay comentarios aún</p>
                                    <p class="text-xs text-gray-400">Inicia la conversación</p>
                                </div>
                            </template>

                            <template x-for="comment in selectedTask?.comments" :key="comment.id">
                                <div class="flex gap-3 group max-w-full">
                                    <div class="flex-shrink-0 mt-1">
                                        <img :src="comment.user?.avatar ? (comment.user.avatar.startsWith('http') ? comment.user.avatar : '/avatars/' + comment.user.avatar) : 'https://ui-avatars.com/api/?name='+encodeURIComponent(comment.user_name || comment.user?.name || 'U')+'&color=FFFFFF&background=22A9C8'" 
                                             class="w-8 h-8 rounded-full bg-gray-200 border border-gray-200">
                                    </div>
                                    <div class="flex-1 min-w-0 overflow-hidden max-w-full">
                                        <div class="bg-white rounded-2xl rounded-tl-none p-3 shadow-sm border border-gray-100 relative group-hover:border-gray-200 transition-all break-words max-w-full" style="overflow-wrap: anywhere; word-break: break-word;">
                                            <div class="flex justify-between items-start mb-1 gap-2">
                                                <span class="text-xs font-bold text-gray-900 break-all" x-text="comment.user_name || comment.user?.name || 'Usuario'"></span>
                                                <span class="text-[10px] text-gray-400 whitespace-nowrap" x-text="new Date(comment.created_at).toLocaleDateString() + ' ' + new Date(comment.created_at).toLocaleTimeString().slice(0,5)"></span>
                                            </div>
                                            
                                            <template x-if="editingCommentId !== comment.id">
                                                <p class="text-sm text-gray-600 whitespace-pre-wrap break-all leading-relaxed" x-text="comment.content"></p>
                                            </template>

                                            <!-- Edit Mode -->
                                            <template x-if="editingCommentId === comment.id">
                                                <div class="mt-2">
                                                    <textarea x-model="editCommentContent" maxlength="500" class="w-full text-sm border-gray-200 rounded-lg focus:ring-[#22A9C8] focus:border-[#22A9C8]" rows="2"></textarea>
                                                    <div class="flex justify-end gap-2 mt-2">
                                                        <button @click="editingCommentId = null" class="text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                                                            <button @click="updateComment(comment.id)" 
                                                                    :disabled="updatingCommentId === comment.id"
                                                                    class="text-xs bg-[#22A9C8] text-white px-3 py-1 rounded-full flex items-center gap-1 disabled:opacity-50 transition-all min-w-[80px] justify-center">
                                                                <template x-if="updatingCommentId === comment.id">
                                                                    <div class="flex items-center gap-1">
                                                                        <span class="animate-spin h-3 w-3 border-2 border-white border-t-transparent rounded-full"></span>
                                                                        <span>Guardando...</span>
                                                                    </div>
                                                                </template>
                                                                <template x-if="updatingCommentId !== comment.id">
                                                                    <span>Guardar</span>
                                                                </template>
                                                            </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex gap-2 mt-1 ml-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="currentUser.id == comment.user_id && editingCommentId != comment.id">
                                            <button @click="startEditingComment(comment)" class="text-[10px] text-gray-400 hover:text-[#22A9C8] font-medium" :disabled="deletingCommentId === comment.id">Editar</button>
                                            <button @click="deleteComment(comment.id)" class="text-[10px] text-gray-400 hover:text-red-500 font-medium flex items-center gap-1" :disabled="deletingCommentId === comment.id">
                                                <template x-if="deletingCommentId === comment.id">
                                                    <span>Eliminando...</span>
                                                </template>
                                                <template x-if="deletingCommentId !== comment.id">
                                                    <span>Eliminar</span>
                                                </template>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Chat Input -->
                        <div class="p-4 bg-white border-t border-gray-200">
                            <div class="relative">
                                <textarea x-model="newCommentText" 
                                          placeholder="Escribe un comentario o @menciona..." 
                                          class="w-full text-sm bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#22A9C8] focus:border-transparent resize-none py-3 pl-4 pr-12 transition-all"
                                          rows="1"
                                          @keydown.enter.prevent="if(!$event.shiftKey) submitComment()"
                                          style="min-height: 48px;"></textarea>
                                <button @click="submitComment()" 
                                        class="absolute right-2 top-2 p-1.5 text-[#22A9C8] hover:bg-blue-50 rounded-full transition-colors disabled:opacity-50 disabled:grayscale"
                                        :disabled="!newCommentText.trim() || isSubmittingComment">
                                    <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 text-center" x-show="newCommentText.length > 0">Presiona Enter para enviar</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Confirmation Modal Overlay (Same as before) -->
        <!-- Confirmation Modal Removed as per user request -->
        <!-- Logic moved to native window.confirm() -->
    </div>
</div>
