<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\VehicleConstants;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Color\Hex;

/**
 * App\Models\Vehicle
 *
 * @property int $id
 * @property string $license_plate
 * @property int $brand_id
 * @property int $model_id
 * @property int|null $color_id
 * @property int $status_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $brand
 * @property-read mixed $color_data
 * @property-read string $model
 * @property-read string $name
 * @property-read mixed $status_data
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Vehicle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle whereColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle whereLicensePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Vehicle withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Vehicle withoutTrashed()
 * @mixin \Eloquent
 */
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

    /**
     * Set color to null if empty value saved
     * @param $value
     */
    public function setColorIdAttribute($value)
    {
        $this->attributes[self::COLOR_ID] = $value ?? null;
    }

    /**
     * Capitalize license plate number before save to DB
     * @param $value
     */
    public function setLicensePlateAttribute($value)
    {
        $this->attributes[self::LICENSE_PLATE] = mb_strtoupper($value);
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

    public function getColorData(?int $color_id = null)
    {
        if(!$color_id) {
            return null;
        }

        $colors = collect(VehicleConstants::COLORS)->map(static function($item){
            $rgb = Hex::fromString($item['hex'])->toRgb();
            $item['red'] = $rgb->red();
            $item['green'] = $rgb->green();
            $item['blue'] = $rgb->blue();

            return $item;
        })->toArray();

        return (array_key_exists($color_id, $colors)) ? $colors[$color_id] : $colors;
    }
}
