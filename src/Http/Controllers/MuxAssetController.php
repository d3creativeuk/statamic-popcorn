<?php

namespace D3Creative\Popcorn\Http\Controllers;

use D3Creative\Popcorn\Mux\MuxClient;
use D3Creative\Popcorn\Mux\MuxVideo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MuxAssetController extends Controller
{
    public function __construct(protected MuxClient $mux) {}

    /**
     * One page of videos that can be played, newest first. Mux has no search,
     * so the panel filters by title itself and asks for further pages when a
     * search needs them.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate(['cursor' => ['nullable', 'string', 'max:512']]);

        $page = $this->mux->assets($request->query('cursor'));

        return response()->json([
            'assets' => collect($page['data'])
                ->filter(fn ($asset) => MuxVideo::playable($asset))
                ->map(fn ($asset) => MuxVideo::summary($asset))
                ->values(),
            'next_cursor' => $page['next_cursor'] ?: null,
        ]);
    }

    /**
     * One video, for following a new upload until Mux has finished preparing
     * it.
     */
    public function show(string $video): JsonResponse
    {
        return response()->json(MuxVideo::summary($this->mux->asset($video)));
    }

    /**
     * Keep the title in the Mux dashboard the same as the one in the field.
     */
    public function title(Request $request, string $video): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $this->mux->rename($video, trim($data['title']));

        return response()->json(['title' => trim($data['title'])]);
    }
}
