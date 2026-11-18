# Popcorn

Upload, choose and play [Mux](https://www.mux.com) videos from a Statamic fieldtype, including videos already in your asset library.

Popcorn adds a **Popcorn Video** field. Editors upload a video from their computer straight to Mux, send one that is already in an asset container, or pick one that is already in the Mux account. The field stores the Mux IDs and a title, and the `{{ popcorn:player }}` tag plays it on the front end with Mux Player.

Mux is the only video host for now. The field is built so that others can be added beside it without changing content saved today.

## Requirements

- Statamic 6
- PHP 8.3 or later
- A Mux account and an access token with **Mux Video** set to **Read and Write**

## Installation

```bash
composer require d3creative/statamic-popcorn
```

Create an access token at dashboard.mux.com under **Settings → Access Tokens** and add both halves to `.env`:

```
MUX_TOKEN_ID=your-token-id
MUX_TOKEN_SECRET=your-token-secret
```

The token is only ever read on the server. It is never sent to the browser.

Then add a **Popcorn Video** field to any blueprint or fieldset. It is listed under Media.

## Permissions

Super users can use Popcorn straight away. Everyone else needs **Upload, choose and rename videos with Popcorn** (`use popcorn`). Uploading costs money in Mux and browsing shows every video in the account, so neither comes with being able to edit an entry.

Without the permission, an editor still sees the video already in a field and can change its title and whether the title is shown. They cannot upload, browse, or rename the video in Mux.

To send a video from the asset library, a user also needs to be able to view the container it is in.

## Using the field

| Button | What it does |
|---|---|
| Upload | Asks for a title, then takes a file by drag and drop or file picker and uploads it from the browser straight to Mux. It never passes through your server, so PHP's upload limits do not apply. |
| Choose from Assets | Opens Statamic's own asset browser on any container you can view. Choose a video file and your server sends a copy to Mux. The file stays in your library. |
| Browse Mux | Lists the videos in your Mux account that are ready to play, in a grid or a table, with search by title. Double-click a video to choose it straight away. |

Once a video is chosen the field shows it in Mux Player, with:

- **Title**, which is also updated in the Mux dashboard when you leave the box.
- **Show Title**, which draws the title on the player on the front end.
- **Remove**, to clear the field so another video can be chosen. Turn off **Allow Replacing** in the field's settings to hide it. Removing a video from the field does not delete it from Mux.

A new upload is saved into the field as soon as Mux has created it, so the entry can be saved straight away. While Mux is still preparing the video the field says so, and the player starts working when it is ready.

### Sending a video from the asset library

The file is sent from your server to Mux, rather than Mux fetching it from a public URL. That means it works for private containers, for sites Mux cannot reach (a local `.test` domain, a staging site behind a password) and for disks without public URLs.

It runs after the page has had its answer, so no queue worker is needed. A large video can take a few minutes to send; the field shows progress until Mux has it. If the send fails, the field says so.

### Browsing Mux

Mux's API has no search, so Popcorn filters titles itself. When you type a search, any videos not yet loaded are fetched so the whole account is searched, up to the newest thousand or so. Only videos that are ready and have a public playback ID are listed. The grid or table choice is remembered for each user.

## Templates

The field gives you:

| Key | Value |
|---|---|
| `playback_id` | Mux's public playback ID |
| `asset_id` | Mux's asset ID |
| `title` | The title, or null |
| `show_title` | Whether the editor asked for the title to be shown |
| `stream_url` | The HLS stream, `https://stream.mux.com/{playback_id}.m3u8` |
| `thumbnail_url` | A WebP thumbnail from Mux |
| `gif_url` | An animated GIF preview from Mux |
| `provider` | Always `mux` for now |

### With the player tag

Load Mux Player once, in your layout's `<head>`:

```antlers
{{ popcorn:scripts }}
```

Then play a field:

```antlers
{{ popcorn:player :video="hero_video" accent-color="#0061ff" }}
```

Every parameter other than `video` becomes an attribute on `<mux-player>`, so anything [Mux Player supports](https://www.mux.com/docs/guides/player-api-reference) can be set from the template. A parameter set to `true` becomes a bare attribute (`muted="true"` gives `muted`); `false` leaves it out.

The title is always sent to Mux Data as `metadata-video-title`, so views are reported against it. It is only drawn on the player when **Show Title** is on.

`{{ popcorn:scripts }}` loads Mux Player from jsDelivr. To load it from somewhere else, publish the config and change `player.script`. If your site already bundles `@mux/mux-player`, you do not need the tag at all.

### Writing the element yourself

```antlers
{{ hero_video }}
    <mux-player
        playback-id="{{ playback_id }}"
        metadata-video-title="{{ title }}"
        {{ if show_title }}title="{{ title }}"{{ /if }}
    ></mux-player>
{{ /hero_video }}
```

### GraphQL

The field is a `PopcornVideo` type with the same keys.

## Configuration

Publish the config to change the defaults:

```bash
php artisan vendor:publish --tag=statamic-popcorn-config
```

| Setting | Default | What it does |
|---|---|---|
| `mux.token_id`, `mux.token_secret` | `MUX_TOKEN_ID`, `MUX_TOKEN_SECRET` | Your Mux access token |
| `mux.video_quality` | `POPCORN_MUX_VIDEO_QUALITY`, or your Mux account's default | `basic`, `plus` or `premium` for videos Popcorn creates. It changes what Mux charges, so check Mux's pricing first |
| `mux.per_page` | `50` | How many videos Browse Mux asks for at a time (Mux allows up to 100) |
| `player.script` | jsDelivr's `@mux/mux-player@3` | Where `{{ popcorn:scripts }}` loads Mux Player from |
| `poll_interval` | `3000` | Milliseconds between status checks while Mux receives or prepares a video |

## What Popcorn does not do

- **Signed playback.** Videos are created with a public playback policy, and videos with only signed playback IDs are not listed.
- **Delete videos in Mux.** Removing a video from a field leaves it in your Mux account. Delete it in the Mux dashboard if you no longer need it.
- **Live streams.** Popcorn handles on-demand videos only.

## Support

Report bugs and request features on [GitHub](https://github.com/d3creativeuk/statamic-popcorn/issues).

## Licence

MIT. See [LICENSE](LICENSE).
