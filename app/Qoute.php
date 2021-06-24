<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qoute extends Model
{
    protected $table = 'qoutes';
    protected $guarded = [];


    public function season(){
    	return $this->belongsTo('App\season');
    }

    public function quotes()
    {
        return $this->belongsToMany('App\QouteDetail','qoute_details','qoute_id');
    }
    
    public function setPaxNameAttribute($value)
    {
        $this->attributes['pax_name'] = json_encode($value);
    }
    public function getBookingFormatedStatusAttribute()
    {
        $status = $this->qoute_to_booking_status;
        switch ($status) {
            case 1:
                return '<span class="badge badge-success">Booked</span>';
                break;
            case 0:
                return '<span class="badge badge-dark">Quote</span>';
                break;
        }
        
        return $status;
    }
    function getBrand() {
        return $this->hasOne(Brand::class,'id', 'brand_name' );
    }

    function getHolidayType() {
        return $this->hasOne(HolidayType::class,'id', 'type_of_holidays' );
    }

    function getCurrency() {
        return $this->hasOne(Currency::class, 'code', 'currency');
    }
    
    public function getPaxDetail()
    {
        return $this->hasMany(QuotePaxDetail::class, 'quote_id', 'id');
    }
    // public function categories()
    // {
    // 	return $this->belongsToMany('App\Model\user\category','category_posts')->withTimestamps();;
    // }
}
