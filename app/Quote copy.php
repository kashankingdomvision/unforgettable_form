<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [ 
        'quote_id', 'season_id', 'brand_id', 'currency_id', 'holiday_type_id', 'ref_name', 'ref_no', 'quote_ref', 
        'lead_passenger', 'sale_person', 'agency', 'agency_name', 'agency_contact', 'dinning_preference', 'bedding_preference', 
        'pax_no', 'markup_amount', 'markup_percentage', 'selling_price', 'profit_percentage', 'selling_currency_oc', 'selling_price_oc',
        'amount_per_person'
    ];
}
