<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable=['title','content','image','slug','manager_id'];

    public function manager(){
        return $this->belongsTo(Manager::class);
    }
}
