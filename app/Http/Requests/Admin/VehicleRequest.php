<?php

namespace App\Http\Requests\Admin;

use App\Rules\Admin\ArrayKeyExists;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vehicle;
use App\Constants\VehicleConstants;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
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
        $vehicle = $this->route('vehicle');
        return [
            Vehicle::LICENSE_PLATE => [
                'required',
                'string',
                'max:15',
                Rule::unique('vehicles')->ignore(optional($vehicle)->id),
            ],
            Vehicle::BRAND_ID => ['required', 'integer', new ArrayKeyExists(VehicleConstants::BRANDS)],
            Vehicle::MODEL_ID => ['required', 'integer', new ArrayKeyExists(VehicleConstants::BRANDS[request()->brand_id]['models'])],
            Vehicle::COLOR_ID => ['nullable', 'integer', new ArrayKeyExists(VehicleConstants::COLORS)],
            Vehicle::STATUS_ID => ['required', 'integer', new ArrayKeyExists(VehicleConstants::STATUSES)],
        ];
    }
}
