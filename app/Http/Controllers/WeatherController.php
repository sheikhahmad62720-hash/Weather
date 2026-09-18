<?php

namespace App\Http\Controllers;

use App\Exceptions\WeatherException;
use App\Http\Requests\WeatherRequest;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(private readonly WeatherService $service) {}

    public function show(WeatherRequest $request): JsonResponse
    {
        try {
            $units = $request->input('units', 'metric');

            $weather = $request->filled('city')
                ? $this->service->forecast($request->string('city')->trim()->toString(), $units)
                : $this->service->forecastByCoordinates(
                    (float) $request->float('lat'),
                    (float) $request->float('lon'),
                    $units,
                );
        } catch (WeatherException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->status);
        }

        return response()->json($weather->toArray());
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'max:120'],
        ]);

        return response()->json([
            'results' => $this->service->search($request->string('q')->trim()->toString()),
        ]);
    }
}
