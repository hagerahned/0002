<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable=['title','description','image','slug','start_at','end_at','manager_id'];

    public function manager(){
        return $this->belongsTo(Manager::class);
    }
    public function instructor(){
        return $this->hasOne(Instructor::class);
    }
}
