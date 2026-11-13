<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlaceList extends Model
{
    protected $fillable = ['name', 'emoji'];

    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }
}
