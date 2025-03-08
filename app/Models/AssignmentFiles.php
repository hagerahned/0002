<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignmentFiles extends Model
{
    use SoftDeletes;
    protected $table = 'assignment_files';
    protected $guarded = [];
    public function assignment(){
        return $this->belongsTo(Assignment::class);
    }
}
