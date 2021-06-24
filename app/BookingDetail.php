<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    protected $table = 'booking_details';

    protected $guarded = ['id'];

    public function getCategory()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::class,'id','supplier');
    }
}
