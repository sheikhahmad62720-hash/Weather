<template>
    <section class="border-b border-stone-200 py-8 dark:border-stone-800">
        <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-400">Conditions</h2>

        <dl class="mt-5 grid grid-cols-2 gap-px overflow-hidden rounded-lg border border-stone-200 bg-stone-200 sm:grid-cols-3 dark:border-stone-800 dark:bg-stone-800">
            <div v-for="item in details" :key="item.label" class="flex flex-col gap-1 bg-white px-4 py-4 dark:bg-stone-900">
                <dt class="text-xs uppercase tracking-wide text-stone-400">{{ item.label }}</dt>
                <dd class="text-sm font-medium">{{ item.value }}</dd>
            </div>
        </dl>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { compassFromDegrees, formatTime } from '../../lib/format';

const props = defineProps({
    current: { type: Object, required: true },
    location: { type: Object, required: true },
    units: { type: String, required: true },
    windLabel: { type: String, required: true },
});

const visibility = computed(() => {
    if (props.current.visibility == null) {
        return null;
    }

    if (props.units === 'metric') {
        return `${(props.current.visibility / 1000).toFixed(1)} km`;
    }

    return `${(props.current.visibility * 0.000621371).toFixed(1)} mi`;
});

const details = computed(() => {
    const items = [
        { label: 'Humidity', value: `${props.current.humidity}%` },
        {
            label: 'Wind',
            value: `${props.current.wind_speed} ${props.windLabel} ${compassFromDegrees(props.current.wind_deg)}`,
        },
        { label: 'Pressure', value: `${props.current.pressure} hPa` },
        { label: 'Cloud cover', value: `${props.current.clouds}%` },
        { label: 'Dew point', value: `${Math.round(props.current.dew_point)}°` },
        { label: 'Sunrise', value: formatTime(props.current.sunrise, props.location.offset) },
        { label: 'Sunset', value: formatTime(props.current.sunset, props.location.offset) },
    ];

    if (visibility.value) {
        items.push({ label: 'Visibility', value: visibility.value });
    }

    if (props.current.uv_index != null) {
        items.push({ label: 'UV index', value: String(props.current.uv_index) });
    }

    return items;
});
</script>