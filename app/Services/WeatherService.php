<?php

namespace App\Services;

use App\Data\CurrentWeather;
use App\Data\DailyForecast;
use App\Data\HourlyForecast;
use App\Data\LocationData;
use App\Data\WeatherData;
use App\Exceptions\CityNotFoundException;
use App\Exceptions\WeatherException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function forecast(string $city, string $units = 'metric'): WeatherData
    {
        $key = 'weather:forecast:'.md5(strtolower(trim($city)).'|'.$units);

        return $this->cached($key, fn () => $this->fetch(['q' => $city], $units));
    }

    public function forecastByCoordinates(float $lat, float $lon, string $units = 'metric'): WeatherData
    {
        $key = 'weather:forecast:'.md5("lat=$lat&lon=$lon|$units");

        return $this->cached($key, fn () => $this->fetch(['lat' => $lat, 'lon' => $lon], $units));
    }

    public function search(string $city): array
    {
        $this->guardApiKey();

        try {
            $response = Http::connectTimeout(3)
                ->timeout(8)
                ->acceptJson()
                ->get(config('services.weather.geo_url').'/direct', [
                    'q' => $city,
                    'limit' => 6,
                    'appid' => config('services.weather.key'),
                ]);
        } catch (ConnectionException) {
            return [];
        }

        if (! $response->successful()) {
            return [];
        }

        return collect($response->json())
            ->map(fn (array $place) => [
                'name' => $place['name'],
                'state' => $place['state'] ?? null,
                'country' => $place['country'],
                'lat' => $place['lat'],
                'lon' => $place['lon'],
            ])
            ->values()
            ->all();
    }

    private function cached(string $key, callable $callback): WeatherData
    {
        $data = Cache::remember($key, config('services.weather.cache_ttl'), $callback);

        return $data instanceof WeatherData ? $data : WeatherData::fromArray($data);
    }

    private function fetch(array $query, string $units): WeatherData
    {
        $this->guardApiKey();

        $params = $query + [
            'units' => $units,
            'appid' => config('services.weather.key'),
        ];

        try {
            $responses = Http::pool(fn (Pool $pool): array => [
                $pool->as('current')
                    ->connectTimeout(3)
                    ->timeout(8)
                    ->acceptJson()
                    ->get(config('services.weather.url').'/weather', $params),
                $pool->as('forecast')
                    ->connectTimeout(3)
                    ->timeout(8)
                    ->acceptJson()
                    ->get(config('services.weather.url').'/forecast', $params),
            ]);
        } catch (ConnectionException) {
            throw new WeatherException('The weather service is temporarily unavailable. Please try again shortly.');
        }

        $this->assertResponseOk($responses['current']);
        $this->assertResponseOk($responses['forecast']);

        return $this->buildWeatherData($responses['current'], $responses['forecast'], $units);
    }

    private function guardApiKey(): void
    {
        if ($this->configured()) {
            return;
        }

        throw new WeatherException('Weather data is not available at the moment. Please try again later.');
    }

    private function configured(): bool
    {
        return filled(config('services.weather.key'))
            && filled(config('services.weather.url'));
    }

    private function assertResponseOk(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        if ($response->status() === 404) {
            throw new CityNotFoundException;
        }

        if ($response->status() === 401) {
            throw new WeatherException('The weather service could not authenticate this request.', 503);
        }

        if ($response->status() === 429) {
            throw new WeatherException("We've received too many requests recently. Please wait a moment and try again.", 429);
        }

        throw new WeatherException('The weather service is temporarily unavailable. Please try again shortly.', 503);
    }

    private function buildWeatherData(Response $currentResponse, Response $forecastResponse, string $units): WeatherData
    {
        $current = $currentResponse->json();
        $forecast = $forecastResponse->json();
        $offset = (int) $current['timezone'];

        return new WeatherData(
            location: new LocationData(
                name: $current['name'],
                country: $current['sys']['country'] ?? '',
                lat: (float) $current['coord']['lat'],
                lon: (float) $current['coord']['lon'],
                offset: $offset,
            ),
            current: $this->buildCurrentWeather($current),
            hourly: $this->buildHourlyForecast($forecast['list'], $current['dt'], $offset),
            daily: $this->buildDailyForecast($forecast['list'], $offset),
            units: $units,
        );
    }

    private function buildCurrentWeather(array $current): CurrentWeather
    {
        $weather = $current['weather'][0];

        return new CurrentWeather(
            temp: round((float) $current['main']['temp'], 1),
            feelsLike: round((float) $current['main']['feels_like'], 1),
            condition: (string) $weather['main'],
            description: (string) $weather['description'],
            code: (string) $weather['icon'],
            humidity: (int) $current['main']['humidity'],
            windSpeed: round((float) $current['wind']['speed'], 1),
            windDeg: (int) ($current['wind']['deg'] ?? 0),
            pressure: (int) $current['main']['pressure'],
            visibility: $current['visibility'] ?? null,
            clouds: (int) ($current['clouds']['all'] ?? 0),
            uvIndex: isset($current['uvi']) ? round((float) $current['uvi'], 1) : null,
            dewPoint: $this->dewPoint((float) $current['main']['temp'], (int) $current['main']['humidity']),
            sunrise: (int) ($current['sys']['sunrise'] ?? 0),
            sunset: (int) ($current['sys']['sunset'] ?? 0),
            isDay: str_ends_with((string) $weather['icon'], 'd'),
        );
    }

    private function buildHourlyForecast(array $list, int $currentTime, int $offset): array
    {
        $future = collect($list)
            ->filter(fn (array $entry) => $entry['dt'] >= $currentTime)
            ->values();

        return $future
            ->take(8)
            ->map(fn (array $entry) => new HourlyForecast(
                time: (int) $entry['dt'],
                temp: round((float) $entry['main']['temp'], 1),
                condition: (string) $entry['weather'][0]['main'],
                description: (string) $entry['weather'][0]['description'],
                code: (string) $entry['weather'][0]['icon'],
                pop: isset($entry['pop']) ? round((float) $entry['pop'] * 100) : null,
                windSpeed: round((float) $entry['wind']['speed'], 1),
            ))
            ->all();
    }

    private function buildDailyForecast(array $list, int $offset): array
    {
        return collect($list)
            ->groupBy(fn (array $entry) => gmdate('Y-m-d', (int) $entry['dt'] + $offset))
            ->map(function ($entries) use ($offset) {
                $peak = $entries->sortByDesc(fn (array $entry) => (float) $entry['main']['temp_max'])->first();

                return new DailyForecast(
                    date: gmdate('Y-m-d', (int) $peak['dt'] + $offset),
                    condition: (string) $peak['weather'][0]['main'],
                    description: (string) $peak['weather'][0]['description'],
                    code: (string) $peak['weather'][0]['icon'],
                    tempMax: round((float) $peak['main']['temp_max'], 1),
                    tempMin: round((float) $entries->min(fn (array $entry) => (float) $entry['main']['temp_min']), 1),
                    pop: isset($peak['pop']) ? round((float) $peak['pop'] * 100) : null,
                );
            })
            ->values()
            ->all();
    }

    private function dewPoint(float $temp, int $humidity): float
    {
        $alpha = (17.27 * $temp / (237.7 + $temp)) + log(max($humidity, 1) / 100);

        return round(237.7 * $alpha / (17.27 - $alpha), 1);
    }
}
