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
    public function the_player_carries_the_playback_id_and_reports_the_title_to_mux_data()
    {
        $this->assertSame(
            '<mux-player playback-id="Play1" stream-type="on-demand" metadata-video-title="Tour &amp; &quot;more&quot;" metadata-video-id="Asset1" style="display: block;"></mux-player>',
            $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video()]),
        );
    }

    #[Test]
    public function the_title_is_drawn_only_when_the_editor_asked()
    {
        $html = $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video(['show_title' => true])]);

        $this->assertStringContainsString(' title="Tour &amp; &quot;more&quot;"', $html);
    }

    #[Test]
    public function other_parameters_become_attributes()
    {
        $html = $this->render('{{ popcorn:player :video="video" accent-color="#0061ff" muted="true" loop="false" }}', ['video' => $this->video()]);

        $this->assertStringContainsString(' accent-color="#0061ff"', $html);
        $this->assertStringContainsString(' muted', $html);
        $this->assertStringNotContainsString('loop', $html);
    }

    #[Test]
    public function the_player_is_a_block_so_a_rounded_box_rounds_its_bottom_corners_too()
    {
        $html = $this->render('{{ popcorn:player :video="video" style="aspect-ratio: 16/9; width: 100%;" }}', ['video' => $this->video()]);

        $this->assertStringContainsString(' style="display: block; aspect-ratio: 16/9; width: 100%;"', $html);
    }

    #[Test]
    public function a_template_that_sets_its_own_display_keeps_it()
    {
        $html = $this->render('{{ popcorn:player :video="video" style="width: 50%; display: inline-block" }}', ['video' => $this->video()]);

        $this->assertStringContainsString(' style="width: 50%; display: inline-block"', $html);
    }

    #[Test]
    public function mux_data_tracks_and_sets_cookies_unless_the_site_turns_them_off()
    {
        $html = $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video()]);

        $this->assertStringNotContainsString('disable-tracking', $html);
        $this->assertStringNotContainsString('disable-cookies', $html);

        config()->set('statamic-popcorn.player.disable_tracking', true);
        config()->set('statamic-popcorn.player.disable_cookies', 'true');

        $html = $this->render('{{ popcorn:player :video="video" }}', ['video' => $this->video()]);

        $this->assertStringContainsString(' disable-tracking', $html);
        $this->assertStringContainsString(' disable-cookies', $html);
    }

    #[Test]
    public function a_template_can_override_the_sites_privacy_defaults()
    {
        config()->set('statamic-popcorn.player.disable_tracking', true);
        config()->set('statamic-popcorn.player.disable_cookies', true);

        // As a consent check would: a variable that is false once the visitor
        // has agreed, and a literal for the other.
        $html = $this->render('{{ popcorn:player :video="video" :disable-tracking="no_consent" disable-cookies="false" }}', [
            'video' => $this->video(),
            'no_consent' => false,
        ]);

        $this->assertStringNotContainsString('disable-tracking', $html);
        $this->assertStringNotContainsString('disable-cookies', $html);
    }

    #[Test]
    public function no_video_renders_nothing()
    {
        $this->assertSame('', trim($this->render('{{ popcorn:player :video="video" }}', ['video' => null])));
    }

    #[Test]
    public function the_scripts_tag_loads_mux_player()
    {
        $this->assertSame(
            '<script src="https://cdn.jsdelivr.net/npm/@mux/mux-player@3" defer></script>',
            $this->render('{{ popcorn:scripts }}'),
        );
    }
}
