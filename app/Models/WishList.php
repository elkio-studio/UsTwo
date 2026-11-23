<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WishList extends Model
{
    protected $fillable = ['name'];

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }
}
