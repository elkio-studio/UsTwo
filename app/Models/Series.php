<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Series extends Model
{
    protected $table = 'series';

    protected $fillable = ['title', 'release_year', 'watched_on', 'tmdb_id', 'poster_path', 'overview'];

    public function ratings(): HasMany
    {
        return $this->hasMany(SeriesRating::class, 'series_id');
    }
}
