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
    | Videos play in Video.js, the player Mux recommends, with its Mux media
    | element. {{ popcorn:scripts }} serves it from your own site: it is built
    | into the add-on and published with it to public/vendor/statamic-popcorn.
    | That way visitors' browsers contact no one but Mux.
    |
    | "cdn" loads it from an @videojs/cdn release instead, for example
    | https://cdn.jsdelivr.net/npm/@videojs/cdn@10.5.0. Keep the exact
    | version: every file in a release imports shared chunks of that same
    | release. A CDN is a third party that sees every visitor's IP address,
    | so leave this null unless you have a reason to use one.
    |
    | "tracking" turns on Mux Data, which reports each view to Mux, and
    | "cookies" lets it set its muxData cookie to recognise returning
    | viewers. Both are off unless you turn them on, because in the UK and EU
    | they usually need a visitor's consent. A template can override either
    | for one player, for example once a visitor has agreed.
    |
    | "border_radius" rounds the player's corners: any CSS length, or a
    | number of pixels. Null keeps the Video.js skin's 28px. A template's
    | border-radius parameter overrides it for one player.
    |
    */

    'player' => [
        'cdn' => env('POPCORN_PLAYER_CDN'),
        'tracking' => env('POPCORN_TRACKING', false),
        'cookies' => env('POPCORN_TRACKING_COOKIES', false),
        'border_radius' => env('POPCORN_PLAYER_BORDER_RADIUS'),
    ],

    // Milliseconds between status checks while Mux receives or processes a
    // video.
    'poll_interval' => 3000,

];
