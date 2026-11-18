# Changelog

All notable changes to `d3creative/statamic-popcorn` are documented here.

This project follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and
[Semantic Versioning](https://semver.org/spec/v2.0.0.html). Releases are git-tag driven
(`composer.json` carries no `version` field).

## [Unreleased]

### Added

- **Popcorn Video fieldtype** (`popcorn`) for Mux videos: upload from the computer straight to Mux, send a video from the asset library, or choose one already in the Mux account.
- **Choose from Assets** (off by default, turned on per field), in Statamic's own asset browser. The server sends the file to Mux, so private containers and sites Mux cannot reach work too.
- **Browse Mux** in a grid or table, with search across the whole account and a remembered view.
- The title is kept in step with the Mux dashboard, and the field shows when Mux is still preparing a new video.
- `{{ popcorn:player }}` and `{{ popcorn:scripts }}` tags for playing a video with Mux Player. The player is a block element, so a rounded box around it rounds all four corners.
- A `PopcornVideo` GraphQL type.
- The `use popcorn` permission.
- `POPCORN_DISABLE_TRACKING` and `POPCORN_DISABLE_COOKIES` turn off Mux Data and its cookie for every player, with a per-template override for consent. The README explains what Mux Player sends and has privacy policy wording for either setting.
- When Mux refuses an upload, for example because a free plan has reached its 10 videos, the field shows Mux's reason with advice, and never blames the access token.
