<?php

namespace D3Creative\Popcorn\Exceptions;

class MissingCredentials extends PopcornException
{
    public function __construct()
    {
        parent::__construct('Popcorn has no Mux access token. Add MUX_TOKEN_ID and MUX_TOKEN_SECRET to .env.');
    }

    public function status(): int
    {
        return 503;
    }
}
