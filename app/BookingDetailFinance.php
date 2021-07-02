<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingDetailFinance extends Model
{ 
    protected $fillable = [
        'booking_detail_id',
        'payment_method_id',
        'deposit_amount',
        'deposit_due_date',
        'paid_date',
        'upload_to_calender',
        'additional_date',
    ];
}
