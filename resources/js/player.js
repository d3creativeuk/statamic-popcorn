// The front-end player, for {{ popcorn:scripts }}: Video.js with its Mux
// media element. Served from the site's own domain (published with the
// add-on), so visitors' browsers only contact Mux, never a third-party CDN.
import '@videojs/html/video/player';
import '@videojs/html/video/skin';
import '@videojs/html/media/mux-video';
