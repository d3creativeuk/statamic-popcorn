<?php

namespace D3Creative\Popcorn\Assets;

use Illuminate\Support\Facades\Gate;
use Statamic\Contracts\Assets\Asset as AssetContract;
use Statamic\Facades\AssetContainer;
use Statamic\Fields\Field;

/**
 * Videos already in the asset library that can be sent to Mux, and the
 * containers they can be chosen from.
 */
class VideoAssets
{
    /**
     * Formats Mux accepts as input that people are likely to have in an asset
     * container. Mux takes others too, but these are the ones worth offering.
     */
    public const EXTENSIONS = ['mp4', 'm4v', 'mov', 'webm', 'mkv', 'avi', 'mpg', 'mpeg', 'wmv', 'ogv', '3gp', 'mxf'];

    public static function isVideo(AssetContract $asset): bool
    {
        return in_array(strtolower((string) $asset->extension()), static::EXTENSIONS, true);
    }

    public static function viewable($user, AssetContract $asset): bool
    {
        return $user && Gate::forUser($user)->allows('view', $asset);
    }

    /**
     * Each container the user may look in, with what Statamic's own asset
     * browser needs to show it, built exactly as the core Assets fieldtype
     * builds it for its selector. Seeing a video is enough to send it to Mux;
     * nothing is uploaded or created in the library, so the browser offers
     * neither.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function containers($user): array
    {
        if (! $user) {
            return [];
        }

        return AssetContainer::all()
            ->filter(fn ($container) => Gate::forUser($user)->allows('view', $container))
            ->map(function ($container) {
                $preload = (new Field('popcorn_source', [
                    'type' => 'assets',
                    'container' => $container->handle(),
                    'max_files' => 1,
                ]))->fieldtype()->preload();

                return [
                    'handle' => $container->handle(),
                    'title' => $container->title(),
                    'browser' => array_merge($preload['container'], ['can_upload' => false, 'can_create_folders' => false]),
                    'columns' => $preload['columns'],
                ];
            })
            ->values()
            ->all();
    }
}
