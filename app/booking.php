<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = ['id'];
    
    
    function getSeason() {
        return $this->hasOne(season::class, 'id','season_id');
    }
}
