<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotePaxDetailLog extends Model
{
    protected $fillable = [ 'quote_id', 'full_name', 'email', 'contact', 'date_of_birth', 'bedding_preference', 'dinning_preference'];
}
