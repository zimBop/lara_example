<?php

namespace App\Http\Requests\Device;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;

class DeviceRequest extends FormRequest
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
            Device::TOKEN => ['required', 'string'],
            Device::TYPE => ['required', 'integer'],
        ];
    }
}
