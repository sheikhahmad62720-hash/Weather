import { ref, watch } from 'vue';

const KEY = 'weather:favorites';

export function useFavorites() {
    const favorites = ref(JSON.parse(localStorage.getItem(KEY) || '[]'));

    watch(favorites, value => localStorage.setItem(KEY, JSON.stringify(value)), { deep: true });

    function isFavorite(city) {
        return favorites.value.some(
            favorite => favorite.name === city.name && favorite.country === city.country,
        );
    }

    function add(city) {
        if (!isFavorite(city)) {
            favorites.value.push({ name: city.name, country: city.country, lat: city.lat, lon: city.lon });
        }
    }

    function remove(city) {
        favorites.value = favorites.value.filter(
            favorite => !(favorite.name === city.name && favorite.country === city.country),
        );
    }

    function toggle(city) {
        if (isFavorite(city)) {
            remove(city);
        } else {
            add(city);
        }
    }

    return { favorites, isFavorite, toggle, remove };
}