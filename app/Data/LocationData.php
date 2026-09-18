<?php

namespace App\Data;

final readonly class LocationData
{
    public function __construct(
        public string $name,
        public string $country,
        public float $lat,
        public float $lon,
        public int $offset,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            country: (string) $data['country'],
            lat: (float) $data['lat'],
            lon: (float) $data['lon'],
            offset: (int) $data['offset'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'country' => $this->country,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'offset' => $this->offset,
        ];
    }
}
