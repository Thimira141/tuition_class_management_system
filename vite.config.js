import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js',
                'resources/js/students.js',
                'resources/css/dt-imports.css',
            ],
            refresh: true,
        }),
    ],
    build: {
    // Raise the warning threshold (default is 500 KB)
    chunkSizeWarningLimit: 1000,
  },
});
