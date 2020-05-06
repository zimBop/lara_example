<?php

namespace App\Http\Requests\TripOrder;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'origin' => ['required', 'array'],
            'destination' => ['required', 'array'],
            'waypoints' => 'array',
            'waypoints.*' => 'array',
        ];
    }
}
