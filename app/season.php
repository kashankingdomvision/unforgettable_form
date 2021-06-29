<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable= [ 
       'name',
       'start_date',
       'end_date',
       'default',
    ];
    public function booking()
    {
        return $this->hasMany('App\booking');
    }
}
