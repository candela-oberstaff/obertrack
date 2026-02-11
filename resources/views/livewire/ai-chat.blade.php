<div class="flex h-[calc(100vh-65px)] bg-white overflow-hidden" x-data="{ 
    sidebarOpen: false,
    pendingMessage: '',
    pendingAttachment: null,
    scrollToBottom() {
        const container = document.getElementById('chat-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
}" x-init="scrollToBottom()">

    <!-- Sidebar (History) -->
    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black/50 lg:hidden transition-opacity"></div>
    
    <!-- Sidebar Content -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed lg:relative z-30 w-64 h-full bg-gray-50 border-r border-gray-200 transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col shrink-0">
        
        <!-- New Chat Button -->
        <div class="p-4">
            <button wire:click="newChat" @click="if(window.innerWidth < 1024) sidebarOpen = false" class="w-full flex items-center gap-3 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nuevo Chat</span>
            </button>
        </div>
        
        <!-- Recent History Section -->
        <div class="flex-1 overflow-y-auto px-2 py-2 space-y-1">
            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Recientes</div>
            @foreach($conversations as $conv)
                <div class="group relative w-full">
                    <button 
                        wire:click="loadConversation({{ $conv->id }})" 
                        @click="if(window.innerWidth < 1024) sidebarOpen = false"
                        class="w-full text-left px-4 py-2 pr-10 rounded-lg text-sm truncate transition-colors {{ $currentConversationId === $conv->id ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}"
                        title="{{ $conv->title ?? 'Nuevo Chat' }}"
                    >
                        {{ $conv->title ?? 'Nuevo Chat' }}
                    </button>
                    <button 
                        wire:click.stop="deleteConversation({{ $conv->id }})"
                        wire:confirm="¿Estás seguro de eliminar este chat?"
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity"
                        title="Eliminar chat"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>

        <!-- User Profile / Settings (Optional Footer) -->
        <div class="p-4 border-t border-gray-200">
             <div class="flex items-center gap-2 text-xs text-gray-400">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                Llama 3.2 Online
             </div>
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col h-full relative">
        
        <!-- Mobile Header -->
        <div class="lg:hidden flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-white">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="font-semibold text-gray-800">Obertrack AI</span>
            <div class="w-8"></div> <!-- Spacer -->
        </div>

        <!-- Messages Container -->
        <div id="chat-container" class="flex-1 overflow-y-auto p-4 sm:p-8 space-y-8 scroll-smooth">
            @if(empty($messages))
                <!-- Welcome State -->
                <div class="h-full flex flex-col items-center justify-center text-center p-8 opacity-50">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-800">¿En qué puedo ayudarte hoy?</h3>
                </div>
            @endif

            @foreach ($messages as $msg)
                <div class="flex w-full {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] lg:max-w-[70%] flex gap-4 {{ $msg['role'] === 'user' ? 'flex-row-reverse' : 'flex-row' }}">
                        
                        <!-- Avatar -->
                        <div class="shrink-0 mt-1"> 
                            @if($msg['role'] === 'user')
                                @if(auth()->user()->profile_photo_path)
                                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" class="w-8 h-8 rounded-full object-cover" alt="User">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            @else
                                <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-indigo-600 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex flex-col {{ $msg['role'] === 'user' ? 'items-end' : 'items-start' }}">
                             <!-- Name (Optional) -->
                             <span class="text-xs text-gray-400 mb-1 px-1">
                                {{ $msg['role'] === 'user' ? 'Tú' : 'AI' }}
                             </span>

                             <!-- Bubble -->
                             <div class="text-[15px] leading-relaxed px-5 py-3.5 shadow-sm 
                                {{ $msg['role'] === 'user' 
                                    ? 'bg-[#f0f4fa] text-gray-800 rounded-2xl rounded-tr-sm' 
                                    : 'bg-white text-gray-800 rounded-2xl rounded-tl-sm border border-gray-100' 
                                }}">
                                <div class="prose prose-sm max-w-none prose-p:my-1 prose-pre:bg-gray-800 prose-pre:text-gray-100">
                                    @if(isset($msg['attachment_path']) && $msg['attachment_path'])
                                        <div class="mb-3">
                                            @if(isset($msg['attachment_type']) && $msg['attachment_type'] === 'image')
                                                <img src="{{ Storage::url($msg['attachment_path']) }}" class="rounded-lg max-w-full h-auto border border-gray-200" alt="Attachment">
                                            @else
                                                <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span class="text-sm text-gray-600 underline">Archivo adjunto</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    {!! Str::markdown($msg['content'] ?? '') !!}
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Optimistic User Message -->
            <template x-if="pendingMessage">
                <div class="flex w-full justify-end">
                    <div class="max-w-[85%] lg:max-w-[70%] flex gap-4 flex-row-reverse">
                        <div class="shrink-0 mt-1"> 
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" class="w-8 h-8 rounded-full object-cover" alt="User">
                            @else
                                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-end">
                             <span class="text-xs text-gray-400 mb-1 px-1">Tú</span>
                             <div class="text-[15px] leading-relaxed px-5 py-3.5 shadow-sm bg-[#f0f4fa] text-gray-800 rounded-2xl rounded-tr-sm">
                                <template x-if="pendingAttachment">
                                    <img :src="pendingAttachment" class="rounded-lg max-w-full h-auto border border-gray-200 mb-2">
                                </template>
                                <div class="prose prose-sm max-w-none prose-p:my-1 prose-pre:bg-gray-800 prose-pre:text-gray-100" x-text="pendingMessage"></div>
                             </div>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="$wire.isTyping" class="flex justify-start w-full pl-12" style="display: none;">
                 <div class="flex items-center gap-1.5 h-8">
                    <span class="w-2 h-2 bg-gray-300 rounded-full animate-bounce"></span>
                    <span class="w-2 h-2 bg-gray-300 rounded-full animate-bounce delay-100"></span>
                    <span class="w-2 h-2 bg-gray-300 rounded-full animate-bounce delay-200"></span>
                </div>
            </div>

            <div class="h-24"></div> <!-- Initial spacer for bottom input -->
        </div>

        <!-- Floating Input Area -->
        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-white via-white to-transparent pt-10 pb-6 px-4">
             <div class="max-w-3xl mx-auto">
                 <form x-data="{ 
                    sending: false, 
                    localMessage: '',
                    submitMessage() {
                        if (this.sending) return;
                        if (!this.localMessage && !this.$wire.attachment) return; // Basic validation
                        
                        this.sending = true;
                        
                        // Optimistic UI
                        this.pendingMessage = this.localMessage;
                        this.pendingAttachment = this.$refs.fileInput.files[0] ? URL.createObjectURL(this.$refs.fileInput.files[0]) : null;
                        
                        // Send to backend
                        const textToSend = this.localMessage;
                        this.localMessage = ''; // Clear input immediately visually
                        
                        this.$wire.sendMessage(textToSend).then(() => {
                            this.pendingMessage = '';
                            this.pendingAttachment = null;
                            this.sending = false;
                            
                            // Reset file input
                            this.$refs.fileInput.value = '';
                            this.$wire.set('attachment', null); // Clear Livewire attachment
                            
                        }).catch(() => {
                            this.sending = false;
                            // Restore message on error (optional, but good UX)
                            this.localMessage = textToSend; 
                            this.pendingMessage = '';
                        });
                        
                        this.scrollToBottom();
                    }
                 }" 
                 @submit.prevent="submitMessage()" 
                 class="relative bg-gray-100 rounded-3xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 focus-within:border-indigo-300 focus-within:ring-2 focus-within:ring-indigo-100 focus-within:bg-white flex flex-col">
                    
                    <!-- Preview -->
                    <div x-show="$wire.attachment" class="px-4 pt-3 pb-1">
                        <!-- ... (same preview code) ... -->
                        <div class="relative inline-block">
                            <template x-if="$wire.attachment">
                                <div class="relative">
                                     <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-2 py-1 text-sm text-gray-600">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                          </svg>
                                         <span x-text="$refs.fileInput.files[0]?.name"></span>
                                         <button type="button" wire:click="$set('attachment', null)" @click="$refs.fileInput.value = ''" class="text-red-500 hover:text-red-700 ml-1">
                                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                 <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                             </svg>
                                         </button>
                                     </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center w-full">
                        <button type="button" @click="$refs.fileInput.click()" class="pl-4 text-gray-400 hover:text-indigo-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </button>
                        
                        <input type="file" x-ref="fileInput" wire:model="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx,.txt" />

                        <input 
                            x-model="localMessage"
                            type="text" 
                            placeholder="Escribe un mensaje..." 
                            class="w-full bg-transparent border-0 rounded-full py-3.5 px-3 text-gray-800 placeholder-gray-500 focus:ring-0"
                            :disabled="$wire.isTyping || sending"
                            autofocus
                        >
                        <button 
                            type="submit" 
                            class="mr-2 p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition-transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center w-9 h-9 shrink-0"
                            :disabled="$wire.isTyping || sending || (!localMessage && !$wire.attachment)"
                        >
                            <svg x-show="!$wire.isTyping && !sending" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                             <div x-show="$wire.isTyping || sending" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" style="display: none;"></div>
                        </button>
                    </div>
                 </form>
                 <div class="text-center mt-2">
                     <p class="text-[10px] text-gray-400">Obertrack AI puede mostrar información imprecisa.</p>
                 </div>
             </div>
        </div>

    </div>

    <!-- Scripts -->
    @script
    <script>
        const container = document.getElementById('chat-container');
        let eventSource = null;
        
        const scrollToBottom = () => {
            if(container) {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
            }
        };

        // Scroll on load
        scrollToBottom();

        // Scroll on message add
        $wire.on('message-added', () => {
             setTimeout(scrollToBottom, 50);
        });
        
        // Streaming Logic
        // We use a named function to allow explicit removal if needed, 
        // but $wire.on should handle component lifecycle cleanup automatically.
        // Helper: show error with retry button inside an AI bubble
        const showErrorWithRetry = (contentArea, aiMsgDiv, conversationId, errorMsg) => {
            contentArea.innerHTML = `
                <div class="flex flex-col items-start gap-3">
                    <div class="flex items-center gap-2 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span class="text-sm">${errorMsg}</span>
                    </div>
                    <button class="ai-retry-btn inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-full transition-all duration-200 hover:shadow-sm active:scale-95" data-conversation-id="${conversationId}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reintentar
                    </button>
                </div>
            `;
            scrollToBottom();

            // Bind retry button
            const retryBtn = contentArea.querySelector('.ai-retry-btn');
            if (retryBtn) {
                retryBtn.addEventListener('click', () => {
                    aiMsgDiv.remove();
                    $wire.retryLastMessage();
                });
            }
        };

        $wire.on('start-streaming', (event) => {
            console.log("Start streaming event received", event);
            const conversationId = event.conversationId;
            const streamUrl = `{{ route('ai.stream') }}?conversation_id=${conversationId}&message=context`;
            
            const messagesContainer = document.getElementById('chat-container');
            
            // Create the AI message bubble dynamically
            const aiMsgDiv = document.createElement('div');
            aiMsgDiv.className = 'flex w-full justify-start';
            aiMsgDiv.innerHTML = `
                <div class="max-w-[85%] lg:max-w-[70%] flex gap-4 flex-row">
                    <div class="shrink-0 mt-1">
                        <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-indigo-600 p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-col items-start">
                            <span class="text-xs text-gray-400 mb-1 px-1">AI</span>
                            <div class="text-[15px] leading-relaxed px-5 py-3.5 shadow-sm bg-white text-gray-800 rounded-2xl rounded-tl-sm border border-gray-100">
                            <div class="prose prose-sm max-w-none prose-p:my-1 prose-pre:bg-gray-800 prose-pre:text-gray-100" id="streaming-content">
                                <div class="flex items-center gap-2 text-gray-400">
                                    <div class="flex gap-1">
                                        <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                        <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                        <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                    </div>
                                    <span class="text-sm animate-pulse">Pensando...</span>
                                </div>
                            </div>
                            </div>
                    </div>
                </div>
            `;
            messagesContainer.appendChild(aiMsgDiv);
            scrollToBottom();

            const contentArea = aiMsgDiv.querySelector('#streaming-content');
            let fullContent = '';
            let isFirstChunk = true;

            if (eventSource) {
                 eventSource.close();
            }

            eventSource = new EventSource(streamUrl);

            eventSource.onopen = function() {
                console.log("Connection established with AI Stream.");
            };

            eventSource.onmessage = function(e) {
                if (e.data === "[DONE]") {
                    eventSource.close();
                    $wire.saveAiResponse(fullContent).then(() => {
                        aiMsgDiv.remove(); 
                        scrollToBottom();
                    });
                    return;
                }

                try {
                    const data = JSON.parse(e.data);
                    
                    if (data.error) {
                        console.error("Backend Error:", data.error);
                        eventSource.close();
                        showErrorWithRetry(contentArea, aiMsgDiv, conversationId, 
                            'No pude procesar tu solicitud. Inténtalo de nuevo en unos segundos.');
                        return;
                    }

                    if (typeof data.content !== 'undefined') {
                        if (isFirstChunk && data.content.length > 0) {
                            contentArea.innerHTML = '';
                            isFirstChunk = false;
                        }
                        fullContent += data.content;
                        if (fullContent.length > 0) {
                            contentArea.innerText = fullContent; 
                            scrollToBottom();
                        }
                    }
                } catch (err) {
                    console.error('Error parsing SSE:', err);
                }
            };

            eventSource.onerror = function(err) {
                console.error("EventSource failed:", err);
                if (eventSource.readyState === 2) { // CLOSED
                    eventSource.close();
                    if (!fullContent) {
                        showErrorWithRetry(contentArea, aiMsgDiv, conversationId, 
                            'La conexión se interrumpió. Espera unos segundos e inténtalo de nuevo.');
                    } else {
                        $wire.saveAiResponse(fullContent).then(() => {
                            aiMsgDiv.remove();
                        });
                    }
                }
            };
        });
        
        const observer = new MutationObserver(() => {
        });
        
        if(container) {
            observer.observe(container, { childList: true, subtree: true, characterData: true });
        }
    </script>
    @endscript
</div>
