<div class="relative" x-data="{ open: @entangle('isOpen') }">
    <button @click="open = !open" @click.away="open = false" type="button" 
        class="flex items-center justify-between w-40 px-4 py-2 text-sm font-medium text-white rounded-full focus:outline-none transition-colors duration-200
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
        
        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 z-[100] w-40 mt-2 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 p-1 space-y-1">
        
        <button wire:click="updateStatus('{{ \App\Models\Task::STATUS_COMPLETED }}')" 
            class="w-full px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-full hover:bg-green-600 transition-colors flex justify-center">
            Finalizado
        </button>
        
        <button wire:click="updateStatus('{{ \App\Models\Task::STATUS_IN_PROGRESS }}')" 
            class="w-full px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-full hover:bg-yellow-600 transition-colors flex justify-center">
            En proceso
        </button>
        
        <button wire:click="updateStatus('{{ \App\Models\Task::STATUS_TODO }}')" 
            class="w-full px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-full hover:bg-red-600 transition-colors flex justify-center">
            Por hacer
        </button>
    </div>
</div>
