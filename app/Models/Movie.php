<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    protected $fillable = ['title', 'release_year', 'watched_on', 'tmdb_id', 'poster_path', 'overview'];

    public function ratings(): HasMany
    {
        return $this->hasMany(MovieRating::class);
    }
}
