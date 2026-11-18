<?php

namespace D3Creative\Popcorn\Tags;

use Illuminate\Foundation\Vite;
use Statamic\Fields\Value;
use Statamic\Tags\Tags;
use Throwable;

/**
 * Plays a Popcorn video on the front end with Video.js and its Mux media
 * element, the player Mux recommends.
 *
 *   {{ popcorn:scripts }}   once, in the layout's <head>; serves the player
 *                           from the site, published with the add-on
 *   {{ popcorn:player :video="hero_video" accent-color="#0061ff" }}
 *
 * The player is three elements: <video-player> holds the state, <video-skin>
 * draws the controls, and <mux-video> plays the stream. A <mux-data> element
 * is added only when tracking is on. Parameters go where they mean
 * something: "style" and "class" to the skin, "accent-color" to the skin's
 * accent, "metadata-*" to Mux Data, and the rest (autoplay, muted, loop,
 * poster-time and so on) to <mux-video>. A parameter set to true becomes a
 * bare attribute; false leaves it out.
 */
class Popcorn extends Tags
{
    /**
     * The player and Mux Data, as built into this add-on's public/build and
     * published to the site's public/vendor/statamic-popcorn/build.
     */
    protected const ENTRY = 'resources/js/player.js';

    protected const TRACKING_ENTRY = 'resources/js/player-mux-data.js';

    /**
     * The same files in an @videojs/cdn release, for sites that set
     * player.cdn.
     */
    protected const CDN_SCRIPTS = ['video.js', 'media/mux-video.js'];

    protected const CDN_TRACKING_SCRIPT = 'extensions/mux-data.js';

    public function player(): string
    {
        $video = $this->params->get('video');

        if ($video instanceof Value) {
            $video = $video->value();
        }

        if (! is_array($video) || blank($video['playback_id'] ?? null)) {
            return '';
        }

        $title = filled($video['title'] ?? null) ? $video['title'] : null;
        $params = $this->params->except(['video', 'style', 'class', 'accent-color', 'tracking', 'cookies'])->all();

        // Always set. Without it, Video.js shows the title Mux has for the
        // asset; an empty one is how "Show Title" off hides it.
        $player = ' content-title="'.e(($video['show_title'] ?? false) ? (string) $title : '').'"';

        $skin = $this->attributes([
            'class' => $this->params->get('class'),
            'style' => $this->style($this->params->get('style'), $this->params->get('accent-color')),
        ]);

        $media = $this->attributes([
            'src' => 'https://stream.mux.com/'.rawurlencode($video['playback_id']).'.m3u8',
            'playsinline' => true,
            'crossorigin' => 'anonymous',
            ...array_filter($params, fn ($name) => ! str_starts_with((string) $name, 'metadata-'), ARRAY_FILTER_USE_KEY),
        ]);

        $data = $this->tracking() ? '<mux-data'.$this->attributes([
            // Views are reported against the title and the Mux asset.
            'metadata-video-title' => $title,
            'metadata-video-id' => $video['asset_id'] ?? null,
            ...array_filter($params, fn ($name) => str_starts_with((string) $name, 'metadata-'), ARRAY_FILTER_USE_KEY),
            'disable-cookies' => ! $this->setting('cookies'),
        ]).'></mux-data>' : '';

        return "<video-player{$player}><video-skin{$skin}><mux-video{$media}></mux-video>{$data}</video-skin></video-player>";
    }

    public function scripts(): string
    {
        $cdn = rtrim((string) config('statamic-popcorn.player.cdn'), '/');

        return $cdn === '' ? $this->bundledScripts() : $this->cdnScripts($cdn);
    }

    /**
     * Served from the site itself, so visitors' browsers contact no one but
     * Mux. A clone of Laravel's Vite, so pointing it at this add-on's build
     * and hot file leaves the site's own @vite alone.
     */
    protected function bundledScripts(): string
    {
        $vite = clone app(Vite::class);
        $vite->useHotFile(dirname(__DIR__, 2).'/public/hot');

        try {
            return (string) $vite(
                [self::ENTRY, ...($this->tracking() ? [self::TRACKING_ENTRY] : [])],
                'vendor/statamic-popcorn/build',
            );
        } catch (Throwable $e) {
            // Most likely the assets were never published. Report it, but
            // don't take the whole page down for a missing video player.
            report($e);

            return '';
        }
    }

    protected function cdnScripts(string $cdn): string
    {
        $files = [...self::CDN_SCRIPTS, ...($this->tracking() ? [self::CDN_TRACKING_SCRIPT] : [])];

        return collect($files)
            ->map(fn ($file) => '<script type="module" src="'.e("{$cdn}/{$file}").'"></script>')
            ->implode('');
    }

    /**
     * Mux Data is off unless the site turns it on, and a template can turn
     * it on or off for one player (or, on popcorn:scripts, for the page).
     */
    protected function tracking(): bool
    {
        return $this->setting('tracking');
    }

    protected function setting(string $name): bool
    {
        $value = $this->params->has($name) ? $this->params->get($name) : config("statamic-popcorn.player.{$name}");

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * The skin is given display: block, as Mux's own example does, so it
     * fills its box and leaves no gap below it the height of a text
     * descender (which would sit under rounded bottom corners). A template
     * that sets its own display keeps it.
     */
    protected function style($style, $accent): string
    {
        $style = trim((string) ($style === true ? '' : $style));

        if (is_string($accent) && $accent !== '') {
            $style = trim('--media-accent-color: '.$accent.'; '.$style);
        }

        if (preg_match('/(^|;)\s*display\s*:/i', $style)) {
            return $style;
        }

        return trim('display: block; '.$style);
    }

    protected function attributes(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $name => $value) {
            // Attribute names come from the template, so anything that is not
            // a plain name is dropped rather than escaped into the markup.
            if (! preg_match('/^[a-zA-Z_:][a-zA-Z0-9_.:-]*$/', (string) $name) || $value === false || $value === null || $value === '') {
                continue;
            }

            $html .= $value === true ? " {$name}" : ' '.$name.'="'.e((string) $value).'"';
        }

        return $html;
    }
}
