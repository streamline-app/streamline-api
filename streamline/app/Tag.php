<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function task(){
    //    return $this->morphedByMany('App\Task', 'taggable');
        return $this->belongsToMany(Task::class, 'taggable');
    }
}
