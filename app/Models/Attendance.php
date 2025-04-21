<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $table = 'attendances';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class)->where('role','student');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function instructor()
    {
        return $this->belongsTo(User::class)->where('role','instructor');
    }

    
}
