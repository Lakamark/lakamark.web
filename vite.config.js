import { defineConfig } from 'vite'
import preact from '@preact/preset-vite'
import mkcert from 'vite-plugin-mkcert'
import { resolve } from 'node:path';

const twigRefreshPlugin = {
    name: 'twig-refresh',
    configureServer ({ watcher, ws }) {
        watcher.add(resolve('templates/**/*.twig'));
        watcher.on('change', function (path) {
            if (path.endsWith('.twig')) {
                ws.send({
                    type: 'full-reload'
                });
            }
        });
    }
}

// https://vite.dev/config/
export default defineConfig({
    plugins: [preact(), mkcert(), twigRefreshPlugin],
    root: './assets',
    base: '/assets',
    server: {
        port: 3000,
        host: '0.0.0.0',
        https: true,
        watch: {
            disableGlobbing: false
        }
    },
    build: {
        manifest: true,
        assetsDir: '',
        outDir: '../public/assets',
        rollupOptions: {
            output: {
                manualChunks: undefined
            },
            input: {
                app: './assets/app.js'
            }
        }
    }
})
