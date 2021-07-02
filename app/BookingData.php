<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingData extends Model
{
    protected $fillable = [ 
        'booking_id', 'ask_for_transfer', 'reponsible_person_ti', 'last_date_ti', 'transfer_detail', 
        'transfer_organize', 'reponsible_person_to', 'last_date_to', 'transfer_organize_detail', 'itinerary_finalised', 
        'reponsible_person_if', 'last_date_if', 'itinerary_finalised_detail', 'itinerary_finalised_date', 'travel_document_prepared', 
        'reponsible_person_tdp', 'last_date_tdp', 'travel_document_prepared_date', 'travel_document_sent', 'reponsible_person_tds', 
        'last_date_tds', 'travel_document_sent_detail', 'travel_document_sent_date', 'app_login_sent', 'reponsible_person_als', 
        'last_date_als', 'app_login_sent_detail',
    ];
}
