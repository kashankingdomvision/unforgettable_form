<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteDetail extends Model
{
    protected $fillable = [ 
        'quote_id', 'category_id', 'supplier_id', 'product_id', 'booking_method_id', 'booked_by_id', 
        'supervisor_id', 'date_of_service', 'booking_date', 'booking_due_date', 'service_details', 'booking_refrence', 
        'booking_type', 'supplier_currency', 'comments', 'estimated_cost', 'markup_amount', 'markup_percentage', 'selling_price', 
        'profit_percentage', 'selling_price_bc', 'markup_amount_bc', 'added_in_sage',
    ];
}
