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
     * is kept.
     *
     * A 401, or a 403 with nothing more than "Unauthorized" or "Forbidden",
     * is the token, and gets a message saying which settings to check. A 403
     * that gives a reason is something else, such as a plan limit, and the
     * reason is shown: blaming the token there would send someone looking in
     * the wrong place.
     */
    public static function from(Response $response, string $doing): self
    {
        $status = $response->status();
        $detail = static::detail($response);

        if ($status === 401 || ($status === 403 && ! static::explains($detail))) {
            return new self('Mux refused the access token. Check MUX_TOKEN_ID and MUX_TOKEN_SECRET, and that the token has Mux Video write access.', $status);
        }

        if ($status === 404) {
            return new self('Mux could not find that video. It may have been deleted in the Mux dashboard.', 404);
        }

        return new self(static::withAdvice(trim("Mux could not {$doing}.".($detail ? ' '.$detail : ''))), $status);
    }

    public static function unreachable(string $doing): self
    {
        return new self("Mux could not be reached to {$doing}. Try again in a moment.");
    }

    /**
     * Mux's wording for a plan limit is not documented, so any reason that
     * talks about a limit, the plan or upgrading gets a pointer to what can
     * be done about it. Also used for an upload or video that Mux marked as
     * errored, which is where a limit may show instead.
     */
    public static function withAdvice(?string $message): ?string
    {
        if ($message && preg_match('/\b(limit|limits|plan|upgrade|quota|exceed(?:s|ed)?|maximum)\b/i', $message)) {
            return rtrim($message, '. ').'. Your Mux plan may have reached its limit: upgrade it, or delete videos you no longer need, at dashboard.mux.com.';
        }

        return $message;
    }

    public function status(): int
    {
        // Mux's 404 means the video is gone, which is the page's answer too.
        // Anything else went wrong upstream.
        return $this->muxStatus === 404 ? 404 : 502;
    }

    protected static function detail(Response $response): ?string
    {
        $detail = $response->json('error.messages.0');

        return is_string($detail) && trim($detail) !== '' ? trim($detail) : null;
    }

    protected static function explains(?string $detail): bool
    {
        return $detail !== null && ! preg_match('/^(unauthori[sz]ed|forbidden)( request)?\.?$/i', $detail);
    }
}
