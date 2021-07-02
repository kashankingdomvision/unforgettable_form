<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingLog extends Model
{
    protected $fillable = [ 
        'booking_log_id', 'season_id', 'brand_id', 'currency_id', 'holiday_type_id', 'ref_name', 'ref_no', 'quote_ref', 
        'lead_passenger', 'sale_person', 'agency', 'agency_name', 'agency_contact', 'dinning_preference', 'bedding_preference', 
        'pax_no', 'markup_amount', 'markup_percentage', 'selling_price', 'profit_percentage', 'selling_currency_oc', 'selling_price_oc',
        'amount_per_person'
    ];
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