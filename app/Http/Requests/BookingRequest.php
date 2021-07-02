<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ref_no'                        =>  'required|string',
            'quote_no'                      =>  'required|string',
            'lead_passenger'                =>  'required|string',
            'brand_name'                    =>  'required',
            'type_of_holidays'              =>  'required',
            'sale_person'                   =>  'required',
            'season_id'                     =>  'required',
            'agency'                        =>  'required|in:true,false',
            'agency_contact_no'             =>  'required'|string,
            'agency_booking'                =>  'required',
            'currency'                      =>  'required',
            'pax_no'                        =>  'required|numeric',
            'dinning_preferences'           =>  'required|string',
            'bedding_preference'            =>  'required|string',
            'quote'                         =>  'required|array',
            'quote.*.booking_due_date'      =>  'required|date',
            'quote.*.supplier_currency'     =>  'required|string',
            'quote.*.estimated_cost'        =>  'required',
            'quote.*.markup_amount'         =>  'required',
            'quote.*.markup_percentage'     =>  'required',
            'quote.*.selling_price'         =>  'required',
            'quote.*.profit_percentage'     =>  'required',
        ];
    }
    
    public function attributes()
    {
        return [
            'ref_no'                        => 'Reference number',
            'quote_no'                      => 'Quote number',
            'lead_passenger'                => 'Lead Passenger name',
            'brand_name'                    => 'Brand name',
            'type_of_holidays'              => 'Holiday type',
            'sale_person'                   => 'Sale person',
            'season_id'                     => 'Season',
            'agency'                        => 'Agency',
            'agency_contact'                => 'Agency contact number',
            'agency_name'                   => 'Agency name',
            'currency'                      => 'Currency name',
            'pax_no'                        => 'Pax number',
            'dinning_preferences'           => 'Dinning preference',
            'bedding_preference'            => 'Bedding preference',
            'quote.*.booking_due_date'      => 'Booking Due Date',
            'quote.*.supplier_currency'     => 'Supplier currency',
            'quote.*.estimated_cost'        => 'Estimated cost',
            'quote.*.markup_amount'         => 'Markup amount',
            'quote.*.markup_percentage'     => 'Markup %',
            'quote.*.selling_price'         => 'Selling price',
            'quote.*.profit_percentage'     => 'profit %',
        ];
    }
}
