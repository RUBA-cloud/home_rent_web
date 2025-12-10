// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'], // add 'resources/css/app.css' if you have it
            refresh: true,
        }),
    ],
    server: { host: '127.0.0.1', hmr: { host: '127.0.0.1' } },
})
