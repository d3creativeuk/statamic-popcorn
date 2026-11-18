<?php

namespace D3Creative\Popcorn\Tests;

use D3Creative\Popcorn\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\User;
use Statamic\Testing\AddonTestCase;
use Statamic\Testing\Concerns\FakesRoles;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

abstract class TestCase extends AddonTestCase
{
    use FakesRoles;
    use PreventsSavingStacheItemsToDisk;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('assets');

        // No test may reach Mux. Anything not faked fails loudly.
        Http::preventStrayRequests();

        // Set after boot rather than in getEnvironmentSetUp(): the addon's
        // config is merged in one level deep, so setting a nested key before
        // the merge would replace that whole branch of the defaults.
        config()->set('statamic-popcorn.mux.token_id', 'test-id');
        config()->set('statamic-popcorn.mux.token_secret', 'test-secret');
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('p', 32)));
        $app['config']->set('statamic.editions.pro', true);
        $app['config']->set('statamic.assets.image_manipulation.generate_presets_on_upload', false);

        $app['config']->set('filesystems.disks.assets', [
            'driver' => 'local',
            'root' => storage_path('app/assets'),
            'url' => '/assets',
        ]);
    }

    protected function superUser()
    {
        return tap(User::make()->id('super')->email('super@example.com')->makeSuper())->save();
    }

    /**
     * A non-super user holding exactly these permissions.
     */
    protected function userWith(array $permissions, string $id = 'editor')
    {
        $this->setTestRoles([$id.'-role' => array_merge(['access cp'], $permissions)]);

        return tap(User::make()->id($id)->email($id.'@example.com')->assignRole($id.'-role'))->save();
    }

    protected function container(string $handle = 'assets')
    {
        return tap(AssetContainer::make($handle)->disk('assets')->title('Assets'))->save();
    }

    /**
     * A file in the container, made as an asset.
     */
    protected function asset(string $path, string $contents = 'not really a video', string $container = 'assets')
    {
        Storage::disk('assets')->put($path, $contents);

        return tap(AssetContainer::findByHandle($container)->makeAsset($path))->save();
    }

    protected function fixture(string $name): array
    {
        return json_decode(file_get_contents(__DIR__.'/__fixtures__/mux/'.$name.'.json'), true);
    }

    protected function muxUrl(string $path): string
    {
        return 'https://api.mux.com'.$path;
    }
}
