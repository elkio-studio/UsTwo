<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'note', 'status'];

    protected $casts = ['latitude' => 'float', 'longitude' => 'float'];
}
