<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function restauturant() {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeRatingSortable($query, $direction = 'desc') {
        return $query->withAvg('reviews', 'score')
                 ->orderBy('reviews_avg_score', $direction);
    }
}
