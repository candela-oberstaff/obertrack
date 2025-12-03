\u003cx-app-layout\u003e
    \u003cx-slot name="header"\u003e
        \u003ch2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"\u003e
            {{ __('Panel de Control de Empleadores') }}
        \u003c/h2\u003e
    \u003c/x-slot\u003e

    \u003cdiv class="py-12"\u003e
        \u003cdiv class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10"\u003e
            
            \u003c!-- Work Hours Management Section --\u003e
            \u003cdiv x-data="{ activeTab: 'pending' }" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"\u003e
                \u003cdiv class="border-b border-gray-200 dark:border-gray-700"\u003e
                    \u003cnav class="-mb-px flex" aria-label="Tabs"\u003e
                        \u003cbutton 
                            @click="activeTab = 'pending'" 
                            :class="{'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'pending', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'pending'}" 
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200"\u003e
                            Semanas Pendientes
                        \u003c/button\u003e
                        \u003cbutton 
                            @click="activeTab = 'current'" 
                            :class="{'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'current', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'current'}" 
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200"\u003e
                            Semana Actual
                        \u003c/button\u003e
                    \u003c/nav\u003e
                \u003c/div\u003e

                \u003cdiv x-show="activeTab === 'pending'" class="p-6"\u003e
                    \u003cx-work-hours.pending-weeks-section :pendingWeeks="$pendingWeeks" /\u003e
                \u003c/div\u003e

                \u003cdiv x-show="activeTab === 'current'" class="p-6"\u003e
                    \u003cx-work-hours.current-week-summary :workHoursSummary="$workHoursSummary" :weekStart="$weekStart" /\u003e
                \u003c/div\u003e
            \u003c/div\u003e

            \u003c!-- Monthly Report Summary --\u003e
            \u003cx-reports.monthly-summary 
                :empleadosInfo="$empleadosInfo" 
                :currentMonth="$currentMonth" 
                :totalApprovedHours="$totalApprovedHours" 
            /\u003e

            \u003c!-- Task Creation and Filtering --\u003e
            \u003cx-tasks.create-task-form :empleados="$empleados" /\u003e

            \u003c!-- Task List --\u003e
            \u003cx-tasks.task-list :tareasEmpleador="$tareasEmpleador" /\u003e

        \u003c/div\u003e
    \u003c/div\u003e

    \u003c!-- Approval Modal --\u003e
    \u003cx-work-hours.approval-modal /\u003e

    \u003c!-- Scripts --\u003e
    \u003cscript\u003e
        window.routes = {
            approveWeekWithComment: "{{ route('work-hours.approve-week-with-comment') }}",
            downloadMonthlyReport: "{{ route('work-hours.download-monthly-report', ['month' => '__MONTH__']) }}"
        };
    \u003c/script\u003e

    @vite([
        'resources/js/work-hours-approval.js', 
        'resources/js/report-download.js', 
        'resources/js/task-management.js'
    ])
\u003c/x-app-layout\u003e