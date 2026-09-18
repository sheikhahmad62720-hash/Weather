<template>
    <section class="border-b border-stone-200 py-8 dark:border-stone-800">
        <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-400">Next 24 hours</h2>

        <ol class="no-scrollbar mt-5 flex gap-1 overflow-x-auto pb-1">
            <li class="flex shrink-0 flex-col items-center gap-2 rounded-md px-4 py-3 text-sm">
                <span class="text-xs font-medium text-stone-500">Now</span>
                <WeatherIcon :code="current.code" class="h-7 w-7 text-stone-600 dark:text-stone-300" />
                <span class="text-sm font-semibold">{{ Math.round(current.temp) }}°</span>
            </li>

            <li v-for="entry in hourly" :key="entry.time" class="flex shrink-0 flex-col items-center gap-2 rounded-md px-4 py-3 text-sm">
                <span class="text-xs font-medium text-stone-500">{{ formatHour(entry.time, offset) }}</span>
                <div class="flex flex-col items-center gap-1">
                    <WeatherIcon :code="entry.code" class="h-7 w-7 text-stone-600 dark:text-stone-300" />
                    <span v-if="entry.pop > 0" class="text-[11px] font-medium text-sky-500">{{ entry.pop }}%</span>
                </div>
                <span class="text-sm font-semibold">{{ Math.round(entry.temp) }}°</span>
            </li>
        </ol>
    </section>
</template>

<script setup>
import WeatherIcon from './WeatherIcon.vue';
import { formatHour } from '../../lib/format';

defineProps({
    current: { type: Object, required: true },
    hourly: { type: Array, required: true },
    offset: { type: Number, required: true },
});
</script>