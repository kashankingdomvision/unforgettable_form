<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
class Booking extends Model
{
    protected $fillable = [ 
        'booking_id', 'season_id', 'brand_id', 'currency_id', 'holiday_type_id', 'ref_name', 'ref_no', 'quote_ref', 
        'lead_passenger', 'sale_person', 'agency', 'agency_name', 'agency_contact', 'dinning_preference', 'bedding_preference', 
        'pax_no', 'markup_amount', 'markup_percentage', 'selling_price', 'profit_percentage', 'selling_currency_oc', 'selling_price_oc',
        'amount_per_person'
    ];
    
    public function getVersionAttribute()
    {
        return 'Booking version '.$this->count().' '.Str::random(4).' '.$this->formated_created_at. ' By '. $this->getQuote->getUser->name;
    }
    
    public function getFormatedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('d/m/Y');
    }
    
    public function getBookingDetail()
    {
        return $this->hasMany(BookingDetail::class, 'booking_id', 'id');
    }
    
    function getSeason() {
        return $this->hasOne(season::class, 'id','season_id');
    }
    
    function getCurrency() {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }
    
    function getBrand() {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    function getHolidayType() {
        return $this->hasOne(HolidayType::class,'id', 'holiday_type_id' );
    }

    public function getBookingPaxDetail()
    {
        return $this->hasMany(BookingPaxDetail::class, 'booking_id', 'id');
    }
    
    public function getBookingData()
    {
        return $this->hasOne(BookingData::class, 'id', 'booking_id');
    }
    
    public function getQuote()
    {
        return $this->hasOne(Quote::class, 'id', 'quote_id');
    }
    
    public function getBookingLogs()
    {
        return $this->hasMany(BookingLog::class, 'booking_id', 'id');
    }
}
