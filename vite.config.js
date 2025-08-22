import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        vue(), // only Vue plugin
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    build: {
        outDir: 'dist',      // Output folder for Capacitor
        emptyOutDir: true,   // Clears folder before build
        rollupOptions: {
            input: resolve(__dirname, 'index.html'), // Use our permanent index.html
        },
    },
});