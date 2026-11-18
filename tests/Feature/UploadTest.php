<?php

namespace D3Creative\Popcorn\Tests\Feature;

use D3Creative\Popcorn\Jobs\SendAssetToMux;
use D3Creative\Popcorn\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class UploadTest extends TestCase
{
    #[Test]
    public function an_upload_url_is_created_for_the_control_panels_own_host()
    {
        Http::fake([$this->muxUrl('/video/v1/uploads') => Http::response($this->fixture('upload-waiting'), 201)]);

        $this->actingAs($this->superUser())
            ->postJson('https://cp.example.test'.$this->path('popcorn.mux.uploads.store'), ['title' => '  Product tour  '])
            ->assertOk()
            ->assertExactJson([
                'upload_id' => 'UploadNew00000000000000000000000000000001',
                'url' => $this->fixture('upload-waiting')['data']['url'],
            ]);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST'
                && $request->url() === $this->muxUrl('/video/v1/uploads')
                && $request->hasHeader('Authorization', 'Basic '.base64_encode('test-id:test-secret'))
                // The browser uploads from the CP's host, not APP_URL.
                && $request['cors_origin'] === 'https://cp.example.test'
                && $request['new_asset_settings'] === [
                    'playback_policy' => ['public'],
                    'meta' => ['title' => 'Product tour'],
                ];
        });
    }

    #[Test]
    public function the_configured_video_quality_is_asked_for()
    {
        config()->set('statamic-popcorn.mux.video_quality', 'plus');
        Http::fake([$this->muxUrl('/video/v1/uploads') => Http::response($this->fixture('upload-waiting'), 201)]);

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.store'), ['title' => 'Tour'])
            ->assertOk();

        Http::assertSent(fn (Request $request) => $request['new_asset_settings']['video_quality'] === 'plus');
    }

    #[Test]
    public function an_upload_needs_a_title()
    {
        Http::fake();

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.store'), ['title' => ''])
            ->assertJsonValidationErrors('title');

        Http::assertNothingSent();
    }

    #[Test]
    public function the_status_reports_the_playback_id_and_whether_the_video_is_ready()
    {
        Http::fake([
            $this->muxUrl('/video/v1/uploads/UploadNew00000000000000000000000000000001') => Http::response($this->fixture('upload-asset-created')),
            $this->muxUrl('/video/v1/assets/AssetReady000000000000000000000000000000001') => Http::response($this->fixture('asset-ready')),
        ]);

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.uploads.show', 'UploadNew00000000000000000000000000000001'))
            ->assertOk()
            ->assertExactJson([
                'status' => 'asset_created',
                'asset_id' => 'AssetReady000000000000000000000000000000001',
                'playback_id' => 'PlaybackReady00000000000000000000000000001',
                'asset_status' => 'ready',
                'error' => null,
            ]);
    }

    #[Test]
    public function a_waiting_upload_has_no_asset_yet()
    {
        Http::fake([$this->muxUrl('/video/v1/uploads/*') => Http::response($this->fixture('upload-waiting'))]);

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.uploads.show', 'UploadNew00000000000000000000000000000001'))
            ->assertOk()
            ->assertJson(['status' => 'waiting', 'asset_id' => null, 'playback_id' => null]);

        Http::assertSentCount(1);
    }

    #[Test]
    public function an_errored_upload_says_why()
    {
        Http::fake([$this->muxUrl('/video/v1/uploads/*') => Http::response($this->fixture('upload-errored'))]);

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.uploads.show', 'UploadNew00000000000000000000000000000001'))
            ->assertOk()
            ->assertJson(['status' => 'errored', 'error' => 'The file you uploaded was not a recognised video.']);
    }

    #[Test]
    public function a_library_video_that_could_not_be_sent_is_reported_without_asking_mux()
    {
        Http::fake();
        SendAssetToMux::fail('UploadNew00000000000000000000000000000001', 'Mux did not accept the file.');

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.uploads.show', 'UploadNew00000000000000000000000000000001'))
            ->assertOk()
            ->assertExactJson(['status' => 'errored', 'error' => 'Mux did not accept the file.']);

        Http::assertNothingSent();
    }

    #[Test]
    public function an_upload_id_that_is_not_a_mux_id_never_reaches_mux()
    {
        Http::fake();

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.uploads.show', 'x').'/../assets')
            ->assertNotFound();

        Http::assertNothingSent();
    }

    #[Test]
    public function a_rejected_token_gets_a_message_that_says_so()
    {
        Http::fake([$this->muxUrl('/video/v1/uploads') => Http::response(['error' => ['type' => 'unauthorized', 'messages' => ['Unauthorized request']]], 401)]);

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.store'), ['title' => 'Tour'])
            ->assertStatus(502)
            ->assertExactJson(['message' => 'Mux refused the access token. Check MUX_TOKEN_ID and MUX_TOKEN_SECRET, and that the token has Mux Video write access.']);
    }

    #[Test]
    public function a_bare_403_is_the_token()
    {
        Http::fake([$this->muxUrl('/video/v1/uploads') => Http::response(['error' => ['type' => 'forbidden', 'messages' => ['Forbidden']]], 403)]);

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.store'), ['title' => 'Tour'])
            ->assertStatus(502)
            ->assertJson(['message' => 'Mux refused the access token. Check MUX_TOKEN_ID and MUX_TOKEN_SECRET, and that the token has Mux Video write access.']);
    }

    /**
     * Mux does not document what it answers when a plan's video limit is
     * reached, so these use a likely wording. What matters is that the
     * reason is shown with advice, and never blamed on the token.
     */
    #[Test]
    public function a_plan_limit_is_shown_with_what_to_do_not_blamed_on_the_token()
    {
        Http::fake([$this->muxUrl('/video/v1/uploads') => Http::response(['error' => ['type' => 'forbidden', 'messages' => ['Free plan accounts are limited to 10 assets']]], 403)]);

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.store'), ['title' => 'Tour'])
            ->assertStatus(502)
            ->assertExactJson(['message' => 'Mux could not create an upload. Free plan accounts are limited to 10 assets. Your Mux plan may have reached its limit: upgrade it, or delete videos you no longer need, at dashboard.mux.com.']);
    }

    #[Test]
    public function a_plan_limit_on_a_402_is_explained_too()
    {
        Http::fake([$this->muxUrl('/video/v1/uploads') => Http::response(['error' => ['type' => 'payment_required', 'messages' => ['Asset limit exceeded. Please upgrade your plan.']]], 402)]);

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.store'), ['title' => 'Tour'])
            ->assertStatus(502)
            ->assertExactJson(['message' => 'Mux could not create an upload. Asset limit exceeded. Please upgrade your plan. Your Mux plan may have reached its limit: upgrade it, or delete videos you no longer need, at dashboard.mux.com.']);
    }

    #[Test]
    public function a_limit_that_shows_only_once_the_file_has_arrived_is_explained_in_the_status()
    {
        $errored = $this->fixture('upload-errored');
        $errored['data']['error']['message'] = 'This account has reached its asset limit';

        Http::fake([$this->muxUrl('/video/v1/uploads/*') => Http::response($errored)]);

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.uploads.show', 'UploadNew00000000000000000000000000000001'))
            ->assertOk()
            ->assertJson([
                'status' => 'errored',
                'error' => 'This account has reached its asset limit. Your Mux plan may have reached its limit: upgrade it, or delete videos you no longer need, at dashboard.mux.com.',
            ]);
    }

    #[Test]
    public function an_ordinary_error_gets_no_advice_about_plans()
    {
        Http::fake([$this->muxUrl('/video/v1/uploads/*') => Http::response($this->fixture('upload-errored'))]);

        $this->actingAs($this->superUser())
            ->getJson(cp_route('popcorn.mux.uploads.show', 'UploadNew00000000000000000000000000000001'))
            ->assertJson(['error' => 'The file you uploaded was not a recognised video.']);
    }

    #[Test]
    public function muxs_own_reason_is_passed_on()
    {
        Http::fake([$this->muxUrl('/video/v1/uploads') => Http::response($this->fixture('error-invalid'), 400)]);

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.store'), ['title' => 'Tour'])
            ->assertStatus(502)
            ->assertExactJson(['message' => 'Mux could not create an upload. meta.title must be 512 characters or fewer']);
    }

    #[Test]
    public function without_credentials_nothing_is_sent_and_the_message_says_what_to_add()
    {
        config()->set('statamic-popcorn.mux.token_secret', null);
        Http::fake();

        $this->actingAs($this->superUser())
            ->postJson(cp_route('popcorn.mux.uploads.store'), ['title' => 'Tour'])
            ->assertStatus(503)
            ->assertExactJson(['message' => 'Popcorn has no Mux access token. Add MUX_TOKEN_ID and MUX_TOKEN_SECRET to .env.']);

        Http::assertNothingSent();
    }

    /**
     * A CP route's path, to put after a different host.
     */
    protected function path(string $name, ...$parameters): string
    {
        return parse_url(cp_route($name, ...$parameters), PHP_URL_PATH);
    }
}
