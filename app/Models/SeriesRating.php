<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeriesRating extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'score', 'note'];
}
