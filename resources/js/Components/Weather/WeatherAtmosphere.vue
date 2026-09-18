<template>
    <div
        aria-hidden="true"
        class="pointer-events-none fixed inset-0 -z-10 transition-all duration-700"
        :style="{ background: gradient }"
    />
</template>

<script setup>
import { computed } from 'vue';
import { theme } from '../../composables/usePreferences';

const props = defineProps({
    code: { type: String, default: '01d' },
});

const tone = computed(() => {
    const base = props.code?.slice(0, 2);
    const day = props.code?.endsWith('d');

    if (base === '01' || base === '02') {
        return day ? 'clear-day' : 'clear-night';
    }

    if (base === '09' || base === '10' || base === '11') {
        return 'rain';
    }

    if (base === '03' || base === '04' || base === '50') {
        return 'cloudy';
    }

    if (base === '13') {
        return 'snow';
    }

    return 'cloudy';
});

const colors = {
    'clear-day': '251, 191, 36',
    'clear-night': '79, 70, 229',
    rain: '56, 189, 248',
    cloudy: '163, 163, 163',
    snow: '147, 197, 253',
};

const gradient = computed(() => {
    const alpha = theme.value === 'dark' ? 0.08 : 0.14;
    const color = colors[tone.value];

    return `radial-gradient(110% 70% at 15% -5%, rgba(${color}, ${alpha}) 0%, transparent 60%)`;
});
</script>