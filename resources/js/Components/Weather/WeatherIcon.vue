<template>
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
    >
        <template v-if="type === 'sun'">
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2" /><path d="M12 20v2" /><path d="M4.93 4.93l1.41 1.41" />
            <path d="M17.66 17.66l1.41 1.41" /><path d="M2 12h2" /><path d="M20 12h2" />
            <path d="M6.34 17.66l-1.41 1.41" /><path d="M19.07 4.93l-1.41 1.41" />
        </template>

        <template v-else-if="type === 'moon'">
            <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z" />
        </template>

        <template v-else-if="type === 'cloud'">
            <path d="M17.5 19a4.5 4.5 0 0 0 .42-8.98 6 6 0 0 0-11.7 1.37A4 4 0 0 0 6.5 19Z" />
        </template>

        <template v-else-if="type === 'sun-cloud'">
            <circle cx="9" cy="7" r="3" />
            <path d="M9 1v1.5" /><path d="M9 12.5V14" /><path d="M3.5 7H2" /><path d="M11.6 2.4l1.06-1.06" />
            <path d="M4.34 11.6L4.34 13.24" /><path d="M1.4 9.9H2.8" />
            <path d="M17.5 19a4.5 4.5 0 0 0 .42-8.98 6 6 0 0 0-11.7 1.37A4 4 0 0 0 6.5 19Z" />
        </template>

        <template v-else-if="type === 'moon-cloud'">
            <path d="M6 19a4 4 0 0 1-1.12-7.86 6 6 0 0 1 10.96-1.33A4.5 4.5 0 0 1 16 19Z" />
        </template>

        <template v-else-if="type === 'rain'">
            <path d="M17.5 19a4.5 4.5 0 0 0 .42-8.98 6 6 0 0 0-11.7 1.37A4 4 0 0 0 6.5 19Z" />
            <path d="M8 19l-1 3" /><path d="M12 19l-1 3" /><path d="M16 19l-1 3" />
        </template>

        <template v-else-if="type === 'sun-rain'">
            <circle cx="10" cy="8" r="3" />
            <path d="M10 1v1.5" /><path d="M10 13.5V15" /><path d="M4.5 8H3" /><path d="M12.6 3.4l1.06-1.06" />
            <path d="M5.34 12.6l1.06 1.06" /><path d="M14.4 8h1.5" />
            <path d="M16 21.5l-1 2.5" /><path d="M20 21.5l-1 2.5" />
            <path d="M17.5 16a4.5 4.5 0 0 0 .42-8.98A5 5 0 0 0 9.2 5.9" />
        </template>

        <template v-else-if="type === 'moon-rain'">
            <path d="M21 12.79A9 9 0 1 1 11.21 3a6 6 0 0 0 9.79 9.79Z" />
            <path d="M7 16.5l-1 2.5" /><path d="M11 16.5l-1 2.5" /><path d="M15 16.5l-1 2.5" />
        </template>

        <template v-else-if="type === 'storm'">
            <path d="M17.5 19a4.5 4.5 0 0 0 .42-8.98 6 6 0 0 0-11.7 1.37A4 4 0 0 0 6.5 19Z" />
            <path d="M12 15l-3 4h3l-1 3" />
        </template>

        <template v-else-if="type === 'snow'">
            <path d="M17.5 19a4.5 4.5 0 0 0 .42-8.98 6 6 0 0 0-11.7 1.37A4 4 0 0 0 6.5 19Z" />
            <path d="M9 19.5v3" /><path d="M9 19.5l-2 -1" /><path d="M9 19.5l2 -1" />
            <path d="M12 18.5v3" /><path d="M12 18.5l-1.7 -1" /><path d="M12 18.5l1.7 -1" />
        </template>

        <template v-else-if="type === 'mist'">
            <path d="M17.5 15a4.5 4.5 0 0 0 .42-8.98 6 6 0 0 0-11.7 1.37A4 4 0 0 0 6.5 15Z" />
            <path d="M4 19h8" /><path d="M14 19h2" /><path d="M3 21.5h14" />
        </template>
    </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    code: { type: String, required: true },
});

const type = computed(() => {
    const base = props.code.slice(0, 2);
    const day = props.code.endsWith('d');

    switch (base) {
        case '01':
            return day ? 'sun' : 'moon';
        case '02':
            return day ? 'sun-cloud' : 'moon-cloud';
        case '03':
        case '04':
            return 'cloud';
        case '09':
            return 'rain';
        case '10':
            return day ? 'sun-rain' : 'moon-rain';
        case '11':
            return 'storm';
        case '13':
            return 'snow';
        case '50':
            return 'mist';
        default:
            return 'cloud';
    }
});
</script>