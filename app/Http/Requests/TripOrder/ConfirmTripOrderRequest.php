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
        $data = [
            'payment_method_id' => ['string'],
            'message_for_driver' => ['string'],
        ];

        if (!$this->input('is_free_trip')) {
            $data['payment_method_id'][] = 'required';
        }

        return $data;
    }
}
