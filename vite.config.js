import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/style.css',
                'resources/css/auth.css',
                'resources/js/app.js',
                'resources/js/member.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
