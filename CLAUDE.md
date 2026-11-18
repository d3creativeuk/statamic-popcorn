# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Statamic 6 addon (Popcorn) for third-party video hosting. Today that is Mux only: one fieldtype (`popcorn`, `src/Fieldtypes/Popcorn.php`), JSON endpoints in the CP for it (`routes/cp.php`, all behind `can:use popcorn`), and a front-end tag (`src/Tags/Popcorn.php`). It began as the `mux_uploader` fieldtype inside d3creative.uk and was moved out on 18 November 2026; content saved by that field loads unchanged (`FieldtypeTest::content_saved_by_the_old_mux_uploader_field_still_loads`).

This repo is the standalone addon (PHP + compiled JS). The host Statamic site used for development is the sibling project at `../d3creative`, and `package.json` resolves `@statamic/cms` via `file:../d3creative/vendor/statamic/cms/resources/dist-package`.

**The host is linked by a Composer path repository** (`../statamic-popcorn`, symlinked), so PHP changes here are live in the host immediately. That `repositories` entry and the `@dev` requirement in the host's `composer.json` must not be committed or deployed: production has no sibling directory. Switch the host to a tagged release first.

## Build and publish

```bash
npm install            # one-time
npm run build          # vite production build to public/build/
npm run dev            # vite build --watch (no HMR)
```

`npm run build` writes only to this addon's `public/build/`. The host serves a **copy** at `d3creative/public/vendor/statamic-popcorn/build/`. After every build, republish from the host:

```bash
cd ../d3creative && php artisan vendor:publish --tag=statamic-popcorn --force
```

Use `--tag`, not `--provider`: the provider form would also force-overwrite a published config. Publishing does not remove old hashed files, so delete any in the host copy that the manifest no longer lists.

## Tests

```bash
composer install
vendor/bin/phpunit
```

`tests/TestCase.php` extends Statamic's `AddonTestCase`. No test reaches Mux: `Http::preventStrayRequests()` is on, and responses come from `tests/__fixtures__/mux/*.json`. The asset fixtures are a real `GET /video/v1/assets` response captured on 18 November 2026 with the IDs replaced; the upload and error fixtures follow Mux's documented shapes and were not captured.

Things that will bite when writing tests:

- **Set addon config after boot** (`config()->set()` in `setUp`), not in `getEnvironmentSetUp`. Statamic merges the addon config one level deep.
- **`SendAssetToMux` runs inside the request.** `dispatchAfterResponse()` fires when the test kernel terminates, so by the time `postJson()` returns the PUT to Mux has already happened.
- **`Antlers::parse()` treats its input as untrusted user content** unless the third argument is `true`, and untrusted content may only use core's default tags. `TagTest` passes `true`; without it every Popcorn tag renders as an empty string.

The Vue side has no tests. Verify it in the host CP.

## Architecture

- **`src/Mux/MuxClient`**: the Mux Video API calls Popcorn makes. Every method returns Mux's `data` or throws a `PopcornException` whose message is safe to show; those render themselves as `{message}` JSON with the right status (`MissingCredentials` 503, `MuxRequestFailed` 502, or 404 when Mux says the video is gone).
- **`src/Mux/MuxVideo`**: shapes a Mux asset for the Browse panel and builds the stream and image URLs. `playbackId()` takes the first **public** playback ID only.
- **`src/Http/Controllers`**: `MuxUploadController` (create a direct upload, send a library asset, upload status) and `MuxAssetController` (list, one video, rename).
- **`src/Jobs/SendAssetToMux`**: PUTs a library asset's stream to a direct upload URL after the response. Failures go in the cache (`popcorn.mux.upload-failed.{id}`, a day) because Mux never hears about a file that did not arrive; the status endpoint checks there first.
- **`src/Assets/VideoAssets`**: which files count as video, and the container data core's `<asset-browser>` needs (built by calling the core Assets fieldtype's `preload()`, as Darkroom does).
- **`resources/js`**: `PopcornFieldtype.vue` (composition API via `Fieldtype.use`) owns the value, uploads and polling; `MuxBrowser.vue` is the Browse Mux drawer; `AssetPicker.vue` is core's asset browser in a Stack, single selection, videos only. `composables/muxElements.js` lazy-loads the player (Video.js with `@videojs/html/media/mux-video`, about 1.2 MB with hls.js) and Mux Uploader so they are fetched only when a field is on screen. The CP player never loads Mux Data, so previews are not counted as views.

## Decisions worth knowing before changing things

- **Video.js, not Mux Player.** Mux put Mux Player for Web in maintenance mode and recommends Video.js 10 with its Mux packages. Both define `<mux-video>`, so they cannot share a page. The CDN version in `player.cdn` must be exact: a release's files import its own shared chunks.
- **Tracking and cookies are off by default.** In Video.js, Mux Data only runs when a `<mux-data>` element is present, so the tag leaves it out unless `player.tracking` (or a `tracking` parameter) is on, and adds `disable-cookies` unless `player.cookies` is on. `popcorn:scripts` only loads `extensions/mux-data.js` when tracking is on.

- **One fieldtype for every host.** The handle is `popcorn`, not `mux_*`. A stored value without a `provider` key is Mux. Adding a host means adding a `provider` config option (default `mux`) and a sibling of `src/Mux`, not a new fieldtype, so existing content keeps working.
- **Library videos are pushed, not pulled.** The old in-site code gave Mux `$asset->absoluteUrl()` to fetch, which fails for private disks and for any site Mux cannot reach (every local `.test` site). The job streams the file with an explicit `Content-Length`, because some storage drivers cannot size their streams and the upload URL wants a length rather than a chunked body.
- **The upload's CORS origin is the request's host**, not `APP_URL`, since the browser uploads from whatever host the CP is on.
- **CP route parameters must not be called `{asset}`.** Statamic binds that name to a Statamic asset in CP routes (`RouteServiceProvider::bindAssets`) and throws on anything else. Mux asset routes use `{video}`.
- **A new upload is stored as soon as it has a playback ID**, before Mux has finished preparing it, so an editor can save the entry straight away. The field then polls `assets/{video}` until it is `ready` and remounts the player.
- **Mux has no search.** `MuxBrowser` filters titles client-side and, while a search is typed, fetches the remaining pages (at most 20 pages of `per_page`). It says when that limit cut the search short.
- **Polling stops on 401, 403, 404 and 419** (a session or permission problem will not fix itself), and after five failures in a row of anything else, showing the last message.
- **Plan limits are not documented by Mux.** Neither the status nor the wording of the refusal at the free plan's 10-video cap is published, and it was not captured live (the account had 9 videos on 18 November 2026, and testing would have used the last slot). So `MuxRequestFailed` only blames the token for a 401 or a 403 with nothing more than "Unauthorized" or "Forbidden"; any other reason is shown, and `withAdvice()` adds upgrade-or-delete advice to any message mentioning a limit, plan, upgrade, quota or maximum, including `errored` upload and asset messages from the status endpoints. Mux Uploader reports a refused endpoint as "Failed to get endpoint: …", so the field keeps the server's message instead (`refusal` in `PopcornFieldtype.vue`). When the real response is seen, capture it as a fixture and tighten the tests.
- **Permissions:** everything in `routes/cp.php` needs `use popcorn`. Sending a library video also needs `view` on that asset; a missing asset and one the user cannot view get the same message.

## Statamic 6 gotchas

- Config file must be named after the addon slug: `config/statamic-popcorn.php`, read as `config('statamic-popcorn.*')`.
- `vite.config.js` must use `@statamic/cms/vite-plugin` so Vue is shared with the CP. Its `vue` option carries `isCustomElement` for `mux-*` tags.
- **Only Tailwind classes already in the CP bundle work.** Custom layout goes in `<style scoped>`.
- Axios is not exported to addons. `composables/useHttp.js` uses `fetch` with the CSRF token from `Statamic.$config.get('csrfToken')`.
- Icons come from Statamic's set by name, and many familiar names are missing: there is no `video` or `check`. Popcorn uses `fieldtype-video`, `folder-play`, `assets`, `upload`, `trash` and `checkmark`. Check `vendor/statamic/cms/resources/svg/icons/` in the host first.
- **`Statamic.$toast` renders its message as HTML.** Anything someone could have typed (a title, a filename, a message from Mux) goes through `escapeHtml()` from `format.js`.
- Bind a function to a custom element with `.prop` (`:endpoint.prop`); Vue otherwise sets an attribute when the element is not defined yet, which it is not until `muxElements.js` has loaded it.
