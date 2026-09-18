<template>
    <div class="min-h-screen">
        <WeatherAtmosphere :code="weather?.current?.code" />

        <AppHeader
            @search="searchCity"
            @search-coords="selectCoords"
            @location="useMyLocation"
        />

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-[minmax(0,1fr)_260px] lg:gap-10">
                <div class="min-w-0">
                    <ErrorBanner v-if="error" :message="error" class="mb-6" @retry="retry" />

                    <p v-if="notice" class="mb-6 rounded-md border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-600 dark:border-stone-800 dark:bg-stone-900 dark:text-stone-300">
                        {{ notice }}
                    </p>

                    <WeatherSkeleton v-if="loading && !weather" />

                    <transition v-else name="fade" mode="out-in">
                        <div v-if="weather" :key="`${weather.location.name}-${weather.units}`" class="pt-2">
                            <CurrentWeather
                                :location="weather.location"
                                :current="weather.current"
                                :date-label="dateLabel"
                                :degree-symbol="degree"
                                :is-favorite="isCurrentFavorite"
                                :location-note="locationNote"
                                @toggle-favorite="toggleFavorite"
                            />

                            <WeatherDetails
                                :current="weather.current"
                                :location="weather.location"
                                :units="weather.units"
                                :wind-label="windUnit"
                            />

                            <HourlyForecast
                                :current="weather.current"
                                :hourly="weather.hourly"
                                :offset="weather.location.offset"
                            />

                            <div class="py-8">
                                <DailyForecast :daily="weather.daily" />
                            </div>
                        </div>
                    </transition>
                </div>

                <aside class="border-t border-stone-200 pt-8 lg:border-l lg:border-t-0 lg:pl-10 lg:pt-0 dark:border-stone-800">
                    <div class="space-y-10">
                        <FavoriteCities
                            :favorites="favorites"
                            :active="isActiveCity"
                            @select="selectFavorite"
                            @remove="removeFavorite"
                        />

                        <RecentSearches :recents="recents" @select="selectCoords" />

                        <p class="text-xs leading-relaxed text-stone-400">
                            Weather data provided by OpenWeatherMap. Forecasts are refreshed regularly and cached for ten minutes.
                        </p>
                    </div>
                </aside>
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import AppHeader from '../../Components/Weather/AppHeader.vue';
import CurrentWeather from '../../Components/Weather/CurrentWeather.vue';
import DailyForecast from '../../Components/Weather/DailyForecast.vue';
import ErrorBanner from '../../Components/Weather/ErrorBanner.vue';
import FavoriteCities from '../../Components/Weather/FavoriteCities.vue';
import HourlyForecast from '../../Components/Weather/HourlyForecast.vue';
import RecentSearches from '../../Components/Weather/RecentSearches.vue';
import WeatherAtmosphere from '../../Components/Weather/WeatherAtmosphere.vue';
import WeatherDetails from '../../Components/Weather/WeatherDetails.vue';
import WeatherSkeleton from '../../Components/Weather/WeatherSkeleton.vue';
import { degree, units, windUnit } from '../../composables/usePreferences';
import { useFavorites } from '../../composables/useFavorites';
import { useRecentSearches } from '../../composables/useRecentSearches';
import { fetchWeather } from '../../services/weatherService';
import { formatFullDate } from '../../lib/format';

const DEFAULT_CITY = 'Lahore';

const weather = ref(null);
const loading = ref(false);
const error = ref('');
const notice = ref('');
const active = ref({ type: 'city', city: DEFAULT_CITY });
const fromLocation = ref(false);

const { favorites, isFavorite, toggle: toggleFavoriteRaw, remove: removeFavorite } = useFavorites();
const { recents, add: addRecent } = useRecentSearches();

let requestId = 0;

const dateLabel = computed(() => weather.value ? formatFullDate(weather.value.location) : '');
const isCurrentFavorite = computed(() => {
    if (!weather.value) return false;
    const { location } = weather.value;
    return isFavorite({ name: location.name, country: location.country });
});

const locationNote = computed(() => (fromLocation.value ? 'Based on your current location' : ''));

function isActiveCity(city) {
    if (!weather.value) return false;

    const { location } = weather.value;

    return (
        Math.abs(location.lat - city.lat) < 0.05 &&
        Math.abs(location.lon - city.lon) < 0.05
    );
}

async function load(query) {
    const id = ++requestId;

    loading.value = true;
    error.value = '';
    notice.value = '';

    try {
        const data = await fetchWeather({ units: units.value, ...query });

        if (id !== requestId) return;

        weather.value = data;
    } catch (err) {
        if (id !== requestId) return;

        error.value = err.message;
    } finally {
        if (id === requestId) {
            loading.value = false;
        }
    }
}

async function searchCity(city) {
    const trimmed = city.trim();

    if (!trimmed) return;

    addRecent({ name: trimmed, country: '' });
    fromLocation.value = false;
    active.value = { type: 'city', city: trimmed };
    await load({ city: trimmed });
}

async function selectCoords(result) {
    addRecent({ name: result.name, country: result.country || '' });
    fromLocation.value = false;
    active.value = { type: 'coords', lat: result.lat, lon: result.lon };
    await load({ lat: result.lat, lon: result.lon });
}

async function selectFavorite(city) {
    active.value = { type: 'coords', lat: city.lat, lon: city.lon };
    await load({ lat: city.lat, lon: city.lon });
}

async function useMyLocation(message, coords) {
    if (message) {
        notice.value = message;
        return;
    }

    fromLocation.value = true;
    active.value = { type: 'coords', lat: coords.latitude, lon: coords.longitude };
    await load({ lat: coords.latitude, lon: coords.longitude });
}

function toggleFavorite() {
    if (!weather.value) return;

    const { location } = weather.value;
    toggleFavoriteRaw({
        name: location.name,
        country: location.country,
        lat: location.lat,
        lon: location.lon,
    });
}

async function retry() {
    await load(active.value.type === 'city' ? { city: active.value.city } : { lat: active.value.lat, lon: active.value.lon });
}

onMounted(() => load({ city: DEFAULT_CITY }));

watch(units, () => {
    if (!weather.value) return;

    const query = active.value.type === 'city'
        ? { city: active.value.city }
        : { lat: active.value.lat, lon: active.value.lon };

    load(query);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 180ms ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>