<?php

namespace App\Models;

use App\Services\AvatarService;

trait HasAvatar
{
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? AvatarService::getUrl($this->avatar) : null;
    }

    public function avatar()
    {
        return $this->morphOne(Avatar::class, 'model');
    }
}
