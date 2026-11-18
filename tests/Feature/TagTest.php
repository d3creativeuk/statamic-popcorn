<?php

namespace D3Creative\Popcorn\Tests\Feature;

use D3Creative\Popcorn\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Facades\Antlers;

class TagTest extends TestCase
{
    /**
     * As a template renders it. Antlers::parse() treats its input as
     * untrusted content by default, which only allows core's own tags.
     */
    protected function render(string $template, array $data = []): string
    {
        return (string) Antlers::parse($template, $data, true);
    }

    protected function video(array $overrides = []): array
    {
        return array_merge([
            'asset_id' => 'Asset1',
            'playback_id' => 'Play1',
            'title' => 'Tour & "more"',
            'show_title' => false,
        ], $overrides);
    }

    #[Test]
    public function the_player_is_video_js_with_the_mux_stream()
    {
        $this->assertSame(
            '<video-player content-title=""><video-skin style="display: block;"><mux-video src="https://stream.mux.com/Play1.m3u8" playsinline crossorigin="anonymous"></mux-video></video-skin></video-player>',
            $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video()]),
        );
    }

    #[Test]
    public function the_title_is_drawn_only_when_the_editor_asked()
    {
        // Empty rather than missing: without it, Video.js shows the title
        // Mux has for the asset.
        $this->assertStringContainsString('<video-player content-title="">', $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video()]));

        $html = $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video(['show_title' => true])]);

        $this->assertStringContainsString('<video-player content-title="Tour &amp; &quot;more&quot;">', $html);
    }

    #[Test]
    public function parameters_go_to_the_element_they_belong_to()
    {
        $html = $this->render(
            '{{ popcorn:player :video="video" accent-color="#0061ff" class="hero" muted="true" loop="false" poster-time="2" }}',
            ['video' => $this->video()],
        );

        $this->assertStringContainsString('<video-skin class="hero" style="display: block; --media-accent-color: #0061ff;">', $html);
        $this->assertStringContainsString(' crossorigin="anonymous" muted poster-time="2"></mux-video>', $html);
        $this->assertStringNotContainsString('loop', $html);
        $this->assertStringNotContainsString('accent-color="', $html);
    }

    #[Test]
    public function the_skin_is_a_block_so_a_rounded_box_rounds_its_bottom_corners_too()
    {
        $html = $this->render('{{ popcorn:player :video="video" style="aspect-ratio: 16/9; width: 100%;" }}', ['video' => $this->video()]);

        $this->assertStringContainsString('<video-skin style="display: block; aspect-ratio: 16/9; width: 100%;">', $html);
    }

    #[Test]
    public function a_template_that_sets_its_own_display_keeps_it()
    {
        $html = $this->render('{{ popcorn:player :video="video" style="width: 50%; display: inline-block" }}', ['video' => $this->video()]);

        $this->assertStringContainsString('<video-skin style="width: 50%; display: inline-block">', $html);
    }

    #[Test]
    public function tracking_and_cookies_are_off_unless_the_site_turns_them_on()
    {
        $this->assertStringNotContainsString('mux-data', $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video()]));

        config()->set('statamic-popcorn.player.tracking', 'true');

        $this->assertStringContainsString(
            '<mux-data metadata-video-title="Tour &amp; &quot;more&quot;" metadata-video-id="Asset1" disable-cookies></mux-data></video-skin>',
            $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video()]),
        );

        config()->set('statamic-popcorn.player.cookies', true);

        $this->assertStringContainsString(
            '<mux-data metadata-video-title="Tour &amp; &quot;more&quot;" metadata-video-id="Asset1"></mux-data>',
            $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video()]),
        );
    }

    #[Test]
    public function a_template_can_turn_tracking_on_for_one_player_once_a_visitor_has_agreed()
    {
        $html = $this->render('{{ popcorn:player :video="video" :tracking="consented" metadata-viewer-user-id="u1" }}', [
            'video' => $this->video(),
            'consented' => true,
        ]);

        $this->assertStringContainsString(' metadata-viewer-user-id="u1" disable-cookies></mux-data>', $html);
        $this->assertStringNotContainsString('metadata-viewer-user-id="u1"></mux-video>', $html);

        config()->set('statamic-popcorn.player.tracking', true);

        $html = $this->render('{{ popcorn:player :video="video" :tracking="consented" }}', [
            'video' => $this->video(),
            'consented' => false,
        ]);

        $this->assertStringNotContainsString('mux-data', $html);
    }

    #[Test]
    public function no_video_renders_nothing()
    {
        $this->assertSame('', trim($this->render('{{ popcorn:player :video="video" }}', ['video' => null])));
    }

    #[Test]
    public function the_scripts_tag_loads_video_js_and_mux_data_only_when_tracking_is_on()
    {
        $this->assertSame(
            '<script type="module" src="https://cdn.jsdelivr.net/npm/@videojs/cdn@10.5.0/video.js"></script>'
            .'<script type="module" src="https://cdn.jsdelivr.net/npm/@videojs/cdn@10.5.0/media/mux-video.js"></script>',
            $this->render('{{ popcorn:scripts }}'),
        );

        $this->assertStringEndsWith(
            '<script type="module" src="https://cdn.jsdelivr.net/npm/@videojs/cdn@10.5.0/extensions/mux-data.js"></script>',
            $this->render('{{ popcorn:scripts tracking="true" }}'),
        );

        config()->set('statamic-popcorn.player.cdn', null);

        $this->assertSame('', trim($this->render('{{ popcorn:scripts }}')));
    }
}
