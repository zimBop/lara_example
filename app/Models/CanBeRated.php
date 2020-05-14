<?php

namespace App\Models;

trait CanBeRated
{
    public function updateRating()
    {
        $reviews = $this->reviews;
        if ($reviews->count()) {
            $this->update([
                self::RATING => round($reviews->avg('rating'), 1)
            ]);
        }
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'model');
    }
}
