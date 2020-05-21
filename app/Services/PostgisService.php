<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PostgisService
{
    /**
     * @see https://en.wikipedia.org/wiki/Decimal_degrees
     */
    protected const SEARCH_RADIUS_DEGREES = 1;

    /**
     * Check that city polygon contains bounds of the client route
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

    public static function findClosestCityId(float $longitude, float $latitude): ?int
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

        return $res[0]->id ?? null;
    }
}
