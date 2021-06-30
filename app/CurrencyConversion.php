<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrencyConversion extends Model
{
    protected $fillable = [
        'from',
        'to',
        'value',
        'manual_rate',
    ];
}
