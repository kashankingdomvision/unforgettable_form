<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    
    
    function getSeason() {
        return $this->hasOne(season::class, 'id','season_id');
    }
    
    function getCurrency() {
        return $this->hasOne(Currency::class, 'code', 'currency');
    }
    
    function getBrand() {
        return $this->hasOne(Brand::class, 'id', 'brand_name');
    }

    function getHolidayType() {
        return $this->hasOne(HolidayType::class,'id', 'type_of_holidays' );
    }

    public function getBookingPaxDetail()
    {
        return $this->hasMany(BookingPaxDetail::class, 'booking_id', 'id');
    }   
}
