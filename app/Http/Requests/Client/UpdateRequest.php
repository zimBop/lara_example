<?php

namespace App\Http\Requests\Client;

use App\Models\Avatar;
use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
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
        /**
         * @var $client Client
         */
        $client = Auth::guard('client')->user();
        $clientIdPart = $client ? ",email," . $client->id : '';

        $rules = [
            Client::FIRST_NAME => ['string', 'max:255'],
            Client::LAST_NAME => ['string', 'max:255'],
            Client::EMAIL => ['email', 'max:255', 'unique:clients' . $clientIdPart],
            Client::BIRTHDAY => ['date_format:m/d/Y'],
            Avatar::FILE_INPUT_NAME => ['image', 'max:10000'],
        ];

        if ($client && !$client->isRegistrationCompleted()) {
            $rules[Client::PASSWORD] = ['required', 'string', 'min:6', 'max:255'];
            $rules[Client::FIRST_NAME][] = 'required';
            $rules[Client::LAST_NAME][] = 'required';
        }

        return $rules;
    }
}
