// Mux Player and Mux Uploader are large, so they are loaded the first time a
// Popcorn field needs them rather than with every Control Panel page. Each is
// a web component: once its module has run, any <mux-player> or
// <mux-uploader> already on the page upgrades itself.

let player = null;
let uploader = null;

export function loadPlayer() {
    return (player ??= import('@mux/mux-player'));
}

export function loadUploader() {
    return (uploader ??= import('@mux/mux-uploader'));
}
