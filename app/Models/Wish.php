<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wish extends Model
{
    protected $fillable = ['url', 'title', 'description', 'price_minor', 'currency'];

    protected $casts = ['price_minor' => 'integer'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
