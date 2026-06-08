import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/address/show.js',
                'resources/js/address/show.init.js',                
                'resources/js/client/show.js',
                'resources/js/client/show.init.js',
                'resources/js/job/index.js',
                'resources/js/jobtemplate/index.js',
                'resources/js/task/show.js',        
                'resources/js/app.js',
                'resources/js/config.js',
                'resources/js/routes.js',
                //-----------------------------------
                'resources/css/app.css',
                //-----------------------------------
                'resources/sass/app.scss',
                                
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        cors: true,      // Allows remote browsers to pull assets safely
        hmr: {
            host: '5.20.166.68', // Your actual public IP from your .env
            port: 5173,
        },
        // Make sure this is not active in production
        //hmr: false, // Disable HMR (Hot Module Replacement) in production
    },
    build: {
        minify: 'terser', // Use 'terser' for minification
        terserOptions: {
            compress: {
                dead_code: false,
                drop_console: false, // Remove console logs |||| 2025-08-08 "SET TO FALSE" REVERT THIS
            },
            // mangle: {
            //     // Keep function names
            //     keep_fnames: true,
            // },
        },
    },
});
