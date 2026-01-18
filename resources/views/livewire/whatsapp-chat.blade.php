<div class="flex h-[calc(100vh-4rem)] bg-white overflow-hidden relative" 
     x-data="{ 
        mobileView: false,
        isUploading: false,
        scrollToBottom() {
            const container = document.getElementById('wa-messages-container');
            if (container) container.scrollTop = container.scrollHeight;
        }
    }"
    x-init="$watch('$wire.messages', () => { setTimeout(scrollToBottom, 100); })"
>
    <!-- SATE: CONNECT / QR -->
    @if($sessionStatus !== 'WORKING')
    <div class="w-full h-full flex flex-col items-center justify-center p-8 text-center bg-gray-50" wire:poll.2s="checkSessionStatus">
        <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <div class="w-20 h-20 bg-[#25D366]/10 rounded-full flex items-center justify-center mx-auto mb-6 text-[#25D366]">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.374-5.03c0-5.445 4.429-9.876 9.88-9.876 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.444-4.432 9.874-9.877 9.874m0-19.896C6.276 1.889 2.058 6.136 2.058 11.64c0 1.74.453 3.407 1.31 4.887l-1.398 5.107 5.253-1.378c1.423.774 3.032 1.183 4.673 1.183h.005c5.351 0 9.697-4.329 9.697-9.673 0-2.583-1.008-5.013-2.837-6.842C17.07 3.064 14.64 2.06 12.05 2.06z"/></svg>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Conectar WhatsApp</h2>
            <p class="text-gray-500 mb-8">Escanea el código QR para vincular tu cuenta de WhatsApp.</p>

            @error('session')
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                    {{ $message }}
                </div>
            @enderror

            @if($sessionStatus === 'STOPPED')
                <button wire:click="startSession" wire:loading.attr="disabled" class="w-full py-3 px-6 bg-[#25D366] hover:bg-[#1DA851] text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <span wire:loading.remove wire:target="startSession">Generar Código QR</span>
                    <span wire:loading wire:target="startSession" class="loading loading-spinner loading-sm"></span>
                </button>
            @elseif($sessionStatus === 'STARTING')
                 <div class="flex flex-col items-center">
                    <span class="loading loading-spinner loading-lg text-[#25D366] mb-4"></span>
                    <p class="text-sm font-medium text-gray-400">Iniciando sesión...</p>
                 </div>
            @elseif($sessionStatus === 'SCAN_QR_CODE')
                <div class="flex flex-col items-center animate-in fade-in zoom-in">
                    @if($qrCodeBase64)
                        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm mb-4">
                            <img src="{{ $qrCodeBase64 }}" class="w-48 h-48 object-contain" alt="Scan QR Code">
                        </div>
                        <p class="text-xs text-center text-gray-400 max-w-xs">Abre WhatsApp en tu teléfono, ve a Dispositivos Vinculados y escanea este código.</p>
                    @else
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                            <p class="text-sm text-yellow-800">⚠️ Esperando código QR...</p>
                            <p class="text-xs text-yellow-600 mt-1">Estado: {{ $sessionStatus }}</p>
                        </div>
                        <button wire:click="checkSessionStatus" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm">
                            Recargar QR
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @else
    <!-- STATE: WORKING (CHAT INTERFACE) -->
    
    <!-- Sidebar -->
    <div class="w-full md:w-1/3 lg:w-1/4 flex flex-col border-r border-gray-100 transition-transform duration-300 ease-in-out"
         :class="mobileView ? 'hidden md:flex' : 'flex'">
        
        <!-- Header -->
        <div class="p-4 bg-white border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">WhatsApp</h2>
            <button wire:click="logout" class="text-xs text-red-500 hover:text-red-700 font-medium px-2 py-1 rounded bg-red-50 hover:bg-red-100 transition-colors">
                Desconectar
            </button>
        </div>

        <!-- Contacts List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            @forelse($contacts as $contact)
                <button 
                    wire:click="selectContact({{ $contact->id }})"
                    @click="mobileView = true"
                    class="w-full p-4 hover:bg-gray-50 transition-all duration-200 text-left flex items-center gap-3 border-b border-gray-50 last:border-0 {{ $selectedContactId === $contact->id ? 'bg-[#25D366]/5' : '' }}"
                >
                    <div class="relative">
                        <x-user-avatar :user="$contact" size="12" class="ring-2 ring-white shadow-sm" />
                         <!-- WhatsApp Icon overlay on avatar -->
                         <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-0.5">
                            <svg class="w-4 h-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.374-5.03c0-5.445 4.429-9.876 9.88-9.876 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.444-4.432 9.874-9.877 9.874m0-19.896C6.276 1.889 2.058 6.136 2.058 11.64c0 1.74.453 3.407 1.31 4.887l-1.398 5.107 5.253-1.378c1.423.774 3.032 1.183 4.673 1.183h.005c5.351 0 9.697-4.329 9.697-9.673 0-2.583-1.008-5.013-2.837-6.842C17.07 3.064 14.64 2.06 12.05 2.06z"/></svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-0.5">
                            <p class="font-semibold text-gray-900 truncate {{ $selectedContactId === $contact->id ? 'text-[#25D366]' : '' }}">
                                {{ $contact->name }}
                            </p>
                        </div>
                        <p class="text-sm text-gray-500 truncate">
                            {{ $contact->job_title ?? 'Sin título' }}
                        </p>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-gray-400">
                    <p>No se encontraron contactos con número de teléfono registrado.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col bg-[#efeae2] relative"
         :class="mobileView ? 'flex fixed inset-0 z-50 md:static md:z-auto' : 'hidden md:flex'"
         style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); opacity: 0.9;">
        
        @if($selectedContactId)
            @php $contact = $contacts->firstWhere('id', $selectedContactId); @endphp
            <!-- Header -->
            <div class="px-4 py-3 bg-white border-b border-gray-200 flex items-center gap-3">
                <button @click="mobileView = false" class="md:hidden p-2 -ml-2 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <div class="relative">
                    <x-user-avatar :user="$contact" size="10" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 leading-tight">{{ $contact->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $selectedPhone }}</p>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 overflow-y-auto p-4 space-y-2 opacity-100" id="wa-messages-container" wire:poll.3s="loadMessages">
                @foreach($messages as $msg)
                    <div class="flex flex-col {{ ($msg['fromMe'] ?? false) ? 'items-end' : 'items-start' }} animate-in fade-in zoom-in-95 duration-200">
                        <div class="max-w-[75%] px-3 py-1.5 rounded-lg shadow-sm text-sm relative px-2
                                    {{ ($msg['fromMe'] ?? false) ? 'bg-[#d9fdd3] text-gray-900 rounded-tr-none' : 'bg-white text-gray-900 rounded-tl-none' }}">
                            
                            @if(isset($msg['body']) && $msg['body'])
                                <p class="whitespace-pre-wrap break-words pr-12 pb-2">{{ $msg['body'] }}</p>
                            @endif

                             <!-- Media handling simplified -->
                            @if(isset($msg['hasMedia']) && $msg['hasMedia'])
                                <div class="mb-1 p-1 bg-gray-100 rounded text-xs text-gray-500 italic flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Multimedia (No soportado en vista previa)
                                </div>
                            @endif

                            <span class="text-[10px] text-gray-500 absolute bottom-1 right-2 inline-flex items-center gap-0.5">
                                {{ \Carbon\Carbon::createFromTimestamp($msg['timestamp'] ?? time())->format('H:i') }}
                                @if(($msg['fromMe'] ?? false))
                                    @if(isset($msg['ack']) && $msg['ack'] >= 2)
                                         <svg class="w-3 h-3 text-blue-500" viewBox="0 0 16 15" width="16" height="15" fill="currentColor"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.283a.419.419 0 0 0 .532.054l6.105-7.61a.418.418 0 0 0-.064-.554l-.477-.372zM9.5 3.3l-.48-.375a.365.365 0 0 0-.51.063L3.155 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.283a.419.419 0 0 0 .532.054l6.105-7.61a.418.418 0 0 0-.064-.555L9.5 3.3z"/></svg>
                                    @else
                                        <span class="text-gray-400">✓</span>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Input Area -->
            <div class="p-3 bg-white/90 backdrop-blur-sm">
                <form wire:submit.prevent="sendMessage" class="flex items-end gap-2">
                    <div class="flex-1 bg-white border border-gray-200 rounded-2xl flex items-center px-4 py-2 focus-within:ring-2 focus-within:ring-[#25D366]/50">
                        <input type="text" wire:model="messageText" placeholder="Escribe un mensaje..." class="flex-1 bg-transparent border-none focus:ring-0 text-sm">
                    </div>
                    <button type="submit" class="p-3 rounded-full bg-[#25D366] hover:bg-[#1DA851] text-white shadow-md transition-all flex items-center justify-center">
                        <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>
        @else
            <!-- Placeholder -->
            <div class="hidden md:flex flex-col items-center justify-center h-full">
                 <div class="w-32 h-32 bg-[#25D366]/10 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-16 h-16 text-[#25D366]/40" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.374-5.03c0-5.445 4.429-9.876 9.88-9.876 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.444-4.432 9.874-9.877 9.874m0-19.896C6.276 1.889 2.058 6.136 2.058 11.64c0 1.74.453 3.407 1.31 4.887l-1.398 5.107 5.253-1.378c1.423.774 3.032 1.183 4.673 1.183h.005c5.351 0 9.697-4.329 9.697-9.673 0-2.583-1.008-5.013-2.837-6.842C17.07 3.064 14.64 2.06 12.05 2.06z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">OberTrack WhatsApp</h3>
                <p class="text-gray-500">Selecciona un contacto para chatear</p>
            </div>
        @endif
    </div>
    @endif
</div>
