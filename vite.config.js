import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/task/show.js',
                'resources/js/routes.js',
                'resources/sass/app.scss',
                'resources/js/app.js',                
            ],
            refresh: true,
        }),
    ],
    server: {
        // Make sure this is not active in production
        //hmr: false, // Disable HMR (Hot Module Replacement) in production
      },
});
