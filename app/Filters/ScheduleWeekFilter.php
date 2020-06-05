<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ScheduleWeekFilter extends QueryFilter
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function year(int $year)
    {
        return $this->builder->whereYear($year);
    }

    public function month(int $month)
    {
        return $this->builder->whereMonth($month);
    }

    public function number(int $number)
    {
        return $this->builder->whereNumber($number);
    }
}
