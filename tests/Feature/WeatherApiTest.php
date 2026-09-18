<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    public function test_it_returns_weather_for_a_city(): void
    {
        Http::preventStrayRequests();

        $this->stubWeatherApi();

        $response = $this->getJson('/api/weather?city=Lahore');

        $response->assertOk()
            ->assertJsonPath('location.name', 'Lahore')
            ->assertJsonPath('location.country', 'PK')
            ->assertJsonPath('location.offset', 18000)
            ->assertJsonPath('current.temp', 32.4)
            ->assertJsonPath('current.feels_like', 35.1)
            ->assertJsonPath('current.condition', 'Clear')
            ->assertJsonPath('current.humidity', 55)
            ->assertJsonPath('current.is_day', true)
            ->assertJsonPath('units', 'metric')
            ->assertJsonCount(8, 'hourly');
    }

    public function test_it_returns_weather_for_coordinates(): void
    {
        Http::preventStrayRequests();

        $this->stubWeatherApi();

        $response = $this->getJson('/api/weather?lat=31.55&lon=74.34');

        $response->assertOk()
            ->assertJsonPath('location.name', 'Lahore')
            ->assertJsonPath('current.temp', 32.4);
    }

    public function test_it_returns_friendly_message_when_the_city_does_not_exist(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'api.openweathermap.org/data/2.5/weather*' => Http::response(['message' => 'city not found'], 404),
            'api.openweathermap.org/data/2.5/forecast*' => Http::response(['message' => 'city not found'], 404),
        ]);

        $this->configureApiCredentials();

        $response = $this->getJson('/api/weather?city=NotACity');

        $response->assertNotFound()
            ->assertJsonPath('message', "We couldn't find that city.");
    }

    public function test_it_returns_a_friendly_message_when_the_api_key_is_missing(): void
    {
        Http::preventStrayRequests();

        config(['services.weather.key' => '']);

        $response = $this->getJson('/api/weather?city=Lahore');

        $response->assertStatus(503)
            ->assertJsonPath('message', 'Weather data is not available at the moment. Please try again later.');
    }

    public function test_it_returns_a_friendly_message_when_the_upstream_api_is_rate_limited(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'api.openweathermap.org/data/2.5/weather*' => Http::response(['message' => 'rate limit exceeded'], 429),
            'api.openweathermap.org/data/2.5/forecast*' => Http::response(['message' => 'rate limit exceeded'], 429),
        ]);

        $this->configureApiCredentials();

        $response = $this->getJson('/api/weather?city=Lahore');

        $response->assertStatus(429)
            ->assertJsonPath('message', "We've received too many requests recently. Please wait a moment and try again.");
    }

    public function test_it_returns_a_friendly_message_when_the_upstream_api_key_is_rejected(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'api.openweathermap.org/data/2.5/weather*' => Http::response(['message' => 'Invalid API key'], 401),
            'api.openweathermap.org/data/2.5/forecast*' => Http::response(['message' => 'Invalid API key'], 401),
        ]);

        $this->configureApiCredentials();

        $response = $this->getJson('/api/weather?city=Lahore');

        $response->assertStatus(503)
            ->assertJsonPath('message', 'The weather service could not authenticate this request.');
    }

    public function test_it_rejects_unknown_units(): void
    {
        $this->configureApiCredentials();

        $response = $this->getJson('/api/weather?city=Lahore&units=kelvin');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('units');
    }

    public function test_it_requires_coordinates_to_be_present_together(): void
    {
        $this->configureApiCredentials();

        $response = $this->getJson('/api/weather?lat=31.55');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('lon');
    }

    public function test_it_caches_weather_responses(): void
    {
        Http::preventStrayRequests();

        $this->stubWeatherApi();

        $this->getJson('/api/weather?city=Lahore')->assertOk();
        $this->getJson('/api/weather?city=Lahore')->assertOk();

        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/weather'));
        Http::assertSent(fn ($request) => str_contains($request->url(), '/forecast'));
    }

    public function test_autocomplete_returns_matching_cities(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'api.openweathermap.org/geo/1.0/direct*' => Http::response([
                ['name' => 'Lahore', 'state' => 'Punjab', 'country' => 'PK', 'lat' => 31.5497, 'lon' => 74.3436],
            ]),
        ]);

        $this->configureApiCredentials();

        $response = $this->getJson('/api/weather/autocomplete?q=Lah');

        $response->assertOk()
            ->assertJsonPath('results.0.name', 'Lahore')
            ->assertJsonPath('results.0.country', 'PK');
    }

    private function configureApiCredentials(): void
    {
        config([
            'services.weather.url' => 'https://api.openweathermap.org/data/2.5',
            'services.weather.geo_url' => 'https://api.openweathermap.org/geo/1.0',
            'services.weather.key' => 'test-key',
        ]);
    }

    private function stubWeatherApi(): void
    {
        $this->configureApiCredentials();

        Http::fake([
            'api.openweathermap.org/data/2.5/weather*' => Http::response($this->currentWeatherPayload()),
            'api.openweathermap.org/data/2.5/forecast*' => Http::response(['list' => $this->forecastListPayload()]),
        ]);
    }

    private function currentWeatherPayload(): array
    {
        return [
            'coord' => ['lat' => 31.5497, 'lon' => 74.3436],
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
            'main' => [
                'temp' => 32.4,
                'feels_like' => 35.1,
                'pressure' => 1009,
                'humidity' => 55,
                'temp_min' => 31.0,
                'temp_max' => 33.0,
            ],
            'visibility' => 10000,
            'wind' => ['speed' => 3.1, 'deg' => 180],
            'clouds' => ['all' => 0],
            'dt' => 1000000,
            'sys' => ['country' => 'PK', 'sunrise' => 998000, 'sunset' => 1005000],
            'timezone' => 18000,
            'name' => 'Lahore',
        ];
    }

    private function forecastListPayload(): array
    {
        return [
            ['dt' => 1000000, 'main' => ['temp' => 32.4, 'temp_min' => 31.0, 'temp_max' => 33.0], 'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']], 'wind' => ['speed' => 3.1], 'pop' => 0.1],
            ['dt' => 1003600, 'main' => ['temp' => 31.0, 'temp_min' => 30.0, 'temp_max' => 31.5], 'weather' => [['main' => 'Clouds', 'description' => 'few clouds', 'icon' => '02d']], 'wind' => ['speed' => 2.2], 'pop' => 0.2],
            ['dt' => 1007200, 'main' => ['temp' => 29.0, 'temp_min' => 28.0, 'temp_max' => 29.5], 'weather' => [['main' => 'Clouds', 'description' => 'scattered clouds', 'icon' => '03d']], 'wind' => ['speed' => 2.0], 'pop' => 0.3],
            ['dt' => 1010800, 'main' => ['temp' => 27.5, 'temp_min' => 26.0, 'temp_max' => 28.0], 'weather' => [['main' => 'Rain', 'description' => 'light rain', 'icon' => '10d']], 'wind' => ['speed' => 2.5], 'pop' => 0.5],
            ['dt' => 1014400, 'main' => ['temp' => 26.0, 'temp_min' => 25.0, 'temp_max' => 27.0], 'weather' => [['main' => 'Rain', 'description' => 'light rain', 'icon' => '10n']], 'wind' => ['speed' => 3.0], 'pop' => 0.6],
            ['dt' => 1097200, 'main' => ['temp' => 30.0, 'temp_min' => 28.0, 'temp_max' => 31.0], 'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']], 'wind' => ['speed' => 1.8], 'pop' => 0.0],
            ['dt' => 1100800, 'main' => ['temp' => 33.0, 'temp_min' => 29.0, 'temp_max' => 34.0], 'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']], 'wind' => ['speed' => 2.1], 'pop' => 0.0],
            ['dt' => 1104400, 'main' => ['temp' => 28.5, 'temp_min' => 26.0, 'temp_max' => 30.0], 'weather' => [['main' => 'Clouds', 'description' => 'broken clouds', 'icon' => '04n']], 'wind' => ['speed' => 2.4], 'pop' => 0.1],
            ['dt' => 1194400, 'main' => ['temp' => 29.0, 'temp_min' => 24.0, 'temp_max' => 29.0], 'weather' => [['main' => 'Clouds', 'description' => 'overcast clouds', 'icon' => '04d']], 'wind' => ['speed' => 1.5], 'pop' => 0.2],
        ];
    }
}
