<template>
    <div>
        <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-400">Recent searches</h2>

        <ul v-if="recents.length" class="mt-3 space-y-0.5">
            <li v-for="city in recents" :key="`${city.name}-${city.country}`">
                <button
                    type="button"
                    class="flex min-w-0 items-center gap-2 rounded-md px-3 py-2 text-left text-sm font-medium text-stone-600 hover:bg-stone-100 dark:text-stone-300 dark:hover:bg-stone-800"
                    @click="$emit('select', city)"
                >
                    <span class="truncate">{{ city.name }}, {{ city.country }}</span>
                </button>
            </li>
        </ul>

        <p v-else class="mt-3 text-xs text-stone-400">Your recent searches will appear here.</p>
    </div>
</template>

<script setup>
defineProps({
    recents: { type: Array, required: true },
});

defineEmits(['select']);
</script>