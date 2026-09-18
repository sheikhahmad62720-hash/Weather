<?php

namespace App\Data;

final readonly class HourlyForecast
{
    public function __construct(
        public int $time,
        public float $temp,
        public string $condition,
        public string $description,
        public string $code,
        public ?float $pop,
        public float $windSpeed,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            time: (int) $data['time'],
            temp: (float) $data['temp'],
            condition: (string) $data['condition'],
            description: (string) $data['description'],
            code: (string) $data['code'],
            pop: isset($data['pop']) ? (float) $data['pop'] : null,
            windSpeed: (float) $data['wind_speed'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'time' => $this->time,
            'temp' => $this->temp,
            'condition' => $this->condition,
            'description' => $this->description,
            'code' => $this->code,
            'pop' => $this->pop,
            'wind_speed' => $this->windSpeed,
        ];
    }
}
