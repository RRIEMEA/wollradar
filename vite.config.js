import { defineConfig } from 'vite';
import legacy from '@vitejs/plugin-legacy';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        legacy({
            targets: ['defaults', 'iOS >= 12'],
        }),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
