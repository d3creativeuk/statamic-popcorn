<?php

namespace D3Creative\Popcorn\Tests\Feature;

use D3Creative\Popcorn\Jobs\SendAssetToMux;
use D3Creative\Popcorn\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class FromAssetTest extends TestCase
{
    protected string $uploadUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container();
        $this->uploadUrl = $this->fixture('upload-waiting')['data']['url'];
    }

    #[Test]
    public function a_library_video_is_sent_from_this_server_to_the_upload_url()
    {
        $this->asset('videos/product-tour.mp4', 'pretend video bytes');

        Http::fake([
            $this->muxUrl('/video/v1/uploads') => Http::response($this->fixture('upload-waiting'), 201),
            'storage.googleapis.com/*' => Http::response('', 200),
        ]);

        // The job runs once the response is sent, which in a test is when the
        // kernel terminates, so it has already finished here.
        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.from-asset'), ['asset' => 'assets::videos/product-tour.mp4'])
            ->assertOk()
            ->assertExactJson(['upload_id' => 'UploadNew00000000000000000000000000000001', 'title' => 'product-tour']);

        Http::assertSent(fn (Request $request) => $request->url() === $this->muxUrl('/video/v1/uploads')
            && $request['new_asset_settings']['meta'] === ['title' => 'product-tour']);

        Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
            && $request->url() === $this->uploadUrl
            && $request->body() === 'pretend video bytes'
            && $request->hasHeader('Content-Length', '19'));

        $this->assertNull(SendAssetToMux::failure('UploadNew00000000000000000000000000000001'));
    }

    #[Test]
    public function the_assets_own_title_is_used_when_it_has_one()
    {
        $this->asset('videos/tour.mp4')->set('title', 'The full product tour')->save();

        Http::fake([
            $this->muxUrl('/video/v1/uploads') => Http::response($this->fixture('upload-waiting'), 201),
            'storage.googleapis.com/*' => Http::response('', 200),
        ]);

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.from-asset'), ['asset' => 'assets::videos/tour.mp4'])
            ->assertOk()
            ->assertJson(['title' => 'The full product tour']);
    }

    #[Test]
    public function a_refused_file_is_recorded_for_the_status_check()
    {
        $this->asset('videos/tour.mp4');

        Http::fake([
            $this->muxUrl('/video/v1/uploads') => Http::response($this->fixture('upload-waiting'), 201),
            'storage.googleapis.com/*' => Http::response('', 403),
        ]);

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.from-asset'), ['asset' => 'assets::videos/tour.mp4'])
            ->assertOk();

        $this->assertSame(
            'Mux did not accept the file. Try again, or upload it from your computer instead.',
            SendAssetToMux::failure('UploadNew00000000000000000000000000000001'),
        );
    }

    #[Test]
    public function a_file_that_is_not_a_video_is_refused_before_mux_is_asked()
    {
        $this->asset('photos/cat.jpg');
        Http::fake();

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.from-asset'), ['asset' => 'assets::photos/cat.jpg'])
            ->assertJsonValidationErrors(['asset' => 'cat.jpg is not a video file.']);

        Http::assertNothingSent();
    }

    #[Test]
    public function a_video_in_a_container_the_user_cannot_view_looks_the_same_as_a_missing_one()
    {
        $this->asset('videos/secret.mp4');
        Http::fake();

        $this->actingAs($this->userWith(['use popcorn']))
            ->postJson(cp_route('popcorn.mux.uploads.from-asset'), ['asset' => 'assets::videos/secret.mp4'])
            ->assertJsonValidationErrors(['asset' => 'That asset could not be found.']);

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.from-asset'), ['asset' => 'assets::videos/missing.mp4'])
            ->assertJsonValidationErrors(['asset' => 'That asset could not be found.']);

        Http::assertNothingSent();
    }

    #[Test]
    public function viewing_the_container_is_enough_to_send_one_of_its_videos()
    {
        $this->asset('videos/tour.mp4');

        Http::fake([
            $this->muxUrl('/video/v1/uploads') => Http::response($this->fixture('upload-waiting'), 201),
            'storage.googleapis.com/*' => Http::response('', 200),
        ]);

        $this->actingAs($this->userWith(['use popcorn', 'view assets assets']))
            ->postJson(cp_route('popcorn.mux.uploads.from-asset'), ['asset' => 'assets::videos/tour.mp4'])
            ->assertOk();
    }
}
