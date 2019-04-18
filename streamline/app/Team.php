<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    public function documents(){
        return $this->hasMany('\App\Document');
    }

}
