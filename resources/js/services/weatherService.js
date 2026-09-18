import axios from 'axios';

const http = axios.create({
    baseURL: '/api',
    headers: { Accept: 'application/json' },
});

export class WeatherApiError extends Error {
    constructor(message) {
        super(message);
        this.name = 'WeatherApiError';
    }
}

function messageFor(error) {
    const { response } = error;

    if (response) {
        if (response.status === 404) {
            return "We couldn't find that city. Try searching for another one.";
        }

        if (response.data?.message) {
            return response.data.message;
        }

        if (response.status === 429) {
            return "We've received too many requests. Please wait a moment and try again.";
        }
    }

    return "We couldn't reach the weather service. Please check your connection and try again.";
}

export async function fetchWeather({ city, lat, lon, units }) {
    try {
        const params = { units };

        if (city) {
            params.city = city;
        } else {
            params.lat = lat;
            params.lon = lon;
        }

        const { data } = await http.get('/weather', { params });

        return data;
    } catch (error) {
        throw new WeatherApiError(messageFor(error));
    }
}

export async function fetchCitySuggestions(query) {
    try {
        const { data } = await http.get('/weather/autocomplete', { params: { q: query } });

        return data.results;
    } catch {
        return [];
    }
}