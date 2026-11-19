<?php

namespace App\Models;

use App\Models\Concerns\HasCatalogMetadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reading extends Model
{
    use HasCatalogMetadata;

    protected const CATALOG_PROVIDER = 'provider';
    protected const CATALOG_METADATA = ['title', 'author', 'publication_date', 'description', 'cover_url'];
    protected const EDITABLE_METADATA = ['title', 'author', 'publication_date', 'description'];

    public const CATEGORIES = ['books', 'manga', 'comics'];

    protected $fillable = [
        'category', 'title', 'title_key', 'description', 'author', 'publication_date',
        'cover_url', 'provider', 'record_type', 'external_id',
    ];

    public function ratings(): HasMany
    {
        return $this->hasMany(ReadingRating::class);
    }
}
