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
            '<mux-player playback-id="Play1" stream-type="on-demand" metadata-video-title="Tour &amp; &quot;more&quot;" metadata-video-id="Asset1"></mux-player>',
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
