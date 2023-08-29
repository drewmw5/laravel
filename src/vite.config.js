import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

const certs = {
    cert: "../certs/fullchain.pem",
    key: "../certs/privkey.pem",
}

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.tsx',
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: true,
        https: certs,
        hmr: {
            host: "podcast.drewmw.local",
        }
    }
});
