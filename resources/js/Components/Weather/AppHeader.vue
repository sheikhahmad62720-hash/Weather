<template>
    <header class="sticky top-0 z-30 border-b border-stone-200 bg-stone-50/80 backdrop-blur dark:border-stone-800 dark:bg-stone-950/80">
        <div class="mx-auto max-w-6xl px-4 py-3.5 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-3">
                <router-link to="/" class="order-1 flex shrink-0 items-center gap-2.5" :aria-label="`${appName} home`">
                    <span class="flex h-9 w-9 items-center justify-center rounded-md bg-stone-900 text-stone-50 dark:bg-stone-100 dark:text-stone-900">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M17.5 19a4.5 4.5 0 0 0 .42-8.98 6 6 0 0 0-11.7 1.37A4 4 0 0 0 6.5 19Z" />
                        </svg>
                    </span>
                    <span class="hidden text-lg font-semibold tracking-tight sm:inline">{{ appName }}</span>
                </router-link>

                <div id="site-search" class="order-3 w-full sm:order-2 sm:ml-3 sm:w-auto sm:min-w-0 sm:max-w-md sm:flex-1">
                    <WeatherSearch
                        @search="handleSearch"
                        @search-coords="handleCoords"
                        @use-location="handleLocation"
                    />
                </div>

                <div class="order-2 ml-auto flex shrink-0 items-center gap-2 sm:order-3 sm:ml-0">
                    <div class="flex items-center rounded-md border border-stone-300 bg-white p-0.5 text-sm font-medium dark:border-stone-700 dark:bg-stone-900" role="group" aria-label="Temperature unit">
                        <button
                            type="button"
                            class="rounded px-2.5 py-1 transition-colors"
                            :class="units === 'metric' ? 'bg-stone-900 text-stone-50 dark:bg-stone-100 dark:text-stone-900' : 'text-stone-500 hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-200'"
                            :aria-pressed="units === 'metric'"
                            @click="setUnits('metric')"
                        >
                            °C
                        </button>
                        <button
                            type="button"
                            class="rounded px-2.5 py-1 transition-colors"
                            :class="units === 'imperial' ? 'bg-stone-900 text-stone-50 dark:bg-stone-100 dark:text-stone-900' : 'text-stone-500 hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-200'"
                            :aria-pressed="units === 'imperial'"
                            @click="setUnits('imperial')"
                        >
                            °F
                        </button>
                    </div>

                    <button
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-md border border-stone-300 bg-white text-stone-600 shadow-sm hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-400/30 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-300 dark:hover:bg-stone-800"
                        :aria-label="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
                        @click="toggleTheme"
                    >
                        <svg v-if="theme === 'dark'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 2v2" /><path d="M12 20v2" /><path d="M4.93 4.93l1.41 1.41" />
                            <path d="M17.66 17.66l1.41 1.41" /><path d="M2 12h2" /><path d="M20 12h2" />
                            <path d="M6.34 17.66l-1.41 1.41" /><path d="M19.07 4.93l-1.41 1.41" />
                        </svg>
                        <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { theme, units, toggleTheme, setUnits } from '../../composables/usePreferences';
import WeatherSearch from './WeatherSearch.vue';

const emit = defineEmits(['search', 'search-coords', 'location']);

const appName = 'Vane Weather';

function handleSearch(city) {
    emit('search', city);
}

function handleCoords(result) {
    emit('search-coords', result);
}

function handleLocation(message, coords) {
    emit('location', message, coords);
}
</script>