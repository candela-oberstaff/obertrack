<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Control de Empleadores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <!-- Work Hours Management Section -->
            <div x-data="{ activeTab: 'pending' }" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        <button 
                            @click="activeTab = 'pending'" 
                            :class="{'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'pending', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'pending'}" 
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                            Semanas Pendientes
                        </button>
                        <button 
                            @click="activeTab = 'current'" 
                            :class="{'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'current', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'current'}" 
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                            Semana Actual
                        </button>
                    </nav>
                </div>

                <div x-show="activeTab === 'pending'" class="p-6">
                    <x-work-hours.pending-weeks-section :pendingWeeks="$pendingWeeks" />
                </div>

                <div x-show="activeTab === 'current'" class="p-6">
                    <x-work-hours.current-week-summary :workHoursSummary="$workHoursSummary" :weekStart="$weekStart" />
                </div>
            </div>

            <!-- Monthly Report Summary -->
            <x-reports.monthly-summary 
                :empleadosInfo="$empleadosInfo" 
                :currentMonth="$currentMonth" 
                :totalApprovedHours="$totalApprovedHours" 
            />

            <!-- Task Creation and Filtering -->
            <x-tasks.create-task-form :empleados="$empleados" />

            <!-- Task List -->
            <x-tasks.task-list :tareasEmpleador="$tareasEmpleador" />

        </div>
    </div>

    <!-- Approval Modal -->
    <x-work-hours.approval-modal />

    <!-- Scripts -->
    <script>
        window.routes = {
            approveWeekWithComment: "{{ route('work-hours.approve-week-with-comment') }}",
            downloadMonthlyReport: "{{ route('work-hours.download-monthly-report', ['month' => '__MONTH__']) }}"
        };
    </script>

    @vite([
        'resources/js/work-hours-approval.js', 
        'resources/js/report-download.js', 
        'resources/js/task-management.js'
    ])
    <x-layout.footer />
</x-app-layout>