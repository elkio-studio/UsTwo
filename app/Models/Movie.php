<?php

namespace App\Models;

use App\Models\Concerns\HasCatalogMetadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasCatalogMetadata;

    protected const CATALOG_PROVIDER = 'tmdb_id';
    protected const CATALOG_METADATA = ['title', 'release_year', 'overview', 'poster_path'];
    protected const EDITABLE_METADATA = ['title', 'release_year', 'overview', 'watched_on'];

    protected $fillable = ['title', 'release_year', 'watched_on', 'tmdb_id', 'poster_path', 'overview'];

    public function ratings(): HasMany
    {
        return $this->hasMany(MovieRating::class);
    }
}
