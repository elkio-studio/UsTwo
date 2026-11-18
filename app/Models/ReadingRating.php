<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingRating extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'score', 'note'];
}
