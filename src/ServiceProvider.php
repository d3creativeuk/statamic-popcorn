<?php

namespace D3Creative\Popcorn;

use D3Creative\Popcorn\Mux\MuxClient;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'publicDirectory' => 'public',
        'buildDirectory' => 'build',
        'input' => ['resources/js/addon.js'],
    ];

    public function register()
    {
        parent::register();

        // Reads the addon's config, which Statamic merges during boot, so it
        // is resolved lazily to see the merged values and any site overrides.
        $this->app->bind(MuxClient::class, fn () => new MuxClient((array) config('statamic-popcorn.mux')));
    }

    public function bootAddon()
    {
        Permission::extend(function () {
            Permission::group('popcorn', 'Popcorn', function () {
                // Uploading costs money in Mux and browsing shows every video
                // in the account, so neither comes with editing an entry.
                Permission::register('use popcorn')
                    ->label('Upload, choose and rename videos with Popcorn');
            });
        });
    }
}
