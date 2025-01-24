<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Slug{
    static function make(Model $model,string $text){
        $slug = Str::slug($text,'');
        if($model->withTrashed()->where('username',$slug)->exists()){
            $newSlug = $slug . random_int(0,1000000);
        }

        return $newSlug?? $slug;
    }
}