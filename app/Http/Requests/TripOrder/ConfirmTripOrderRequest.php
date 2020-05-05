<?php

namespace App\Http\Requests\TripOrder;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmTripOrderRequest extends FormRequest
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
            'payment_method_id' => ['required', 'string'],
            'message_for_driver' => ['string'],
        ];
    }
}
