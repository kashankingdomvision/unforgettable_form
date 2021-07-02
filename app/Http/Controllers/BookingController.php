<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Season;
use Cache;
use App\User;
use App\Booking;
use App\PaymentMethod;
use App\Airline;
use App\Brand;
use App\HolidayType;
use App\Currency;
use App\Category;
use App\Supplier;
use App\BookingMethod;
;
class BookingController extends Controller
{
    public $pagination = 10;
    
    public function season_Index()
    {
        $data['seasons'] = Season::orderBy('seasons.created_at', 'desc')->groupBy('seasons.id', 'seasons.name')->get(['seasons.id', 'seasons.name']);
        return view('bookings.season_listing', $data);
    }
    
    private function curl_data($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return $output = curl_exec($ch);
    }
    
    public function delete_booking_season($id)
    {
        Season::destroy(decrypt($id));
        return Redirect::route('view-booking-season')->with('success_message', 'Deleted Successfully');
    }

    // public function update_booking(Request $request, $id)
    // {
    //     if ($request->isMethod('post')) {
    //         $this->validate($request, ['ref_no' => 'required'], ['required' => 'Reference number is required']);
    //         $this->validate($request, ['lead_passenger_name' => 'required'], ['required' => 'Lead Passenger Name is required']);
    //         $this->validate($request, ['brand_name' => 'required'], ['required' => 'Please select Brand Name']);
    //         $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
    //         $this->validate($request, ['sale_person' => 'required'], ['required' => 'Please select Sale Person']);
    //         $this->validate($request, ['season_id' => 'required|numeric'], ['required' => 'Please select Booking Season']);
    //         $this->validate($request, ['agency_name' => 'required_if:agency_booking,2'], ['required_if' => 'Agency Name is required']);
    //         $this->validate($request, ['agency_contact_no' => 'required_if:agency_booking,2'], ['required_if' => 'Agency No is required']);
    //         $this->validate($request, ['agency_booking' => 'required'], ['required' => 'Agency is required']);
    //         $this->validate($request, ['currency' => 'required'], ['required' => 'Booking Currency is required']);
    //         $this->validate($request, ['group_no' => 'required'], ['required' => 'Pax No is required']);
    //         $this->validate($request, ['dinning_preferences' => 'required'], ['required' => 'Dinning Preferences is required']);
    //         $this->validate($request, ['bedding_preference' => 'required'], ['required' => 'Bedding Preferences is required']);
            
    //         $this->validate($request, ["booking_due_date" => "required|array", "booking_due_date.*" => "required"]);
    //         $this->validate($request, ["cost" => "required|array", "cost.*" => "required"]);
    //         $this->validate($request, ['fb_airline_name_id' => 'required_if:flight_booked,yes'], ['required_if' => 'Airline is required']);
    //         $this->validate($request, ['fb_payment_method_id' => 'required_if:flight_booked,yes'], ['required_if' => 'Payment is required']);
    //         $this->validate($request, ['fb_booking_date' => 'required_if:flight_booked,yes'], ['required_if' => 'Booking Date is required']);
    //         $this->validate($request, ['fb_airline_ref_no' => 'required_if:flight_booked,yes'], ['required_if' => 'Airline Ref No is required']);
    //         $this->validate($request, ['flight_booking_details' => 'required_if:flight_booked,yes'], ['required_if' => 'Flight Booking Details is required']);
    //         $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Transfer Organised Details is required']);

    //         $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Itinerary Finalised Details is required']);
    //         $this->validate($request, ['itf_current_date' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Itinerary Finalised Date is required']);
    //         $this->validate($request, ['tdp_current_date' => 'required_if:document_prepare,yes'], ['required_if' => 'Travel Document Prepared Date is required']);

    //         $this->validate($request, ['documents_sent_details' => 'required_if:documents_sent,yes'], ['required_if' => 'Document Details is required']);
    //         $this->validate($request, ['tds_current_date' => 'required_if:documents_sent,yes'], ['required_if' => 'Travel Document Sent Date is required']);

    //         $this->validate($request, ['aps_person' => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Responsible Person is required']);
    //         $this->validate($request, ['aps_last_date' => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Date is required']);
    //         $this->validate($request, ['electronic_copy_details' => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'App Login Sent Details is required']);

    //         $season = season::find($request->season_id);


    //         $booking = Booking::find($id);
          
    //         $booking_log = new BookingLog;
    //         $bookingDetailLogNumber = $this->increment_log_no($this->get_log_no('BookingLog','booking_id', $id));
    //         $booking_log->booking_id = $booking->id;
    //         $booking_log->log_no = $bookingDetailLogNumber;
    //         // $booking_log->reference_name = $booking->reference_name;
    //         $booking_log->ref_no = $booking->ref_no;
    //         $booking_log->qoute_id = $booking->qoute_id;
    //         $booking_log->quotation_no = $booking->quotation_no;
    //         $booking_log->dinning_preferences = $booking->dinning_preferences;
    //         $booking_log->lead_passenger_name = $booking->lead_passenger_name;
    //         $booking_log->brand_name = $booking->brand_name;
    //         $booking_log->type_of_holidays = $booking->type_of_holidays;
    //         $booking_log->sale_person = $booking->sale_person;
    //         $booking_log->season_id = $booking->season_id;
    //         $booking_log->agency_booking = $booking->agency_booking;
    //         $booking_log->agency_name = $booking->agency_name;
    //         $booking_log->agency_contact_no = $booking->agency_contact_no;
    //         $booking_log->currency = $booking->currency;
    //         $booking_log->convert_currency = $booking->convert_currency;
    //         $booking_log->group_no = $booking->group_no;
    //         $booking_log->net_price = $booking->net_price;
    //         $booking_log->markup_amount = $booking->markup_amount;
    //         $booking_log->selling = $booking->selling;
    //         $booking_log->gross_profit = $booking->gross_profit;
    //         $booking_log->markup_percent = $booking->markup_percent;
    //         $booking_log->show_convert_currency = $booking->show_convert_currency;
    //         $booking_log->per_person = $booking->per_person;
    //         $booking_log->created_date = date("Y-m-d");
    //         $booking_log->user_id = Auth::user()->id;
    //         // $booking_log->pax_name = $booking->pax_name;
    //         $booking_log->bedding_preference = $booking->bedding_preference;
            
    //         $booking_log->save();
            
    //         if($booking->getBookingPaxDetail){
    //             foreach ($booking->getBookingPaxDetail as $pax) {           
    //               $data = $this->getPaxDetailArray($pax);
    //               $data['booking_log_id'] = $booking_log->id;
    //               BookingPaxDetailLog::create($data);
    //             }
    //             BookingPaxDetail::where('booking_id', $booking->id)->delete();
    //         }

    //         $booking = Booking::updateOrCreate(
    //             ['quotation_no' => $request->quotation_no],
    //             [
    //                 'ref_no' => $request->ref_no,
    //                 // 'reference_name' => $request->reference,
    //                 'qoute_id'                  => $request->qoute_id,
    //                 'quotation_no'              => $request->quotation_no,
    //                 'dinning_preferences'       => $request->dinning_preferences,
    //                 'bedding_preference'        => $request->bedding_preference,
    //                 'lead_passenger_name'       => $request->lead_passenger_name,
    //                 'brand_name'                => $request->brand_name,
    //                 'type_of_holidays'          => $request->type_of_holidays,
    //                 'sale_person'               => $request->sale_person,
    //                 'season_id'                 => $request->season_id,
    //                 'agency_booking'            => $request->agency_booking,
    //                 'agency_name'               => $request->agency_name,
    //                 'agency_contact_no'         => $request->agency_contact_no,
    //                 'currency'                  => $request->currency,
    //                 'convert_currency'          => $request->convert_currency,
    //                 'group_no'                  => $request->group_no,
    //                 'net_price'                 => $request->net_price,
    //                 'markup_amount'             => $request->markup_amount,
    //                 'selling'                   => $request->selling,
    //                 'gross_profit'              => $request->gross_profit,
    //                 'markup_percent'            => $request->markup_percent,
    //                 'show_convert_currency'     => $request->show_convert_currency,
    //                 'per_person'                => $request->per_person,

    //                 'flight_booked'             => !empty($request->flight_booked) ? $request->flight_booked : null,
    //                 'fb_person'                 => !empty($request->fb_person) && ($request->flight_booked != 'NA') ? $request->fb_person : null,
    //                 'fb_last_date'              => $request->fb_last_date && ($request->flight_booked != 'NA') ? Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d') : null,
    //                 'fb_airline_name_id'        => !empty($request->fb_airline_name_id) && ($request->flight_booked == 'yes') ? $request->fb_airline_name_id : null,
    //                 'fb_payment_method_id'      => !empty($request->fb_payment_method_id) && ($request->flight_booked == 'yes') ? $request->fb_payment_method_id : null,
    //                 'fb_booking_date'           => $request->fb_booking_date && ($request->flight_booked == 'yes') ? Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d') : null,
    //                 'fb_airline_ref_no'         => !empty($request->fb_airline_ref_no) && ($request->flight_booked == 'yes') ? $request->fb_airline_ref_no : null,
    //                 'flight_booking_details'    => !empty($request->flight_booking_details) && ($request->flight_booked == 'yes') ? $request->flight_booking_details : null,

    //                 'asked_for_transfer_details' => $request->asked_for_transfer_details,
    //                 'aft_person' => $request->aft_person && ($request->asked_for_transfer_details != 'NA') ? $request->aft_person : null,
    //                 'aft_last_date' => $request->aft_last_date && ($request->asked_for_transfer_details != 'NA') ? Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d') : null,
    //                 'transfer_details' => $request->transfer_details && ($request->asked_for_transfer_details == 'yes') ? $request->transfer_details : null,

    //                 'transfer_organised' => $request->transfer_organised,
    //                 'to_person' => $request->to_person && ($request->transfer_organised != 'NA') ? $request->to_person : null,
    //                 'to_last_date' => $request->to_last_date && ($request->transfer_organised != 'NA') ? Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d') : null,
    //                 'transfer_organised_details' => $request->transfer_organised_details && ($request->transfer_organised == 'yes') ? $request->transfer_organised_details : null,

    //                 'itinerary_finalised' => $request->itinerary_finalised,
    //                 'itf_person' => $request->itf_person && ($request->itinerary_finalised != 'NA') ? $request->itf_person : null,
    //                 'itf_last_date' => $request->itf_last_date && ($request->itinerary_finalised != 'NA') ? Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d') : null,
    //                 'itinerary_finalised_details' => $request->itinerary_finalised_details && ($request->itinerary_finalised == 'yes') ? $request->itinerary_finalised_details : null,
    //                 'itf_current_date' => $request->itf_current_date && ($request->itinerary_finalised == 'yes') ? Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d') : null,

    //                 'document_prepare' => $request->document_prepare,
    //                 'dp_person' => $request->dp_person && ($request->document_prepare != 'NA') ? $request->dp_person : null,
    //                 'dp_last_date' => $request->dp_last_date && ($request->document_prepare != 'NA') ? Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d') : null,
    //                 'tdp_current_date' => $request->tdp_current_date && ($request->document_prepare == 'yes') ? Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d') : null,

    //                 'documents_sent' => $request->documents_sent,
    //                 'ds_person' => $request->ds_person && ($request->documents_sent != 'NA') ? $request->ds_person : null,
    //                 'ds_last_date' => $request->ds_last_date && ($request->documents_sent != 'NA') ? Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d') : null,
    //                 'documents_sent_details' => $request->documents_sent_details && ($request->documents_sent == 'yes') ? $request->documents_sent_details : null,
    //                 'tds_current_date' => $request->tds_current_date && ($request->documents_sent == 'yes') ? Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d') : null,

    //                 'electronic_copy_sent' => $request->electronic_copy_sent,
    //                 'aps_person' => $request->aps_person && ($request->electronic_copy_sent == 'yes') ? $request->aps_person : null,
    //                 'aps_last_date' => $request->aps_last_date && ($request->electronic_copy_sent == 'yes') ? Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d') : null,
    //                 'electronic_copy_details' => $request->electronic_copy_details && ($request->electronic_copy_sent == 'yes') ? $request->electronic_copy_details : null,
    //             ]
    //         );
            
    //         if($request->has('pax')){
    //             foreach ($request->pax as $pax) {           
    //               $data = $this->getPaxDetailArray($pax);
    //               $data['booking_id'] = $booking->id;
    //               BookingPaxDetail::create($data);
    //           }
    //         }
            

    //         $bookingDetails = BookingDetail::where('booking_id', $booking->id)->get();

    //         foreach ($bookingDetails as $key => $bookingDetail) {
    //             $bookingDetailLog = new BookingDetailLog;
    //             $bookingDetailLog->booking_id = $booking->id;
    //             $bookingDetailLog->log_no = $bookingDetailLogNumber;
    //             $bookingDetailLog->qoute_id = $bookingDetail->qoute_id;
    //             $bookingDetailLog->quotation_no = $bookingDetail->quotation_no;
    //             $bookingDetailLog->row = $key + 1;
    //             $bookingDetailLog->date_of_service = $bookingDetail->date_of_service ? Carbon::parse(str_replace('/', '-', $bookingDetail->date_of_service))->format('Y-m-d') : null;
    //             $bookingDetailLog->service_details = $bookingDetail->service_details;
    //             $bookingDetailLog->category_id = $bookingDetail->category;
    //             $bookingDetailLog->supplier = $bookingDetail->supplier;
    //             $bookingDetailLog->product = $bookingDetail->product;
    //             $bookingDetailLog->booking_date = $bookingDetail->booking_date ? Carbon::parse(str_replace('/', '-', $bookingDetail->booking_date))->format('Y-m-d') : null;
    //             $bookingDetailLog->booking_due_date = $bookingDetail->booking_due_date ? Carbon::parse(str_replace('/', '-', $bookingDetail->booking_due_date))->format('Y-m-d') : null;
    //             $bookingDetailLog->booked_by = $bookingDetail->booked_by;
    //             $bookingDetailLog->booking_refrence = $bookingDetail->booking_refrence;
    //             $bookingDetailLog->booking_type = $bookingDetail->booking_type;
    //             $bookingDetailLog->comments = $bookingDetail->comments;
    //             $bookingDetailLog->supplier_currency = $bookingDetail->supplier_currency;
    //             $bookingDetailLog->cost = $bookingDetail->cost;
    //             $bookingDetailLog->actual_cost = $bookingDetail->actual_cost;
    //             $bookingDetailLog->supervisor_id = $bookingDetail->supervisor;
    //             $bookingDetailLog->added_in_sage = $bookingDetail->added_in_sage;
    //             $bookingDetailLog->qoute_base_currency = $bookingDetail->qoute_base_currency;
    //             $bookingDetailLog->qoute_invoice = $bookingDetail->qoute_invoice;
    //             $bookingDetailLog->save();

    //             $financebookingDetails = FinanceBookingDetail::where('booking_detail_id', $bookingDetail->id)->get();

    //             // dd($financebookingDetails);

    //             foreach ($financebookingDetails as $financebookingDetail) {
    //                 $financeBookingDetailLog = new FinanceBookingDetailLog;

    //                 $financeBookingDetailLog->booking_detail_id = $bookingDetailLog->id;
    //                 $financeBookingDetailLog->log_no = $bookingDetailLogNumber;
    //                 $financeBookingDetailLog->row = $key + 1;
    //                 $financeBookingDetailLog->deposit_amount = !empty($financebookingDetail->deposit_amount) ? $financebookingDetail->deposit_amount : null;
    //                 $financeBookingDetailLog->deposit_due_date = $financebookingDetail->deposit_due_date ? Carbon::parse(str_replace('/', '-', $financebookingDetail->deposit_due_date))->format('Y-m-d') : null;
    //                 $financeBookingDetailLog->paid_date = $financebookingDetail->paid_date ? Carbon::parse(str_replace('/', '-', $financebookingDetail->deposit_due_date))->format('Y-m-d') : null;
    //                 $financeBookingDetailLog->payment_method = $financebookingDetail->payment_method ?? null;
    //                 $financeBookingDetailLog->upload_to_calender = $financebookingDetail->upload_calender;
    //                 $financeBookingDetailLog->additional_date = $financebookingDetail->additional_date;
    //                 $financeBookingDetailLog->save();
    //             }
    //         }

    //         if (!empty($request->actual_cost)) {
    //             foreach ($request->actual_cost as $key => $cost) {
    //                 if (!is_null($request->qoute_invoice)) {
    //                     if (array_key_exists($key, $request->qoute_invoice)) {
    //                         $oldFileName = $request->qoute_invoice_record[$key];

    //                         $file = $request->qoute_invoice[$key];
    //                         $newFile = $request->qoute_invoice[$key]->getClientOriginalName();
    //                         $name = pathinfo($newFile, PATHINFO_FILENAME);
    //                         $extension = pathinfo($newFile, PATHINFO_EXTENSION);
    //                         $filename = $name . '-' . rand(pow(10, 4 - 1), pow(10, 4) - 1) . '.' . $extension;

    //                         $folder = public_path('booking/' . $request->qoute_id);

    //                         if (!File::exists($folder)) {
    //                             File::makeDirectory($folder, 0775, true, true);
    //                         }

    //                         // $destinationPath = public_path('booking/'. $request->qoute_id .'/'.  $oldFileName);
    //                         // File::delete($destinationPath);

    //                         $file->move(public_path('booking/' . $request->qoute_id), $filename);
    //                     } else {
    //                         $filename = isset($request->qoute_invoice_record[$key]) ? $request->qoute_invoice_record[$key] : null;
    //                     }
    //                 } else {
    //                     $filename = isset($request->qoute_invoice_record[$key]) ? $request->qoute_invoice_record[$key] : null;
    //                 }

    //                 $arrayBookingDetail = [
    //                     'qoute_id' => $request->qoute_id,
    //                     'booking_id' => $booking->id,
    //                     'quotation_no' => $request->quotation_no,
    //                     'row' => $key + 1,
    //                     'date_of_service' => $request->date_of_service[$key] ? Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d') : null,
    //                     'service_details' => $request->service_details[$key],
    //                     'category_id' => $request->category[$key],
    //                     'supplier' => $request->supplier[$key],
    //                     'product' => $request->product[$key],
    //                     'booking_date' => $request->booking_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d') : null,
    //                     'booking_due_date' => $request->booking_due_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d') : null,
    //                     // 'booking_method'    => $request->booking_method[$key],
    //                     'booked_by' => $request->booked_by[$key],
    //                     'booking_refrence' => $request->booking_refrence[$key],
    //                     'booking_type' => $request->booking_type[$key],
    //                     'comments' => $request->comments[$key],
    //                     'cost' => $request->cost[$key],
    //                     'actual_cost' => $request->actual_cost[$key],
    //                     'supervisor_id' => $request->supervisor[$key],
    //                     'added_in_sage' => $request->added_in_sage[$key],
    //                     'qoute_base_currency' => $request->qoute_base_currency[$key],
    //                     'qoute_invoice' => $filename,
    //                 ];

    //                 if ($request->has('supplier_currency') && !empty($request->supplier_currency)) {
    //                     $arrayBookingDetail['supplier_currency'] = $request->supplier_currency[$key];
    //                 }

    //                 $bookingDetail = BookingDetail::updateOrCreate(
    //                     [
    //                         'quotation_no' => $request->quotation_no,
    //                         'row' => $key + 1,
    //                     ],
    //                     $arrayBookingDetail
    //                 );
    //                 $nowDate = Carbon::now()->toDateString();
    //                 foreach ($request->deposit_due_date[$key] as $ikey => $deposit_due_date) {
    //                     if ($request->upload_calender[$key][$ikey] == true && $deposit_due_date != null) {
    //                         $supplier = ($request->has('supplier_currency')) ? $request->supplier_currency[$key] : $bookingDetail->supplier_currency;
    //                         $event = new Event;
    //                         $event->name = "To Pay " . $request->deposit_amount[$key][$ikey] . ' ' . $supplier . " to Supplier";
    //                         $event->description = 'Event description';

    //                         $addDate = (int) $request->additional_date[$key][$ikey];

    //                         if (Carbon::parse(str_replace('/', '-', $deposit_due_date))->subDays($addDate)->toDateString() >= $nowDate && $addDate != 0) {
    //                             $event->startDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->subDays($addDate) : null;
    //                             $event->endDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->subDays($addDate) : null;
    //                         } else {
    //                             $event->startDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->startOfDay() : null;
    //                             $event->endDate = ($deposit_due_date != null) ? Carbon::parse(str_replace('/', '-', $deposit_due_date))->endOfDay() : null;
    //                         }
    //                         // $event->addAttendee(['email' => 'kashan.kingdomvision@gmail.com']);
    //                         // $event->save();
    //                     }

    //                     FinanceBookingDetail::updateOrCreate(
    //                         [
    //                             'booking_detail_id' => $bookingDetail->id,
    //                             'row' => $ikey + 1,
    //                         ],
    //                         [
    //                             'upload_to_calender' => $request->upload_calender[$key][$ikey],
    //                             'deposit_amount' => !empty($request->deposit_amount[$key][$ikey]) ? $request->deposit_amount[$key][$ikey] : null,
    //                             'deposit_due_date' => $request->deposit_due_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
    //                             'paid_date' => $request->paid_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
    //                             'payment_method' => $request->payment_method[$key][$ikey] ?? null,
    //                             'additional_date' => $request->additional_date[$key][$ikey] ?? null,
    //                         ]
    //                     );
    //                 }
    //             }
    //         }

    //         return response()->json(['success_message' => 'Booking Updated Successfully']);

    //     // return Redirect::route('update-booking', $id)->with('success_message', 'Updated Successfully');
    //     } else {
    //         $get_user_branches = Cache::remember('get_user_branches', 200, function () {
    //             $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
    //             // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
    //             $output = $this->curl_data($url);
    //             return json_decode($output);
    //         });

    //         $get_holiday_type = Cache::remember('get_holiday_type', 200, function () {
    //             $url = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
    //             // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
    //             $output = $this->curl_data($url);
    //             return json_decode($output);
    //         });

    //         // $get_ref = Cache::remember('get_ref', 60, function () {
    //         //     // $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
    //         //     $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_ref';
    //         //     $output =  $this->curl_data($url);
    //         //     return json_decode($output)->data;
    //         // });

    //         // $get_user_branches = Cache::remember('get_user_branches', 60, function () {
    //         //     // $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
    //         //     $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
    //         //     $output =  $this->curl_data($url);
    //         //     return json_decode($output);
    //         // });

    //         // $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
    //         //     $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
    //         //     // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
    //         //     $output =  $this->curl_data($url);
    //         //     return json_decode($output);
    //         // });

    //         $booking = Booking::where('id', '=', $id)->first();

    //         return view('booking.update_booking')->with([

    //             'booking'           => $booking,
    //             'booking_email'     => booking_email::where('booking_id', '=', $id)->get(),
    //             'users'             => User::all()->sortBy('name'),
    //             'seasons'           => season::all(),
    //             // 'get_refs'          => $get_ref,
    //             'get_user_branches' => $get_user_branches,
    //             'record'            => old_booking::where('id', '=', $id)->get()->first(),
    //             'currencies'        => Currency::where('status', 1)->orderBy('id', 'ASC')->get(),
    //             'get_holiday_type'  => $get_holiday_type,
    //             'booking_details'   => BookingDetail::where('booking_id', $id)->get(),
    //             'categories'        => Category::all()->sortBy('name'),
    //             'suppliers'         => Supplier::all()->sortBy('name'),
    //             'users'             => User::all()->sortBy('name'),
    //             'booking_methods'   => BookingMethod::all()->sortBy('id'),
    //             'supervisors'       => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
    //             'payment_method'    => payment::all()->sortBy('name'),
    //             'id'                => $id,
    //             'booking_logs'      => BookingLog::where('booking_id', $id)->orderBy('log_no', 'DESC')->get(),
    //             'airlines'          => airline::all(),
    //             'payments'          => payment::all(),
    //             'products'          => Product::all()->sortBy('name'),
    //             'brands'            => Brand::orderBy('id','ASC')->get(),
    //             'holiday_types'     => HolidayType::where('brand_id',$booking->brand_name)->get(),
    //         ]);
    //     }
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $season = Season::findOrFail(decrypt($id));
        $data['bookings'] = $season->getBooking()->paginate($this->pagination);
        return view('bookings.listing', $data);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $book  =Booking::findOrFail(decrypt($id));
        $data['booking']           = Booking::findOrFail(decrypt($id));
        $data['users']             = User::orderBy('name', 'asc')->get();
        $data['seasons']           = Season::get();
        $data['currencies']        = Currency::where('status', 1)->get();
        $data['categories']        = Category::get();
        $data['suppliers']         = Supplier::get();
        $data['booking_methods']   = BookingMethod::get();
        $data['payment_method']          = PaymentMethod::get();
        $data['brands']            = Brand::orderBy('id','ASC')->get();
        $data['booking_logs']      = [];
        $data['supervisors']       = User::whereHas('getRole', function($query){
                                            $query->where('slug', 'supervisor');
                                        });
        $data['holiday_types']     = HolidayType::get();

        return view('bookings.edit',$data);
     
        // $booking = Booking::where('id', '=', decrypt($id))->first();
        // return view('bookings.edit')->with([

        //     'booking'           => $booking,
        //     // 'booking_email'     => booking_email::where('booking_id', '=', $id)->get(),
        //     'users'             => User::all()->sortBy('name'),
        //     'seasons'           => season::all(),
        //     // 'get_refs'          => $get_ref,
        //     'get_user_branches' => $get_user_branches,
        //     // 'record'            => old_booking::where('id', '=', $id)->get()->first(),
        //     'currencies'        => Currency::where('status', 1)->orderBy('id', 'ASC')->get(),
        //     'get_holiday_type'  => $get_holiday_type,
        //     // 'booking_details'   => BookingDetail::where('booking_id', $id)->get(),
        //     // 'categories'        => Category::all()->sortBy('name'),
        //     // 'suppliers'         => Supplier::all()->sortBy('name'),
        //     // 'users'             => User::all()->sortBy('name'),
        //     // 'booking_methods'   => BookingMethod::all()->sortBy('id'),
        //     // 'supervisors'       => User::where('role_id', 5)->orderBy('name', 'ASC')->get(),
        //     // 'payment_method'    => payment::all()->sortBy('name'),
        //     // 'id'                => $id,
        //     // 'booking_logs'      => BookingLog::where('booking_id', $id)->orderBy('log_no', 'DESC')->get(),
        //     'booking_logs'      => [],
        //     // 'airlines'          => airline::all(),
        //     'payments'          => PaymentMethod::all(),
        //     // 'products'          => Product::all()->sortBy('name'),
        //     'brands'            => Brand::orderBy('id','ASC')->get(),
        //     'holiday_types'     => HolidayType::where('brand_id',$booking->brand_name)->get(),
        // ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Booking::destroy(decrypt($id));
        return redirect()->route('categories.index')->with('success_message', 'Booking deleted successfully'); 
    }
}
