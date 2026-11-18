<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reading extends Model
{
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
