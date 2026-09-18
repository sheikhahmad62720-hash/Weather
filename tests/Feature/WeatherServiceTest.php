<?php

namespace Tests\Feature;

use App\Services\WeatherService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherServiceTest extends TestCase
{
    public function test_it_aggregates_forecast_into_hourly_and_daily_data(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'api.openweathermap.org/data/2.5/weather*' => Http::response([
                'coord' => ['lat' => 31.5497, 'lon' => 74.3436],
                'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
                'main' => ['temp' => 32.4, 'feels_like' => 35.1, 'pressure' => 1009, 'humidity' => 55],
                'visibility' => 10000,
                'wind' => ['speed' => 3.1, 'deg' => 180],
                'clouds' => ['all' => 0],
                'dt' => 1000000,
                'sys' => ['country' => 'PK', 'sunrise' => 998000, 'sunset' => 1005000],
                'timezone' => 0,
                'name' => 'Lahore',
            ]),
            'api.openweathermap.org/data/2.5/forecast*' => Http::response([
                'list' => [
                    ['dt' => 1000000, 'main' => ['temp' => 32.4, 'temp_min' => 31.0, 'temp_max' => 33.0], 'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']], 'wind' => ['speed' => 3.1], 'pop' => 0.1],
                    ['dt' => 1003600, 'main' => ['temp' => 31.0, 'temp_min' => 30.0, 'temp_max' => 31.5], 'weather' => [['main' => 'Clouds', 'description' => 'few clouds', 'icon' => '02d']], 'wind' => ['speed' => 2.2], 'pop' => 0.2],
                    ['dt' => 1097200, 'main' => ['temp' => 30.0, 'temp_min' => 28.0, 'temp_max' => 31.0], 'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']], 'wind' => ['speed' => 1.8], 'pop' => 0.0],
                    ['dt' => 1100800, 'main' => ['temp' => 33.0, 'temp_min' => 29.0, 'temp_max' => 34.0], 'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']], 'wind' => ['speed' => 2.1], 'pop' => 0.0],
                    ['dt' => 1104400, 'main' => ['temp' => 28.5, 'temp_min' => 26.0, 'temp_max' => 30.0], 'weather' => [['main' => 'Clouds', 'description' => 'broken clouds', 'icon' => '04n']], 'wind' => ['speed' => 2.4], 'pop' => 0.1],
                    ['dt' => 1194400, 'main' => ['temp' => 29.0, 'temp_min' => 24.0, 'temp_max' => 29.0], 'weather' => [['main' => 'Clouds', 'description' => 'overcast clouds', 'icon' => '04d']], 'wind' => ['speed' => 1.5], 'pop' => 0.2],
                ],
            ]),
        ]);

        config(['services.weather.key' => 'test-key']);

        $weather = app(WeatherService::class)->forecast('Lahore');

        $this->assertCount(6, $weather->hourly);
        $this->assertCount(3, $weather->daily);

        $firstDaily = $weather->daily[0];
        $this->assertSame(1000000, $weather->hourly[0]->time);
        $this->assertSame(33.0, $firstDaily->tempMax);
        $this->assertSame(30.0, $firstDaily->tempMin);
        $this->assertSame(34.0, $weather->daily[1]->tempMax);
        $this->assertSame(29.0, $weather->daily[2]->tempMax);
        $this->assertSame(22.2, $weather->current->dewPoint);
    }

    public function test_it_returns_empty_results_when_the_geo_api_fails(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'api.openweathermap.org/geo/1.0/direct*' => Http::response([], 500),
        ]);

        config(['services.weather.key' => 'test-key']);

        $this->assertSame([], app(WeatherService::class)->search('Lah'));
    }
}
