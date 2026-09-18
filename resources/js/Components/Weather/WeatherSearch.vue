<template>
    <div ref="rootEl" class="relative w-full">
        <form role="search" class="flex items-center gap-2" @submit.prevent="submit" autocomplete="off">
            <label for="city-search" class="sr-only">Search for a city</label>

            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path d="M21 21l-4.35-4.35" />
                </svg>

                <input
                    id="city-search"
                    v-model="query"
                    type="text"
                    placeholder="Search for a city…"
                    class="h-11 w-full rounded-md border border-stone-300 bg-white pl-9 pr-12 text-sm text-stone-900 shadow-sm placeholder:text-stone-400 focus:border-stone-400 focus:outline-none focus:ring-2 focus:ring-stone-400/30 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100 dark:placeholder:text-stone-500 dark:focus:border-stone-500"
                    @input="onInput"
                    @keydown.esc="closeResults"
                    @focus="onFocus"
                />

                <button
                    v-if="query"
                    type="button"
                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded p-1 text-stone-400 hover:text-stone-600 dark:hover:text-stone-200"
                    aria-label="Clear search"
                    @click="clear"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <button
                type="button"
                class="inline-flex h-11 shrink-0 items-center gap-2 rounded-md border border-stone-300 bg-white px-3 text-sm font-medium text-stone-700 shadow-sm hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-400/30 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800"
                :disabled="locating"
                @click="useLocation"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 21s-7-5.2-7-11a7 7 0 0 1 14 0c0 5.8-7 11-7 11Z" />
                    <circle cx="12" cy="10" r="2.5" />
                </svg>
                <span class="hidden sm:inline">{{ locating ? 'Locating…' : 'My location' }}</span>
            </button>
        </form>

        <div
            v-if="results.length && open"
            id="city-suggestions"
            class="absolute inset-x-0 top-full z-20 mt-2 overflow-hidden rounded-md border border-stone-200 bg-white shadow-lg dark:border-stone-700 dark:bg-stone-900"
        >
            <p class="px-4 pb-1 pt-3 text-xs uppercase tracking-wider text-stone-400">Suggestions</p>
            <ul>
                <li v-for="result in results" :key="`${result.name}-${result.lat}-${result.lon}`">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between gap-4 px-4 py-2.5 text-left text-sm text-stone-700 hover:bg-stone-50 dark:text-stone-200 dark:hover:bg-stone-800"
                        @mousedown.prevent="selectResult(result)"
                    >
                        <span class="font-medium">{{ result.name }}</span>
                        <span class="text-xs text-stone-400">{{ [result.state, result.country].filter(Boolean).join(', ') }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue';
import { fetchCitySuggestions } from '../../services/weatherService';

const emit = defineEmits(['search', 'search-coords', 'use-location']);

const rootEl = ref(null);
const query = ref('');
const results = ref([]);
const open = ref(false);
const locating = ref(false);
let debounceTimer;

const trimmedQuery = computed(() => query.value.trim());

function onClickOutside(event) {
    if (rootEl.value && !rootEl.value.contains(event.target)) {
        closeResults();
    }
}

onMounted(() => document.addEventListener('click', onClickOutside));
onUnmounted(() => document.removeEventListener('click', onClickOutside));

async function fetchResults() {
    if (trimmedQuery.value.length < 2) {
        results.value = [];
        return;
    }

    results.value = await fetchCitySuggestions(trimmedQuery.value);
    open.value = results.value.length > 0;
}

function onInput() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchResults, 320);
}

function onFocus() {
    if (trimmedQuery.value.length >= 2) {
        open.value = results.value.length > 0;
    }
}

function closeResults() {
    open.value = false;
}

function selectResult(result) {
    open.value = false;
    query.value = '';
    emit('search-coords', result);
}

function submit() {
    if (!trimmedQuery.value) {
        return;
    }

    open.value = false;
    emit('search', trimmedQuery.value);
    query.value = '';
}

function clear() {
    query.value = '';
    results.value = [];
    closeResults();
}

function useLocation() {
    if (!navigator.geolocation) {
        emit('use-location', 'Your browser does not support location services.');
        return;
    }

    locating.value = true;

    navigator.geolocation.getCurrentPosition(
        position => {
            locating.value = false;
            emit('use-location', null, position.coords);
        },
        error => {
            locating.value = false;

            const message =
                error.code === error.PERMISSION_DENIED
                    ? 'Location access was denied. You can still search for a city instead.'
                    : 'We could not get your location. Please try again.';

            emit('use-location', message);
        },
        { timeout: 10000, maximumAge: 600000 },
    );
}
</script>