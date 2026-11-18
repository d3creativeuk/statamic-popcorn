<?php

namespace D3Creative\Popcorn\Tests\Feature;

use D3Creative\Popcorn\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class AssetsTest extends TestCase
{
    #[Test]
    public function only_playable_videos_are_listed_with_what_the_panel_shows()
    {
        Http::fake([$this->muxUrl('/video/v1/assets*') => Http::response($this->fixture('assets-page'))]);

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.assets.index'))
            ->assertOk()
            ->assertExactJson([
                'assets' => [[
                    'asset_id' => 'AssetReady000000000000000000000000000000001',
                    'playback_id' => 'PlaybackReady00000000000000000000000000001',
                    'title' => 'Statamic maintenance walkthrough',
                    'status' => 'ready',
                    'duration' => 228.4,
                    'aspect_ratio' => '16:9',
                    'resolution_tier' => '1080p',
                    'max_width' => 1920,
                    'max_height' => 1080,
                    'created_at' => 1794592873,
                    'thumbnail' => 'https://image.mux.com/PlaybackReady00000000000000000000000000001/thumbnail.webp?width=320&height=180&fit_mode=smartcrop',
                    'error' => null,
                ]],
                'next_cursor' => 'CursorPageTwo',
            ]);

        Http::assertSent(fn (Request $request) => $request->url() === $this->muxUrl('/video/v1/assets?limit=50'));
    }

    #[Test]
    public function the_next_page_is_asked_for_by_cursor()
    {
        Http::fake([$this->muxUrl('/video/v1/assets*') => Http::response(['data' => [], 'next_cursor' => null])]);

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.assets.index', ['cursor' => 'CursorPageTwo']))
            ->assertOk()
            ->assertExactJson(['assets' => [], 'next_cursor' => null]);

        Http::assertSent(fn (Request $request) => $request->url() === $this->muxUrl('/video/v1/assets?limit=50&cursor=CursorPageTwo'));
    }

    #[Test]
    public function one_video_can_be_followed_until_it_is_ready()
    {
        Http::fake([$this->muxUrl('/video/v1/assets/AssetPreparing00000000000000000000000000001') => Http::response($this->fixture('asset-preparing'))]);

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.assets.show', 'AssetPreparing00000000000000000000000000001'))
            ->assertOk()
            ->assertJson(['status' => 'preparing', 'playback_id' => 'PlaybackPreparing0000000000000000000000001']);
    }

    #[Test]
    public function a_video_deleted_in_mux_is_a_404_with_a_plain_message()
    {
        Http::fake([$this->muxUrl('/video/v1/assets/*') => Http::response(['error' => ['type' => 'not_found', 'messages' => ['Not found']]], 404)]);

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.assets.show', 'AssetGone'))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Mux could not find that video. It may have been deleted in the Mux dashboard.']);
    }

    #[Test]
    public function the_title_is_kept_in_step_with_mux()
    {
        Http::fake([$this->muxUrl('/video/v1/assets/AssetReady000000000000000000000000000000001') => Http::response($this->fixture('asset-ready'))]);

        $this->actingAs($this->superUser())
            ->patchJson(cp_route('popcorn.mux.assets.title', 'AssetReady000000000000000000000000000000001'), ['title' => ' New title '])
            ->assertOk()
            ->assertExactJson(['title' => 'New title']);

        Http::assertSent(fn (Request $request) => $request->method() === 'PATCH' && $request->data() === ['meta' => ['title' => 'New title']]);
    }

    #[Test]
    public function a_title_cannot_be_blank()
    {
        Http::fake();

        $this->actingAs($this->superUser())
            ->patchJson(cp_route('popcorn.mux.assets.title', 'AssetReady000000000000000000000000000000001'), ['title' => ''])
            ->assertJsonValidationErrors('title');

        Http::assertNothingSent();
    }
}
