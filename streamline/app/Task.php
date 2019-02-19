<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function tags(){
        //return $this->morphToMany('App\Tag', 'taggable');
        return $this->belongsToMany(Tag::class, 'taggable');
    }
}
