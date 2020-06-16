<?php

namespace App\Filters;

use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TripsFilter extends QueryFilter
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function driver_id(int $driver_id)
    {
        return $this->builder->whereHas('shift', function ($builder) use ($driver_id) {
            $builder->whereDriverId($driver_id);
        });
    }

    public function start(string $date)
    {
        $dateObject = Carbon::createFromFormat('m/d/Y', $date);

        return $this->builder->whereDate(Trip::UPDATED_AT, '>=', $dateObject);
    }

    public function end(string $date)
    {
        $dateObject = Carbon::createFromFormat('m/d/Y', $date);

        return $this->builder->whereDate(Trip::UPDATED_AT, '<=', $dateObject);
    }
}
