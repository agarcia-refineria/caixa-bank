import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/select2.css',
                'resources/css/datatable.css',
                'resources/css/shepherd.css',

                'resources/css/fonts.css',

                'resources/js/app.js',
                'resources/js/institutions.js'
            ],
            refresh: true,
        }),
    ],
});
