<?php

namespace D3Creative\Popcorn\Http\Controllers;

use D3Creative\Popcorn\Assets\VideoAssets;
use D3Creative\Popcorn\Jobs\SendAssetToMux;
use D3Creative\Popcorn\Mux\MuxClient;
use D3Creative\Popcorn\Mux\MuxVideo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Statamic\Facades\Asset;
use Statamic\Facades\User;

class MuxUploadController extends Controller
{
    public function __construct(protected MuxClient $mux) {}

    /**
     * A direct upload URL for the browser to send a file from the computer.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $upload = $this->mux->createUpload($this->origin($request), trim($data['title']));

        return response()->json(['upload_id' => $upload['id'], 'url' => $upload['url']]);
    }

    /**
     * Send a video that is already in the asset library. The file goes from
     * this server to Mux once the response is sent (SendAssetToMux), and the
     * field polls show() as it would for a browser upload.
     */
    public function fromAsset(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset' => ['required', 'string', 'max:1024'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $asset = Asset::find($data['asset']);

        // Not found and not allowed look the same, so the answer says nothing
        // about what is in a container the user cannot see.
        if (! $asset || ! VideoAssets::viewable(User::current(), $asset)) {
            throw ValidationException::withMessages(['asset' => __('That asset could not be found.')]);
        }

        if (! VideoAssets::isVideo($asset)) {
            throw ValidationException::withMessages(['asset' => __(':file is not a video file.', ['file' => $asset->basename()])]);
        }

        $title = filled($data['title'] ?? null)
            ? trim($data['title'])
            : ($asset->get('title') ?: pathinfo($asset->basename(), PATHINFO_FILENAME));

        $upload = $this->mux->createUpload($this->origin($request), $title);

        SendAssetToMux::dispatchAfterResponse($asset->id(), $upload['id'], $upload['url']);

        return response()->json(['upload_id' => $upload['id'], 'title' => $title]);
    }

    /**
     * Where an upload has got to. Once Mux has made the asset, its playback ID
     * is known straight away, although the video only plays once the asset's
     * own status is "ready".
     */
    public function show(string $upload): JsonResponse
    {
        if ($failure = SendAssetToMux::failure($upload)) {
            return response()->json(['status' => 'errored', 'error' => $failure]);
        }

        $data = $this->mux->upload($upload);
        $status = $data['status'] ?? 'waiting';

        $result = [
            'status' => $status,
            'asset_id' => $data['asset_id'] ?? null,
            'playback_id' => null,
            'asset_status' => null,
            'error' => $data['error']['message'] ?? null,
        ];

        if ($result['asset_id']) {
            $asset = $this->mux->asset($result['asset_id']);

            $result['playback_id'] = MuxVideo::playbackId($asset);
            $result['asset_status'] = $asset['status'] ?? null;
            $result['error'] ??= $asset['errors']['messages'][0] ?? null;
        }

        return response()->json($result);
    }

    /**
     * Mux only accepts browser uploads from the origin named when the upload
     * is created. The Control Panel's own host is the one the browser is on,
     * which is not always APP_URL (a staging copy, a second domain).
     */
    protected function origin(Request $request): string
    {
        return $request->getSchemeAndHttpHost();
    }
}
