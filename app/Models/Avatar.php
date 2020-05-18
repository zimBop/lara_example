<?php

namespace App\Models;

use App\Constants\Disk;
use App\Services\AvatarService;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Avatar
 *
 * @property int $id
 * @property string $filename
 * @property string $extension
 * @property int $filesize
 * @property int|null $model_id
 * @property string|null $model_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar whereFilesize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Avatar whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Avatar extends Model
{
    public const FILENAME = 'filename';
    public const EXTENSION = 'extension';
    public const FILESIZE = 'filesize';
    public const MODEL_ID = 'model_id';
    public const MODEL_TYPE = 'model_type';
    public const FILE_INPUT_NAME = 'avatar';
    public const DISK = 'disk';

    protected $fillable = [
        self::FILENAME,
        self::FILESIZE,
    ];
}
