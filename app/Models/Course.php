<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable=['title','description','image','slug','course_start','course_end','manager_id','apply_start','apply_end'];

    public function manager(){
        return $this->belongsTo(Manager::class);
    }
    public function instructor(){
        return $this->hasOne(Instructor::class);
    }

    public function students(){
        return $this->belongsToMany(User::class,'course_user')->withPivot('status')->withTimestamps();
    }
}
