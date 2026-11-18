<?php

namespace D3Creative\Popcorn\Jobs;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Statamic\Facades\Asset;
use Throwable;

/**
 * Sends a video from the asset library to a Mux direct upload URL.
 *
 * This server pushes the file rather than giving Mux the asset's URL to fetch,
 * so it works for private containers, for sites Mux cannot reach (a local .test
 * domain, a staging site behind a password) and for disks without public URLs.
 * It runs after the response is sent, so no queue worker is needed and the
 * field polls the upload's status meanwhile.
 *
 * Mux only learns that something went wrong if the file arrives, so a failure
 * here is recorded for the status endpoint to report. Without that the upload
 * would sit at "waiting" until Mux timed it out an hour later.
 */
class SendAssetToMux
{
    use Dispatchable;

    public function __construct(
        public string $assetId,
        public string $uploadId,
        public string $uploadUrl,
    ) {}

    public function handle(): void
    {
        // A large video can take minutes to send. The request has already been
        // answered, so nobody is waiting on this process.
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        ignore_user_abort(true);

        try {
            $asset = Asset::find($this->assetId);

            if (! $asset) {
                static::fail($this->uploadId, 'The video was moved or deleted before it could be sent to Mux.');

                return;
            }

            $stream = $asset->stream();

            $response = Http::withBody(Utils::streamFor($stream), $asset->mimeType() ?: 'application/octet-stream')
                // Some storage drivers cannot say how long their stream is, and
                // Mux's upload URL wants a length rather than a chunked body.
                ->withHeaders(['Content-Length' => (string) $asset->size()])
                ->timeout(0)
                ->put($this->uploadUrl);

            if ($response->failed()) {
                Log::warning('Popcorn could not send an asset to Mux.', ['asset' => $this->assetId, 'upload' => $this->uploadId, 'status' => $response->status()]);
                static::fail($this->uploadId, 'Mux did not accept the file. Try again, or upload it from your computer instead.');
            }
        } catch (Throwable $e) {
            Log::warning('Popcorn could not send an asset to Mux.', ['asset' => $this->assetId, 'upload' => $this->uploadId, 'exception' => $e->getMessage()]);
            static::fail($this->uploadId, 'The video could not be sent to Mux. Try again, or upload it from your computer instead.');
        }
    }

    public static function fail(string $uploadId, string $message): void
    {
        Cache::put(static::key($uploadId), $message, now()->addDay());
    }

    public static function failure(string $uploadId): ?string
    {
        return Cache::get(static::key($uploadId));
    }

    protected static function key(string $uploadId): string
    {
        return 'popcorn.mux.upload-failed.'.$uploadId;
    }
}
