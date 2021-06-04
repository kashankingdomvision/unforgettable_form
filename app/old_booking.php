<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class old_booking extends Model
{
    protected $table = 'old_bookings';
    protected $guarded = [];

    public function season(){
    	return $this->belongsTo('App\season');
    }
    public function booking_email(){
        return $this->hasMany('App\booking_email');
    }
}
