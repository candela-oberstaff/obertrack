<div 
    class="flex h-[calc(100vh-4rem)] bg-white overflow-hidden" 
    wire:poll.3s="refreshMessages"
    x-data="{ 
        mobileView: false,
        visiblySelectedUser: @entangle('selectedUserId'),
        isUploading: false,
        uploadProgress: 0,
        tempAttachmentName: '',
        
        selectContactOptimistic(userId) {
            this.visiblySelectedUser = userId;
            this.mobileView = true;
            $wire.selectContact(userId);
            // Scroll to bottom after short delay to allow rendering
            setTimeout(() => this.scrollToBottom(), 300);
        },

        scrollToBottom() {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        closeMobileChat() {
            this.mobileView = false;
            this.visiblySelectedUser = null;
            $wire.set('selectedUserId', null);
        }
    }"
    x-init="
        $watch('visiblySelectedUser', value => {
            if (value) setTimeout(() => scrollToBottom(), 100);
        });
        
        Livewire.hook('morph.updated', () => {
             // Only scroll if we are already near bottom or it's a new message
             scrollToBottom();
        });
    "
>
    <!-- Contacts Sidebar -->
    <div 
        class="w-full md:w-1/3 lg:w-1/4 flex flex-col border-r border-gray-100 transition-transform duration-300 ease-in-out"
        :class="visiblySelectedUser ? 'hidden md:flex' : 'flex'"
    >
        <!-- Header -->
        <div class="p-4 bg-white border-b border-gray-100 flex justify-between items-center no-select">
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">Mensajes</h2>
            <!-- Connection Status Indicator (Simulated) -->
            <div class="flex items-center gap-1.5" title="Estado de conexión">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                </span>
                <span class="text-[10px] font-medium text-gray-400">En línea</span>
            </div>
        </div>

        <!-- Search (Visual only for now) -->
        <div class="p-4 pt-2">
            <div class="relative">
                <input type="text" placeholder="Buscar..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Contacts List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            @forelse($contacts as $contact)
                <button 
                    @click="selectContactOptimistic({{ $contact->id }})"
                    class="w-full p-4 hover:bg-gray-50 transition-all duration-200 text-left flex items-center gap-3 border-b border-gray-50 last:border-0 group"
                    :class="visiblySelectedUser == {{ $contact->id }} ? 'bg-primary/5' : ''"
                >
                    <div class="relative">
                        <x-user-avatar :user="$contact" size="12" class="ring-2 ring-white shadow-sm group-hover:scale-105 transition-transform" />
                        @if($contact->activeStatus()) 
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-0.5">
                            <p 
                                class="font-semibold text-gray-900 truncate transition-colors"
                                :class="visiblySelectedUser == {{ $contact->id }} ? 'text-primary' : ''"
                            >
                                {{ $contact->name }}
                            </p>
                            @if($contact->unread_messages_count > 0)
                                <span class="bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                                    {{ $contact->unread_messages_count }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 truncate group-hover:text-gray-600 font-{{ $contact->unread_messages_count > 0 ? 'bold text-gray-800' : 'normal' }}">
                            {{ $contact->job_title ?? 'Usuario' }}
                        </p>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-gray-400 flex flex-col items-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <p class="font-medium">No hay contactos</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Main Chat Area -->
    <div 
        class="flex-1 flex flex-col bg-[#F3F4F6] relative transition-all duration-300"
        :class="visiblySelectedUser ? 'flex fixed inset-0 z-50 md:static md:z-auto' : 'hidden md:flex'"
    >
        <template x-if="visiblySelectedUser">
            <div class="flex flex-col h-full w-full">
                <!-- Chat Header -->
                <div class="px-4 py-3 bg-white/90 backdrop-blur-md border-b border-gray-200 sticky top-0 z-10 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <button @click="closeMobileChat()" class="md:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        
                        <!-- Header Content (Reactive to Livewire) -->
                        @if($selectedUserId)
                            @php $selectedContact = $contacts->firstWhere('id', $selectedUserId); @endphp
                            <div class="flex items-center gap-3 animate-in fade-in duration-300">
                                <div class="relative">
                                    <x-user-avatar :user="$selectedContact" size="10" />
                                    @if($selectedContact->activeStatus())
                                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 leading-tight">{{ $selectedContact->name }}</h3>
                                    <div class="flex items-center gap-1.5">
                                        <div wire:loading.remove wire:target="selectContact">
                                            <p class="text-xs text-green-600 font-medium">En línea</p>
                                        </div>
                                        <div wire:loading wire:target="selectContact" class="flex items-center gap-1">
                                            <span class="loading loading-spinner loading-xs text-primary w-3 h-3"></span>
                                            <p class="text-xs text-primary font-medium">Conectando...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                             <!-- Optimistic Header Skeleton -->
                             <div class="flex items-center gap-3 animate-pulse">
                                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                                <div class="space-y-1">
                                    <div class="h-4 w-24 bg-gray-200 rounded"></div>
                                    <div class="h-3 w-16 bg-gray-200 rounded"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if($jitsiUrl)
                        <a 
                            href="{{ $jitsiUrl }}" 
                            target="_blank"
                            class="p-2.5 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-xl transition-all duration-300 flex items-center gap-2 font-medium"
                            title="Iniciar videollamada"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span class="hidden sm:inline">Videollamada</span>
                        </a>
                    @endif
                </div>

                <!-- Messages Stream -->
                <div class="flex-1 overflow-y-auto p-4 space-y-6 custom-scrollbar scroll-smooth relative" id="messages-container">
                    
                    <!-- Loading Overlay -->
                    <div wire:loading.flex wire:target="selectContact" class="absolute inset-0 bg-white/50 backdrop-blur-sm z-20 flex flex-col items-center justify-center">
                         <div class="w-12 h-12 border-4 border-primary/30 border-t-primary rounded-full animate-spin mb-3"></div>
                         <p class="text-primary font-medium animate-pulse">Cargando historial...</p>
                    </div>

                    @if($messages && count($messages) > 0)
                        @php 
                            $lastDate = null; 
                        @endphp
                        
                        @foreach($messages as $message)
                            @php
                                $msgDate = \Carbon\Carbon::parse($message->created_at);
                                $dateStr = $msgDate->isToday() ? 'Hoy' : ($msgDate->isYesterday() ? 'Ayer' : $msgDate->format('d/m/Y'));
                            @endphp

                            @if($lastDate !== $dateStr)
                                <div class="flex justify-center my-4">
                                    <span class="bg-gray-100 px-3 py-1 rounded-full text-[11px] font-bold text-gray-500 uppercase tracking-wide border border-gray-200">
                                        {{ $dateStr }}
                                    </span>
                                </div>
                                @php $lastDate = $dateStr; @endphp
                            @endif

                            <div class="flex flex-col {{ $message->from_user_id == auth()->id() ? 'items-end' : 'items-start' }} group animate-in fade-in slide-in-from-bottom-2 duration-300">
                                <div class="max-w-[85%] lg:max-w-[70%] relative">
                                    <div class="px-5 py-3 shadow-sm text-[15px] leading-relaxed 
                                        {{ $message->from_user_id == auth()->id() 
                                            ? 'bg-primary text-white rounded-2xl rounded-tr-none' 
                                            : 'bg-white text-gray-800 rounded-2xl rounded-tl-none border border-gray-100' 
                                        }}">
                                        
                                        @if($message->message)
                                            <p class="whitespace-pre-wrap break-words">{{ $message->message }}</p>
                                        @endif
                                        
                                        @if(isset($message->attachment_path) && $message->attachment_path)
                                            <div class="mt-3 -mx-2 mb-1">
                                                @php
                                                    $path = $message->attachment_path;
                                                    $isUrl = \Illuminate\Support\Str::startsWith($path, ['http://', 'https://']);
                                                    $url = $isUrl ? $path : Storage::url($path);
                                                    $isImg = \Illuminate\Support\Str::endsWith($path, ['.jpg', '.jpeg', '.png', '.gif', '.webp']);
                                                @endphp
                                                @if($isImg)
                                                    <div class="relative group/img cursor-pointer">
                                                        <img src="{{ $url }}" class="rounded-xl w-full max-h-60 object-cover hover:opacity-95 transition">
                                                        <a href="{{ $url }}" target="_blank" class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover/img:bg-black/20 transition-all opacity-0 group-hover/img:opacity-100 rounded-xl">
                                                            <svg class="w-8 h-8 text-white drop-shadow-lg transform scale-90 group-hover/img:scale-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        </a>
                                                    </div>
                                                @else
                                                    <a href="{{ $url }}" target="_blank" class="flex items-center gap-3 p-3 bg-black/5 rounded-xl hover:bg-black/10 transition group/file">
                                                        <div class="p-2 bg-white rounded-lg text-primary shadow-sm group-hover/file:scale-110 transition-transform">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="font-medium text-sm truncate opacity-90">Archivo adjunto</p>
                                                            <p class="text-xs opacity-70 font-medium">Clic para abrir</p>
                                                        </div>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1 mx-1 {{ $message->from_user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <span class="text-[10px] font-bold text-gray-300">
                                            {{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}
                                        </span>
                                        @if($message->from_user_id == auth()->id())
                                            @if($message->read_at)
                                                <svg class="w-3.5 h-3.5 text-blue-500" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.68593 10.5186L0.985934 6.81855L2.24793 5.55655L4.68793 7.99655L5.78993 6.89455L14.7179 0.778553L15.8239 2.39455L5.79593 9.26255L4.68593 10.5186ZM9.50793 7.21855L8.24593 5.95655L11.8399 2.36055L13.1019 3.62255L9.50793 7.21855Z" fill="currentColor"/></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 text-gray-300" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.68593 10.5186L0.985934 6.81855L2.24793 5.55655L4.68793 7.99655L5.78993 6.89455L14.7179 0.778553L15.8239 2.39455L5.79593 9.26255L4.68593 10.5186ZM9.50793 7.21855L8.24593 5.95655L11.8399 2.36055L13.1019 3.62255L9.50793 7.21855Z" fill="currentColor"/></svg>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Only show empty state if NOT loading -->
                        <div wire:loading.remove wire:target="selectContact" class="h-full flex flex-col items-center justify-center p-8 text-center animate-in fade-in zoom-in duration-500">
                            <div class="w-24 h-24 bg-white rounded-full shadow-sm flex items-center justify-center mb-6">
                                <span class="text-4xl">👋</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Comienza la conversación</h3>
                            @if(isset($selectedContact))
                                <p class="text-gray-500 max-w-xs">Saluda a {{ $selectedContact->name }} y comienza a colaborar.</p>
                            @else
                                <p class="text-gray-500 max-w-xs">Selecciona un contacto para comenzar.</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Input Area -->
                <div class="p-4 bg-white border-t border-gray-100">
                    <!-- Upload Progress Bar -->
                    <div x-show="isUploading" class="mb-3 animate-in slide-in-from-bottom-2">
                         <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-primary">Subiendo archivo...</span>
                            <span class="text-xs font-medium text-gray-500" x-text="uploadProgress + '%'"></span>
                         </div>
                         <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-primary h-2 rounded-full transition-all duration-300" :style="'width: ' + uploadProgress + '%'"></div>
                         </div>
                    </div>

                    @if($attachment)
                        <div class="mb-4 mx-2 p-3 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-between animate-in slide-in-from-bottom-2">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center border border-gray-100 overflow-hidden">
                                    @if($attachment->getMimeType() && str_starts_with($attachment->getMimeType(), 'image/'))
                                        <img src="{{ $attachment->temporaryUrl() }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 truncate max-w-[150px]">{{ $attachment->getClientOriginalName() }}</p>
                                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">{{ number_format($attachment->getSize() / 1024, 0) }} KB</p>
                                </div>
                            </div>
                            <button wire:click="removeAttachment" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endif

                    <form 
                        wire:submit.prevent="sendMessage" 
                        x-on:submit="$refs.input.value = ''; scrollToBottom();" 
                        class="flex items-end gap-2"
                    >
                        <input 
                            type="file" 
                            wire:model="attachment" 
                            id="file-upload" 
                            class="hidden" 
                            accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt"
                            x-on:livewire-upload-start="isUploading = true; uploadProgress = 0"
                            x-on:livewire-upload-finish="isUploading = false"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="uploadProgress = $event.detail.progress"
                        >
                        <label 
                            for="file-upload" 
                            class="p-3 text-gray-400 hover:text-primary hover:bg-gray-100 rounded-xl cursor-pointer transition-colors mb-1"
                            title="Adjuntar archivo"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </label>

                        <div class="flex-1 bg-gray-50 border border-gray-200 focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary rounded-2xl transition-all shadow-sm">
                            <input 
                                x-ref="input"
                                wire:model="messageText"
                                type="text" 
                                placeholder="Escribe un mensaje..." 
                                class="w-full px-4 py-3 bg-transparent border-none focus:ring-0 text-gray-800 placeholder-gray-400"
                            >
                        </div>

                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            wire:target="sendMessage, attachment"
                            class="p-3 bg-primary hover:bg-primary-hover text-white rounded-xl shadow-lg hover:shadow-xl shadow-primary/30 transition-all duration-300 transform hover:scale-105 active:scale-95 mb-1 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed group"
                        >
                            <svg wire:loading.remove wire:target="sendMessage, attachment" class="w-6 h-6 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            <span wire:loading wire:target="sendMessage, attachment" class="loading loading-spinner loading-xs text-white"></span>
                        </button>
                    </form>
                </div>
            </div>
        </template>

        <template x-if="!visiblySelectedUser">
             <!-- Desktop Placeholder State -->
            <div class="hidden md:flex flex-col items-center justify-center h-full bg-white/50">
                <div class="w-32 h-32 bg-primary/5 rounded-full flex items-center justify-center mb-6 animate-pulse">
                    <svg class="w-16 h-16 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">OberTrack Chat</h3>
                <p class="text-gray-500 text-lg">Selecciona un contacto para comenzar</p>
            </div>
        </template>
    </div>
</div>
