<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpaceInvitation extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'status'];

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}