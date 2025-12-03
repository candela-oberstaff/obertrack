import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/work-hours-approval.js',
                'resources/js/report-download.js',
                'resources/js/task-management.js',
            ],
            refresh: true,
        }),
    ],
});
