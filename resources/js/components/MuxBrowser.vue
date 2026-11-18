<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import { Button, Icon, Input, Listing, Stack, ToggleGroup, ToggleItem } from '@statamic/cms/ui';
import { http } from '../composables/useHttp.js';
import { duration, shortDate } from '../composables/format.js';

/**
 * Choose a video already in the Mux account. The drawer copies the layout of
 * core's asset selector (search and view toggle above, count and buttons in
 * the footer) so it feels like choosing an asset anywhere else.
 *
 * Mux's API has no search, so titles are filtered here. While a search is
 * typed, any pages not yet loaded are fetched so the whole account is
 * searched, not just the first page.
 */
const props = defineProps({
    url: { type: String, required: true },
});

const emit = defineEmits(['choose']);
const open = defineModel('open', { type: Boolean, default: false });

const PREFERENCE = 'popcorn.mux.browser.mode';

// Enough for a thousand-odd videos at Mux's page size. A search past that
// says so rather than quietly missing the rest.
const MAX_PAGES = 20;

const videos = ref([]);
const nextCursor = ref(null);
const loading = ref(false);
const failed = ref(null);
const pages = ref(0);
const query = ref('');
const selected = ref(null);
const mode = ref(Statamic.$preferences.get(PREFERENCE) || 'grid');
const search = ref(null);

const filtered = computed(() => {
    const words = query.value.toLowerCase().trim().split(/\s+/).filter(Boolean);

    if (!words.length) {
        return videos.value;
    }

    return videos.value.filter((video) => {
        const title = (video.title || '').toLowerCase();

        return words.every((word) => title.includes(word));
    });
});

const rows = computed(() =>
    filtered.value.map((video) => ({
        ...video,
        id: video.asset_id,
        title: video.title || __('Untitled'),
        length: duration(video.duration),
        resolution: video.resolution_tier ? video.resolution_tier.toUpperCase() : video.max_height ? `${video.max_height}p` : '',
        date: shortDate(video.created_at),
    })),
);

const columns = [
    { field: 'thumbnail', label: '', sortable: false, visible: true },
    { field: 'title', label: __('Title'), sortable: true, visible: true },
    { field: 'length', label: __('Duration'), sortable: true, visible: true },
    { field: 'resolution', label: __('Resolution'), sortable: true, visible: true },
    { field: 'date', label: __('Date'), sortable: true, visible: true },
];

const listingSelections = computed(() => (selected.value ? [selected.value.asset_id] : []));

const searchingPastLimit = computed(() => query.value.trim() && nextCursor.value && pages.value >= MAX_PAGES);

watch(open, async (isOpen) => {
    if (!isOpen) {
        return;
    }

    videos.value = [];
    nextCursor.value = null;
    pages.value = 0;
    failed.value = null;
    query.value = '';
    selected.value = null;

    await load();

    // As core's selector does: ready to type as soon as the drawer has loaded.
    await nextTick();
    search.value?.$el?.querySelector('input')?.focus();
});

watch(mode, (value) => Statamic.$preferences.set(PREFERENCE, value));

// A search reads every remaining page, one after another.
watch(query, () => loadForSearch());

async function loadForSearch() {
    while (open.value && query.value.trim() && nextCursor.value && !loading.value && !failed.value && pages.value < MAX_PAGES) {
        await load(nextCursor.value);
    }
}

async function load(cursor = null) {
    loading.value = true;
    failed.value = null;

    try {
        const data = await http('GET', cursor ? `${props.url}?cursor=${encodeURIComponent(cursor)}` : props.url);

        videos.value = cursor ? [...videos.value, ...data.assets] : data.assets;
        nextCursor.value = data.next_cursor;
        pages.value++;
    } catch (e) {
        failed.value = e.message;
    } finally {
        loading.value = false;
    }
}

function select(video) {
    selected.value = selected.value?.asset_id === video.asset_id ? null : video;
}

function listingSelected(ids) {
    selected.value = ids.length ? (videos.value.find((video) => video.asset_id === ids[ids.length - 1]) ?? null) : null;
}

function choose(video = selected.value) {
    if (!video) {
        return;
    }

    emit('choose', video);
    open.value = false;
}
</script>

<template>
    <Stack v-model:open="open" inset :show-close-button="false">
        <div v-if="open" class="flex h-full min-h-0 flex-col">
            <div class="flex flex-1 flex-col gap-4 overflow-auto p-4">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="flex-1">
                        <Input
                            ref="search"
                            v-model="query"
                            icon="magnifying-glass"
                            clearable
                            :placeholder="__('Search titles...')"
                            class="w-full max-w-xs"
                        />
                    </div>

                    <ToggleGroup v-model="mode" size="sm">
                        <ToggleItem icon="layout-grid" value="grid" />
                        <ToggleItem icon="layout-list" value="table" />
                    </ToggleGroup>
                </div>

                <div v-if="failed" class="py-12 text-center">
                    <p class="mb-4 text-sm text-red-600 dark:text-red-400" v-text="failed" />
                    <Button size="sm" :text="__('Try again')" @click="load(videos.length ? nextCursor : null)" />
                </div>

                <div v-else-if="loading && !videos.length" class="flex items-center justify-center py-12">
                    <Icon name="loading" />
                </div>

                <div v-else-if="!filtered.length && !loading" class="py-12 text-center text-sm text-gray-500">
                    <template v-if="query.trim()">{{ __('No videos have a title matching ":query".', { query }) }}</template>
                    <template v-else>{{ __('There are no ready videos in this Mux account yet.') }}</template>
                </div>

                <div v-else-if="mode === 'grid'" class="popcorn-grid">
                    <button
                        v-for="video in filtered"
                        :key="video.asset_id"
                        type="button"
                        class="popcorn-tile"
                        :class="{ 'is-selected': selected?.asset_id === video.asset_id }"
                        :aria-pressed="selected?.asset_id === video.asset_id"
                        :title="__('Double-click to choose')"
                        @click="select(video)"
                        @dblclick="choose(video)"
                    >
                        <span class="popcorn-tile__image">
                            <img :src="video.thumbnail" alt="" loading="lazy" />
                            <span v-if="video.duration" class="popcorn-tile__duration" v-text="duration(video.duration)" />
                            <span v-if="selected?.asset_id === video.asset_id" class="popcorn-tile__tick">
                                <Icon name="checkmark" />
                            </span>
                        </span>
                        <span class="popcorn-tile__text">
                            <span class="popcorn-tile__title" v-text="video.title || __('Untitled')" />
                            <span class="popcorn-tile__meta">
                                <span v-if="video.max_width">{{ video.max_width }}&times;{{ video.max_height }}</span>
                                <span v-if="video.resolution_tier" class="uppercase" v-text="video.resolution_tier" />
                                <span v-if="video.created_at" class="ml-auto" v-text="shortDate(video.created_at)" />
                            </span>
                        </span>
                    </button>
                </div>

                <Listing
                    v-else
                    :items="rows"
                    :columns="columns"
                    :selections="listingSelections"
                    :max-selections="1"
                    :allow-search="false"
                    :allow-presets="false"
                    :allow-customizing-columns="false"
                    :sortable="true"
                    @update:selections="listingSelected"
                >
                    <template #cell-thumbnail="{ row }">
                        <div class="aspect-video w-20 overflow-hidden rounded bg-gray-900">
                            <img :src="row.thumbnail" alt="" loading="lazy" class="h-full w-full object-cover" />
                        </div>
                    </template>
                </Listing>

                <div v-if="nextCursor && !query.trim() && !failed" class="text-center">
                    <Button size="sm" variant="ghost" :text="__('Load more')" :disabled="loading" @click="load(nextCursor)" />
                </div>

                <p v-if="loading && videos.length && query.trim()" class="text-center text-sm text-gray-500" v-text="__('Searching more videos...')" />
                <p v-if="searchingPastLimit" class="text-center text-sm text-gray-500" v-text="__('Only the newest :n videos were searched.', { n: videos.length })" />
            </div>

            <div class="flex items-center justify-between border-t bg-gray-100 dark:bg-gray-850 dark:border-gray-700 px-4 py-2 sm:p-4">
                <div class="text-sm text-gray-700 dark:text-gray-200" v-text="selected ? selected.title || __('Untitled') : __('No video selected')" />

                <div class="flex items-center space-x-3">
                    <Button variant="ghost" :text="__('Cancel')" @click="open = false" />
                    <Button variant="primary" :text="__('Select')" :disabled="!selected" @click="choose()" />
                </div>
            </div>
        </div>
    </Stack>
</template>

<style scoped>
/* Only Tailwind classes already in the Control Panel's bundle exist, so the
   grid is laid out here. Colours follow the theme through currentColor. */
.popcorn-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(12rem, 1fr));
    gap: 1rem;
}

.popcorn-tile {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 2px solid transparent;
    border-radius: 0.5rem;
    text-align: left;
    opacity: 0.8;
    transition: opacity 0.15s, border-color 0.15s;
}

.popcorn-tile:hover,
.popcorn-tile:focus-visible {
    opacity: 1;
    border-color: color-mix(in srgb, currentColor 25%, transparent);
}

.popcorn-tile:focus-visible {
    outline: 2px solid var(--theme-color-primary, #4f46e5);
    outline-offset: 2px;
}

.popcorn-tile.is-selected {
    opacity: 1;
    border-color: var(--theme-color-primary, #4f46e5);
}

.popcorn-tile__image {
    position: relative;
    display: block;
    aspect-ratio: 16 / 9;
    background: #111827;
}

.popcorn-tile__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.popcorn-tile__duration {
    position: absolute;
    right: 0.25rem;
    bottom: 0.25rem;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    background: rgb(0 0 0 / 0.7);
    color: #fff;
    font-size: 0.75rem;
}

.popcorn-tile__tick {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: color-mix(in srgb, var(--theme-color-primary, #4f46e5) 35%, transparent);
}

.popcorn-tile__text {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
    padding: 0.375rem 0.5rem;
    font-size: 0.75rem;
}

.popcorn-tile__title {
    overflow: hidden;
    font-weight: 500;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.popcorn-tile__meta {
    display: flex;
    gap: 0.5rem;
    opacity: 0.7;
}
</style>
