<?php

namespace App\Http\Requests\Admin;

use App\Models\ScheduleGap;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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
        return [
            ScheduleGap::START => ['required', 'array'],
            ScheduleGap::START . '.*' => ['date_format:H:i:s'],
            ScheduleGap::END => ['required', 'array'],
            ScheduleGap::END . '.*' => ['date_format:H:i:s'],
            'drivers' => ['array'],
            'drivers.*' => ['integer'],
            'cities' => ['array'],
            'cities.*' => ['integer'],
        ];
    }
}
