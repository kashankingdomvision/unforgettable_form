<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingLog extends Model
{
    function getBrand() {
        return $this->hasOne(Brand::class, 'id', 'brand_name');
    }

    function getHolidayType() {
        return $this->hasOne(HolidayType::class,'id', 'type_of_holidays' );
    }
    
    public function getBookingPaxDetailLog()
    {
        return $this->hasMany(BookingPaxDetailLog::class, 'booking_log_id', 'id');
    }
}