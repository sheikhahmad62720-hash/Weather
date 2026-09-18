<?php

namespace App\Exceptions;

class CityNotFoundException extends WeatherException
{
    public function __construct(string $message = 'We couldn\'t find that city.')
    {
        parent::__construct($message, status: 404);
    }
}
