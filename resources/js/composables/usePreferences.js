import { computed, ref, watch } from 'vue';

const THEME_KEY = 'weather:theme';
const UNITS_KEY = 'weather:units';

function initialTheme() {
    const stored = localStorage.getItem(THEME_KEY);

    if (stored === 'light' || stored === 'dark') {
        return stored;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

export const theme = ref(initialTheme());
export const units = ref(localStorage.getItem(UNITS_KEY) || 'metric');

export const degree = computed(() => (units.value === 'metric' ? '°C' : '°F'));
export const windUnit = computed(() => (units.value === 'metric' ? 'm/s' : 'mph'));

export function toggleTheme() {
    theme.value = theme.value === 'dark' ? 'light' : 'dark';
}

export function setUnits(value) {
    if (value === 'metric' || value === 'imperial') {
        units.value = value;
    }
}

watch(
    theme,
    value => {
        document.documentElement.classList.toggle('dark', value === 'dark');
        localStorage.setItem(THEME_KEY, value);
    },
    { immediate: true },
);

watch(units, value => localStorage.setItem(UNITS_KEY, value));