<?php

namespace D3Creative\Popcorn\Mux;

use D3Creative\Popcorn\Exceptions\MissingCredentials;
use D3Creative\Popcorn\Exceptions\MuxRequestFailed;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * The parts of the Mux Video API that Popcorn uses. Every method answers with
 * Mux's own `data`, or throws a PopcornException with a message fit to show.
 */
class MuxClient
{
    public function __construct(protected array $config) {}

    public function configured(): bool
    {
        return filled($this->config['token_id'] ?? null) && filled($this->config['token_secret'] ?? null);
    }

    /**
     * A direct upload: a one-off URL the browser (or this server) PUTs a file
     * to. Mux makes the asset once the file has arrived.
     *
     * @return array{id: string, url: string}
     */
    public function createUpload(string $corsOrigin, ?string $title = null): array
    {
        $settings = ['playback_policy' => ['public']];

        if ($quality = $this->config['video_quality'] ?? null) {
            $settings['video_quality'] = $quality;
        }

        if (filled($title)) {
            $settings['meta'] = ['title' => $title];
        }

        $data = $this->send('create an upload', fn ($http) => $http->post('/video/v1/uploads', [
            'new_asset_settings' => $settings,
            'cors_origin' => $corsOrigin,
        ]));

        return ['id' => $data['id'], 'url' => $data['url']];
    }

    public function upload(string $id): array
    {
        return $this->send('check the upload', fn ($http) => $http->get('/video/v1/uploads/'.rawurlencode($id)));
    }

    public function asset(string $id): array
    {
        return $this->send('find the video', fn ($http) => $http->get('/video/v1/assets/'.rawurlencode($id)));
    }

    /**
     * One page of the account's videos, newest first, with the cursor for the
     * next page (null on the last).
     *
     * @return array{data: array<int, array>, next_cursor: ?string}
     */
    public function assets(?string $cursor = null): array
    {
        $query = ['limit' => (int) ($this->config['per_page'] ?? 50)];

        if (filled($cursor)) {
            $query['cursor'] = $cursor;
        }

        $response = $this->request('list videos', fn ($http) => $http->get('/video/v1/assets', $query));

        return [
            'data' => (array) $response->json('data', []),
            'next_cursor' => $response->json('next_cursor'),
        ];
    }

    public function rename(string $id, string $title): array
    {
        return $this->send('rename the video', fn ($http) => $http->patch('/video/v1/assets/'.rawurlencode($id), [
            'meta' => ['title' => $title],
        ]));
    }

    protected function send(string $doing, \Closure $call): array
    {
        return (array) $this->request($doing, $call)->json('data', []);
    }

    protected function request(string $doing, \Closure $call): Response
    {
        if (! $this->configured()) {
            throw new MissingCredentials;
        }

        try {
            $response = $call($this->http());
        } catch (ConnectionException) {
            throw MuxRequestFailed::unreachable($doing);
        }

        if ($response->failed()) {
            throw MuxRequestFailed::from($response, $doing);
        }

        return $response;
    }

    protected function http(): PendingRequest
    {
        return Http::withBasicAuth($this->config['token_id'], $this->config['token_secret'])
            ->baseUrl(rtrim($this->config['base_url'] ?? 'https://api.mux.com', '/'))
            ->acceptJson()
            ->asJson()
            ->timeout(20);
    }
}
