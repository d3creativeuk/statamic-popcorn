<script setup>
import { computed, ref, watch } from 'vue';
import { Button, ListingSearch, Select, Stack, ToggleGroup, ToggleItem } from '@statamic/cms/ui';
import { assetName, escapeHtml, extension } from '../composables/format.js';

/**
 * Choose a video already in the asset library to send to Mux, in Statamic's
 * own asset browser: the same search, folders, breadcrumbs and views as the
 * Assets section. Any container the user can look in is offered; nothing can
 * be uploaded or created from here.
 *
 * The browser is core's globally registered <asset-browser>, mounted in a
 * Stack the way core's asset selector mounts it. One video is chosen, and
 * the selection is kept here rather than by the browser's max-files, so a
 * file that is not a video can be refused with a reason.
 */
const props = defineProps({
    containers: { type: Array, required: true },
    extensions: { type: Array, required: true },
});

const emit = defineEmits(['choose']);
const open = defineModel('open', { type: Boolean, default: false });

// Templates cannot see the Statamic global, so it is read here.
const perPage = Statamic.$config.get('paginationSize');

const handle = ref(null);
const search = ref(null);
const browser = ref(null);

// The ids ticked in the browser, shared with its listing. Core changes it in
// place for clicks but replaces it after a Delete, Rename or Move from a
// row's menu, so updated() takes whatever it is handed, as core's own asset
// selector does. Never more than one.
const selections = ref([]);
const chosen = ref(null);

const current = computed(() => props.containers.find((container) => container.handle === handle.value) ?? props.containers[0] ?? null);
const containerOptions = computed(() => props.containers.map((container) => ({ value: container.handle, label: container.title })));

watch(open, (isOpen) => {
    if (isOpen) {
        handle.value = current.value?.handle ?? null;
        selections.value = [];
        chosen.value = null;
    }
});

function changeContainer(value) {
    handle.value = value;
    selections.value = [];
    chosen.value = null;
}

// A row from the page the browser is showing, or, for an id with no row
// (renamed or moved a moment ago), what can be read from the id itself.
function row(id) {
    return (browser.value?.assets ?? []).find((candidate) => candidate.id === id) ?? { id, basename: assetName(id) };
}

// A double-click arrives as two clicks and a double-click, each of which
// would say the same thing, so a message already showing is not repeated.
let lastToast = { text: null, at: 0 };

function refuse(name) {
    const text = __(':file is not a video. Choose an MP4, MOV, WebM or other video file.', { file: escapeHtml(name) });

    if (text === lastToast.text && Date.now() - lastToast.at < 2500) {
        return;
    }

    lastToast = { text, at: Date.now() };
    Statamic.$toast.error(text);
}

function set(id) {
    if (id) {
        const candidate = row(id);

        if (!props.extensions.includes(String(candidate.extension ?? extension(candidate.basename)).toLowerCase())) {
            refuse(candidate.basename);
            id = chosen.value;
        }
    }

    chosen.value = id;
    selections.value = id ? [id] : [];
}

// The browser has ticked or unticked something, possibly a whole page at
// once. The newest tick wins.
function updated(ids) {
    const added = ids.filter((id) => id !== chosen.value);

    set(added.length ? added[added.length - 1] : ids.includes(chosen.value) ? chosen.value : null);
}

// Clicking a file opens the editor elsewhere. Here it ticks it, as core's
// own asset selector does.
function toggle(asset) {
    set(chosen.value === asset.id ? null : asset.id);
}

// As core's selector does: ready to type as soon as the drawer has loaded.
function focusSearch() {
    search.value?.focus?.();
}

function choose() {
    if (!chosen.value) {
        return;
    }

    emit('choose', { id: chosen.value, name: row(chosen.value).basename });
    open.value = false;
}
</script>

<template>
    <Stack v-model:open="open" inset :show-close-button="false">
        <div v-if="open && current" class="flex h-full min-h-0 flex-col">
            <div class="flex flex-1 flex-col gap-4 overflow-auto p-4">
                <asset-browser
                    ref="browser"
                    :key="current.handle"
                    :container="current.browser"
                    :initial-per-page="perPage"
                    :initial-columns="current.columns"
                    selected-path="/"
                    :selected-assets="selections"
                    :allow-bulk-actions="false"
                    @initialized="focusSearch"
                    @selections-updated="updated"
                    @edit-asset="toggle"
                >
                    <template #header="{ mode, modeChanged }">
                        <div class="mb-4 flex items-center gap-2 sm:gap-3">
                            <div class="flex flex-1 items-center gap-2 sm:gap-3">
                                <ListingSearch ref="search" />
                                <Select
                                    v-if="containers.length > 1"
                                    class="w-56"
                                    :model-value="current.handle"
                                    :options="containerOptions"
                                    @update:model-value="changeContainer"
                                />
                            </div>

                            <ToggleGroup :model-value="mode" @update:model-value="modeChanged">
                                <ToggleItem icon="layout-grid" value="grid" />
                                <ToggleItem icon="layout-list" value="table" />
                            </ToggleGroup>
                        </div>
                    </template>
                </asset-browser>
            </div>

            <div class="flex items-center justify-between border-t bg-gray-100 dark:bg-gray-850 dark:border-gray-700 px-4 py-2 sm:p-4">
                <div class="text-sm text-gray-700 dark:text-gray-200">
                    <div v-text="chosen ? row(chosen).basename : __('No video selected')" />
                    <div class="text-xs opacity-75" v-text="__('A copy is sent to Mux. The file stays in your library.')" />
                </div>

                <div class="flex items-center space-x-3">
                    <Button variant="ghost" :text="__('Cancel')" @click="open = false" />
                    <Button variant="primary" :text="__('Send to Mux')" :disabled="!chosen" @click="choose" />
                </div>
            </div>
        </div>
    </Stack>
</template>
