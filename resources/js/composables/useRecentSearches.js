import { ref, watch } from 'vue';

const KEY = 'weather:recents';
const LIMIT = 5;

export function useRecentSearches() {
    const recents = ref(JSON.parse(localStorage.getItem(KEY) || '[]'));

    watch(recents, value => localStorage.setItem(KEY, JSON.stringify(value)));

    function add(city) {
        recents.value = [city, ...recents.value.filter(item => item.name !== city.name)].slice(0, LIMIT);
    }

    return { recents, add };
}