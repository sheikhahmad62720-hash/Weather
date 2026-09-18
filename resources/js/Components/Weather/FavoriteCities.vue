<template>
    <div>
        <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-400">Saved cities</h2>

        <ul v-if="favorites.length" class="mt-3 space-y-0.5">
            <li v-for="city in favorites" :key="`${city.name}-${city.country}`">
                <div class="group flex items-center gap-2 rounded-md px-3 py-2 hover:bg-stone-100 dark:hover:bg-stone-800">
                    <button
                        type="button"
                        class="min-w-0 flex-1 truncate text-left text-sm font-medium"
                        :class="active(city) ? 'text-stone-900 dark:text-stone-100' : 'text-stone-600 dark:text-stone-300'"
                        @click="$emit('select', city)"
                    >
                        {{ city.name }}, {{ city.country }}
                    </button>

                    <button
                        type="button"
                        class="shrink-0 text-stone-400 opacity-0 transition-opacity hover:text-red-500 focus:outline-none focus:opacity-100 group-hover:opacity-100"
                        :aria-label="`Remove ${city.name} from saved cities`"
                        @click="$emit('remove', city)"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </li>
        </ul>

        <p v-else class="mt-3 text-xs text-stone-400">Save a city to see it here.</p>
    </div>
</template>

<script setup>
defineProps({
    favorites: { type: Array, required: true },
    active: { type: Function, required: true },
});

defineEmits(['select', 'remove']);
</script>