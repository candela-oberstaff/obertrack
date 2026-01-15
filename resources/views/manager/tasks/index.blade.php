<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tareas asignadas a mi equipo') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        startDate: '',
        endDate: '',
        searchQuery: '',
        allTasks: @json($tareas),
        get filteredTasks() {
            let tasks = [...this.allTasks];
            if (this.startDate || this.endDate) {
                tasks = tasks.filter(t => {
                    const date = t.end_date.split('T')[0];
                    if (this.startDate && date < this.startDate) return false;
                    if (this.endDate && date > this.endDate) return false;
                    return true;
                });
            }
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                tasks = tasks.filter(t => {
                    const matchTitle = t.title.toLowerCase().includes(q);
                    const matchAssignee = t.assignees.some(a => 
                        a.name.toLowerCase().includes(q) || 
                        (a.email && a.email.toLowerCase().includes(q))
                    );
                    return matchTitle || matchAssignee;
                });
            }
            return tasks;
        },
        formatDate(d) {
            if (!d) return '--/--/----';
            const parts = d.split('T')[0].split('-');
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                        <div class="w-full lg:w-1/3 relative">
                            <input type="text" x-model="searchQuery" placeholder="Buscar por tarea o profesional (nombre/email)..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 p-1 rounded-lg border border-gray-200 dark:border-gray-600 w-full lg:w-auto">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-2 uppercase">Fecha:</span>
                            <input type="date" x-model="startDate" class="border-none bg-transparent focus:ring-0 text-sm p-1 rounded-lg dark:text-white">
                            <span class="text-gray-400">/</span>
                            <input type="date" x-model="endDate" class="border-none bg-transparent focus:ring-0 text-sm p-1 rounded-lg dark:text-white">
                        </div>

                        <button 
                            x-show="startDate || endDate || searchQuery" 
                            @click="startDate = ''; endDate = ''; searchQuery = ''"
                            class="text-xs text-gray-400 hover:text-primary font-medium transition-colors"
                        >
                            Limpiar
                        </button>

                        <a href="{{ route('manager.tasks.create') }}" class="w-full lg:w-auto text-center inline-flex items-center justify-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-hover active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Crear Nueva Tarea
                        </a>
                    </div>
                </div>
            </div>

            <template x-for="tarea in filteredTasks" :key="tarea.id">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 transition duration-300 ease-in-out hover:shadow-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200" x-text="tarea.title"></h3>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-800': tarea.priority === 'low',
                                        'bg-yellow-100 text-yellow-800': tarea.priority === 'medium',
                                        'bg-orange-100 text-orange-800': tarea.priority === 'high',
                                        'bg-red-100 text-red-800': tarea.priority === 'urgent'
                                    }" x-text="tarea.priority.charAt(0).toUpperCase() + tarea.priority.slice(1)">
                                </span>
                                <button class="text-primary hover:text-blue-800 focus:outline-none flex items-center" @click="$el.closest('.p-6').querySelector('.task-details').classList.toggle('hidden'); $el.querySelector('svg').classList.toggle('rotate-180')">
                                    <span class="mr-2 text-sm">Detalles</span>
                                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div class="task-details hidden">
                            <div class="mb-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-gray-600 dark:text-gray-400" x-text="tarea.description"></p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Asignado a</p>
                                    <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
                                        <template x-for="(a, index) in tarea.assignees" :key="a.id">
                                            <span>
                                                <span x-text="a.name"></span><span x-if="index < tarea.assignees.length - 1">, </span>
                                            </span>
                                        </template>
                                        <template x-if="tarea.assignees.length === 0">
                                            <span class="text-gray-400 italic">Sin asignar</span>
                                        </template>
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fechas</p>
                                    <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
                                        <span x-text="formatDate(tarea.start_date)"></span> - <span x-text="formatDate(tarea.end_date)"></span>
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</p>
                                    <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100" x-text="tarea.completed ? 'Completada' : 'Pendiente'"></p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex justify-between items-center mb-4">
                                     <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Comentarios</h4>
                                     <a :href="'/manager/tasks/' + tarea.id + '/edit'" class="text-sm text-primary hover:underline">Gestionar Tarea</a>
                                </div>
                                <div class="comments-container space-y-4">
                                    <template x-for="comment in tarea.comments" :key="comment.id">
                                        <div class="comment bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-600">
                                            <p class="text-gray-800 dark:text-gray-200 mb-2" x-text="comment.content"></p>
                                            <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                                                <span x-text="comment.user.name + ' - ' + new Date(comment.created_at).toLocaleString()"></span>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!tarea.comments || tarea.comments.length === 0">
                                        <p class="text-sm text-gray-500 italic">No hay comentarios aún.</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="filteredTasks.length === 0" class="text-center py-12 text-gray-500">
                No se encontraron tareas con estos criterios.
            </div>
        </div>
    </div>
</x-app-layout>