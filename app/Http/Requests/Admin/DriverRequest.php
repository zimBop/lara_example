<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Driver;

class DriverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $driver = $this->route('driver');
        return [
            Driver::FIRST_NAME => ['required', 'string', 'max:100'],
            Driver::LAST_NAME => ['required', 'string', 'max:100'],
            Driver::EMAIL => [
                'required',
                'email',
                Rule::unique('drivers')->ignore(optional($driver)->id),
            ],
            Driver::PASSWORD => [
                'string',
                Rule::requiredIf($driver === null), // password required only if we create driver
            ],
            //Driver::IS_ACTIVE => ['required', 'boolean'], // saved for future
        ];
    }
}
