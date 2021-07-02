<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    protected $fillable = [ 
        'booking_id', 'category_id', 'supplier_id', 'product_id', 'booking_method_id', 'booked_by_id', 
        'supervisor_id', 'date_of_service', 'booking_date', 'booking_due_date', 'service_details', 'booking_refrence', 
        'booking_type', 'supplier_currency', 'comments', 'estimated_cost', 'markup_amount', 'markup_percentage', 'selling_price', 
        'profit_percentage', 'selling_price_bc', 'markup_amount_bc', 'added_in_sage',
    ];

    public function getCategory()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::class,'id','supplier');
    }
    
    public function getBookingFinance()
    {
        return $this->hasMany(BookingDetailFinance::class, 'booking_detail_id', 'id');
        
    }
    
}
