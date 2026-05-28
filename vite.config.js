import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/main.jsx'],
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: '0.0.0.0', // Biar bisa diakses dari luar kontainer oleh Docker Compose
        hmr: {
            host: 'localhost', // 💡 Ini kuncinya! Memaksa browser memanggil ke localhost:5173, bukan [::]:5173
        },
        watch: {
            usePolling: true, // Opsional: Biar hot reload di Windows <-> Docker makin responsif
        },
    },
});
