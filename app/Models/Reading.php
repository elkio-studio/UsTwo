<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    public const CATEGORIES = ['books', 'manga', 'comics'];

    protected $fillable = [
        'category', 'title', 'title_key', 'description', 'author', 'publication_date',
        'cover_url', 'provider', 'record_type', 'external_id',
    ];
}
