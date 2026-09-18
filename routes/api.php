<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/weather', [WeatherController::class, 'show'])
    ->name('weather.show');

Route::get('/weather/autocomplete', [WeatherController::class, 'autocomplete'])
    ->name('weather.autocomplete');
