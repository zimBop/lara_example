<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\VehicleConstants;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Color\Hex;

class Vehicle extends Model
{
    use SoftDeletes;

    public const ID = 'id';
    public const LICENSE_PLATE = 'license_plate';
    public const BRAND_ID = 'brand_id';
    public const MODEL_ID = 'model_id';
    public const COLOR_ID = 'color_id';
    public const STATUS_ID = 'status_id';

    public const BRAND = 'brand';
    public const MODEL = 'model';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::LICENSE_PLATE,
        self::BRAND_ID,
        self::MODEL_ID,
        self::COLOR_ID,
        self::STATUS_ID,
    ];

    protected $appends = [
        self::BRAND,
        self::MODEL,
    ];

    /**
     * Get Brand name, e.g. "Tesla"
     * @return string
     */
    public function getBrandAttribute(): string
    {
        return VehicleConstants::BRANDS[$this->brand_id]['name'];
    }

    /**
     * Get Model name, e.g. "Model X"
     * @return string
     */
    public function getModelAttribute(): string
    {
        return VehicleConstants::BRANDS[$this->brand_id]['models'][$this->model_id];
    }

    /**
     * Get full model name, e.g. "Tesla Model X"
     * @return string
     * */
    public function getNameAttribute(): string
    {
        return sprintf('%s %s', $this->brand, $this->model);
    }

    public function getStatusDataAttribute()
    {
        return $this->getStatusData($this->status_id);
    }

    public function getStatusData(?int $status_id): array
    {
        $statuses = VehicleConstants::STATUSES;
        return ($status_id && array_key_exists($status_id, $statuses)) ? $statuses[$status_id] : $statuses;
    }

    public function getColorDataAttribute()
    {
        return $this->getColorData($this->color_id);
    }

    public function getColorData(?int $color_id)
    {
        $colors = collect(VehicleConstants::COLORS)->map(static function($item){
            $rgb = Hex::fromString($item['hex'])->toRgb();
            $item['red'] = $rgb->red();
            $item['green'] = $rgb->green();
            $item['blue'] = $rgb->blue();

            return $item;
        })->toArray();

        return ($color_id && array_key_exists($color_id, $colors)) ? $colors[$color_id] : $colors;
    }
}
