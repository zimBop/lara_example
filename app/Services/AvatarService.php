<?php

namespace App\Services;

use App\Models\Avatar;
use App\Constants\Disk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AvatarService
{
    /**
     * @param UploadedFile $file
     * @param string $disk
     * @param string|null $fileName
     * @return Avatar
     */
    public static function add(UploadedFile $file, $disk = Disk::CLIENT_AVATARS, string $fileName = null): Avatar
    {
        $avatar = new Avatar();
        $avatar->filesize = $file->getSize();
        $avatar->extension = $file->getClientOriginalExtension();

        $fileName = date('Y/m/')  . ($fileName ?: $file->hashName());

        $result = $file->storeAs('', $fileName, compact('disk'));

        $avatar->filename = $result ? $fileName : '';
        $avatar->save();

        return $avatar;
    }

    /**
     * @param string $fileName
     * @param string $disk
     * @return string
     */
    public static function delete(?string $fileName, $disk = Disk::CLIENT_AVATARS): string
    {
        if (!$fileName) {
            return '';
        }

        if (!Storage::disk($disk)->exists($fileName)) {
            return '';
        }

        $result = Storage::disk($disk)->delete($fileName);

        return $result ? '' : $fileName;
    }

    public static function getUrl(Avatar $avatar, $disk = Disk::CLIENT_AVATARS)
    {
        return Storage::disk($disk)->url($avatar->filename);
    }

    public static function update(?UploadedFile $file, Model $model, string $disk = Disk::CLIENT_AVATARS)
    {
        if ($model->avatar) {
            $model->avatar()->delete();
            self::delete($model->avatar->filename, $disk);
        }

        $avatar = self::add($file);

        if ($avatar) {
            $model->avatar()->save($avatar);
        }
    }
}
