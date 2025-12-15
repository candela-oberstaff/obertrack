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
         class="absolute z-50 w-40 mt-2 bg-white dark:bg-gray-700 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-600 focus:outline-none">
        
        <div class="py-1">
            <button wire:click="updateStatus('{{ \App\Models\Task::STATUS_COMPLETED }}')" 
                class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-green-50 dark:hover:bg-green-900/[0.2] hover:text-green-700 dark:hover:text-green-400">
                <span class="w-2 h-2 mr-2 bg-green-500 rounded-full"></span>
                Finalizado
            </button>
            
            <button wire:click="updateStatus('{{ \App\Models\Task::STATUS_IN_PROGRESS }}')" 
                class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-yellow-50 dark:hover:bg-yellow-900/[0.2] hover:text-yellow-700 dark:hover:text-yellow-400">
                <span class="w-2 h-2 mr-2 bg-yellow-500 rounded-full"></span>
                En proceso
            </button>
            
            <button wire:click="updateStatus('{{ \App\Models\Task::STATUS_TODO }}')" 
                class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-red-900/[0.2] hover:text-red-700 dark:hover:text-red-400">
                <span class="w-2 h-2 mr-2 bg-red-500 rounded-full"></span>
                Por hacer
            </button>
        </div>
    </div>
</div>
