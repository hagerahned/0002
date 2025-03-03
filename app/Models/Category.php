<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $table = 'categories';
    protected $guarded = [];

    public function manager(){
        return $this->belongsTo(Manager::class);
    }

    public function courses(){
        return $this->hasMany(Course::class);
    }
}
