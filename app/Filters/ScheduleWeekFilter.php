<?php

namespace App\Filters;

use App\Models\ScheduleWeek;
use Illuminate\Http\Request;

class ScheduleWeekFilter extends QueryFilter
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function year(int $year)
    {
        return $this->builder->where(ScheduleWeek::YEAR, $year);
    }

    public function number(int $number)
    {
        return $this->builder->whereNumber($number);
    }
}
