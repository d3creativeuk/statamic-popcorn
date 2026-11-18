<?php

namespace D3Creative\Popcorn\Mux;

/**
 * Turns Mux's asset records into what the field and templates use, and builds
 * the public URLs Mux serves a playback ID from.
 */
class MuxVideo
{
    /**
     * The first public playback ID. Popcorn only creates public ones, but an
     * asset made elsewhere may also carry signed IDs, which would not play
     * without a token.
     */
    public static function playbackId(array $asset): ?string
    {
        foreach ($asset['playback_ids'] ?? [] as $playback) {
            if (($playback['policy'] ?? 'public') === 'public' && filled($playback['id'] ?? null)) {
                return $playback['id'];
            }
        }

        return null;
    }

    /**
     * What the Browse Mux panel shows for one video.
     */
    public static function summary(array $asset): array
    {
        $playbackId = static::playbackId($asset);
        $video = collect($asset['tracks'] ?? [])->firstWhere('type', 'video') ?? [];

        return [
            'asset_id' => $asset['id'],
            'playback_id' => $playbackId,
            'title' => $asset['meta']['title'] ?? null,
            'status' => $asset['status'] ?? null,
            'duration' => isset($asset['duration']) ? (float) $asset['duration'] : null,
            'aspect_ratio' => $asset['aspect_ratio'] ?? null,
            'resolution_tier' => $asset['resolution_tier'] ?? null,
            'max_width' => $video['max_width'] ?? null,
            'max_height' => $video['max_height'] ?? null,
            // Mux sends a Unix timestamp as a string.
            'created_at' => isset($asset['created_at']) ? (int) $asset['created_at'] : null,
            'thumbnail' => $playbackId ? static::thumbnailUrl($playbackId, ['width' => 320, 'height' => 180, 'fit_mode' => 'smartcrop']) : null,
            'error' => $asset['errors']['messages'][0] ?? null,
        ];
    }

    /**
     * Only videos that can be played are offered for choosing.
     */
    public static function playable(array $asset): bool
    {
        return ($asset['status'] ?? null) === 'ready' && static::playbackId($asset) !== null;
    }

    public static function streamUrl(string $playbackId): string
    {
        return 'https://stream.mux.com/'.rawurlencode($playbackId).'.m3u8';
    }

    public static function thumbnailUrl(string $playbackId, array $query = []): string
    {
        return static::imageUrl($playbackId, 'thumbnail.webp', $query);
    }

    public static function gifUrl(string $playbackId, array $query = []): string
    {
        return static::imageUrl($playbackId, 'animated.gif', $query);
    }

    protected static function imageUrl(string $playbackId, string $file, array $query): string
    {
        $url = 'https://image.mux.com/'.rawurlencode($playbackId).'/'.$file;

        return $query ? $url.'?'.http_build_query($query) : $url;
    }
}
