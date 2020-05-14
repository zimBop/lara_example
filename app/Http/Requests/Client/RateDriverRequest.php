<?php

namespace App\Http\Requests\Client;

use App\Models\Review;
use App\Models\Tip;
use Illuminate\Foundation\Http\FormRequest;

class RateDriverRequest extends FormRequest
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
            Review::TRIP_ID => ['required', 'integer'],
            Review::RATING => ['required', 'integer', 'between:1,5'],
            Review::COMMENT => ['string'],
            Tip::AMOUNT => ['integer'],
            Tip::PAYMENT_METHOD_ID => ['string'],
        ];
    }
}
