<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HolidayType extends Model
{
    protected $fillable = [	 
            'brand_id', 'name'
    ];
    
    public function getBrand()
    {
        return $this->hasOne(Brand::class, 'brand_id', 'id');
    }
}
