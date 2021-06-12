<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateDetail extends Model
{
    protected $fillable = [
        'template_id',
        'date_of_service',
        'service_details',
        'category_id',
        'supplier',
        'booking_date',
        'booking_due_date',
        'booking_method',
        'booked_by',
        'booking_refrence',
        'booking_type',
        'comments',
        'supplier_currency',
        'cost',
        'actual_cost',
        'supervisor_id',
        'added_in_sage',
        'qoute_base_currency',
    ];
    
    public function getCategory()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier');
    }
    
    public function getSupervisor()
    {
        return $this->hasOne(User::class, 'id', 'supervisor_id');
    }
    
    public function getBookedBy()
    {
        return $this->hasOne(User::class, 'id', 'booked_by');
    }
    
    public function getBookinMethod()
    {
        return $this->hasOne(BookingMethod::class, 'id', 'booking_method');
    }
}
