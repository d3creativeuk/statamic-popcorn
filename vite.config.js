import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import statamic from '@statamic/cms/vite-plugin';

export default defineConfig({
    publicDir: false,
    plugins: [
        laravel({
            publicDirectory: 'public',
            buildDirectory: 'build',
            input: ['resources/js/addon.js'],
        }),
        ...statamic({
            vue: {
                template: {
                    // Video.js and Mux Uploader are web components, not Vue
                    // components, so Vue leaves them for the browser.
                    compilerOptions: {
                        isCustomElement: (tag) => tag.startsWith('mux-') || tag.startsWith('video-'),
                    },
                },
            },
        }),
    ],
    build: {
        // Video.js with hls.js is about 1.2 MB. It is only fetched when a
        // Popcorn field is on screen, never with the rest of the Control Panel.
        chunkSizeWarningLimit: 1200,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('@videojs/')) {
                        return 'video-player';
                    }

                    if (id.includes('@mux/mux-uploader')) {
                        return 'mux-uploader';
                    }
                },
            },
        },
    },
});
