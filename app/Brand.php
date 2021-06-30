<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $fillable = [
        'name', 'email_address', 'address', 'phone', 'logo',
    ];
    
   
    public function getHolidayTypes()
    {
        return $this->hasMany(HolidayType::class, 'brand_id', 'id');
    }
    
    public function getLogoAttribute($value)
    {
        return url(Storage::url($value));
    }
    
}
