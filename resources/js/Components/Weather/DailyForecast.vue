<template>
    <section>
        <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-400">
            {{ forecastHeading(daily.length) }}
        </h2>

        <ul class="mt-1 divide-y divide-stone-100 dark:divide-stone-800">
            <li v-for="(day, index) in daily" :key="day.date" class="flex items-center gap-3 py-3">
                <span class="w-16 shrink-0 text-sm font-medium">{{ dayLabel(day.date, index) }}</span>

                <div class="flex min-w-0 flex-1 items-center gap-2.5">
                    <WeatherIcon :code="day.code" class="h-6 w-6 shrink-0 text-stone-600 dark:text-stone-300" />
                    <span class="hidden truncate text-sm text-stone-500 sm:inline">{{ day.condition }}</span>
                    <span v-if="day.pop > 0" class="shrink-0 text-xs font-medium text-sky-500">{{ day.pop }}%</span>
                </div>

                <div class="flex shrink-0 items-center gap-2 text-sm tabular-nums">
                    <span class="font-semibold">{{ Math.round(day.temp_max) }}°</span>
                    <span class="text-stone-400">{{ Math.round(day.temp_min) }}°</span>
                </div>
            </li>
        </ul>
    </section>
</template>

<script setup>
import WeatherIcon from './WeatherIcon.vue';
import { dayLabel } from '../../lib/format';

defineProps({
    daily: { type: Array, required: true },
});

const forecastHeading = count => `${count}-day forecast`;
</script>