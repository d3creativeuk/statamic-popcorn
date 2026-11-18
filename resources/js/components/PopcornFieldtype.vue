<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import { Fieldtype } from '@statamic/cms';
import { Button, Field, Icon, Input, Switch } from '@statamic/cms/ui';
import AssetPicker from './AssetPicker.vue';
import MuxBrowser from './MuxBrowser.vue';
import { http } from '../composables/useHttp.js';
import { escapeHtml } from '../composables/format.js';
import { loadPlayer, loadUploader } from '../composables/muxElements.js';

/**
 * One Mux video: upload one from the computer, send one that is already in
 * the asset library, or choose one already in the Mux account. The value is
 * { asset_id, playback_id, title, show_title }.
 */
const emit = defineEmits(Fieldtype.emits);
const props = defineProps(Fieldtype.props);
const { expose, update, isReadOnly, defineReplicatorPreview } = Fieldtype.use(emit, props);

defineExpose(expose);
defineReplicatorPreview(() => props.value?.title || props.value?.playback_id || '');

const meta = computed(() => props.meta ?? {});
const urls = computed(() => meta.value.urls ?? {});
const hasVideo = computed(() => !!props.value?.playback_id);
const allowReplacing = computed(() => props.config.allow_reupload !== false);
const canManage = computed(() => !!meta.value.can && !!meta.value.configured && !isReadOnly.value);
const canPick = computed(() => canManage.value && props.config.allow_assets === true && (meta.value.containers ?? []).length > 0);

// "upload" while the title and drop zone for a new file are showing.
const mode = ref(null);
const pendingTitle = ref('');
// A file on its way to Mux: { kind: 'browser' | 'library', progress, message }.
const transfer = ref(null);
// Mux has the file and is preparing it, so the player cannot play it yet.
const processing = ref(false);
const error = ref(null);
const browsing = ref(false);
const picking = ref(false);
// Bumped to remount the player once a processing video is ready.
const playerKey = ref(0);
const uploaderReady = ref(false);
// The drop zone and file button find their uploader by id, and an entry can
// hold several Popcorn fields, so each field has its own.
const uploaderId = `popcorn-uploader-${Math.random().toString(36).slice(2, 10)}`;

let uploadId = null;
let lastSyncedTitle = props.value?.title ?? null;
let timer = null;
let unmounted = false;

onMounted(() => {
    loadPlayer();
});

onBeforeUnmount(() => {
    unmounted = true;
    clearTimeout(timer);
});

function url(name, id) {
    return urls.value[name].replace('__id__', encodeURIComponent(id));
}

function wait() {
    return new Promise((resolve) => {
        timer = setTimeout(resolve, meta.value.pollInterval || 3000);
    });
}

// A session or permission problem will not fix itself, so polling stops at
// once. Anything else (a dropped connection, a busy moment at Mux) is tried
// again, but not forever: an answer Mux keeps refusing ends the wait with
// its message instead of leaving the field waiting.
const MAX_FAILURES = 5;

function isFinal(e, failures) {
    return [401, 403, 404, 419].includes(e?.status) || failures >= MAX_FAILURES;
}

function fail(message) {
    transfer.value = null;
    processing.value = false;
    error.value = message || __('Mux could not process this video.');
}

// ---------------------------------------------------------------------------
// Uploading from the computer
// ---------------------------------------------------------------------------

async function startUpload() {
    error.value = null;
    mode.value = 'upload';
    await loadUploader();
    uploaderReady.value = true;
}

function cancelUpload() {
    mode.value = null;
    pendingTitle.value = '';
}

// Why the upload URL was refused, if it was. Mux Uploader reports that
// failure too, but as "Failed to get endpoint: …", so this is shown instead.
let refusal = null;

// Mux Uploader calls this for the URL to send the file to. This is where a
// refusal from Mux arrives before any of the file has been sent, such as a
// plan that has reached its limit.
async function uploadUrl() {
    refusal = null;

    try {
        const data = await http('POST', urls.value.upload, { title: pendingTitle.value.trim() });
        uploadId = data.upload_id;

        return data.url;
    } catch (e) {
        refusal = e.message;
        throw e;
    }
}

function uploadStarted() {
    error.value = null;
    transfer.value = reactive({ kind: 'browser', progress: 0, message: null });
}

function uploadProgress(event) {
    if (transfer.value) {
        transfer.value.progress = event.detail;
    }
}

function uploadFinished() {
    transfer.value.progress = 100;
    transfer.value.message = __('Uploaded. Waiting for Mux...');
    follow(uploadId, pendingTitle.value.trim());
}

function uploadFailed(event) {
    fail(refusal || event?.detail?.message || __('The upload failed. Try again.'));
    refusal = null;
}

// ---------------------------------------------------------------------------
// Sending a video from the asset library
// ---------------------------------------------------------------------------

async function sendFromLibrary(asset) {
    error.value = null;
    transfer.value = reactive({ kind: 'library', progress: null, message: __('Sending :file to Mux...', { file: asset.name }) });

    try {
        const data = await http('POST', urls.value.fromAsset, { asset: asset.id });
        follow(data.upload_id, data.title);
    } catch (e) {
        fail(e.message);
    }
}

// ---------------------------------------------------------------------------
// Following an upload until Mux has a video, then until it can play
// ---------------------------------------------------------------------------

async function follow(id, title) {
    let failures = 0;

    while (!unmounted) {
        await wait();

        if (unmounted) {
            return;
        }

        let data;

        try {
            data = await http('GET', url('uploadStatus', id));
            failures = 0;
        } catch (e) {
            if (isFinal(e, ++failures)) {
                return fail(e.message);
            }

            continue;
        }

        if (['errored', 'cancelled', 'timed_out'].includes(data.status) || data.asset_status === 'errored') {
            return fail(data.error);
        }

        if (data.playback_id) {
            // Saved into the field straight away, so the entry can be saved
            // while Mux is still preparing the video.
            update({ asset_id: data.asset_id, playback_id: data.playback_id, title: title || null, show_title: false });
            lastSyncedTitle = title || null;
            transfer.value = null;
            mode.value = null;
            pendingTitle.value = '';

            if (data.asset_status !== 'ready') {
                waitUntilReady(data.asset_id);
            }

            return;
        }

        if (!transfer.value) {
            return;
        }

        if (data.status !== 'waiting') {
            transfer.value.message = __('Mux is preparing the video...');
        }
    }
}

async function waitUntilReady(assetId) {
    let failures = 0;
    processing.value = true;

    while (!unmounted && props.value?.asset_id === assetId) {
        await wait();

        try {
            const video = await http('GET', url('asset', assetId));
            failures = 0;

            if (video.status === 'ready') {
                processing.value = false;
                playerKey.value++;

                return;
            }

            if (video.status === 'errored') {
                return fail(video.error);
            }
        } catch (e) {
            // The video is already in the field and saved with the entry, so
            // giving up here only stops the "preparing" notice.
            if (isFinal(e, ++failures)) {
                processing.value = false;

                return;
            }
        }
    }

    processing.value = false;
}

// ---------------------------------------------------------------------------
// Choosing a video already in Mux
// ---------------------------------------------------------------------------

function chooseFromMux(video) {
    error.value = null;
    update({ asset_id: video.asset_id, playback_id: video.playback_id, title: video.title || null, show_title: false });
    lastSyncedTitle = video.title || null;
}

// ---------------------------------------------------------------------------
// The chosen video
// ---------------------------------------------------------------------------

function setTitle(title) {
    update({ ...props.value, title });
}

function setShowTitle(show) {
    update({ ...props.value, show_title: show });
}

// The title in the Mux dashboard follows the one here, sent when the box
// loses focus rather than on every key.
async function syncTitle() {
    const title = String(props.value?.title ?? '').trim();

    if (!canManage.value || !props.value?.asset_id || !title || title === lastSyncedTitle) {
        return;
    }

    try {
        await http('PATCH', url('title', props.value.asset_id), { title });
        lastSyncedTitle = title;
    } catch (e) {
        Statamic.$toast.error(__('The title could not be updated in Mux: :reason', { reason: escapeHtml(e.message) }));
    }
}

function remove() {
    update(null);
    error.value = null;
    processing.value = false;
}
</script>

<template>
    <div class="popcorn-fieldtype">
        <!-- A video is chosen -->
        <div v-if="hasVideo" class="space-y-3">
            <div class="popcorn-player">
                <mux-player
                    :key="playerKey"
                    :playback-id="value.playback_id"
                    :title="value.title || ''"
                    stream-type="on-demand"
                />
                <div v-if="processing" class="popcorn-player__notice">
                    <Icon name="loading" />
                    <span v-text="__('Mux is preparing this video. It will play here when it is ready.')" />
                </div>
            </div>

            <div class="flex flex-wrap items-start gap-6">
                <Field :label="__('Title')" :instructions="canManage ? __('Also shown in the Mux dashboard.') : null" class="min-w-0 flex-1">
                    <Input
                        :model-value="value.title || ''"
                        :read-only="isReadOnly"
                        @update:model-value="setTitle"
                        @blur="syncTitle"
                        @keydown.enter.prevent="$event.target.blur()"
                    />
                </Field>
                <Field :label="__('Show Title')" :instructions="__('Draw the title on the player.')" class="shrink-0">
                    <Switch :model-value="value.show_title || false" :disabled="isReadOnly" @update:model-value="setShowTitle" />
                </Field>
            </div>

            <div v-if="allowReplacing && !isReadOnly">
                <Button size="sm" variant="ghost" icon="trash" :text="__('Remove')" @click="remove" />
            </div>
        </div>

        <!-- Uploading from the computer, or on its way to Mux. The uploader
             stays mounted (only hidden) while its file is being sent. -->
        <div v-else-if="mode === 'upload' || transfer" class="space-y-3">
            <template v-if="mode === 'upload'">
                <Field v-show="!transfer" :label="__('Title')" :instructions="__('Name the video before uploading. Also shown in the Mux dashboard.')" required>
                    <Input v-model="pendingTitle" />
                </Field>
                <template v-if="uploaderReady">
                    <!-- Mux Uploader does the uploading but draws none of
                         its own interface: its large heading and black
                         button look out of place in the Control Panel. Its
                         drop zone and file button are placed here instead,
                         laid out and worded like core's Assets field, whose
                         strings Statamic already translates. -->
                    <mux-uploader
                        :id="uploaderId"
                        style="display: none"
                        no-drop
                        no-progress
                        no-status
                        no-retry
                        :endpoint.prop="uploadUrl"
                        @uploadstart="uploadStarted"
                        @progress="uploadProgress"
                        @success="uploadFinished"
                        @uploaderror="uploadFailed"
                    />
                    <mux-uploader-drop
                        v-show="pendingTitle.trim() && !transfer"
                        :mux-uploader="uploaderId"
                        class="popcorn-drop border border-gray-300 dark:border-gray-700 dark:bg-gray-850 text-xs text-gray-600 dark:text-gray-400"
                    >
                        <span slot="heading" />
                        <span slot="separator" />
                        <span class="popcorn-drop__idle">
                            <Icon name="upload-cloud" class="size-5 shrink-0 text-gray-500" />
                            <span class="min-w-0">
                                {{ __('Drag & drop here or') }}&nbsp;<mux-uploader-file-select :mux-uploader="uploaderId"><button
                                    type="button"
                                    class="underline underline-offset-2 cursor-pointer hover:text-gray-925 dark:hover:text-gray-200"
                                >{{ __('choose a file') }}</button></mux-uploader-file-select>.
                            </span>
                        </span>
                        <span class="popcorn-drop__active">
                            <Icon name="upload-cloud" class="size-5 shrink-0" />
                            <span class="text-sm" v-text="__('Drop to Upload')" />
                        </span>
                    </mux-uploader-drop>
                </template>
            </template>

            <div v-if="transfer" class="space-y-2">
                <div v-if="transfer.progress !== null" class="flex items-center gap-3">
                    <div class="popcorn-progress">
                        <div class="popcorn-progress__bar" :style="{ width: transfer.progress + '%' }" />
                    </div>
                    <span class="text-sm tabular-nums text-gray-600 dark:text-gray-400">{{ Math.round(transfer.progress) }}%</span>
                </div>
                <div v-else class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <Icon name="loading" />
                    <span v-text="transfer.message" />
                </div>
                <p v-if="transfer.progress !== null && transfer.message" class="text-xs text-gray-500 dark:text-gray-400" v-text="transfer.message" />
            </div>

            <Button v-if="mode === 'upload' && !transfer" size="sm" :text="__('Cancel')" @click="cancelUpload" />
        </div>

        <!-- Nothing chosen and nothing that can be done -->
        <p v-else-if="!canManage" class="text-sm text-gray-500 dark:text-gray-400">
            <template v-if="meta.can && !meta.configured">{{ __('Popcorn needs a Mux access token. Add MUX_TOKEN_ID and MUX_TOKEN_SECRET to .env.') }}</template>
            <template v-else>{{ __('No video.') }}</template>
        </p>

        <!-- Nothing chosen yet -->
        <div v-else class="flex flex-wrap items-center gap-2">
            <Button icon="upload" :text="__('Upload')" @click="startUpload" />
            <Button v-if="canPick" icon="assets" :text="__('Choose from Assets')" @click="picking = true" />
            <Button variant="ghost" icon="folder-play" :text="__('Browse Mux')" @click="browsing = true" />
        </div>

        <p v-if="error" class="mt-2 text-sm text-red-600 dark:text-red-400" v-text="error" />

        <MuxBrowser v-if="canManage" v-model:open="browsing" :url="urls.assets" @choose="chooseFromMux" />
        <AssetPicker
            v-if="canPick"
            v-model:open="picking"
            :containers="meta.containers"
            :extensions="meta.videoExtensions"
            @choose="sendFromLibrary"
        />
    </div>
</template>

<style scoped>
.popcorn-player {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16 / 9;
    border-radius: 0.5rem;
    background: #111827;
}

.popcorn-player mux-player {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}

.popcorn-player__notice {
    position: absolute;
    inset: auto 0 0 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: rgb(0 0 0 / 0.75);
    color: #fff;
    font-size: 0.875rem;
}

/* Mux's drop zone is a centred column with a thick dashed border. These
   outrank its own :host styles and make it the slim bordered row of core's
   Assets field. While a file is dragged over it, Mux sets [active]. */
.popcorn-drop {
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-start;
    gap: 0.5rem;
    min-height: 2.75rem;
    padding: 0.5rem 0.75rem;
    border-style: solid;
    border-radius: 0.75rem;
    transition: border-color 0.15s, background-color 0.15s;
}

.popcorn-drop[active] {
    border-style: dashed;
    border-color: var(--theme-color-primary, #4f46e5);
    background: color-mix(in srgb, var(--theme-color-primary, #4f46e5) 8%, transparent);
    color: var(--theme-color-primary, #4f46e5);
}

.popcorn-drop__idle,
.popcorn-drop__active {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.popcorn-drop[active] .popcorn-drop__idle,
.popcorn-drop:not([active]) .popcorn-drop__active {
    display: none;
}

.popcorn-progress {
    flex: 1;
    height: 0.5rem;
    overflow: hidden;
    border-radius: 9999px;
    background: color-mix(in srgb, currentColor 15%, transparent);
}

.popcorn-progress__bar {
    height: 100%;
    border-radius: 9999px;
    background: var(--theme-color-primary, #4f46e5);
    transition: width 0.3s;
}
</style>
