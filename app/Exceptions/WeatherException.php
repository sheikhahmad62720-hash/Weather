<?php

namespace App\Exceptions;

use RuntimeException;

class WeatherException extends RuntimeException
{
    public function __construct(
        string $message,
        public int $status = 503,
    ) {
        parent::__construct($message);
    }
}
