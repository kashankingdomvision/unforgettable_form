<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class booking_email extends Model
{
    protected $guarded = [];

    public function booking(){
    	return $this->belongsTo('App\booking');
    }
}
