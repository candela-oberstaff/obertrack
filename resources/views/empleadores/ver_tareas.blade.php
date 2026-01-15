<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Seguimiento de Tareas') }}
        </h2>
    </x-slot>

    <!-- CONTENEDOR PRINCIPAL CON ESTADO DEL MODAL -->
    <div x-data="{ showIndividualTaskModal: false }">

        <div class="">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

                <!-- ======================================== -->
                <!--               MODAL FORM                 -->
                <!-- ======================================== -->
                <div 
                    x-show="showIndividualTaskModal"
                    x-transition.opacity
                    @open-individual-task-modal.window="showIndividualTaskModal = true"
                    @keydown.escape.window="showIndividualTaskModal = false"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                    style="display:none;"
                >
                    <div class="bg-white w-full max-w-3xl rounded-xl shadow-lg relative">

                        <!-- Botón cerrar -->
                        <button 
                            @click="showIndividualTaskModal = false"
                            class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl"
                        >
                            ✕
                        </button>

                        <!-- Aquí incluimos el formulario TAL CUAL -->
                        <div class="p-6 max-h-[90vh] overflow-y-auto">
                            <x-tasks.create-task-form :empleados="$empleados" />
                        </div>
                    </div>
                </div>

                <!-- ======================================== -->
                <!--              LISTA DE TAREAS             -->
                <!-- ======================================== -->
                <x-tasks.task-list :tareasEmpleador="$tareasEmpleador" />

            </div>
        </div>

    </div>

    @vite(['resources/js/task-management.js'])
    <x-layout.footer />
</x-app-layout>
