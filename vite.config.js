import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import statamic from '@statamic/cms/vite-plugin';

export default defineConfig({
    publicDir: false,
    plugins: [
        laravel({
            publicDirectory: 'public',
            buildDirectory: 'build',
            // addon.js is the Control Panel. The player entries are for the
            // front end, through {{ popcorn:scripts }}, and are never loaded in
            // the CP (the service provider registers addon.js only).
            input: ['resources/js/addon.js', 'resources/js/player.js', 'resources/js/player-mux-data.js'],
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
    // The build is served from wherever a site publishes it
    // (vendor/statamic-popcorn/build), not from /build/, the base the Laravel
    // plugin gives it. Imports between chunks are relative anyway, but Vite's
    // preload hints for them were not: they asked for /build/assets/..., which
    // 404s on every site. Relative URLs work wherever the files end up.
    experimental: {
        renderBuiltUrl(filename, { hostType }) {
            return hostType === 'js' ? { relative: true } : undefined;
        },
    },
    build: {
        // Video.js with hls.js is about 1.2 MB. It is only fetched when a
        // Popcorn field is on screen, never with the rest of the Control Panel.
        chunkSizeWarningLimit: 1200,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Video.js is left to split naturally. Naming its chunks
                    // here would pull the modules they share into whichever
                    // chunk claimed them first, so Mux Data's could end up
                    // loading with every player. Left alone, code used only by
                    // player-mux-data.js stays with that entry.
                    if (id.includes('@mux/mux-uploader')) {
                        return 'mux-uploader';
                    }
                },
            },
        },
    },
});
