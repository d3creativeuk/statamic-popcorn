<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mux
    |--------------------------------------------------------------------------
    |
    | An access token from dashboard.mux.com > Settings > Access Tokens, with
    | Mux Video set to Read and Write. Both halves are only ever read on the
    | server; neither is sent to the browser.
    |
    | "video_quality" is the quality level for videos Popcorn creates: basic,
    | plus or premium. Leave it null to use your Mux account's default. It
    | changes what Mux charges for encoding and storage, so check Mux's
    | pricing before raising it.
    |
    | "per_page" is how many videos the Browse Mux panel asks Mux for at a
    | time. Mux allows up to 100.
    |
    */

    'mux' => [
        'token_id' => env('MUX_TOKEN_ID'),
        'token_secret' => env('MUX_TOKEN_SECRET'),
        'base_url' => env('POPCORN_MUX_BASE_URL', 'https://api.mux.com'),
        'video_quality' => env('POPCORN_MUX_VIDEO_QUALITY'),
        'per_page' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Player
    |--------------------------------------------------------------------------
    |
    | Where {{ popcorn:scripts }} loads Mux Player from on the front end. Sites
    | that bundle @mux/mux-player themselves don't need the tag at all.
    |
    */

    'player' => [
        'script' => 'https://cdn.jsdelivr.net/npm/@mux/mux-player@3',

        // Mux Player reports every view to Mux Data and sets a "muxData"
        // cookie unless told not to. These are the defaults for every
        // {{ popcorn:player }}; a template can override either one, for
        // example to turn tracking on only once a visitor has consented.
        'disable_tracking' => env('POPCORN_DISABLE_TRACKING', false),
        'disable_cookies' => env('POPCORN_DISABLE_COOKIES', false),
    ],

    // Milliseconds between status checks while Mux receives or processes a
    // video.
    'poll_interval' => 3000,

];
