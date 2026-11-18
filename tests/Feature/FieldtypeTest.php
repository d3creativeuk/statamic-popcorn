<?php

namespace D3Creative\Popcorn\Tests\Feature;

use D3Creative\Popcorn\Fieldtypes\Popcorn;
use D3Creative\Popcorn\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Fields\Field;

class FieldtypeTest extends TestCase
{
    protected function fieldtype(array $config = [])
    {
        return (new Field('video', array_merge(['type' => 'popcorn'], $config)))->fieldtype();
    }

    #[Test]
    public function it_is_registered_as_popcorn()
    {
        $this->assertInstanceOf(Popcorn::class, $this->fieldtype());
    }

    #[Test]
    public function the_stored_value_is_the_ids_title_and_switch()
    {
        $this->assertSame([
            'asset_id' => 'Asset1',
            'playback_id' => 'Play1',
            'title' => 'Tour',
            'show_title' => true,
        ], $this->fieldtype()->process([
            'asset_id' => 'Asset1',
            'playback_id' => 'Play1',
            'title' => '  Tour ',
            'show_title' => true,
            'thumbnail' => 'not kept',
        ]));
    }

    #[Test]
    public function no_playback_id_is_no_video()
    {
        $this->assertNull($this->fieldtype()->process(null));
        $this->assertNull($this->fieldtype()->process(['asset_id' => 'Asset1', 'playback_id' => '']));
        $this->assertNull($this->fieldtype()->preProcess('a string'));
    }

    #[Test]
    public function content_saved_by_the_old_mux_uploader_field_still_loads()
    {
        // The shape d3creative.uk stored before Popcorn existed.
        $old = [
            'asset_id' => 'iRNahwF801sW900XekrTMt4zpBzn01ljDndDdGwggZj00jM',
            'playback_id' => 'Z102VAYroUAJdb6t01MPXx6W00NjG02HaoQr2vtv6fBjLIc',
            'title' => 'Statamic maintenance email signup v3',
            'show_title' => false,
        ];

        $this->assertSame($old, $this->fieldtype()->preProcess($old));
        $this->assertSame('Z102VAYroUAJdb6t01MPXx6W00NjG02HaoQr2vtv6fBjLIc', $this->fieldtype()->augment($old)['playback_id']);
    }

    #[Test]
    public function templates_get_the_mux_urls()
    {
        $this->assertSame([
            'provider' => 'mux',
            'asset_id' => 'Asset1',
            'playback_id' => 'Play1',
            'title' => null,
            'show_title' => false,
            'stream_url' => 'https://stream.mux.com/Play1.m3u8',
            'thumbnail_url' => 'https://image.mux.com/Play1/thumbnail.webp',
            'gif_url' => 'https://image.mux.com/Play1/animated.gif',
        ], $this->fieldtype()->augment(['asset_id' => 'Asset1', 'playback_id' => 'Play1']));

        $this->assertNull($this->fieldtype()->augment(null));
    }

    #[Test]
    public function the_preload_tells_the_field_what_the_user_may_do()
    {
        $this->container();

        $this->actingAs($this->userWith(['view assets assets']));
        $meta = $this->fieldtype()->preload();

        $this->assertFalse($meta['can']);
        $this->assertTrue($meta['configured']);
        $this->assertSame([], $meta['containers']);
        $this->assertStringEndsWith('/popcorn/mux/uploads/__id__', $meta['urls']['uploadStatus']);

        $this->actingAs($this->userWith(['use popcorn', 'view assets assets'], 'uploader'));
        $meta = $this->fieldtype()->preload();

        $this->assertTrue($meta['can']);
        $this->assertSame([], $meta['containers'], 'The asset library is off unless the field turns it on');

        $meta = $this->fieldtype(['allow_assets' => true])->preload();

        $this->assertSame('assets', $meta['containers'][0]['handle']);
        $this->assertFalse($meta['containers'][0]['browser']['can_upload']);
        $this->assertFalse($meta['containers'][0]['browser']['can_create_folders']);
    }

    #[Test]
    public function the_listing_shows_the_title()
    {
        $this->assertSame('Tour', $this->fieldtype()->preProcessIndex(['playback_id' => 'Play1', 'title' => 'Tour']));
    }
}
