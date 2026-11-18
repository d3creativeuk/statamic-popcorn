<?php

namespace D3Creative\Popcorn\GraphQL;

use Rebing\GraphQL\Support\Type;
use Statamic\Facades\GraphQL;

/**
 * A Popcorn field over GraphQL: the same keys a template gets.
 */
class PopcornVideoType extends Type
{
    const NAME = 'PopcornVideo';

    protected $attributes = [
        'name' => self::NAME,
    ];

    public function fields(): array
    {
        $string = fn (string $key) => [
            'type' => GraphQL::string(),
            'resolve' => fn ($video) => $video[$key] ?? null,
        ];

        return [
            'provider' => $string('provider'),
            'asset_id' => $string('asset_id'),
            'playback_id' => $string('playback_id'),
            'title' => $string('title'),
            'show_title' => [
                'type' => GraphQL::boolean(),
                'resolve' => fn ($video) => (bool) ($video['show_title'] ?? false),
            ],
            'stream_url' => $string('stream_url'),
            'thumbnail_url' => $string('thumbnail_url'),
            'gif_url' => $string('gif_url'),
        ];
    }
}
