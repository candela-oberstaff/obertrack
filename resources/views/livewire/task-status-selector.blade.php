<div class="relative" x-data="{ dropdownOpen: @entangle('isOpen') }" @click.away="dropdownOpen = false">
    <button @click="dropdownOpen = !dropdownOpen" type="button" 
        class="flex items-center justify-between w-40 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white rounded-full focus:outline-none transition-all duration-300 shadow-sm
        @if($status === \App\Models\Task::STATUS_COMPLETED) bg-green-500 hover:bg-green-600
        @elseif($status === \App\Models\Task::STATUS_IN_PROGRESS) bg-yellow-500 hover:bg-yellow-600
        @else bg-red-500 hover:bg-red-600 @endif">
        
        <span>
            @if($status === \App\Models\Task::STATUS_COMPLETED)
                Finalizado
            @elseif($status === \App\Models\Task::STATUS_IN_PROGRESS)
                En proceso
            @else
                Por hacer
            @endif
        </span>
        
        <svg class="w-3 h-3 ml-2 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="dropdownOpen" 
         x-cloak
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
         class="absolute right-0 z-[100] w-48 mt-2 bg-white rounded-3xl shadow-2xl ring-1 ring-black/5 p-2 space-y-1 overflow-hidden">
        
        <div class="px-3 py-2 border-b border-gray-50 mb-1">
            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Cambiar estado</span>
        </div>

        <button type="button" wire:click="updateStatus('{{ \App\Models\Task::STATUS_COMPLETED }}')" 
            class="w-full px-4 py-2.5 text-[9px] font-bold text-green-600 bg-green-50 rounded-2xl hover:bg-green-100 transition-colors flex items-center gap-3 uppercase tracking-widest group">
            <span class="w-2 h-2 rounded-full bg-green-500 group-hover:scale-125 transition-transform"></span>
            Finalizado
        </button>
        
        <button type="button" wire:click="updateStatus('{{ \App\Models\Task::STATUS_IN_PROGRESS }}')" 
            class="w-full px-4 py-2.5 text-[9px] font-bold text-yellow-600 bg-yellow-50 rounded-2xl hover:bg-yellow-100 transition-colors flex items-center gap-3 uppercase tracking-widest group">
            <span class="w-2 h-2 rounded-full bg-yellow-500 group-hover:scale-125 transition-transform"></span>
            En proceso
        </button>
        
        <button type="button" wire:click="updateStatus('{{ \App\Models\Task::STATUS_TODO }}')" 
            class="w-full px-4 py-2.5 text-[9px] font-bold text-red-600 bg-red-50 rounded-2xl hover:bg-red-100 transition-colors flex items-center gap-3 uppercase tracking-widest group">
            <span class="w-2 h-2 rounded-full bg-red-500 group-hover:scale-125 transition-transform"></span>
            Por hacer
        </button>
    </div>
</div>
