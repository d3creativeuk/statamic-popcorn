<?php

namespace D3Creative\Popcorn\Tests\Feature;

use D3Creative\Popcorn\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class PermissionsTest extends TestCase
{
    public static function routes(): array
    {
        return [
            'create upload' => ['postJson', 'popcorn.mux.uploads.store', null],
            'send library video' => ['postJson', 'popcorn.mux.uploads.from-asset', null],
            'upload status' => ['getJson', 'popcorn.mux.uploads.show', 'Upload1'],
            'list videos' => ['getJson', 'popcorn.mux.assets.index', null],
            'one video' => ['getJson', 'popcorn.mux.assets.show', 'Asset1'],
            'rename' => ['patchJson', 'popcorn.mux.assets.title', 'Asset1'],
        ];
    }

    #[Test]
    #[DataProvider('routes')]
    public function editing_content_is_not_enough_to_reach_mux(string $method, string $route, ?string $id)
    {
        Http::fake();

        $this->actingAs($this->userWith(['view assets assets']))
            ->{$method}(cp_route($route, $id ? [$id] : []), ['title' => 'Tour', 'asset' => 'assets::a.mp4'])
            ->assertForbidden();

        Http::assertNothingSent();
    }

    #[Test]
    public function the_popcorn_permission_is_enough()
    {
        Http::fake([$this->muxUrl('/video/v1/assets*') => Http::response(['data' => [], 'next_cursor' => null])]);

        $this->actingAs($this->userWith(['use popcorn']))
            ->getJson(cp_route('popcorn.mux.assets.index'))
            ->assertOk();
    }
}
