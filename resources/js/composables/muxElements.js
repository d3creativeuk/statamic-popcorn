// The video player and Mux Uploader are large, so they are loaded the first
// time a Popcorn field needs them rather than with every Control Panel page.
// Each is a web component: once its module has run, any <video-player> or
// <mux-uploader> already on the page upgrades itself.
//
// The player is Video.js with its Mux media element, which Mux recommends in
// place of Mux Player. Mux Data is left out, so previews in the Control Panel
// are never counted as views.

let player = null;
let uploader = null;

export function loadPlayer() {
    return (player ??= Promise.all([
        import('@videojs/html/video/player'),
        import('@videojs/html/video/skin'),
        import('@videojs/html/media/mux-video'),
    ]));
}

export function loadUploader() {
    return (uploader ??= import('@mux/mux-uploader'));
}
