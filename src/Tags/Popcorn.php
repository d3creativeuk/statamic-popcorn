<?php

namespace D3Creative\Popcorn\Tags;

use Statamic\Fields\Value;
use Statamic\Tags\Tags;

/**
 * Plays a Popcorn video on the front end with Mux Player.
 *
 *   {{ popcorn:scripts }}   once, in the layout's <head>
 *   {{ popcorn:player :video="hero_video" accent-color="#0061ff" }}
 *
 * Every parameter other than "video" becomes an attribute on <mux-player>, so
 * anything Mux Player supports can be set from the template. A parameter set
 * to true becomes a bare attribute (autoplay, muted, loop); false leaves it
 * out.
 */
class Popcorn extends Tags
{
    public function player(): string
    {
        $video = $this->params->get('video');

        if ($video instanceof Value) {
            $video = $video->value();
        }

        if (! is_array($video) || blank($video['playback_id'] ?? null)) {
            return '';
        }

        $title = $video['title'] ?? null;

        // The title always goes to Mux Data, so views are reported against it.
        // It is only drawn on the player when the editor asked for that.
        $attributes = array_filter([
            'playback-id' => $video['playback_id'],
            'stream-type' => 'on-demand',
            'metadata-video-title' => $title,
            'metadata-video-id' => $video['asset_id'] ?? null,
            'title' => ($video['show_title'] ?? false) ? $title : null,
        ], fn ($value) => filled($value));

        foreach ($this->params->except('video')->all() as $name => $value) {
            $attributes[$name] = $value;
        }

        return '<mux-player'.$this->attributes($attributes).'></mux-player>';
    }

    public function scripts(): string
    {
        $src = config('statamic-popcorn.player.script');

        return $src ? '<script src="'.e($src).'" defer></script>' : '';
    }

    protected function attributes(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $name => $value) {
            // Attribute names come from the template, so anything that is not
            // a plain name is dropped rather than escaped into the markup.
            if (! preg_match('/^[a-zA-Z_:][a-zA-Z0-9_.:-]*$/', (string) $name) || $value === false || $value === null) {
                continue;
            }

            $html .= $value === true ? " {$name}" : ' '.$name.'="'.e((string) $value).'"';
        }

        return $html;
    }
}
