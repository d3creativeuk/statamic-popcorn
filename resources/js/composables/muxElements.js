// The video player and Mux Uploader are large, so they are loaded the first
// time a Popcorn field needs them rather than with every Control Panel page.
// Each is a web component. Wait for loadPlayer() before putting the player's
// elements on the page: its three modules finish in no fixed order, and
// player elements that upgrade out of order leave the controls unconnected.
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
