<template>
    <section class="border-b border-stone-200 pb-8 dark:border-stone-800">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-display text-3xl font-medium tracking-tight sm:text-4xl">
                        {{ location.name }}
                    </h1>
                    <span v-if="location.country" class="mt-1 text-sm text-stone-500">{{ location.country }}</span>

                    <button
                        type="button"
                        class="mt-1 flex h-8 w-8 items-center justify-center rounded-md text-stone-400 hover:bg-stone-200/60 hover:text-stone-600 focus:outline-none focus:ring-2 focus:ring-stone-400/30 dark:hover:bg-stone-800 dark:hover:text-stone-200"
                        :aria-label="isFavorite ? `Remove ${location.name} from favorites` : `Save ${location.name} to favorites`"
                        :class="{ 'text-amber-500 hover:text-amber-500': isFavorite }"
                        @click="$emit('toggle-favorite')"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5" :fill="isFavorite ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 21v0L3.9 12.9c-1.9-1.9-1.9-5 0-6.9 1.9-1.9 5-1.9 6.9 0l1.2 1.2 1.2-1.2c1.9-1.9 5-1.9 6.9 0 1.9 1.9 1.9 5 0 6.9L12 21Z" />
                        </svg>
                    </button>
                </div>

                <p class="mt-1.5 text-sm text-stone-500">{{ dateLabel }} · {{ description }}</p>
                <p v-if="locationNote" class="mt-1 text-xs text-stone-400">{{ locationNote }}</p>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-x-8 gap-y-4">
            <div class="flex items-end gap-3">
                <span class="font-display text-7xl font-medium leading-none tracking-tight sm:text-8xl">
                    {{ roundedTemp }}°
                </span>
                <WeatherIcon :code="current.code" class="h-14 w-14 text-stone-700 dark:text-stone-300" />
            </div>

            <div class="space-y-1 text-sm">
                <p class="font-medium capitalize">{{ current.condition }}</p>
                <p class="text-stone-500">
                    Feels like {{ Math.round(current.feels_like) }}° · {{ degreeSymbol }}
                </p>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import WeatherIcon from './WeatherIcon.vue';

const props = defineProps({
    location: { type: Object, required: true },
    current: { type: Object, required: true },
    dateLabel: { type: String, required: true },
    degreeSymbol: { type: String, required: true },
    isFavorite: { type: Boolean, default: false },
    locationNote: { type: String, default: '' },
});

defineEmits(['toggle-favorite']);

const roundedTemp = computed(() => Math.round(props.current.temp));
</script>