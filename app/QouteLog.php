<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QouteLog extends Model
{
    function getBrand() {
        return $this->hasOne(Brand::class,'id', 'brand_name');
    }

    function getHolidayType() {
        return $this->hasOne(HolidayType::class,'id', 'type_of_holidays');
    }
    
    public function getPaxDetailLog()
    {
        return $this->hasMany(QuotePaxDetailLog::class, 'quote_id', 'id');
    }
}
