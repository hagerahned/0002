<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{

    protected $table = 'likes';
    protected $guarded = [];

    public function likeable()
    {
        return $this->morphTo();
    }
}
