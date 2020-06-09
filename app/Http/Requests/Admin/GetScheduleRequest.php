<?php

namespace App\Http\Requests\Admin;

use App\Models\ScheduleWeek;
use Illuminate\Foundation\Http\FormRequest;

class GetScheduleRequest extends FormRequest
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
            ScheduleWeek::YEAR => ['integer'],
            ScheduleWeek::NUMBER => ['integer'],
        ];
    }
}
