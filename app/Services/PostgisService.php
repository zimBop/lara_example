<?php

namespace App\Services;

use App\Constants\TripMessages;
use App\Constants\TripStatuses;
use App\Exceptions\Trip\TripException;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PostgisService
{
    /**
     * @see https://en.wikipedia.org/wiki/Decimal_degrees
     */
    protected const SEARCH_RADIUS_DEGREES = 1;

    protected const CLOSEST_DRIVERS_NUMBER = 5;

    /**
     * Check that city polygon contains bounds of the client's route
     *
     * @param int $cityId
     * @param array $bounds
     * @return bool
     */
    public static function isCityPolygonContainsRouteBounds(int $cityId, array $bounds): bool
    {
        $multipoint = 'MULTIPOINT(';

        foreach ($bounds as $bound) {
            $multipoint .= (float)$bound['lng'] . ' ' . (float)$bound['lat'] . ',';
        }

        $multipoint = rtrim($multipoint, ',') . ')';

        $result = DB::select("
            SELECT ST_Contains(
                (select polygon from cities where id = {$cityId}),
                ST_GeomFromText('{$multipoint}', 4326)
            ) as is_contains;
        ");

        return $result[0]->is_contains ?? false;
    }

    public static function findClosestCityId(float $longitude, float $latitude): int
    {
        $res = DB::select("
            WITH point AS (
                SELECT ST_GeomFromText('POINT({$longitude} {$latitude})', 4326) AS geom
            )
            SELECT
                c.id,
                ST_Distance(point.geom, c.center) AS distance
            FROM cities c
            JOIN point
            ON ST_Dwithin(point.geom, c.center, " . self::SEARCH_RADIUS_DEGREES . ")
            ORDER BY distance
            LIMIT 1
        ");

        if (!$res) {
            throw new TripException(200, TripMessages::CITY_NOT_FOUND);
        }

        return $res[0]->id;
    }

    public static function findClosestDrivers(float $longitude, float $latitude, int $cityId, bool $withoutActiveTrips = true): Collection
    {
        return Driver::whereHas('shifts', static function ($query) use ($withoutActiveTrips) {
                $query->active()
                    ->when($withoutActiveTrips, static function ($query) {
                        return $query->whereDoesntHave('trips', static function ($query) {
                            $query->active()->where('status', '<', TripStatuses::UNRATED);
                        });
                    });
            })
            ->select('drivers.*')
            ->join('shifts', 'shifts.driver_id', '=', 'drivers.id')
            ->join('driver_locations', 'driver_locations.shift_id', '=', 'shifts.id')
            ->whereRaw(
                '(select count(*) from shift_trip_order where shift_trip_order.shift_id = shifts.id) <= '
                . config('app.max_trip_orders_per_driver')
            )
            ->where('shifts.city_id', $cityId)
            ->whereNull('shifts.finished_at')
            ->whereRaw("shifts.started_at > (NOW() - INTERVAL '1 DAY')")
            ->orderByRaw("driver_locations.location <-> ST_GeomFromText('POINT($longitude $latitude)', 4326)")
            ->limit(self::CLOSEST_DRIVERS_NUMBER)
            ->get();
    }
}
