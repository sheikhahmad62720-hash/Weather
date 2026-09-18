<?php

namespace App\Data;

final readonly class DailyForecast
{
    public function __construct(
        public string $date,
        public string $condition,
        public string $description,
        public string $code,
        public float $tempMax,
        public float $tempMin,
        public ?float $pop,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            date: (string) $data['date'],
            condition: (string) $data['condition'],
            description: (string) $data['description'],
            code: (string) $data['code'],
            tempMax: (float) $data['temp_max'],
            tempMin: (float) $data['temp_min'],
            pop: isset($data['pop']) ? (float) $data['pop'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'condition' => $this->condition,
            'description' => $this->description,
            'code' => $this->code,
            'temp_max' => $this->tempMax,
            'temp_min' => $this->tempMin,
            'pop' => $this->pop,
        ];
    }
}
