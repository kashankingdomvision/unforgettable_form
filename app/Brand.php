<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name', 'email_address', 'address', 'phone', 'logo',
    ];
    
   
    public function getHolidayTypes()
    {
        return $this->hasMany(HolidayType::class, 'brand_id', 'id');
    }
    
}
