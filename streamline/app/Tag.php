<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * Attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'tasks_completed', 'average_time', 'average_accuracy', 'task_over_to_under', 'color', 'team', 'userID'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'taggable');
    }
}
