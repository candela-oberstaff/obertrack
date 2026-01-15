<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center max-w-7xl mx-auto px-4 sm:px-0">
             <h2 class="font-bold text-2xl md:text-3xl text-gray-800 dark:text-gray-800 leading-tight">
                {{ __('Seguimiento de tareas') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-white min-h-screen" x-data="{
        startDate: '',
        endDate: '',
        searchQuery: '',
        matches(task) {
            const taskDate = task.date;
            const taskTitle = task.title.toLowerCase();
            const q = this.searchQuery.toLowerCase();

            if (this.searchQuery && !taskTitle.includes(q)) return false;
            if (!this.startDate && !this.endDate) return true;
            if (this.startDate && taskDate < this.startDate) return false;
            if (this.endDate && taskDate > this.endDate) return false;
            return true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            {{-- Date Filters --}}
            <section class="px-2 sm:px-0 flex flex-col sm:flex-row items-center gap-4">
                <div class="flex items-center gap-2 bg-gray-50 rounded-2xl border border-gray-200 p-2 shadow-sm w-full sm:w-auto">
                    <span class="text-xs font-bold text-gray-500 ml-2 uppercase">Filtrar por fecha:</span>
                    <input type="date" x-model="startDate" class="border-none bg-transparent focus:ring-0 text-sm p-1 rounded-lg w-full sm:w-auto" placeholder="Desde">
                    <span class="text-gray-400">/</span>
                    <input type="date" x-model="endDate" class="border-none bg-transparent focus:ring-0 text-sm p-1 rounded-lg w-full sm:w-auto" placeholder="Hasta">
                </div>

                {{-- Search Box --}}
                <div class="relative w-full sm:w-64">
                    <input type="text" x-model="searchQuery" placeholder="Buscar tarea..." class="w-full pl-10 pr-4 py-2 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary text-sm bg-gray-50">
                    <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <button @click="startDate = ''; endDate = ''; searchQuery = ''" class="text-xs text-gray-400 hover:text-primary transition-colors font-medium" x-show="startDate || endDate || searchQuery">
                    Limpiar filtros
                </button>
            </section>

            {{-- Vistazo General Cards --}}
            <section class="px-2 sm:px-0">
                <h3 class="text-primary font-medium text-base md:text-lg mb-4">Vistazo general</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
                    {{-- Pending Tasks Card --}}
                    <div class="bg-gray-100 rounded-3xl p-6 md:p-8 flex justify-between items-center shadow-none">
                        <div>
                            <p class="text-gray-800 font-bold mb-2 text-sm md:text-base">Tareas pendientes</p>
                            <p class="text-4xl md:text-6xl font-extrabold text-black">{{ str_pad($pendingTasksCount, 2, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="text-right text-[10px] md:text-sm text-gray-500">

                            <p>Grupales: {{ $teamTasks->where('completed', false)->count() }}</p>
                        </div>
                    </div>

                    {{-- Completed Tasks Card --}}
                    <div class="bg-gray-100 rounded-3xl p-6 md:p-8 flex justify-between items-center shadow-none">
                        <div>
                            <p class="text-gray-800 font-bold mb-2 text-sm md:text-base">Tareas completadas con éxito</p>
                            <div class="flex items-center">
                                <p class="text-4xl md:text-6xl font-extrabold text-black">{{ str_pad($completedTasksCount, 2, '0', STR_PAD_LEFT) }}</p>
                                <div class="ml-4 bg-green-500 rounded-full p-1.5 md:p-2 text-white">
                                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Team Assignments Table --}}
            <section class="px-2 sm:px-0">
                <h3 class="text-primary font-medium text-base md:text-lg mb-4">Mis asignaciones</h3>
                <div class="bg-white rounded-3xl border-2 border-primary p-2 md:p-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-black text-sm font-bold">
                                    <th class="p-4 pl-6">Título</th>
                                    <th class="p-4">Fecha límite</th>
                                    <th class="p-4">Asignado</th>
                                    <th class="p-4">Estado</th>
                                    <th class="p-4 text-center">Comentarios</th>
                                    <th class="p-4 text-center">Archivos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamTasks as $task)
                                    <tr class="bg-gray-50 hover:bg-gray-100 transition group rounded-lg" 
                                        x-show="matches({ date: '{{ $task->end_date->format('Y-m-d') }}', title: '{{ addslashes($task->title) }}' })">
                                        <td class="p-4 pl-6 font-medium text-gray-800 rounded-l-lg">
                                            {{ $task->title }}
                                            <div class="text-xs text-gray-500 font-normal mt-1">{{ Str::limit($task->description, 50) }}</div>
                                        </td>
                                        <td class="p-4">
                                            <span class="{{ \Carbon\Carbon::parse($task->end_date)->isPast() && !$task->completed ? 'text-red-500' : 'text-gray-600' }}">
                                                {{ \Carbon\Carbon::parse($task->end_date)->format('d-m-Y') }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex -space-x-2 overflow-hidden">
                                                @foreach($task->assignees->take(3) as $assignee)
                                                     <x-user-avatar :user="$assignee" size="8" class="ring-2 ring-white -ml-2 first:ml-0" title="{{ $assignee->name }}" />
                                                @endforeach
                                                @if($task->assignees->count() > 3)
                                                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 text-xs font-bold ring-2 ring-white">
                                                        +{{ $task->assignees->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <livewire:task-status-selector :task="$task" :wire:key="'task-status-'.$task->id" />
                                        </td>
                                        <td class="p-4 text-center">
                                            <button @click="Livewire.dispatch('open-task-comments', { taskId: '{{ $task->id }}' })" class="text-gray-500 hover:text-primary transition flex items-center justify-center mx-auto space-x-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                                <span>{{ $task->comments->count() }}</span>
                                            </button>
                                        </td>
                                        <td class="p-4 text-center rounded-r-lg">
                                            <button @click="Livewire.dispatch('open-task-files', { taskId: '{{ $task->id }}' })" class="text-gray-500 hover:text-primary transition flex items-center justify-center mx-auto space-x-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span>{{ $task->attachments->count() }}</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-6 text-center text-gray-500">No tienes asignaciones en equipo.</td>
                                    </tr>
                                @endforelse
                                <!-- Empty state when filtered -->
                                <tr x-show="[...$el.parentElement.children].filter(c => c.tagName === 'TR' && c.style.display !== 'none').length === 0" style="display: none;">
                                    <td colspan="6" class="p-6 text-center text-gray-500 italic">No se encontraron tareas en este rango de fechas.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>


        </div>
    </div>

    {{-- Livewire Modals --}}
    <livewire:task-comments-modal />
    <livewire:task-files-modal />

</x-app-layout>