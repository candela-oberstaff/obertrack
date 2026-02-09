<div class="flex h-[calc(100vh-65px)] bg-white overflow-hidden" x-data="{ 
    sidebarOpen: false,
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
                <button 
                    wire:click="loadConversation({{ $conv->id }})" 
                    @click="if(window.innerWidth < 1024) sidebarOpen = false"
                    class="w-full text-left px-4 py-2 rounded-lg text-sm truncate transition-colors {{ $currentConversationId === $conv->id ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}"
                    title="{{ $conv->title ?? 'Nuevo Chat' }}"
                >
                    {{ $conv->title ?? 'Nuevo Chat' }}
                </button>
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
                                    {!! Str::markdown($msg['content']) !!}
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Typing Indicator -->
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
                 <form wire:submit.prevent="sendMessage" class="relative bg-gray-100 rounded-full shadow-sm hover:shadow-md transition-shadow border border-gray-200 focus-within:border-indigo-300 focus-within:ring-2 focus-within:ring-indigo-100 focus-within:bg-white">
                    <input 
                        wire:model="userMessage" 
                        type="text" 
                        placeholder="Escribe un mensaje..." 
                        class="w-full bg-transparent border-0 rounded-full py-3.5 pl-6 pr-14 text-gray-800 placeholder-gray-500 focus:ring-0"
                        :disabled="$wire.isTyping"
                        autofocus
                    >
                    <button 
                        type="submit" 
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition-transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center w-9 h-9"
                        :disabled="$wire.isTyping || !$wire.userMessage"
                    >
                        <svg x-show="!$wire.isTyping" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                         <div x-show="$wire.isTyping" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" style="display: none;"></div>
                    </button>
                 </form>
                 <div class="text-center mt-2">
                     <p class="text-[10px] text-gray-400">Obertrack AI puede mostrar información imprecisa.</p>
                 </div>
             </div>
        </div>

    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('livewire:initialized', () => {
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
 
             Livewire.on('message-added', () => {
                 setTimeout(scrollToBottom, 50);
             });
             
             // Streaming Logic
             Livewire.on('start-streaming', (event) => {
                const conversationId = event.conversationId;
                const streamUrl = `{{ route('ai.stream') }}?conversation_id=${conversationId}&message=context`;
                
                // Create a temporary placeholder for the AI response
                // We directly manipulate the DOM for speed, then sync with Livewire at the end
                // Actually, let's append a bubble to the UI using Alpine/DOM first
                
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
                                    <span class="animate-pulse">Thinking...</span>
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

                // Use fetch with a reader for better control than EventSource (optional, but ES is easier for text/event-stream)
                eventSource = new EventSource(streamUrl);

                eventSource.onopen = function() {
                    console.log("Connection established with AI Stream.");
                };

                eventSource.onmessage = function(e) {
                    if (e.data === "[DONE]") {
                        eventSource.close();
                        @this.saveAiResponse(fullContent);
                        aiMsgDiv.remove(); 
                        return;
                    }

                    try {
                        const data = JSON.parse(e.data);
                        // Check for errors from backend
                        if (data.error) {
                            console.error("Backend Error:", data.error);
                            eventSource.close();
                            contentArea.innerText = "Error: " + data.error;
                            @this.saveAiResponse("Error: " + data.error);
                            aiMsgDiv.remove();
                            return;
                        }

                        if (typeof data.content !== 'undefined') {
                            if (isFirstChunk && data.content.length > 0) {
                                contentArea.innerHTML = ''; // Remove "Thinking..."
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
                    // Only close if readyState is CLOSED (2). If CONNECTING (0), it might be retrying.
                    if (eventSource.readyState === 2) {
                        eventSource.close();
                        if (!fullContent) {
                             contentArea.innerText = "Error de conexión/timeout. Intente de nuevo.";
                             @this.saveAiResponse("Error de conexión (Timeout).");
                             aiMsgDiv.remove();
                        } else {
                            @this.saveAiResponse(fullContent);
                            aiMsgDiv.remove();
                        }
                    }
                };
             });
             
            const observer = new MutationObserver(() => {
                // container.scrollTop = container.scrollHeight; // removed auto-scroll on every mutation to avoid annoying user if scrolling up
            });
            
            if(container) {
                observer.observe(container, { childList: true, subtree: true, characterData: true });
                scrollToBottom();
            }
        });
    </script>
</div>
