<?php

namespace D3Creative\Popcorn\Exceptions;

use Illuminate\Http\Client\Response;

class MuxRequestFailed extends PopcornException
{
    public function __construct(string $message, public readonly ?int $muxStatus = null)
    {
        parent::__construct($message);
    }

    /**
     * Mux answers errors with {"error": {"type": "...", "messages": [...]}}.
     * Its messages are safe to show and say what was wrong, so the first one
     * is kept. A rejected token gets a message of its own, since Mux's
     * "Unauthorized request" does not say which side is wrong.
     */
    public static function from(Response $response, string $doing): self
    {
        if (in_array($response->status(), [401, 403])) {
            return new self('Mux refused the access token. Check MUX_TOKEN_ID and MUX_TOKEN_SECRET, and that the token has Mux Video write access.', $response->status());
        }

        if ($response->status() === 404) {
            return new self('Mux could not find that video. It may have been deleted in the Mux dashboard.', 404);
        }

        $detail = $response->json('error.messages.0');

        return new self(
            trim("Mux could not {$doing}.".(is_string($detail) && $detail !== '' ? ' '.$detail : '')),
            $response->status(),
        );
    }

    public static function unreachable(string $doing): self
    {
        return new self("Mux could not be reached to {$doing}. Try again in a moment.");
    }

    public function status(): int
    {
        // Mux's 404 means the video is gone, which is the page's answer too.
        // Anything else went wrong upstream.
        return $this->muxStatus === 404 ? 404 : 502;
    }
}
