<?php

namespace App\Data;

final readonly class CurrentWeather
{
    public function __construct(
        public float $temp,
        public float $feelsLike,
        public string $condition,
        public string $description,
        public string $code,
        public int $humidity,
        public float $windSpeed,
        public int $windDeg,
        public int $pressure,
        public ?int $visibility,
        public int $clouds,
        public ?float $uvIndex,
        public float $dewPoint,
        public int $sunrise,
        public int $sunset,
        public bool $isDay,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            temp: (float) $data['temp'],
            feelsLike: (float) $data['feels_like'],
            condition: (string) $data['condition'],
            description: (string) $data['description'],
            code: (string) $data['code'],
            humidity: (int) $data['humidity'],
            windSpeed: (float) $data['wind_speed'],
            windDeg: (int) $data['wind_deg'],
            pressure: (int) $data['pressure'],
            visibility: isset($data['visibility']) ? (int) $data['visibility'] : null,
            clouds: (int) $data['clouds'],
            uvIndex: isset($data['uv_index']) ? (float) $data['uv_index'] : null,
            dewPoint: (float) $data['dew_point'],
            sunrise: (int) $data['sunrise'],
            sunset: (int) $data['sunset'],
            isDay: (bool) $data['is_day'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'temp' => $this->temp,
            'feels_like' => $this->feelsLike,
            'condition' => $this->condition,
            'description' => $this->description,
            'code' => $this->code,
            'humidity' => $this->humidity,
            'wind_speed' => $this->windSpeed,
            'wind_deg' => $this->windDeg,
            'pressure' => $this->pressure,
            'visibility' => $this->visibility,
            'clouds' => $this->clouds,
            'uv_index' => $this->uvIndex,
            'dew_point' => $this->dewPoint,
            'sunrise' => $this->sunrise,
            'sunset' => $this->sunset,
            'is_day' => $this->isDay,
        ];
    }
}
