<?php

namespace D3Creative\Popcorn\Exceptions;

use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * Something the person in the Control Panel can be told in plain words. The
 * field shows the message as it is, so it never carries credentials or a raw
 * response.
 */
abstract class PopcornException extends RuntimeException
{
    abstract public function status(): int;

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], $this->status());
    }
}
