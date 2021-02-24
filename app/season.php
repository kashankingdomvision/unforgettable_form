<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class season extends Model
{
    protected $guarded = [];

    public function booking(){
        return $this->hasMany('App\booking');
    }
}
