# Vane Weather

A simple, professional weather app built with **Laravel** on the backend and **Vue 3** on the frontend. Weather data comes from the [OpenWeatherMap API](https://openweathermap.org/).

## Features

- Search any city with autocomplete suggestions
- "My location" button using the browser's geolocation
- Current conditions, hourly outlook (24h) and 5-day daily forecast
- Celsius / Fahrenheit toggle and light / dark theme (remembered in localStorage)
- Save favorite cities, recent searches
- Responsive layout, skeleton loading, friendly error states

## Requirements

- PHP 8.4+
- Composer
- Node.js 20+

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# get a free key from https://openweathermap.org/api
# then set it in .env:
#   WEATHER_API_KEY=your_key_here
#   WEATHER_API_URL=https://api.openweathermap.org/data/2.5
#   WEATHER_GEO_URL=https://api.openweathermap.org/geo/1.0
#   WEATHER_CACHE_TTL=600
```

## Run it

```bash
composer run dev
```

Or in two terminals:

```bash
php artisan serve
npm run dev
```

Then open `http://localhost:8000`.

## Tests

```bash
php artisan test
```

## API

- `GET /api/weather?city=Lahore` — weather by city
- `GET /api/weather?lat=31.55&lon=74.34` — weather by coordinates
- `GET /api/weather/autocomplete?q=Lah` — city suggestions

Cache: forecasts are cached for 10 minutes (configured via `WEATHER_CACHE_TTL`).

## License

MIT