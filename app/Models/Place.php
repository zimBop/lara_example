<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Place
 *
 * @property int $id
 * @property int $client_id
 * @property string $name Name of the place given by a client
 * @property string $place_id Google Places API Query Autocomplete - place_id
 * @property string $description Google Places API Query Autocomplete - description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place wherePlaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Place whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Place extends Model
{
    public const CLIENT_ID = 'client_id';
    public const DESCRIPTION = 'description';
    public const ID = 'id';
    public const NAME = 'name';
    public const PLACE_ID = 'place_id';

    protected $fillable = [
        self::CLIENT_ID,
        self::DESCRIPTION,
        self::NAME,
        self::PLACE_ID,
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
