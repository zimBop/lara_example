<?php

namespace App\Http\Requests\Client\Place;

use App\Models\Place;
use Illuminate\Foundation\Http\FormRequest;

class StorePlaceRequest extends FormRequest
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
            Place::DESCRIPTION => ['required', 'string'],
            Place::NAME => ['required', 'string'],
            Place::PLACE_ID => ['required', 'string'],
        ];
    }
}
