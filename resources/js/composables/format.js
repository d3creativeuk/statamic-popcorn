/**
 * Text made safe to put in a toast. Statamic shows toast messages as HTML, so
 * anything someone else could have typed, such as a video title, is escaped.
 */
export function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[character]);
}

/**
 * "3:48", or "1:02:05" for an hour or more.
 */
export function duration(seconds) {
    if (!seconds) {
        return '';
    }

    const total = Math.floor(seconds);
    const h = Math.floor(total / 3600);
    const m = Math.floor((total % 3600) / 60);
    const s = String(total % 60).padStart(2, '0');

    return h ? `${h}:${String(m).padStart(2, '0')}:${s}` : `${m}:${s}`;
}

export function shortDate(timestamp) {
    if (!timestamp) {
        return '';
    }

    return new Date(timestamp * 1000).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
}

/**
 * An asset's filename from its id: "assets::videos/intro.mp4" -> "intro.mp4".
 */
export function assetName(id) {
    const path = String(id ?? '');
    const at = path.indexOf('::');

    return (at === -1 ? path : path.slice(at + 2)).split('/').pop();
}

export function extension(name) {
    const parts = String(name ?? '').split('.');

    return parts.length > 1 ? parts.pop().toLowerCase() : '';
}
