<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPaxDetailLog extends Model
{
    protected $fillable = [ 'booking_log_id', 'full_name', 'email', 'contact', 'date_of_birth', 'bedding_preference', 'dinning_preference'];
    
}
