<?php

namespace D3Creative\Popcorn\Fieldtypes;

use D3Creative\Popcorn\Assets\VideoAssets;
use D3Creative\Popcorn\GraphQL\PopcornVideoType;
use D3Creative\Popcorn\Mux\MuxClient;
use D3Creative\Popcorn\Mux\MuxVideo;
use Illuminate\Support\Facades\Gate;
use Statamic\Facades\GraphQL;
use Statamic\Facades\User;
use Statamic\Fields\Fieldtype;

/**
 * One video, hosted by Mux. The stored value is the Mux asset and playback IDs
 * with a title, so it reads the same in a YAML file as it does in a template.
 *
 * Mux is the only host so far. A value without a "provider" key is a Mux
 * video, so any later host can be added beside it without touching content
 * saved now.
 */
class Popcorn extends Fieldtype
{
    protected static $handle = 'popcorn';

    protected static $title = 'Popcorn Video';

    protected $categories = ['media'];

    protected $keywords = ['video', 'mux', 'player', 'upload'];

    public function icon()
    {
        return 'fieldtype-video';
    }

    protected function configFieldItems(): array
    {
        return [
            'allow_reupload' => [
                'display' => __('Allow Replacing'),
                'instructions' => __('Show a Remove button once a video is chosen, so it can be replaced.'),
                'type' => 'toggle',
                'default' => true,
            ],
        ];
    }

    public function preload()
    {
        $user = User::current();
        $can = $user && Gate::forUser($user)->allows('use popcorn');
        $mux = app(MuxClient::class);

        return [
            'configured' => $mux->configured(),
            'can' => $can,
            'pollInterval' => (int) config('statamic-popcorn.poll_interval', 3000),
            'urls' => [
                'upload' => cp_route('popcorn.mux.uploads.store'),
                'fromAsset' => cp_route('popcorn.mux.uploads.from-asset'),
                'uploadStatus' => cp_route('popcorn.mux.uploads.show', '__id__'),
                'assets' => cp_route('popcorn.mux.assets.index'),
                'asset' => cp_route('popcorn.mux.assets.show', '__id__'),
                'title' => cp_route('popcorn.mux.assets.title', '__id__'),
            ],
            // Only worth building when the user can send a library video.
            'containers' => $can ? VideoAssets::containers($user) : [],
            'videoExtensions' => VideoAssets::EXTENSIONS,
        ];
    }

    public function defaultValue()
    {
        return null;
    }

    public function preProcess($data)
    {
        return is_array($data) && filled($data['playback_id'] ?? null) ? $this->clean($data) : null;
    }

    public function process($data)
    {
        return is_array($data) && filled($data['playback_id'] ?? null) ? $this->clean($data) : null;
    }

    public function preProcessIndex($data)
    {
        return is_array($data) ? ($data['title'] ?? $data['playback_id'] ?? null) : null;
    }

    public function augment($value)
    {
        if (! is_array($value) || blank($value['playback_id'] ?? null)) {
            return null;
        }

        $playbackId = $value['playback_id'];

        return [
            'provider' => 'mux',
            'asset_id' => $value['asset_id'] ?? null,
            'playback_id' => $playbackId,
            'title' => $value['title'] ?? null,
            'show_title' => (bool) ($value['show_title'] ?? false),
            'stream_url' => MuxVideo::streamUrl($playbackId),
            'thumbnail_url' => MuxVideo::thumbnailUrl($playbackId),
            'gif_url' => MuxVideo::gifUrl($playbackId),
        ];
    }

    public function toGqlType()
    {
        return GraphQL::type(PopcornVideoType::NAME);
    }

    public function addGqlTypes()
    {
        GraphQL::addType(PopcornVideoType::class);
    }

    protected function clean(array $data): array
    {
        $title = is_string($data['title'] ?? null) ? trim($data['title']) : null;

        return [
            'asset_id' => $data['asset_id'] ?? null,
            'playback_id' => $data['playback_id'],
            'title' => $title === '' ? null : $title,
            'show_title' => (bool) ($data['show_title'] ?? false),
        ];
    }
}
