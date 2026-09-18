<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeatherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'city' => ['required_without:lat', 'string', 'max:120'],
            'lat' => ['required_without:city', 'numeric', 'between:-90,90'],
            'lon' => ['required_without:city', 'numeric', 'between:-180,180'],
            'units' => ['sometimes', 'in:metric,imperial'],
        ];
    }
}
