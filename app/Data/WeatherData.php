<?php

namespace App\Data;

final readonly class WeatherData
{
    /**
     * @param  array<int, HourlyForecast>  $hourly
     * @param  array<int, DailyForecast>  $daily
     */
    public function __construct(
        public LocationData $location,
        public CurrentWeather $current,
        public array $hourly,
        public array $daily,
        public string $units,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            location: LocationData::fromArray($data['location']),
            current: CurrentWeather::fromArray($data['current']),
            hourly: array_map(
                fn (array $item) => HourlyForecast::fromArray($item),
                $data['hourly'],
            ),
            daily: array_map(
                fn (array $item) => DailyForecast::fromArray($item),
                $data['daily'],
            ),
            units: (string) $data['units'],
        );
    }

    /**
     * @return array{
     *     location: array<string, mixed>,
     *     current: array<string, mixed>,
     *     hourly: array<int, array<string, mixed>>,
     *     daily: array<int, array<string, mixed>>,
     *     units: string,
     * }
     */
    public function toArray(): array
    {
        return [
            'location' => $this->location->toArray(),
            'current' => $this->current->toArray(),
            'hourly' => array_map(fn (HourlyForecast $item) => $item->toArray(), $this->hourly),
            'daily' => array_map(fn (DailyForecast $item) => $item->toArray(), $this->daily),
            'units' => $this->units,
        ];
    }
}
