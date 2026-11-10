<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    public const USERNAME_PATTERN = 'regex:/^[A-Za-z0-9_]{3,32}$/D';

    protected $fillable = ['name', 'email', 'password', 'username', 'username_key'];

    protected $hidden = ['password', 'username_key'];

    protected $casts = ['password' => 'hashed'];

    public function spaces(): BelongsToMany
    {
        return $this->belongsToMany(Space::class);
    }
}
