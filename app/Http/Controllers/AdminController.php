<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect as FacadesRedirect;
use Request as Routerequest;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\GoogleCalendar\Event;
use App\Mail\DueDateMail;
use Carbon\Carbon;
use App\User;
use App\season;
use App\Booking;
use App\BookingDetail;
use App\FinanceBookingDetail;
use App\airline;
use App\payment;
use App\supervisor;
use App\booking_email;
use App\role;
use App\Supplier;
use App\supplier_category;
use App\supplier_product;
use App\Category;
use App\Currency;
use App\code;
use App\Product;
use App\BookingMethod;
use App\CurrencyConversions;
use App\Qoute;
use App\QouteDetail;
use App\QouteEmail;
use App\QouteLog;
use App\QouteDetailLog;
use App\ZohoCredential;
use File;
use Image;
use Response;
use Validator;
use Redirect;
use DB;
use Cache;
use Input;
use Hash;
use Session;
use Config;
use App\old_booking;
use App\BookingLog;
use App\BookingDetailLog;
use App\FinanceBookingDetailLog;

use App\Template;
class AdminController extends Controller
{
    public $cacheTimeOut;
    public function __construct(Request $request)
    {
        $this->cacheTimeOut = 1800;
    }
    
    public function index()
    {
        return view('admin.index');
    }
    
    public function logout()
    {
        if (Auth::check()) {
            $id = Auth::user()->id;
            Auth::logout();
            user::where('id', '=', $id)->update(array('is_login' => 0));
            session()->flush();
            return Redirect::route('login')->with('success_message', 'Your session has been ended!');
        } else {
            return Redirect::route('admin');
        }
    }
    public function get_chapter(Request $request)
    {
        $matchThese    = ['book_id' => $request->input('id')];
        $item_rec      = DB::table('chapters')->where($matchThese)->select('id', 'title')->get();
        if ($request->ajax()) {
            return response()->json([
                'item_rec' => $item_rec
            ]);
        }
    }

        
    public function create_user(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                    'username'  => 'required|string',
                    'email'     => 'required|email|unique:users',
                    'role'      => 'required',
                    'password'  => 'required',
                    // 'brand'     => 'required',
                    // 'currency'  => 'required',
                    // 'supervisor'=> 'required|sometimes',
            ]);

            $user = new User;
            $user->name           = $request->username;
            $user->role_id        = (int) $request->role;
            $user->email          = $request->email;
            $user->supervisor_id  = $request->supervisor ?? NULL;
            $user->brand_name     = $request->brand ?? NULL;
            $user->currency_id    = $request->currency ?? NULL;
            $user->password   =    bcrypt($request->password);
            $user->save();

            return Redirect::route('view-user')->with('success_message', 'Created Successfully');

        } else {
            
            $branch  = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output =  $this->curl_data($url);
                
                return json_decode($output);
            });
            
            $data['roles']          = role::all();
            $data['supervisors']    = User::where('role_id',5)->orderBy('name','ASC')->get();
            $data['currencies']     = Currency::get();
            $data['brands']         = $branch;
            return view('user.create_user', $data);
            // return view('user.create_user')->with(['name' => '', 'id' => '', 'roles' => role::all(), 'supervisors' => User::where('role_id',5)->orderBy('name','ASC')->get() ]);
        }
    }

    public function view_user(Request $request)
    {
        $data['data'] = user::get();
        return view('user.view_user', $data);
    }


    public function update_user(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->isMethod('post')) {

            $request->validate([
                'username'  => 'required|string',
                'role'      => 'required',
                // 'brand'     => 'required',
                // 'currency'  => 'required',
                // 'supervisor'=> 'required|sometimes',
            ]);


            $user->name           = $request->username;
            $user->role_id        = (int) $request->role;
            $user->email          = $request->email;
            $user->supervisor_id  = $request->supervisor ?? NULL;
            $user->brand_name     = $request->brand ?? NULL;
            $user->currency_id    = $request->currency ?? NULL;
            if($request->has('password') && $request->password != NULL){
                $user->password   =    bcrypt($request->password);
            }
            $user->save();


            return Redirect::route('view-user')->with('success_message', 'Update Successfully');

        } else {
            $branch  = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });
            $data['data']           = $user;
            $data['roles']          = role::all();
            $data['supervisors']    = User::where('role_id',5)->orderBy('name','ASC')->get();
            $data['currencies']     = Currency::get();
            $data['brands']         = $branch;

            return view('user.update_user', $data);
        }
    }
    public function delete_user($id)
    {
        // if (booking::where('user_id', $id)->count() == 1) {
        //     return Redirect::route('view-user')->with('error_message', 'You can not delete this user because user already in use');
        // }
        user::destroy('id', '=', $id);
        return Redirect::route('view-user')->with('success_message', 'Delete Successfully');
    }

    // CRUD related to seasson
    public function create_season(Request $request)
    {

        if ($request->isMethod('post')) {

            $this->validate($request, ['name'  => 'required|unique:seasons']);
            $this->validate($request, ['start_date'  => 'required']);
            $this->validate($request, ['end_date'  => 'required']);

            if($request->end_date < $request->start_date ){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'end_date' => ['End date should be greater start date.']
                ]);
            }

            if($request->set_default_season == 1){
                season::query()->update(['default_season' => 0]);
            }

            $season = new season;
            $season->name               = $request->name;
            $season->default_season     = $request->set_default_season;
            $season->start_date         = Carbon::parse(str_replace('/', '-', $request->start_date))->format('Y-m-d');
            $season->end_date           = Carbon::parse(str_replace('/', '-', $request->end_date))->format('Y-m-d');
            $season->save();

            return Redirect::route('view-season')->with('success_message', 'Created Successfully');
        } else {
            return view('season.create_season')->with(['name' => '', 'id' => '']);
        }
    }

    public function view_season(Request $request)
    {
        return view('season.view_season')->with('data', season::all());
    }
    public function update_season(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            $this->validate($request, ['name'    =>  'required|unique:seasons,name,'.$id]);
            $this->validate($request, ['start_date'  => 'required']);
            $this->validate($request, ['end_date'  => 'required']);

            if($request->end_date < $request->start_date ){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'end_date' => ['End date should be greater start date.']
                ]);
            }

            if($request->set_default_season == 1){
                season::query()->update(['default_season' => 0]);
            }

            season::where('id',$id)->update(array(
                'name'      => $request->name,
                'default_season'  => $request->set_default_season,
                'start_date'      => Carbon::parse(str_replace('/', '-', $request->start_date))->format('Y-m-d'),
                'end_date'        => Carbon::parse(str_replace('/', '-', $request->end_date))->format('Y-m-d'),
            ));

            return Redirect::route('view-season')->with('success_message', 'Update Successfully');
        } else {
            return view('season.update_season')->with(['data' => season::find($id), 'id' => $id]);
        }
    }
    public function delete_season($id)
    {
        if (booking::where('season_id', $id)->count() >= 1) {
            return Redirect::route('view-season')->with('error_message', 'You can not delete this record because season already in use');
        }
        season::destroy('id', '=', $id);
        return Redirect::route('view-season')->with('success_message', 'Delete Successfully');
    }
    //
    public function create_supervisor(Request $request)
    {

        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required']);
            $this->validate($request, ['email' => 'required|email|unique:supervisors']);
            supervisor::create(array(
                'name'  => $request->name,
                'email' => $request->email
            ));
            return Redirect::route('create-supervisor')->with('success_message', 'Created Successfully');
        } else {
            return view('supervisor.create_supervisor')->with(['name' => '', 'id' => '', 'email' => '']);
        }
    }
    public function view_supervisor(Request $request)
    {
        return view('supervisor.view_supervisor')->with('data', supervisor::all());
    }
    public function update_supervisor(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:100|unique:supervisors,email,' . $id,
                'name'  => 'required'
            ]);
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            supervisor::where('id', '=', $id)->update(
                array(
                    'email' => $request->email,
                    'name'  => $request->name
                )
            );
            return Redirect::route('view-supervisor')->with('success_message', 'Update Successfully');
        } else {
            return view('supervisor.update_supervisor')->with(['data' => supervisor::find($id), 'id' => $id]);
        }
    }
    public function delete_supervisor($id)
    {
        if (User::where('supervisor_id', $id)->count() == 1) {
            return Redirect::route('view-supervisor')->with('error_message', 'You can not delete this record because supervisor already in use');
        }
        supervisor::destroy('id', '=', $id);
        return Redirect::route('view-supervisor')->with('success_message', 'Delete Successfully');
    }
    //
    public function create_booking(Request $request)
    {
        if ($request->isMethod('post')) {

            // $this->validate($request, ['supplier'                     => 'required'], ['required' => 'Please select Supplier']);
            // $this->validate($request, ['ref_no'                     => 'required'], ['required' => 'Reference number is required']);
            // $this->validate($request, ['brand_name'                 => 'required'], ['required' => 'Please select Brand Name']);
            // $this->validate($request, ['season_id'                  => 'required|numeric'], ['required' => 'Please select Booking Season']);
            // $this->validate($request, ['agency_booking'             => 'required'], ['required' => 'Please select Agency']);
            // $this->validate($request, ['pax_no'                     => 'required'], ['required' => 'Please select PAX No']);
            // $this->validate($request, ['date_of_travel'             => 'required'], ['required' => 'Please select date of travel']);
            // $this->validate($request, ['flight_booked'              => 'required'], ['required' => 'Please select flight booked']);

            // $this->validate($request, ['fb_airline_name_id'         => 'required_if:flight_booked,yes'], ['required_if' => 'Please select flight airline name']);

            // $this->validate($request, ['fb_payment_method_id'       => 'required_if:flight_booked,yes'], ['required_if' => 'Please select payment method']);

            // $this->validate($request, ['fb_booking_date'            => 'required_if:flight_booked,yes'], ['required_if' => 'Please select booking date']);

            // $this->validate($request, ['fb_airline_ref_no'          => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter airline reference number']);

            // $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter flight booking details']);
            // //
            // // $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'],['required_if' => 'Please select booked person']);
            // $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'], ['required_if' => 'Plesse enter flight booking date']);
            // //
            // // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
            // $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'], ['required_if' => 'Plesse enter transfer date']);
            // // $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'], ['required_if' => 'Plesse enter document sent date']);
            // // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'], ['required_if' => 'Plesse enter document sent date']);
            // //
            // $this->validate($request, ['asked_for_transfer_details' => 'required'], ['required' => 'Please select asked for transfer detail box']);
            // $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'], ['required_if' => 'Please transfer detail']);
            // $this->validate($request, ['form_sent_on'               => 'required'], ['required' => 'Please select form sent on']);
            // // $this->validate($request, ['transfer_info_received'     => 'required'],['required' => 'Please select transfer info received']);
            // // $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'],['required_if' => 'Please transfer info detail']);

            // $this->validate($request, ['itinerary_finalised'        => 'required'], ['required' => 'Please select itinerary finalised']);
            // $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Please enter itinerary finalised details']);

            // // $this->validate($request, ['itf_person'                => 'required_if:itinerary_finalised,no'],['required_if' => 'Please select itinerary person']);
            // $this->validate($request, ['itf_last_date'              => 'required_if:itinerary_finalised,no'], ['required_if' => 'Plesse enter itinerary sent date']);

            // $this->validate($request, ['documents_sent'             => 'required'], ['required' => 'Please select documents sent']);
            // $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'], ['required_if' => 'Please enter document sent details']);

            // $this->validate($request, ['electronic_copy_sent'       => 'required'], ['required' => 'Please select electronic copy sent']);
            // $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Please enter electronic copy details']);

            // $this->validate($request, ['transfer_organised'         => 'required'], ['required' => 'Please select transfer organised']);
            // $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Please enter transfer organised details']);
            // $this->validate($request, ['type_of_holidays'           => 'required'], ['required' => 'Please select type of holidays']);
            // $this->validate($request, ['sale_person'                => 'required'], ['required' => 'Please select type of sale person']);
            // $this->validate($request, ['tdp_current_date'              => 'required_if:document_prepare,yes'], ['required_if' => 'Plesse enter Travel Document Prepared Date']);

            if ($request->form_received_on == '0000-00-00') {
                $form_received_on = NULL;
            } else {
                $form_received_on = $request->form_received_on;
            }
            //
            if ($request->app_login_date == '0000-00-00') {
                $app_login_date = NULL;
            } else {
                $app_login_date = $request->app_login_date;
            }
            //
            $booking_id = booking::create(array(
                'ref_no'                      => $request->ref_no,
                'brand_name'                  => $request->brand_name,
                'season_id'                   => $request->season_id,
                'agency_booking'              => $request->agency_booking,
                'pax_no'                      => $request->pax_no,
                'date_of_travel'              => Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d'),
                'flight_booked'               => $request->flight_booked,
                'fb_airline_name_id'          => $request->fb_airline_name_id,
                'fb_payment_method_id'        => $request->fb_payment_method_id,
                'fb_booking_date'             => Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d'),
                'fb_airline_ref_no'           => $request->fb_airline_ref_no,
                'fb_last_date'                => Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d'),
                'fb_person'                   => $request->fb_person,
                //
                'aft_last_date'                => Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d'),
                'aft_person'                   => $request->aft_person,
                'ds_last_date'                 => Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d'),
                'ds_person'                    => $request->ds_person,
                'to_last_date'                 => Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d'),
                'to_person'                    => $request->to_person,
                //
                'document_prepare'             => $request->document_prepare,
                'dp_last_date'                 => Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d'),
                'dp_person'                    => $request->dp_person,
                //
                //
                'flight_booking_details'      => $request->flight_booking_details,
                'asked_for_transfer_details'  => $request->asked_for_transfer_details,
                'transfer_details'            => $request->transfer_details,
                'form_sent_on'                => Carbon::parse(str_replace('/', '-', $request->form_sent_on))->format('Y-m-d'),
                'form_received_on'            => $form_received_on,
                'app_login_date'              => $app_login_date,
                // 'transfer_info_received'      => $request->transfer_info_received,
                // 'transfer_info_details'       => $request->transfer_info_details,
                'itinerary_finalised'         => $request->itinerary_finalised,
                'itinerary_finalised_details' => $request->itinerary_finalised_details,
                'itf_last_date'               => Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d'),
                'itf_person'                  => $request->itf_person,
                'documents_sent'              => $request->documents_sent,
                'documents_sent_details'      => $request->documents_sent_details,
                'electronic_copy_sent'        => $request->electronic_copy_sent,
                'electronic_copy_details'     => $request->electronic_copy_details,
                'transfer_organised'          => $request->transfer_organised,
                'transfer_organised_details'  => $request->transfer_organised_details,

                'sale_person'                 => $request->sale_person,
                'deposit_received'            => $request->deposit_received == '' ? 0 : $request->deposit_received,
                'remaining_amount_received'   => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
                'fso_person'                  => $request->fso_person,
                'fso_last_date'               => Carbon::parse(str_replace('/', '-', $request->fso_last_date))->format('Y-m-d'),
                'aps_person'                  => $request->aps_person,
                'aps_last_date'               => Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d'),
                'finance_detail'              => $request->finance_detail,
                'destination'                 => $request->destination,
                'user_id'                     => Auth::user()->id,
                'itf_current_date'            => Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d'),
                'tdp_current_date'            => Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d'),
                'tds_current_date'            => Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d'),
                // 'holiday'                     => $request->holiday,

            ));




            if ($request->flight_booked == 'yes') {
                //Sending email
                $template   = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;
                $template   .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
                $template   .= '<h1>Last Date Of Flight Booking : ' . $request->fb_last_date . '</h1>';

                if ($request->fb_person == '') {
                    $email = Auth::user()->email;
                    $template   .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
                } else {
                    $record = User::where('id', $request->fb_person)->get()->first();
                    $email  = $record->email;
                    $name   = $record->name;
                    $template   .= '<h1>Responsible Person : ' . $name . '</h1>';
                }
                $data['to']        = $email;
                $data['name']      = config('app.name');
                $data['from']      = config('app.mail');
                $data['subject']   = "Task Flight Booked Alert";
                try {
                    \Mail::send("email_template.flight_booked_alert", ['template' => $template], function ($m) use ($data) {
                        $m->from($data['from'], $data['name']);
                        $m->to($data['to'])->subject($data['subject']);
                    });
                } catch (Swift_RfcComplianceException $e) {
                    return $e->getMessage();
                }
                //Sending email
            }
            if ($request->form_received_on == '0000-00-00') {
                //Sending email
                $template     = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;

                $template   .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
                $template   .= '<h1>Reminder for sent on date : ' . $request->fso_last_date . '</h1>';

                if ($request->fso_person == '') {
                    $email = Auth::user()->email;
                    $template   .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
                } else {
                    $record = User::where('id', $request->fso_person)->get()->first();
                    $email  = $record->email;
                    $name   = $record->name;
                    $template   .= '<h1>Responsible Person : ' . $name . '</h1>';
                }
                $data['to']        = $email;
                $data['name']      = config('app.name');
                $data['from']      = config('app.mail');
                $data['subject']   = "Reminder for form sent on";
                try {
                    \Mail::send("email_template.form_sent_on", ['template' => $template], function ($m) use ($data) {
                        $m->from($data['from'], $data['name']);
                        $m->to($data['to'])->subject($data['subject']);
                    });
                } catch (Swift_RfcComplianceException $e) {
                    return $e->getMessage();
                }
                //Sending email
            }

            if ($request->electronic_copy_sent == 'no') {
                //Sending email
                $template    = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;

                $template   .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
                $template   .= '<h1>App Reminder Sent Date : ' . $request->aps_last_date . '</h1>';

                if ($request->aps_person == '') {
                    $email = Auth::user()->email;
                    $template   .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
                } else {
                    $record = User::where('id', $request->aps_person)->get()->first();
                    $email  = $record->email;
                    $name   = $record->name;
                    $template   .= '<h1>Responsible Person : ' . $name . '</h1>';
                }
                $data['to']        = $email;
                $data['name']      = config('app.name');
                $data['from']      = config('app.mail');
                $data['subject']   = "Reminder for app login sent";
                try {
                    \Mail::send("email_template.app_login_sent", ['template' => $template], function ($m) use ($data) {
                        $m->from($data['from'], $data['name']);
                        $m->to($data['to'])->subject($data['subject']);
                    });
                } catch (Swift_RfcComplianceException $e) {
                    return $e->getMessage();
                }
                //Sending email
            }

            return Redirect::route('create-booking')->with('success_message', 'Created Successfully');
        } else {

            $get_ref = Cache::remember('get_ref', $this->cacheTimeOut, function () {
                $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_ref';
                $output =  $this->curl_data($url);
                //   return json_decode($output)->data;
            });

            $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });

            $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
                $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_holiday_type';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });

            $booking_email = booking_email::where('booking_id', '=', 1)->get();
            return view('booking.create_booking')->with([
                'get_holiday_type' => $get_holiday_type,
                'seasons' => season::all(),
                'persons' => user::all(),
                'get_refs' => $get_ref,
                'get_user_branches' => $get_user_branches,
                'booking_email' => $booking_email,
                'payment' => payment::all(),
                'airline' => airline::all()
            ]);
        }
    }

    public function view_booking_season(Request $request)
    {
        $group_by_seasons = season::join('codes', 'codes.season_id', '=', 'seasons.id')->orderBy('seasons.created_at', 'desc')->groupBy('seasons.id', 'seasons.name')->get(['seasons.id', 'seasons.name']);
        return view('booking.view_booking_season')->with('data', $group_by_seasons);
    }

    public function delete_booking_season($id)
    {
        season::destroy('id', '=', $id);
        return Redirect::route('view-booking-season')->with('success_message', 'Deleted Successfully');
    }

    public function view_booking(Request $request, $id)
    {
        //
        $staff = Cache::remember('staff', $this->cacheTimeOut, function () {
            return User::orderBy('id', 'DESC')->get();
        });
        //
        $get_ref = Cache::remember('get_ref', $this->cacheTimeOut, function () {
            $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
            $output =  $this->curl_data($url);
            return json_decode($output)->data;
        });
        //
        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });
        //
        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });
        $query = old_booking::join('seasons', 'seasons.id', '=', 'old_bookings.season_id')
            ->join('users', 'users.id', '=', 'old_bookings.user_id')
            ->leftjoin('users as user_fb', 'user_fb.id', '=', 'old_bookings.fb_person')
            ->leftjoin('users as user_ti', 'user_ti.id', '=', 'old_bookings.aft_person')
            ->leftjoin('users as user_to', 'user_to.id', '=', 'old_bookings.to_person')
            ->leftjoin('users as user_itf', 'user_itf.id', '=', 'old_bookings.itf_person')
            ->leftjoin('users as user_tdp', 'user_tdp.id', '=', 'old_bookings.dp_person')
            ->leftjoin('users as user_ds', 'user_ds.id', '=', 'old_bookings.ds_person')
            ->leftjoin('airlines', 'airlines.id', '=', 'old_bookings.fb_airline_name_id')
            ->leftjoin('payments', 'payments.id', '=', 'old_bookings.fb_payment_method_id')->where('old_bookings.season_id', '=', $id);

        if ($request->created_at != '') {
            $date  = explode('-', $request->created_at);
            $start_date = $date[0];
            $end_date   = $date[1];

            $start_created_at = Carbon::parse($start_date)->format('Y-m-d');
            $end_created_at   = Carbon::parse($end_date)->format('Y-m-d');
            $query =  $query->whereRaw('DATE(old_bookings.created_at) >= ?', $start_created_at);
            $query =  $query->whereRaw('DATE(old_bookings.created_at) <= ?', $end_created_at);
        }
        if ($request->created_by != '') {
            $query =  $query->where('old_bookings.user_id', '=', $request->created_by);
        }
        if ($request->ref_no != '') {
            $query =  $query->where('old_bookings.ref_no', '=', $request->ref_no);
        }
        if ($request->date_of_travel != '') {
            $date  = explode('-', $request->date_of_travel);
            $start_date = $date[0];
            $end_date   = $date[1];

            $query =  $query->where('old_bookings.date_of_travel', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            $query =  $query->where('old_bookings.date_of_travel', '<=', Carbon::parse($end_date)->format('Y-m-d'));
        }
        if ($request->brand_name != '') {
            $query =  $query->where('old_bookings.brand_name', '=', $request->brand_name);
        }
        if ($request->season_id != '') {
            $query =  $query->where('old_bookings.season_id', '=', $request->season_id);
        }
        if ($request->agency_booking != '') {
            $query =  $query->where('old_bookings.agency_booking', '=', $request->agency_booking);
        }
        if ($request->flight_booked != '') {
            $query =  $query->where('old_bookings.flight_booked', '=', $request->flight_booked);
        }
        if ($request->form_sent_on != '') {
            $date  = explode('-', $request->form_sent_on);
            $start_date = $date[0];
            $end_date   = $date[1];
            $query =  $query->where('old_bookings.form_sent_on', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            $query =  $query->where('old_bookings.form_sent_on', '<=', Carbon::parse($end_date)->format('Y-m-d'));
        }
        if ($request->type_of_holidays != '') {
            $query =  $query->where('old_bookings.type_of_holidays', '=', $request->type_of_holidays);
        }
        if ($request->fb_payment_method_id != '') {
            $query =  $query->where('old_bookings.fb_payment_method_id', '=', $request->fb_payment_method_id);
        }
        if ($request->fb_airline_name_id != '') {
            $query =  $query->where('old_bookings.fb_airline_name_id', '=', $request->fb_airline_name_id);
        }
        if ($request->fb_responsible_person != '') {
            $query =  $query->where('old_bookings.fb_person', '=', $request->fb_responsible_person);
        }
        if ($request->ti_responsible_person != '') {
            $query =  $query->where('old_bookings.aft_person', '=', $request->ti_responsible_person);
        }
        if ($request->to_responsible_person != '') {
            $query =  $query->where('old_bookings.to_person', '=', $request->to_responsible_person);
        }
        if ($request->itf_responsible_person != '') {
            $query =  $query->where('old_bookings.itf_person', '=', $request->itf_responsible_person);
        }
        if ($request->dp_responsible_person != '') {
            $query =  $query->where('old_bookings.dp_person', '=', $request->dp_responsible_person);
        }
        if ($request->ds_responsible_person != '') {
            $query =  $query->where('old_bookings.ds_person', '=', $request->ds_responsible_person);
        }
        if ($request->pax_no != '') {
            $query =  $query->where('old_bookings.pax_no', '=', $request->pax_no);
        }
        if ($request->asked_for_transfer_details != '') {
            $query =  $query->where('old_bookings.asked_for_transfer_details', '=', $request->asked_for_transfer_details);
        }
        if ($request->transfer_organised != '') {
            $query =  $query->where('old_bookings.transfer_organised', '=', $request->transfer_organised);
        }
        if ($request->itinerary_finalised != '') {
            $query =  $query->where('old_bookings.itinerary_finalised', '=', $request->itinerary_finalised);
        }
        $query = $query->orderBy('old_bookings.created_at', 'desc')->paginate(10, ['old_bookings.*', 'airlines.name as airline_name', 'payments.name as payment_name', 'seasons.name', 'users.name as username', 'user_fb.name as fbusername', 'user_ti.name as tiusername', 'user_to.name as tousername', 'user_itf.name as itfusername', 'user_tdp.name as tdpusername', 'user_ds.name as dsusername'])->appends($request->all());

        return view('booking.view_booking')->with([
            'data'                  => $query,
            'book_id'               => $id,
            'staffs'                => $staff,
            'get_refs'              => $get_ref,
            'get_holiday_type'      => $get_holiday_type,
            'type_of_holidays'      => $request->type_of_holidays,
            'get_user_branches'     => $get_user_branches,
            'created_at'            => $request->created_at,
            'created_by'            => $request->created_by,
            'ref_no'                => $request->ref_no,
            'date_of_travel'        => $request->date_of_travel,
            'brand_name'            => $request->brand_name,
            'seasons'               => season::all(),
            'session_id'            => $request->season_id,
            'agency_booking'        => $request->agency_booking,
            'flight_booked'         => $request->flight_booked,
            'form_sent_on'          => $request->form_sent_on,
            'payment'               => payment::all(), 'airline' => airline::all(),
            'fb_payment_method_id'  => $request->fb_payment_method_id,
            'fb_airline_name_id'    => $request->fb_airline_name_id,
            'fb_responsible_person' => $request->fb_responsible_person,
            'ti_responsible_person' => $request->ti_responsible_person,
            'to_responsible_person' => $request->to_responsible_person,
            'itf_responsible_person'=> $request->itf_responsible_person,
            'dp_responsible_person' => $request->dp_responsible_person,
            'ds_responsible_person' => $request->ds_responsible_person,
            'pax_no'                => $request->pax_no, 'asked_for_transfer_details' => $request->asked_for_transfer_details,
            'transfer_organised'    => $request->transfer_organised,
            'itinerary_finalised'   => $request->itinerary_finalised
        ]);
    }
    public function delete_booking($season_id, $booking_id)
    {
        booking::destroy('id', '=', $booking_id);
        return Redirect::route('view-booking', $season_id)->with('success_message', 'Deleted Successfully');
    }


    function cf_remote_request($url, $_args = array()) {
		// prepare array
		$array = array(
			//'status' => false,
			'message' => array(
				'101' => 'Invalid url',
				'102' => 'cURL Error #: ',
				'200' => 'cURL Successful #: ',
				'400' => '400 Bad Request',
			)
		);

		// initalize args
		$args = array(
			'method' 		=> 'POST',
			'timeout' 		=> 45,
			'redirection' 	=> 5,
			'httpversion' 	=> '1.0',
			'blocking' 		=> true,
			'ssl' => true,
			'headers' => array(),
			'body' => array(),
			'returntransfer' => true,
			'encoding' => '',
			'maxredirs' => 10,
			'format' => 'JSON'
		);

		if( empty($url) ) {
			$code = 101;
			$response = array('status' => $code, 'body' => $array['message'][$code]);
			return $response;
		}

		if( !empty($_args) && is_array($_args) )
			$args = array_merge($args, $_args);

		$fields = $args['body'];
		if( strtolower($args['method']) == 'post' && is_array($fields) )
			$fields = http_build_query( $fields );

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL 			=> $url,
			CURLOPT_RETURNTRANSFER 	=> $args['returntransfer'],
			CURLOPT_ENCODING 		=> $args['encoding'],
			CURLOPT_MAXREDIRS 		=> $args['maxredirs'],
			CURLOPT_HTTP_VERSION 	=> $args['httpversion'],// CURL_HTTP_VERSION_1_1,
			CURLOPT_USERAGENT 		=> $_SERVER['HTTP_USER_AGENT'],
			//CURLOPT_HEADER 			=> true,
			CURLINFO_HEADER_OUT 	=> true,
			CURLOPT_TIMEOUT 		=> $args['timeout'],
			CURLOPT_CONNECTTIMEOUT 	=> $args['timeout'],
			CURLOPT_SSL_VERIFYPEER 	=> $args['ssl'] === true ? true : false,
			//CURLOPT_SSL_VERIFYHOST 	=> $args['ssl'] === true ? true : false,
            // CURLOPT_CAPATH     		=> APPPATH . 'certificates/ca-bundle.crt',
			CURLOPT_CUSTOMREQUEST 	=> $args['method'],
			CURLOPT_POSTFIELDS 		=> $fields,
			CURLOPT_HTTPHEADER 		=> $args['headers'],
		));

		$curl_response 	= curl_exec($curl);
		$err 			= curl_error($curl);
		$curl_info = array(
			'status' 		=> curl_getinfo($curl, CURLINFO_HTTP_CODE),
			'header' 		=> curl_getinfo($curl, CURLINFO_HEADER_OUT),
			'total_time' 	=> curl_getinfo($curl, CURLINFO_TOTAL_TIME)
		);

		curl_close($curl);


		if( $err ) {
			$response = array('message' => $err, 'body' => $err);

		} else {
			if( $curl_info['status'] == 200
			&& in_array($args['format'], array('ARRAY', 'OBJECT'))
			&& !empty($curl_response) && is_string($curl_response) ) {
				$curl_response = json_decode( $curl_response, $args['format'] == 'ARRAY' ? true : false );
                $curl_response = ( json_last_error() == JSON_ERROR_NONE ) ? $curl_response : $curl_response;
			}
            else{
                $curl_response = json_decode($curl_response, TRUE);
            }

			$response = array(
				//'message' 	=> $array['message'][ $curl_info['status'] ],
				'body' 		=> $curl_response
			);
		}

		$response = array_merge($curl_info, $response);
		return $response;
	}


    public function refresh_token()
    {
        $zoho_credentials = ZohoCredential::findOrFail(1);
        $refresh_token = $zoho_credentials->refresh_token;
        $url = "https://accounts.zoho.com/oauth/v2/token?refresh_token=" . $refresh_token . "&client_id=1000.0VJP33J6LLOQ63896U88RWYIVJRSFD&client_secret=81212149f53ee4039b280b420835d64b8443c96a83&grant_type=refresh_token";
        $args = array('ssl' => false, 'format' => 'ARRAY');
        $response = $this->cf_remote_request($url, $args);
        if( $response['status'] == 200 ) {
			$body = $response['body'];
            $zoho_credentials->access_token = $body['access_token'];
            $zoho_credentials->save();
		}
    }




    // get reference function start
    public function get_ref_detail(Request $request){

        if (!Qoute::where('ref_no', $request->id)->exists()) {
            $ajax_response = array();

            if ($request->reference_name == "zoho") {
                $zoho_credentials = ZohoCredential::findOrFail(1);
                $ref = $request->id;
                // $refresh_token = '1000.18cb2e5fbe397a6422d8fcece9b67a06.d71539ff6e5fa8364879574343ab799a';
                $url = "https://www.zohoapis.com/crm/v2/Deals/search?criteria=(Booking_Reference:equals:{$ref})";
                $args = array(
                    'method' 	=> 'GET',
                    'ssl' 		=> false,
                    'format' 	=> 'ARRAY',
                    'headers' 	=> array(
                        "Authorization:" . 'Zoho-oauthtoken ' . $zoho_credentials->access_token,
                        "Content-Type: application/json",
                    )
                );

                $response = $this->cf_remote_request($url, $args);

                if ($response['status'] == 200) {
                    $responses_data = array_shift($response['body']['data']);
                    $passenger_id = $responses_data['id'];

                    $url = "https://www.zohoapis.com/crm/v2/Passengers/search?criteria=(Deal:equals:{$passenger_id})";
                    $passenger_response = $this->cf_remote_request($url, $args);

                    if ($passenger_response['status'] == 200) {
                        $pax_no = count($passenger_response['body']['data']);
                    }

                    $ajax_response = array(
                        "holiday_type" => isset($responses_data['Holiday_Type']) && !empty($responses_data['Holiday_Type'])  ? $responses_data['Holiday_Type'] : null,
                        "sale_person"  => isset($responses_data['Owner']['email']) && !empty($responses_data['Owner']['email']) ? $responses_data['Owner']['email'] : null,
                        "currency"     => isset($responses_data['Currency']) && !empty($responses_data['Currency']) ? $responses_data['Currency'] : null ,
                        "pax"          => isset($pax_no) && !empty($pax_no) ?  $pax_no : null
                    );
                }
            }
        }else{
            $errors['ref_no'] = "Quote is already generated. are you sure you want to create another one.";
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
        
        if ($request->ajax()) {
            return response()->json($ajax_response);
        }
            return redirect()->back();
    }

    //get reference funtion end
    private function curl_data($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return $output = curl_exec($ch);
    }

    public function update_booking(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            // old code start

            // $this->validate($request, ['ref_no'                     => 'required'], ['required' => 'Reference number is required']);
            // $this->validate($request, ['brand_name'                 => 'required'], ['required' => 'Please select Brand Name']);
            // $this->validate($request, ['season_id'                  => 'required|numeric'], ['required' => 'Please select Booking Season']);
            // $this->validate($request, ['agency_booking'             => 'required'], ['required' => 'Please select Agency']);
            // $this->validate($request, ['pax_no'                     => 'required'], ['required' => 'Please select PAX No']);
            // $this->validate($request, ['date_of_travel'             => 'required'], ['required' => 'Please select date of travel']);
            // $this->validate($request, ['flight_booked'              => 'required'], ['required' => 'Please select flight booked']);
            // $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter flight booking details']);
            // $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'], ['required_if' => 'Please select booked person']);
            // $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'], ['required_if' => 'Plesse enter flight booking date']);
            // //
            // // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
            // $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'], ['required_if' => 'Plesse enter transfer date']);
            // $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'], ['required_if' => 'Please select document person']);
            // $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'], ['required_if' => 'Plesse enter document sent date']);
            // // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'], ['required_if' => 'Plesse enter document sent date']);
            // //
            // $this->validate($request, ['asked_for_transfer_details' => 'required'], ['required' => 'Please select asked for transfer detail box']);
            // $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'], ['required_if' => 'Please transfer detail']);
            // $this->validate($request, ['form_sent_on'               => 'required'], ['required' => 'Please select form sent on']);
            // $this->validate($request, ['transfer_info_received'     => 'required'], ['required' => 'Please select transfer info received']);
            // $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'], ['required_if' => 'Please transfer info detail']);
            // $this->validate($request, ['itinerary_finalised'        => 'required'], ['required' => 'Please select itinerary finalised']);
            // $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Please enter itinerary finalised details']);

            // $this->validate($request, ['documents_sent'             => 'required'], ['required' => 'Please select documents sent']);
            // $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'], ['required_if' => 'Please enter document sent details']);

            // $this->validate($request, ['electronic_copy_sent'       => 'required'], ['required' => 'Please select electronic copy sent']);
            // $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Please enter electronic copy details']);

            // $this->validate($request, ['transfer_organised'         => 'required'], ['required' => 'Please select transfer organised']);
            // $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Please enter transfer organised details']);
            // $this->validate($request, ['type_of_holidays'           => 'required'], ['required' => 'Please select type of holidays']);
            // $this->validate($request, ['sale_person'                => 'required'], ['required' => 'Please select type of sale person']);

            // if ($request->form_received_on == '0000-00-00') {
            //     $form_received_on = NULL;
            // } elseif ($request->form_received_on == '') {
            //     $form_received_on = NULL;
            // } else {
            //     $form_received_on = $request->form_received_on;
            // }

            // if ($request->app_login_date == '0000-00-00') {
            //     $app_login_date = NULL;
            // } elseif ($request->app_login_date == '') {
            //     $app_login_date = NULL;
            // } else {
            //     $app_login_date = $request->app_login_date;
            // }

            // booking::where('id', '=', $id)->update(array(
            //     'ref_no'                      => $request->ref_no,
            //     'brand_name'                  => $request->brand_name,
            //     'season_id'                   => $request->season_id,
            //     'agency_booking'              => $request->agency_booking,
            //     'pax_no'                      => $request->pax_no,
            //     'date_of_travel'              => Carbon::parse($request->date_of_travel)->format('Y-m-d'),
            //     'flight_booked'               => $request->flight_booked,
            //     'flight_booking_details'      => $request->flight_booking_details,
            //     'asked_for_transfer_details'  => $request->asked_for_transfer_details,
            //     'transfer_details'            => $request->transfer_details,
            //     'form_sent_on'                => Carbon::parse($request->form_sent_on)->format('Y-m-d'),
            //     'form_received_on'            => $form_received_on,
            //     'app_login_date'              => $app_login_date,
            //     'transfer_info_received'      => $request->transfer_info_received,
            //     'transfer_info_details'       => $request->transfer_info_details,
            //     'itinerary_finalised'         => $request->itinerary_finalised,
            //     'itinerary_finalised_details' => $request->itinerary_finalised_details,
            //     'documents_sent'              => $request->documents_sent,
            //     'documents_sent_details'      => $request->documents_sent_details,
            //     'electronic_copy_sent'        => $request->electronic_copy_sent,
            //     'electronic_copy_details'     => $request->electronic_copy_details,
            //     'transfer_organised'          => $request->transfer_organised,
            //     'transfer_organised_details'  => $request->transfer_organised_details,
            //     'type_of_holidays'            => $request->type_of_holidays,
            //     'sale_person'                 => $request->sale_person,
            //     'deposit_received'            => $request->deposit_received == '' ? 0 : $request->deposit_received,
            //     'remaining_amount_received'   => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
            //     'finance_detail'              => $request->finance_detail,
            //     'destination'                 => $request->destination
            // ));

            // old code end

            $this->validate($request, ['ref_no'           => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['lead_passenger_name' => 'required'], ['required' => 'Lead Passenger Name is required']);
            $this->validate($request, ['brand_name'       => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person'      => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['season_id'        => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_name'       => 'required_if:agency_booking,2'], ['required_if' => 'Agency Name is required']);
            $this->validate($request, ['agency_contact_no' => 'required_if:agency_booking,2'], ['required_if' => 'Agency No is required']);
            $this->validate($request, ['agency_booking'    => 'required'], ['required' => 'Agency is required']);
            $this->validate($request, ['currency'          => 'required'], ['required' => 'Booking Currency is required']);
            $this->validate($request, ['group_no'          => 'required'], ['required' => 'Pax No is required']);
            $this->validate($request, ['dinning_preferences' => 'required'], ['required' => 'Dinning Preferences is required']);
            $this->validate($request, [ "booking_due_date"    => "required|array", "booking_due_date.*"  => "required" ]);
            $this->validate($request, [ "cost"    => "required|array", "cost.*"  => "required"]);
            $this->validate($request, ['fb_airline_name_id'  => 'required_if:flight_booked,yes'], ['required_if' => 'Airline is required']);
            $this->validate($request, ['fb_payment_method_id'  => 'required_if:flight_booked,yes'], ['required_if' => 'Payment is required']);
            $this->validate($request, ['fb_booking_date'  => 'required_if:flight_booked,yes'], ['required_if' => 'Booking Date is required']);
            $this->validate($request, ['fb_airline_ref_no'  => 'required_if:flight_booked,yes'], ['required_if' => 'Airline Ref No is required']);
            $this->validate($request, ['flight_booking_details'  => 'required_if:flight_booked,yes'], ['required_if' => 'Flight Booking Details is required']);
            $this->validate($request, ['transfer_organised_details'  => 'required_if:transfer_organised,yes'], ['required_if' => 'Transfer Organised Details is required']);
            
            $this->validate($request, ['itinerary_finalised_details'  => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Itinerary Finalised Details is required']);
            $this->validate($request, ['itf_current_date'  => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Itinerary Finalised Date is required']);
            $this->validate($request, ['tdp_current_date'  => 'required_if:document_prepare,yes'], ['required_if' => 'Travel Document Prepared Date is required']);
            
            $this->validate($request, ['documents_sent_details'  => 'required_if:documents_sent,yes'], ['required_if' => 'Document Details is required']);
            $this->validate($request, ['tds_current_date'  => 'required_if:documents_sent,yes'], ['required_if' => 'Travel Document Sent Date is required']);
           
            $this->validate($request, ['aps_person'  => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Responsible Person is required']);
            $this->validate($request, ['aps_last_date'  => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Date is required']);
            $this->validate($request, ['electronic_copy_details'  => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'App Login Sent Details is required']);


            $season = season::find($request->season_id);

            // $booking_error = [];
            // if(!empty($request->booking_date)){
            //     foreach($request->booking_date as $key => $date){

            //         if(!is_null($date)){
            //             $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($request->booking_due_date[$key])){
            //             $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
            //         }else{
            //             $booking_due_date  = null;
            //         }

            //         if(!is_null($request->date_of_service[$key])){
            //             $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
            //         }else{
            //             $date_of_service  = null;
            //         }

            //         if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( ($date > $booking_due_date ) ){
            //                 $booking_error[$key+1] = "Booking Date should be smaller than due date";
            //             }
            //         }

            //         if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
            //                 $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
            //             }
            //         }

            //     }
            // }

            // if(!empty($booking_error)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'booking_date' => (object) $booking_error
            //     ]);
            // }


            // $errors = [];
            // foreach ($request->booking_due_date as $key => $duedate) {
            //     $duedate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $duedate))->format('Y-m-d')));

            //     $startDate = date('Y-m-d', strtotime($season->start_date));
            //     $endDate   = date('Y-m-d', strtotime($season->end_date));

            //     $bookingdate     = (isset($request->booking_date) && !empty($request->booking_date[$key]))? $request->booking_date[$key] : NULL;
            //     if($bookingdate != NULL){
            //         $bookingdate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
            //     }
            //     $dateofservice   = (isset($request->date_of_service) && !empty($request->date_of_service[$key]))? $request->date_of_service[$key] : NULL;
            //     if ($dateofservice != null) {
            //         $dateofservice   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $dateofservice))->format('Y-m-d')));
            //     }
            //     $error = [];
            //     $dueresult = false;
            //     $dofresult = false;
            //     $bookresult = false;

            //     if($this->checkInSession($duedate, $season) == false){
            //         $a[$key+1] = 'Due Date should be season date range.';
            //     }else{
            //         $dueresult = true;
            //     }
            //     if($bookingdate != NULL && $this->checkInSession($bookingdate, $season) == false){
            //         $b[$key+1]  = 'Booking Date should be season date range.';
            //     }else{
            //         $bookresult = true;
            //     }
            //     if($dateofservice != NULL && $this->checkInSession($dateofservice, $season) == false){
            //         $c[$key+1]  = 'Date of service should be season date range.';
            //     }else{
            //         $dofresult = true;
            //     }

            //     if($dateofservice != NULL && $bookingdate  == NULL){
            //         $b[$key+1]  = 'Booking Date field is required before the date of service.';
            //         $bookresult = false;
            //     }

            //     if($bookresult == true){
            //         if($bookingdate != null && $bookingdate < $duedate){
            //             $b[$key+1]  = 'Booking Date should be smaller than booking due date.';
            //         }
            //     }

            //     if($dofresult == true){
            //         if ($bookingdate != null && $bookingdate > $dateofservice) {
            //             $c[$key+1]  = 'Date of service should be smaller than booking date.';
            //         }
            //     }


            //     $error['date_of_service'] = (isset($c) && count($c) >0 )? (object) $c : NULL;
            //     $error['booking_date'] = (isset($b) && count($b) >0 )? (object) $b : NULL;
            //     $error['booking_due_date'] = (isset($a) && count($a) >0 )? (object) $a : NULL;

            //     $errors = $error;
            // }

            // if(count($errors) > 0){
            //   if($error['date_of_service'] != NULL || $error['date_of_service'] != NULL || $error['date_of_service'] != NULL){
            //     throw \Illuminate\Validation\ValidationException::withMessages($errors);
            //     }
            // }

            $booking = Booking::find($id);

            $booking_log = new BookingLog;
            $bookingDetailLogNumber             = $this->increment_log_no($this->get_log_no('BookingLog',$id));
            $booking_log->booking_id            =  $booking->id;
            $booking_log->log_no                =  $bookingDetailLogNumber;
            $booking_log->reference_name        =  $booking->reference_name;
            $booking_log->ref_no                =  $booking->ref_no;
            $booking_log->qoute_id              =  $booking->qoute_id;
            $booking_log->quotation_no          =  $booking->quotation_no;
            $booking_log->dinning_preferences   =  $booking->dinning_preferences;
            $booking_log->lead_passenger_name   =  $booking->lead_passenger_name;
            $booking_log->brand_name            =  $booking->brand_name;
            $booking_log->type_of_holidays      =  $booking->type_of_holidays;
            $booking_log->sale_person           =  $booking->sale_person;
            $booking_log->season_id             =  $booking->season_id;
            $booking_log->agency_booking        =  $booking->agency_booking;
            $booking_log->agency_name           =  $booking->agency_name;
            $booking_log->agency_contact_no     =  $booking->agency_contact_no;
            $booking_log->currency              =  $booking->currency;
            $booking_log->convert_currency      =  $booking->convert_currency;
            $booking_log->group_no              =  $booking->group_no;
            $booking_log->net_price             =  $booking->net_price;
            $booking_log->markup_amount         =  $booking->markup_amount;
            $booking_log->selling               =  $booking->selling;
            $booking_log->gross_profit          =  $booking->gross_profit;
            $booking_log->markup_percent        =  $booking->markup_percent;
            $booking_log->show_convert_currency =  $booking->show_convert_currency;
            $booking_log->per_person            =  $booking->per_person;
            $booking_log->created_date          =  date("Y-m-d");
            $booking_log->user_id               =  Auth::user()->id;
            $booking_log->save(); 


            $booking = Booking::updateOrCreate(
                [ 'quotation_no' => $request->quotation_no ],

                [
                    'ref_no'                  =>  $request->ref_no,
                    'reference_name'          =>  $request->reference,
                    'qoute_id'                =>  $request->qoute_id,
                    'quotation_no'            =>  $request->quotation_no,
                    'dinning_preferences'     =>  $request->dinning_preferences,
                    'lead_passenger_name'     =>  $request->lead_passenger_name,
                    'brand_name'              =>  $request->brand_name,
                    'type_of_holidays'        =>  $request->type_of_holidays,
                    'sale_person'             =>  $request->sale_person,
                    'season_id'               =>  $request->season_id,
                    'agency_booking'          =>  $request->agency_booking,
                    'agency_name'             =>  $request->agency_name,
                    'agency_contact_no'       =>  $request->agency_contact_no,
                    'currency'                =>  $request->currency,
                    'convert_currency'        =>  $request->convert_currency,
                    'group_no'                =>  $request->group_no,
                    'net_price'               =>  $request->net_price,
                    'markup_amount'           =>  $request->markup_amount,
                    'selling'                 =>  $request->selling,
                    'gross_profit'            =>  $request->gross_profit,
                    'markup_percent'          =>  $request->markup_percent,
                    'show_convert_currency'   =>  $request->show_convert_currency,
                    'per_person'              =>  $request->per_person,

                    'flight_booked'           =>  !empty($request->flight_booked) ? $request->flight_booked : null,
                    'fb_person'               =>  !empty($request->fb_person) && ($request->flight_booked != 'NA') ? $request->fb_person : null,
                    'fb_last_date'            =>  $request->fb_last_date && ($request->flight_booked != 'NA') ? Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d') : null,
                    'fb_airline_name_id'      =>  !empty($request->fb_airline_name_id) && ($request->flight_booked == 'yes') ? $request->fb_airline_name_id : null,
                    'fb_payment_method_id'    =>  !empty($request->fb_payment_method_id) && ($request->flight_booked == 'yes') ? $request->fb_payment_method_id : null,
                    'fb_booking_date'         =>  $request->fb_booking_date && ($request->flight_booked == 'yes') ? Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d') : null,
                    'fb_airline_ref_no'       =>  !empty($request->fb_airline_ref_no) && ($request->flight_booked == 'yes') ? $request->fb_airline_ref_no : null,
                    'flight_booking_details'  =>  !empty($request->flight_booking_details) && ($request->flight_booked == 'yes') ? $request->flight_booking_details : null,

                    'asked_for_transfer_details' =>  $request->asked_for_transfer_details,
                    'aft_person'                 =>  $request->aft_person && ($request->asked_for_transfer_details != 'NA') ? $request->aft_person : null,
                    'aft_last_date'              =>  $request->aft_last_date && ($request->asked_for_transfer_details != 'NA') ? Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d') : null,
                    'transfer_details'           =>  $request->transfer_details && ($request->asked_for_transfer_details == 'yes') ? $request->transfer_details : null,

                    'transfer_organised'         =>  $request->transfer_organised,
                    'to_person'                  =>  $request->to_person && ($request->transfer_organised != 'NA') ? $request->to_person : null,
                    'to_last_date'               =>  $request->to_last_date && ($request->transfer_organised != 'NA') ? Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d') : null,
                    'transfer_organised_details' =>  $request->transfer_organised_details && ($request->transfer_organised == 'yes') ? $request->transfer_organised_details : null,

                    'itinerary_finalised'         =>  $request->itinerary_finalised,
                    'itf_person'                  =>  $request->itf_person && ($request->itinerary_finalised != 'NA') ? $request->itf_person : null,
                    'itf_last_date'               =>  $request->itf_last_date && ($request->itinerary_finalised != 'NA') ? Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d') : null,
                    'itinerary_finalised_details' =>  $request->itinerary_finalised_details && ($request->itinerary_finalised == 'yes') ? $request->itinerary_finalised_details : null,
                    'itf_current_date'            =>  $request->itf_current_date && ($request->itinerary_finalised == 'yes') ? Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d') : null,

                    'document_prepare'         =>  $request->document_prepare,
                    'dp_person'                =>  $request->dp_person && ($request->document_prepare != 'NA') ? $request->dp_person : null,
                    'dp_last_date'             =>  $request->dp_last_date && ($request->document_prepare != 'NA') ? Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d') : null,
                    'tdp_current_date'         =>  $request->tdp_current_date && ($request->document_prepare == 'yes') ? Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d') : null,
                    
                    'documents_sent'         =>  $request->documents_sent,
                    'ds_person'              =>  $request->ds_person  && ($request->documents_sent != 'NA') ? $request->ds_person : null,
                    'ds_last_date'           =>  $request->ds_last_date && ($request->documents_sent != 'NA') ? Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d') : null,
                    'documents_sent_details' =>  $request->documents_sent_details && ($request->documents_sent == 'yes') ? $request->documents_sent_details : null,
                    'tds_current_date'       =>  $request->tds_current_date && ($request->documents_sent == 'yes') ? Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d') : null,

                    'electronic_copy_sent'    =>  $request->electronic_copy_sent,
                    'aps_person'              =>  $request->aps_person && ($request->electronic_copy_sent == 'yes') ? $request->aps_person : null,
                    'aps_last_date'           =>  $request->aps_last_date && ($request->electronic_copy_sent == 'yes') ? Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d') : null,
                    'electronic_copy_details' =>  $request->electronic_copy_details && ($request->electronic_copy_sent == 'yes') ? $request->electronic_copy_details : null,
                ]
            );


            $bookingDetails = BookingDetail::where('booking_id',$booking->id)->get();

            foreach($bookingDetails as $key => $bookingDetail){

                $bookingDetailLog = new BookingDetailLog;
                $bookingDetailLog->booking_id          = $booking->id;
                $bookingDetailLog->log_no              = $bookingDetailLogNumber;
                $bookingDetailLog->qoute_id            = $bookingDetail->qoute_id;
                $bookingDetailLog->quotation_no        = $bookingDetail->quotation_no;
                $bookingDetailLog->row                 = $key+1;
                $bookingDetailLog->date_of_service     = $bookingDetail->date_of_service ? Carbon::parse(str_replace('/', '-', $bookingDetail->date_of_service))->format('Y-m-d') : null;
                $bookingDetailLog->service_details     = $bookingDetail->service_details;
                $bookingDetailLog->category_id         = $bookingDetail->category;
                $bookingDetailLog->supplier            = $bookingDetail->supplier;
                $bookingDetailLog->booking_date        = $bookingDetail->booking_date ? Carbon::parse(str_replace('/', '-', $bookingDetail->booking_date))->format('Y-m-d') : null;
                $bookingDetailLog->booking_due_date    = $bookingDetail->booking_due_date ? Carbon::parse(str_replace('/', '-', $bookingDetail->booking_due_date))->format('Y-m-d') : null;
                $bookingDetailLog->booked_by           = $bookingDetail->booked_by;
                $bookingDetailLog->booking_refrence    = $bookingDetail->booking_refrence;
                $bookingDetailLog->booking_type        = $bookingDetail->booking_type;
                $bookingDetailLog->comments            = $bookingDetail->comments;
                $bookingDetailLog->supplier_currency   = $bookingDetail->supplier_currency;
                $bookingDetailLog->cost                = $bookingDetail->cost;
                $bookingDetailLog->actual_cost         = $bookingDetail->actual_cost;
                $bookingDetailLog->supervisor_id       = $bookingDetail->supervisor;
                $bookingDetailLog->added_in_sage       = $bookingDetail->added_in_sage;
                $bookingDetailLog->qoute_base_currency = $bookingDetail->qoute_base_currency;
                $bookingDetailLog->qoute_invoice       = $bookingDetail->qoute_invoice;
                $bookingDetailLog->save(); 


                $financebookingDetails = FinanceBookingDetail::where('booking_detail_id',$bookingDetail->id)->get();

                // dd($financebookingDetails);
                
                foreach($financebookingDetails as $financebookingDetail){
                    
                    $financeBookingDetailLog = new FinanceBookingDetailLog;

                    $financeBookingDetailLog->booking_detail_id  =  $bookingDetailLog->id;
                    $financeBookingDetailLog->log_no             =  $bookingDetailLogNumber;
                    $financeBookingDetailLog->row                =  $key+1;
                    $financeBookingDetailLog->deposit_amount     =  !empty($financebookingDetail->deposit_amount) ? $financebookingDetail->deposit_amount : null;
                    $financeBookingDetailLog->deposit_due_date   =  $financebookingDetail->deposit_due_date ? Carbon::parse(str_replace('/', '-', $financebookingDetail->deposit_due_date))->format('Y-m-d') : null;
                    $financeBookingDetailLog->paid_date          =  $financebookingDetail->paid_date ? Carbon::parse(str_replace('/', '-', $financebookingDetail->deposit_due_date))->format('Y-m-d') : null;
                    $financeBookingDetailLog->payment_method     =  $financebookingDetail->payment_method ?? NULL;
                    $financeBookingDetailLog->upload_to_calender =  $financebookingDetail->upload_calender;
                    $financeBookingDetailLog->save();

                }
            }


            if(!empty($request->actual_cost)){
                foreach($request->actual_cost as $key => $cost){

                    if(!is_null($request->qoute_invoice)){

                        if(array_key_exists($key,$request->qoute_invoice))
                        {

                            $oldFileName = $request->qoute_invoice_record[$key];

                            $file       = $request->qoute_invoice[$key];
                            $newFile    = $request->qoute_invoice[$key]->getClientOriginalName();
                            $name       =  pathinfo($newFile, PATHINFO_FILENAME);
                            $extension  =  pathinfo($newFile, PATHINFO_EXTENSION);
                            $filename   =  $name.'-'.rand(pow(10, 4-1), pow(10, 4)-1).'.'.$extension;

                            $folder = public_path('booking/' . $request->qoute_id );

                            if (!File::exists($folder)) {
                                File::makeDirectory($folder, 0775, true, true);
                            }

                            // $destinationPath = public_path('booking/'. $request->qoute_id .'/'.  $oldFileName);
                            // File::delete($destinationPath);

                            $file->move(public_path('booking/' . $request->qoute_id ), $filename);

                        }

                        else{
                            $filename = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
                        }
                    }
                    else{

                        $filename = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
                    }
                    
                    $arrayBookingDetail =  [
                        'qoute_id'          => $request->qoute_id,
                        'booking_id'        => $booking->id,
                        'quotation_no'      => $request->quotation_no,
                        'row'               => $key+1,
                        'date_of_service'   => $request->date_of_service[$key] ? Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d') : null,
                        'service_details'   => $request->service_details[$key],
                        'category_id'       => $request->category[$key],
                        'supplier'          => $request->supplier[$key],
                        'booking_date'      => $request->booking_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d') : null,
                        'booking_due_date'  => $request->booking_due_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d') : null,
                        // 'booking_method'    => $request->booking_method[$key],
                        'booked_by'         => $request->booked_by[$key],
                        'booking_refrence'  => $request->booking_refrence[$key],
                        'booking_type'      => $request->booking_type[$key],
                        'comments'          => $request->comments[$key],
                        'cost'              => $request->cost[$key],
                        'actual_cost'       => $request->actual_cost[$key],
                        'supervisor_id'     => $request->supervisor[$key],
                        'added_in_sage'     => $request->added_in_sage[$key],
                        'qoute_base_currency' => $request->qoute_base_currency[$key],
                        'qoute_invoice'     => $filename,
                    ];
                    
                    if($request->has('supplier_currency') && !empty($request->supplier_currency)){
                        $arrayBookingDetail['supplier_currency'] =  $request->supplier_currency[$key];
                    }

                    $bookingDetail = BookingDetail::updateOrCreate(
                        [
                            'quotation_no' => $request->quotation_no,
                            'row' => $key+1,
                        ],
                        $arrayBookingDetail
                    );
                    $nowDate = Carbon::now()->toDateString();
                    foreach($request->deposit_due_date[$key] as $ikey => $deposit_due_date){
                    
                    
                        if($request->upload_calender[$key][$ikey]  == true && $deposit_due_date != NULL){
                            $supplier = ($request->has('supplier_currency'))? $request->supplier_currency[$key] : $bookingDetail->supplier_currency;
                            $event = new Event;
                            $event->name        = "To Pay ".$request->deposit_amount[$key][$ikey].' '.$supplier." to Supplier";
                            $event->description = 'Event description';
                            
                            $addDate          = (int)$request->additional_date[$key][$ikey];

                            if(Carbon::parse(str_replace('/', '-', $deposit_due_date))->subDays($addDate)->toDateString() >= $nowDate && $addDate != 0){
                                $event->startDate   = ($deposit_due_date != NULL)? Carbon::parse(str_replace('/', '-', $deposit_due_date))->subDays($addDate): NULL;
                                $event->endDate     = ($deposit_due_date != NULL)? Carbon::parse(str_replace('/', '-', $deposit_due_date))->subDays($addDate): NULL;
                            }else{
                                $event->startDate   = ($deposit_due_date != NULL)? Carbon::parse(str_replace('/', '-', $deposit_due_date))->startOfDay(): NULL;
                                $event->endDate     = ($deposit_due_date != NULL)? Carbon::parse(str_replace('/', '-', $deposit_due_date))->endOfDay(): NULL;
                            }
                            // $event->addAttendee(['email' => 'kashan.kingdomvision@gmail.com']);
                            // $event->save();

                        }

                        FinanceBookingDetail::updateOrCreate(
                            [
                                'booking_detail_id' => $bookingDetail->id,
                                'row' => $ikey+1,
                            ],
                            [
                                'upload_to_calender' => $request->upload_calender[$key][$ikey],
                                'deposit_amount'     =>  !empty($request->deposit_amount[$key][$ikey]) ? $request->deposit_amount[$key][$ikey] : null,
                                'deposit_due_date'   =>  $request->deposit_due_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'paid_date'          =>  $request->paid_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'payment_method'     =>  $request->payment_method[$key][$ikey]??NULL,
                            ]

                        );

                    }

                }
            }

            return response()->json(['success_message' => 'Booking Updated Successfully']);

            // return Redirect::route('update-booking', $id)->with('success_message', 'Updated Successfully');
        } else {


            $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });

            $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });

            // $get_ref = Cache::remember('get_ref', 60, function () {
            //     // $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
            //     $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_ref';
            //     $output =  $this->curl_data($url);
            //     return json_decode($output)->data;
            // });


            // $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            //     // $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
            //     $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            //     $output =  $this->curl_data($url);
            //     return json_decode($output);
            // });

            // $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
            //     $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            //     // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            //     $output =  $this->curl_data($url);
            //     return json_decode($output);
            // });
 
            return view('booking.update_booking')->with([

                'booking'           =>  Booking::where('id', '=', $id)->first(),
                'booking_email'     =>  booking_email::where('booking_id', '=', $id)->get(),
                'users'             =>  user::all(),
                'seasons'           =>  season::all(),
                // 'get_refs'          => $get_ref,
                'get_user_branches' =>  $get_user_branches,
                'record'            =>  old_booking::where('id', '=', $id)->get()->first(),
                'currencies'        =>  Currency::all()->sortBy('name'),
                'get_holiday_type'  =>  $get_holiday_type,
                'booking_details'   =>  BookingDetail::where('booking_id',$id)->get(),
                'categories'        =>  Category::all()->sortBy('name'),
                'suppliers'         =>  Supplier::all()->sortBy('name'),
                'users'             =>  User::all()->sortBy('name'),
                'booking_methods'   =>  BookingMethod::all()->sortBy('id'),
                'supervisors'       =>  User::where('role_id',5)->orderBy('name','ASC')->get(),
                'payment_method'    =>  payment::all()->sortBy('name'),
                'id'                =>  $id,
                'booking_logs'      =>  BookingLog::where('booking_id',$id)->get(),
                'airlines'          =>  airline::all(),
                'payments'          =>  payment::all(),
            ]);
        }
    }

    public function view_booking_version($booking_id,$log_no){
        
        $booking_log         = BookingLog::where('booking_id',$booking_id)->where('log_no',$log_no)->first();
        $booking_detail_logs = BookingDetailLog::where('booking_id',$booking_id)->where('log_no',$log_no)->get();

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('booking.view-booking-version')->with([
            'booking_log'         => $booking_log,
            'booking_detail_logs' => $booking_detail_logs,
            'seasons'             => season::all(),
            'currencies'          => Currency::all()->sortBy('name'),
            'categories'          => Category::all()->sortBy('name'),
            'suppliers'           => Supplier::all()->sortBy('name'),
            'booking_methods'     => BookingMethod::all()->sortBy('id'),
            'users'               => User::all()->sortBy('name'),
            'supervisors'         => User::where('role_id',5)->orderBy('name','ASC')->get(),
            'get_user_branches'   => $get_user_branches,
            'get_holiday_type'    => $get_holiday_type,
            'payment_method'      => payment::all()->sortBy('name'),
        ]);
    
    }

    // view quotation version in update booking
    public function view_quotation_version($quote_id,$log_no){

        $qoute_log = QouteLog::where('qoute_id',$quote_id)->where('log_no',$log_no)->first();

        $qoute_detail_logs = QouteDetailLog::where('qoute_id',$quote_id)->where('log_no',$log_no)->get();

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('booking.view-booking-quotation-version')->with([
            'qoute_log'          => $qoute_log,
            'qoute_detail_logs'  => $qoute_detail_logs,
            'seasons'            => season::all(),
            'currencies'         => Currency::all()->sortBy('name'),
            'categories'         => Category::all()->sortBy('name'),
            'suppliers'          => Supplier::all()->sortBy('name'),
            'booking_methods'    => BookingMethod::all()->sortBy('id'),
            'users'              => User::all()->sortBy('name'),
            'supervisors'        => User::where('role_id',5)->orderBy('name','ASC')->get(),
            'get_user_branches'  => $get_user_branches,
            'get_holiday_type'   => $get_holiday_type
        ]);
    }

    public function view_quotation($id){

        return view('booking.view-quotation')->with([

            'qoute'           => Qoute::findOrFail($id),
            'qoute_details'   => QouteDetail::where('qoute_id',$id)->get(),
            'seasons'         => season::all(),
            'currencies'      => Currency::all()->sortBy('name'),
            'categories'      => Category::all()->sortBy('name'),
            'suppliers'       => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'users'           => User::all()->sortBy('name'),
            'supervisors'     => User::where('role_id',5)->orderBy('name','ASC')->get(),
        ]);
    }

    public function delete_multi_booking(Request $request, $id)
    {
        $customMessages = ['required' => 'Please select at least one checkbox'];
        $this->validate($request, ['multi_val'    => 'required'], $customMessages);
        foreach ($request->multi_val as $val) {
            booking::destroy('id', '=', $val);
        }
        return Redirect::route('view-booking', $id)->with('success_message', 'Action Perform Successfully');
    }

    public function create_airline(Request $request)
    {

        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required'], ['required' => 'Name is required.']);

            airline::create(array(
                'name'  => $request->name

            ));
            return Redirect::route('view-airline')->with('success_message', 'Created Successfully');
        } else {
            return view('airline.create_airline')->with(['name' => '', 'id' => '']);
        }
    }
    public function view_airline(Request $request)
    {
        return view('airline.view_airline')->with('data', airline::all());
    }

    public function update_airline(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), ['name'  => 'required'], ['required' => 'Name is required.']);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            airline::where('id', '=', $id)->update(
                array(

                    'name'  => $request->name
                )
            );
            return Redirect::route('view-airline')->with('success_message', 'Update Successfully');
        } else {
            return view('airline.update_airline')->with(['data' => airline::find($id), 'id' => $id]);
        }
    }

    public function delete_airline($id)
    {
        // if (booking::where('fb_airline_name_id', $id)->count() >= 1) {
        //     return Redirect::route('view-airline')->with('error_message', 'You can not delete this record because season already in use');
        // }
        airline::destroy('id', '=', $id);
        return Redirect::route('view-airline')->with('success_message', 'Deleted Successfully');
    }
    public function create_payment(Request $request)
    {

        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required'], ['required' => 'Name is required']);

            payment::create(array(
                'name'  => $request->name,

            ));
            return Redirect::route('view-payment')->with('success_message', 'Created Successfully');
        } else {
            return view('payment.create_payment')->with(['name' => '', 'id' => '', 'email' => '']);
        }
    }
    public function view_payment(Request $request)
    {
        return view('payment.view_payment')->with('data',payment::all()->sortByDesc("id") );
    }

    public function update_payment(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [ 'name'  => 'required' ], ['required' => 'Name is required.']);


            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            payment::where('id', '=', $id)->update(
                array(
                    'name'  => $request->name
                )
            );
            return Redirect::route('view-payment')->with('success_message', 'Update Successfully');
        } else {
            return view('payment.update_payment')->with(['data' => payment::find($id), 'id' => $id]);
        }
    }

    public function delete_payment($id)
    {
        // if (booking::where('fb_payment_method_id', $id)->count() >= 1) {
        //     return Redirect::route('view-payment')->with('error_message', 'You can not delete this record because season already in use');
        // }
        payment::destroy('id', '=', $id);
        return Redirect::route('view-payment')->with('success_message', 'Deleted Successfully');
    }

    public function add_role(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required'], ['required' => 'Name is required']);

            role::create(array(
                'name' => $request->name
            ));

            return Redirect::route('view-role')->with('success_message', 'Created Successfully');
        }
        return view('roles.create');
    }

    public function view_roles(Request $request)
    {
        return view('roles.view_roles')->with(['data' => role::all()]);
    }

    public function del_role(Request $request, $id)
    {
        role::destroy('id', '=', $id);
        return Redirect::route('view-role')->with('success_message', 'Deleted Successfully');
    }

    public function update_role(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required'], ['required' => 'Name is required']);

            role::where('id', '=', $id)->update(array(
                'name' => $request->name
            ));

            return Redirect::route('view-role')->with('success_message', 'Update Successfully');
        }
        return view('roles.update_role')->with(['data' => role::find($id)]);
    }

    public function add_category(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required|unique:categories']);

            Category::create(array(
                'name' => $request->name
            ));
            return Redirect::route('view-category')->with('success_message', 'Category Added Successfully');
        }
        return view('category.add_category');
    }

    public function view_category(Request $request)
    {
        return view('category.view_categories')->with(['data' => Category::all()]);
    }
    public function delete_category(Request $request, $id)
    {
        Category::destroy('id', '=', $id);
        return Redirect::route('view-category')->with('success_message', 'Category Successfully Deleted!!');
    }
    public function update_category(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required']);

            Category::where('id', '=', $id)->update(array(
                'name' => $request->name
            ));

            return Redirect::route('view-category')->with('success_message', 'Category Successfully Updated!!');
        }
        return view('category.update_category')->with(['data' => Category::find($id)]);
    }

    public function details_supplier($id) {

        $supplier = Supplier::findOrFail(decrypt($id));
        $data = [
            'name'      => $supplier->name,
            'email'     => $supplier->email,
            'phone'     => $supplier->phone,
            'currency'  => $supplier->currency->name??NULL,
        ];

        $category = [];
        foreach ($supplier->categories as $categoires) {
            $c = [
                'name' => $categoires->name,
            ];
            array_push($category, $c);
        }

        $product = [];
        foreach ($supplier->products as $pro) {
            $p = [
                'name' => $pro->name,
            ];
            array_push($product, $p);
        }

        $data['category'] = $category;
        $data['product']  = $product;

        return view('supplier.detail_supplier', $data);
    }

    public function add_supplier(Request $request)
    {
        if ($request->isMethod('post')) {

            // dd($request->all());

            $this->validate($request, ['username' => 'required'], ['required' => 'Name is required']);
            // $this->validate($request, ['email' => 'required|unique:suppliers'], ['required' => 'Email is required']);
            // $this->validate($request, ['phone' => 'required|unique:suppliers'], ['required' => 'Phone Number is required']);
            $this->validate($request, ['categories' => 'required'], ['required' => 'Category is required']);
            // $this->validate($request, ['products' => 'required'], ['required' => 'Product is required']);
            // $this->validate($request, ['currency' => 'required'], ['required' => 'Currency is required']);

            $supplier = new Supplier();
            $supplier->name = $request->username;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->currency_id = $request->currency;
            $supplier->save();

            $supplier->categories()->sync($request->categories);
            $supplier->products()->sync($request->products);

            // if (!empty($request->categories)) {
            //     foreach ($request->categories as $category) {
            //         $cat = new supplier_category();
            //         $cat->supplier_id = $supplier->id;
            //         $cat->category_id = $category;
            //         $cat->save();
            //     }
            // }

            // if (!empty($request->products)) {
            //     foreach ($request->products as $product) {
            //         $prod = new supplier_product();
            //         $prod->supplier_id = $supplier->id;
            //         $prod->product_id = $product;
            //         $prod->save();
            //     }
            // }

            return Redirect::route('view-supplier')->with('success_message', 'Supplier Added Successfully');
        }

        $categories = Category::all();
        $products = Product::all();
        $currencies = Currency::all();

        return view('supplier.create_supplier')->with([ 'categories' => $categories, 'products' => $products, 'currencies' => $currencies  ]);
    }

    public function view_supplier(Request $request)
    {
        $suppliers = Supplier::all();
        return view('supplier.view_suppliers')->with('suppliers',$suppliers);

        // $set = [];
        // $cat = DB::select('select suppliers.* , categories.name as category from supplier_categories INNER JOIN suppliers ON suppliers.id = supplier_categories.supplier_id INNER JOIN categories ON supplier_categories.category_id = categories.id');
        // // var_dump($cat);
        // foreach ($cat as $c) {
        //     if (empty($set[0][$c->id])) {
        //         $set[0][$c->id] = [];
        //     }
        //     array_push($set[0][$c->id], $c->category);
        // }

        // $prod_set = [];
        // $prod = DB::select('select suppliers.* , products.name as product from supplier_products INNER JOIN suppliers ON suppliers.id = supplier_products.supplier_id INNER JOIN products ON supplier_products.product_id = products.id');
        // // var_dump($cat);
        // foreach ($prod as $c) {
        //     if (empty($prod_set[0][$c->id])) {
        //         $prod_set[0][$c->id] = [];
        //     }
        //     array_push($prod_set[0][$c->id], $c->product);
        // }

        // return view('supplier.view_suppliers')->with(['data' => DB::select('select suppliers.* , categories.name as category from supplier_categories INNER JOIN suppliers ON suppliers.id = supplier_categories.supplier_id INNER JOIN categories ON supplier_categories.category_id = categories.id GROUP BY suppliers.id'), 'categories' => $set, 'prod' => $prod_set]);
    }


    public function view_supplier_products(){

        $supplier_products = supplier_product::join('suppliers','suppliers.id','=','supplier_products.supplier_id')
        ->leftJoin('products','products.id','=','supplier_products.product_id')
        ->select('suppliers.id as supplier_id','suppliers.name as supplier_name','products.id as product_id','products.code','products.name','products.description')
        ->get();

        return view('supplier.view_supplier_product')->with('supplier_products',$supplier_products);
    }

    public function view_supplier_categories(){

        $supplier_categories = supplier_category::join('suppliers','suppliers.id','=','supplier_categories.supplier_id')
        ->leftJoin('categories','categories.id','=','supplier_categories.category_id')
        ->select('suppliers.id as supplier_id','suppliers.name as supplier_name','categories.id as category_id','categories.name as category_name')
        ->get();

        return view('supplier.view_supplier_category')->with('supplier_categories',$supplier_categories);
    }


    public function delete_supplier(Request $request, $id)
    {
        $supplier = Supplier::findOrFail(decrypt($id));
        $supplier->delete();
        return Redirect::route('view-supplier')->with('success_message', 'Supplier Successfully Deleted!!');
    }

    public function update_supplier(Request $request, $id)
    {
        if($request->isMethod('post')) {

            $this->validate($request, ['username' => 'required'], ['required' => 'Name is required']);
            // $this->validate($request, ['email' => 'required|email|unique:suppliers,email,'.$id], ['required' => 'Email is required']);
            // $this->validate($request, ['phone' => 'required|unique:suppliers,phone,'.$id, ], ['required' => 'Phone Number is required']);
            $this->validate($request, ['categories' => 'required'], ['required' => 'Product is required']);
            // $this->validate($request, ['products' => 'required'], ['required' => 'Currency is required']);

            $supplier = Supplier::findOrFail($id);
            $supplier->name = $request->username;
            $supplier->email = $request->email;
            $supplier->phone = $request->phone;
            $supplier->currency_id = $request->currency;
            $supplier->save();

            $supplier->categories()->sync($request->categories);
            $supplier->products()->sync($request->products);


            // supplier_product::where('supplier_id', $id)->delete();
            // supplier_category::where('supplier_id', $id)->delete();

            // Supplier::where('id', '=', $id)->update(array(
            //     'name' => $request->username,
            //     'email' => $request->email,
            //     'phone' => $request->phone
            // ));

            // if (!empty($request->categories)) {
            //     foreach ($request->categories as $category) {
            //         $cat = new supplier_category();
            //         $cat->supplier_id = $id;
            //         $cat->category_id = $category;
            //         $cat->save();
            //     }
            // }
            // if (!empty($request->products)) {
            //     foreach ($request->products as $product) {
            //         $prod = new supplier_product();
            //         $prod->supplier_id = $id;
            //         $prod->product_id = $product;
            //         $prod->save();
            //     }
            // }

            return Redirect::route('view-supplier')->with('success_message', 'Supplier Successfully Updated!!');
        }


        // $categories = DB::select('SELECT category_id as category FROM supplier_categories WHERE supplier_id = ' . $id);
        // $cat = [];
        // foreach ($categories as $value) {
        //     array_push($cat, $value->category);
        // }
        // $products = DB::select('SELECT product_id as product FROM supplier_products WHERE supplier_id = ' . $id);
        // $prod = [];
        // foreach ($products as $value) {
        //     array_push($prod, $value->product);
        // }

        $supplier = Supplier::find($id);
        $categories = Category::all();
        $products = Product::all();
        $currencies = Currency::all();

        $supplier_category = supplier_category::where('supplier_id', $id)->pluck('category_id')->toArray();
        $supplier_product = supplier_product::where('supplier_id', $id)->pluck('product_id')->toArray();

        return view('supplier.update_supplier')->with([ 'supplier' => $supplier, 'categories' => $categories, 'products' => $products,  'currencies' => $currencies, 'supplier_category' => $supplier_category, 'supplier_product' => $supplier_product ]);
    }

    public function add_product(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['code' => 'required|unique:products']);
            $this->validate($request, ['name' => 'required']);
            $this->validate($request, ['description' => 'required']);

            Product::create(array(
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description
            ));
            return Redirect::route('view-product')->with('success_message', 'Product Added Successfully');
        }
        return view('product.add_product');
    }

    public function view_product(Request $request)
    {
        return view('product.view_products')->with(['data' => Product::all()]);
    }
    public function delete_product(Request $request, $id)
    {
        Product::destroy('id', '=', $id);
        return Redirect::route('view-product')->with('success_message', 'Product Successfully Deleted!!');
    }
    public function update_product(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required']);
            $this->validate($request, ['code' => 'required']);

            Product::where('id', '=', $id)->update(array(
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description
            ));

            return Redirect::route('view-product')->with('success_message', 'Product Successfully Updated!!');
        }
        return view('product.update_product')->with(['data' => Product::find($id)]);
    }


    public function create_code(Request $request)
    {

        if ($request->isMethod('post')) {

            $season = season::find($request->season_id);
            $start_date = $season->start_date;
            $end_date = $season->end_date;

            $this->validate($request, ['ref_no'         => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['brand_name'                 => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays'                 => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person'                 => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['category'                 => 'required'], ['required' => 'Please select Category']);
            $this->validate($request, ['product'                 => 'required'], ['required' => 'Please select Product']);
            $this->validate($request, ['season_id'                  => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_booking'             => 'required'], ['required' => 'Please select Agency']);
            $this->validate($request, ['pax_no'                     => 'required'], ['required' => 'Please select PAX No']);
            $this->validate($request, ['supplier'                 => 'required'], ['required' => 'Please select Supplier']);
            $this->validate($request, ['date_of_travel'  => 'required'], ['required' => 'Please select date of travel']);

            if($request->date_of_travel){
                if($request->date_of_travel < $start_date || $request->date_of_travel > $end_date ){
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'date_of_travel' => ['Wrong Date Selected']
                    ]);

                }
            }

            // $this->validate($request, ['fb_airline_name_id'         => 'required_if:flight_booked,yes'], ['required_if' => 'Please select flight airline name']);

            // $this->validate($request, ['fb_payment_method_id'       => 'required_if:flight_booked,yes'], ['required_if' => 'Please select payment method']);

            // $this->validate($request, ['fb_booking_date'            => 'required_if:flight_booked,yes'], ['required_if' => 'Please select booking date']);

            // $this->validate($request, ['fb_airline_ref_no'          => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter airline reference number']);

            // $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter flight booking details']);
            // //
            // // $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'],['required_if' => 'Please select booked person']);
            // $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'], ['required_if' => 'Plesse enter flight booking date']);
            // //
            // // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
            // $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'], ['required_if' => 'Plesse enter transfer date']);
            // // $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'], ['required_if' => 'Plesse enter document sent date']);
            // // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
            // $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'], ['required_if' => 'Plesse enter document sent date']);
            // //
            // $this->validate($request, ['asked_for_transfer_details' => 'required'], ['required' => 'Please select asked for transfer detail box']);
            // $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'], ['required_if' => 'Please transfer detail']);
            // $this->validate($request, ['form_sent_on'               => 'required'], ['required' => 'Please select form sent on']);
            // // $this->validate($request, ['transfer_info_received'     => 'required'],['required' => 'Please select transfer info received']);
            // // $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'],['required_if' => 'Please transfer info detail']);

            // $this->validate($request, ['itinerary_finalised'        => 'required'], ['required' => 'Please select itinerary finalised']);
            // $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Please enter itinerary finalised details']);

            // // $this->validate($request, ['itf_person'                => 'required_if:itinerary_finalised,no'],['required_if' => 'Please select itinerary person']);
            // $this->validate($request, ['itf_last_date'              => 'required_if:itinerary_finalised,no'], ['required_if' => 'Plesse enter itinerary sent date']);

            // $this->validate($request, ['documents_sent'             => 'required'], ['required' => 'Please select documents sent']);
            // $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'], ['required_if' => 'Please enter document sent details']);

            // $this->validate($request, ['electronic_copy_sent'       => 'required'], ['required' => 'Please select electronic copy sent']);
            // $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Please enter electronic copy details']);

            // $this->validate($request, ['transfer_organised'         => 'required'], ['required' => 'Please select transfer organised']);
            // $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Please enter transfer organised details']);

            // $this->validate($request, ['sale_person'                => 'required'], ['required' => 'Please select type of sale person']);
            // $this->validate($request, ['tdp_current_date'              => 'required_if:document_prepare,yes'], ['required_if' => 'Plesse enter Travel Document Prepared Date']);

            if ($request->form_received_on == '0000-00-00') {
                $form_received_on = NULL;
            } else {
                $form_received_on = $request->form_received_on;
            }
            //
            if ($request->app_login_date == '0000-00-00') {
                $app_login_date = NULL;
            } else {
                $app_login_date = $request->app_login_date;
            }
            //
            $booking_id = code::create(array(
                'ref_no'                      => $request->ref_no,
                'brand_name'                  => $request->brand_name,
                'season_id'                   => $request->season_id,
                'agency_booking'              => $request->agency_booking,
                'pax_no'                      => $request->pax_no,
                'date_of_travel'              => Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d'),
                'category'                    => $request->category,
                'supplier'                    => $request->supplier,
                'product'                     => $request->product,
                'flight_booked'               => $request->flight_booked,
                'fb_airline_name_id'          => $request->fb_airline_name_id,
                'fb_payment_method_id'        => $request->fb_payment_method_id,
                'fb_booking_date'             => Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d'),
                'fb_airline_ref_no'           => $request->fb_airline_ref_no,
                'fb_last_date'                => Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d'),
                'fb_person'                   => $request->fb_person,
                //
                'aft_last_date'                => Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d'),
                'aft_person'                   => $request->aft_person,
                'ds_last_date'                 => Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d'),
                'ds_person'                    => $request->ds_person,
                'to_last_date'                 => Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d'),
                'to_person'                    => $request->to_person,
                //
                'document_prepare'             => $request->document_prepare,
                'dp_last_date'                 => Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d'),
                'dp_person'                    => $request->dp_person,
                //
                //
                'flight_booking_details'      => $request->flight_booking_details,
                'asked_for_transfer_details'  => $request->asked_for_transfer_details,
                'transfer_details'            => $request->transfer_details,
                'form_sent_on'                => Carbon::parse(str_replace('/', '-', $request->form_sent_on))->format('Y-m-d'),
                'form_received_on'            => $form_received_on,
                'app_login_date'              => $app_login_date,
                // 'transfer_info_received'      => $request->transfer_info_received,
                // 'transfer_info_details'       => $request->transfer_info_details,
                'itinerary_finalised'         => $request->itinerary_finalised,
                'itinerary_finalised_details' => $request->itinerary_finalised_details,
                'itf_last_date'               => Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d'),
                'itf_person'                  => $request->itf_person,
                'documents_sent'              => $request->documents_sent,
                'documents_sent_details'      => $request->documents_sent_details,
                'electronic_copy_sent'        => $request->electronic_copy_sent,
                'electronic_copy_details'     => $request->electronic_copy_details,
                'transfer_organised'          => $request->transfer_organised,
                'transfer_organised_details'  => $request->transfer_organised_details,
                'type_of_holidays'            => $request->type_of_holidays,
                'sale_person'                 => $request->sale_person,
                'deposit_received'            => $request->deposit_received == '' ? 0 : $request->deposit_received,
                'remaining_amount_received'   => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
                'fso_person'                  => $request->fso_person,
                'fso_last_date'               => Carbon::parse(str_replace('/', '-', $request->fso_last_date))->format('Y-m-d'),
                'aps_person'                  => $request->aps_person,
                'aps_last_date'               => Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d'),
                'finance_detail'              => $request->finance_detail,
                'destination'                 => $request->destination,
                'user_id'                     => Auth::user()->id,
                'itf_current_date'            => Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d'),
                'tdp_current_date'            => Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d'),
                'tds_current_date'            => Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d'),
                'holiday'                     => $request->holiday,

            ));

            // if ($request->flight_booked == 'yes') {
            //     //Sending email
            //     $template   = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;
            //     $template   .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
            //     $template   .= '<h1>Last Date Of Flight Booking : ' . $request->fb_last_date . '</h1>';

            //     if ($request->fb_person == '') {
            //         $email = Auth::user()->email;
            //         $template   .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
            //     } else {
            //         $record = User::where('id', $request->fb_person)->get()->first();
            //         $email  = $record->email;
            //         $name   = $record->name;
            //         $template   .= '<h1>Responsible Person : ' . $name . '</h1>';
            //     }
            //     $data['to']        = $email;
            //     $data['name']      = config('app.name');
            //     $data['from']      = config('app.mail');
            //     $data['subject']   = "Task Flight Booked Alert";
            //     try {
            //         // \Mail::send("email_template.flight_booked_alert", ['template' => $template], function ($m) use ($data) {
            //         //     $m->from($data['from'], $data['name']);
            //         //     $m->to($data['to'])->subject($data['subject']);
            //         // });
            //     } catch (Swift_RfcComplianceException $e) {
            //         return $e->getMessage();
            //     }
            //     //Sending email
            // }
            // if ($request->form_received_on == '0000-00-00') {
            //     //Sending email
            //     $template     = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;

            //     $template   .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
            //     $template   .= '<h1>Reminder for sent on date : ' . $request->fso_last_date . '</h1>';

            //     if ($request->fso_person == '') {
            //         $email = Auth::user()->email;
            //         $template   .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
            //     } else {
            //         $record = User::where('id', $request->fso_person)->get()->first();
            //         $email  = $record->email;
            //         $name   = $record->name;
            //         $template   .= '<h1>Responsible Person : ' . $name . '</h1>';
            //     }
            //     $data['to']        = $email;
            //     $data['name']      = config('app.name');
            //     $data['from']      = config('app.mail');
            //     $data['subject']   = "Reminder for form sent on";
            //     try {
            //         // \Mail::send("email_template.form_sent_on", ['template' => $template], function ($m) use ($data) {
            //         //     $m->from($data['from'], $data['name']);
            //         //     $m->to($data['to'])->subject($data['subject']);
            //         // });
            //     } catch (Swift_RfcComplianceException $e) {
            //         return $e->getMessage();
            //     }
            //     //Sending email
            // }

            // if ($request->electronic_copy_sent == 'no') {
            //     //Sending email
            //     $template    = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/' . $booking_id->id;

            //     $template   .= '<h1>Reference Number : ' . $request->ref_no . '</h1>';
            //     $template   .= '<h1>App Reminder Sent Date : ' . $request->aps_last_date . '</h1>';

            //     if ($request->aps_person == '') {
            //         $email = Auth::user()->email;
            //         $template   .= '<h1>Responsible Person : ' . Auth::user()->name . '</h1>';
            //     } else {
            //         $record = User::where('id', $request->aps_person)->get()->first();
            //         $email  = $record->email;
            //         $name   = $record->name;
            //         $template   .= '<h1>Responsible Person : ' . $name . '</h1>';
            //     }
            //     $data['to']        = $email;
            //     $data['name']      = config('app.name');
            //     $data['from']      = config('app.mail');
            //     $data['subject']   = "Reminder for app login sent";
            //     try {
            //         // \Mail::send("email_template.app_login_sent", ['template' => $template], function ($m) use ($data) {
            //         //     $m->from($data['from'], $data['name']);
            //         //     $m->to($data['to'])->subject($data['subject']);
            //         // });
            //     } catch (Swift_RfcComplianceException $e) {
            //         return $e->getMessage();
            //     }
            //     //Sending email
            // }

            return Redirect::route('creat-code')->with('success_message', 'Created Successfully');
        } else{
            $get_ref = Cache::remember('get_ref', $this->cacheTimeOut, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_ref';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_ref';
                $output =  $this->curl_data($url);
                //   return json_decode($output)->data;
            });

            $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });

            $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });

            $booking_email = booking_email::where('booking_id', '=', 1)->get();
            return view('code.create-code')->with(['get_holiday_type' => $get_holiday_type, 'seasons' => season::all(), 'persons' => user::all(), 'get_refs' => $get_ref, 'get_user_branches' => $get_user_branches, 'booking_email' => $booking_email, 'payment' => payment::all(), 'airline' => airline::all(), 'categories' => Category::all(), 'products' => Product::all(),'suppliers' => Supplier::all()]);
        }
    }

    function checkInSession($date, $season) {
        $startDate = date('Y-m-d', strtotime($season->start_date));
        $endDate   = date('Y-m-d', strtotime($season->end_date));
        if (($date >= $startDate) && ($date <= $endDate)){
            return true;
        }else{
           return false;
        }
    }

    public function delete_quote($id)
    {
        $qoute = Qoute::findOrFail(decrypt($id));
        $qoute->delete();
        return Redirect::route('view-quote')->with('success_message', 'Supplier Successfully Updated!!');

    }

    public function convert_quote_to_booking($id){

        $qoute = Qoute::find($id);
        $qoute->qoute_to_booking_status = 1;
        $qoute->qoute_to_booking_date   = date('Y-m-d');
        $qoute->save();

        $booking = new Booking;
        $booking->reference_name        =  $qoute->reference_name;
        $booking->ref_no                =  $qoute->ref_no;
        $booking->qoute_id              =  $id;
        $booking->quotation_no          =  $qoute->quotation_no;
        $booking->dinning_preferences   =  $qoute->dinning_preferences;
        $booking->lead_passenger_name   =  $qoute->lead_passenger_name;
        $booking->brand_name            =  $qoute->brand_name;
        $booking->type_of_holidays      =  $qoute->type_of_holidays;
        $booking->sale_person           =  $qoute->sale_person;
        $booking->season_id             =  $qoute->season_id;
        $booking->agency_booking        =  $qoute->agency_booking;
        $booking->agency_name           =  $qoute->agency_name;
        $booking->agency_contact_no     =  $qoute->agency_contact_no;
        $booking->currency              =  $qoute->currency;
        $booking->convert_currency      =  $qoute->convert_currency;
        $booking->group_no              =  $qoute->group_no;
        $booking->net_price             =  $qoute->net_price;
        $booking->markup_amount         =  $qoute->markup_amount;
        $booking->selling               =  $qoute->selling;
        $booking->gross_profit          =  $qoute->gross_profit;
        $booking->markup_percent        =  $qoute->markup_percent;
        $booking->show_convert_currency =  $qoute->show_convert_currency;
        $booking->per_person            =  $qoute->per_person;
        $booking->port_tax              =  $qoute->port_tax;
        $booking->total_per_person      =  $qoute->total_per_person;
        $booking->qoute_to_booking_date = date('Y-m-d');
        $booking->save();

        $qouteDetails = QouteDetail::where('qoute_id',$id)->get();
        foreach($qouteDetails as $key => $qouteDetail){

            $bookingDetail = new BookingDetail;
            $bookingDetail->qoute_id            = $id;
            $bookingDetail->booking_id          = $booking->id;
            $bookingDetail->quotation_no        = $qoute->quotation_no;
            $bookingDetail->row                 = $key+1;
            $bookingDetail->date_of_service     = $qouteDetail->date_of_service ? Carbon::parse(str_replace('/', '-', $qouteDetail->date_of_service))->format('Y-m-d') : null;
            $bookingDetail->service_details     = $qouteDetail->service_details;
            $bookingDetail->category_id         = $qouteDetail->category;
            $bookingDetail->supplier            = $qouteDetail->supplier;
            $bookingDetail->booking_date        = $qouteDetail->booking_date ? Carbon::parse(str_replace('/', '-', $qouteDetail->booking_date))->format('Y-m-d') : null;
            $bookingDetail->booking_due_date    = $qouteDetail->booking_due_date ? Carbon::parse(str_replace('/', '-', $qouteDetail->booking_due_date))->format('Y-m-d') : null;
            $bookingDetail->booked_by           = $qouteDetail->booked_by;
            $bookingDetail->booking_refrence    = $qouteDetail->booking_refrence;
            $bookingDetail->booking_type        = $qouteDetail->booking_type;
            $bookingDetail->comments            = $qouteDetail->comments;
            $bookingDetail->supplier_currency   = $qouteDetail->supplier_currency;
            $bookingDetail->cost                = $qouteDetail->cost;
            $bookingDetail->actual_cost         = $qouteDetail->actual_cost;
            $bookingDetail->supervisor_id       = $qouteDetail->supervisor;
            $bookingDetail->added_in_sage       = $qouteDetail->added_in_sage;
            $bookingDetail->qoute_base_currency = $qouteDetail->qoute_base_currency;
            $bookingDetail->save();
        }

        return Redirect::route('view-quote')->with('success_message', 'Quotation Converted Successfully. ');
    }

    public function create_quote(Request $request){

        if($request->isMethod('post')){

            $this->validate($request, ['ref_no'           => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['lead_passenger_name' => 'required'], ['required' => 'Lead Passenger Name is required']);
            $this->validate($request, ['brand_name'       => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person'      => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['season_id'        => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_name'       => 'required_if:agency_booking,2'], ['required_if' => 'Agency Name is required']);
            $this->validate($request, ['agency_contact_no' => 'required_if:agency_booking,2'], ['required_if' => 'Agency No is required']);
            $this->validate($request, ['agency_booking'    => 'required'], ['required' => 'Agency is required']);
            $this->validate($request, ['currency'          => 'required'], ['required' => 'Booking Currency is required']);
            $this->validate($request, ['group_no'          => 'required'], ['required' => 'Pax No is required']);
            $this->validate($request, ['dinning_preferences'          => 'required'], ['required' => 'Dinning Preferences is required']);
            $this->validate($request, [ "booking_due_date"    => "required|array", "booking_due_date.*"  => "required" ]);
            $this->validate($request, [ "cost"    => "required|array", "cost.*"  => "required"]);

            $season = season::find($request->season_id);

            // if(!empty($request->date_of_service)){
            //     $error_array = [];
            //     foreach($request->date_of_service as $key => $date){
        
            //         $start = date('Y-m-d', strtotime($season->start_date));
            //         $end   = date('Y-m-d', strtotime($season->end_date));

            //         if(!is_null($date)){
            //             $date  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($date) && !is_null($start)  && !is_null($end)){
            //             if( !(($date >= $start) && ($date <= $end)) ){
            //                 $error_array[$key+1] = "Date of service should be season date range.";
            //             }
            //         }
         
            //     }
            // }

            // if(!empty($error_array)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'date_of_service' =>  (object) $error_array
            //     ]);
            // }

            // $booking_error = [];
            // if(!empty($request->booking_date)){
            //     foreach($request->booking_date as $key => $date){

            //         if(!is_null($date)){
            //             $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($request->booking_due_date[$key])){
            //             $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
            //         }else{
            //             $booking_due_date  = null;
            //         }

            //         if(!is_null($request->date_of_service[$key])){
            //             $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
            //         }else{
            //             $date_of_service  = null;
            //         }

            //         if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( ($date > $booking_due_date ) ){
            //                 $booking_error[$key+1] = "Booking Date should be smaller than due date";
            //             }
            //         }

            //         if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
            //                 $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
            //             }
            //         }

            //     }
            // }

            // if(!empty($booking_error)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'booking_date' => (object) $booking_error
            //     ]);
            $errors = [];
            foreach ($request->booking_due_date as $key => $duedate) {
                $duedate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $duedate))->format('Y-m-d')));
                
                $startDate = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $season->start_date))->format('Y-m-d')));
                $endDate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $season->end_date))->format('Y-m-d')));
                $bookingdate     = (isset($request->booking_date) && !empty($request->booking_date[$key]))? $request->booking_date[$key] : NULL;
                if($bookingdate != NULL){
                    $bookingdate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
                }
                $dateofservice   = (isset($request->date_of_service) && !empty($request->date_of_service[$key]))? $request->date_of_service[$key] : NULL;
                if ($dateofservice != null) {
                    $dateofservice   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $dateofservice))->format('Y-m-d')));
                }
                $error = [];
                $dueresult = false;
                $dofresult = false;
                $bookresult = false;

                if($this->checkInSession($duedate, $season) == false){
                    $a[$key+1] = 'Due Date should be season date range.';
                }else{
                    $dueresult = true;
                }
                if($bookingdate != NULL && $this->checkInSession($bookingdate, $season) == false){
                    $b[$key+1]  = 'Booking Date should be season date range.';
                }else{
                    $bookresult = true;
                }
                if($dateofservice != NULL && $this->checkInSession($dateofservice, $season) == false){
                    $c[$key+1]  = 'Date of service should be season date range.';
                }else{
                    $dofresult = true;
                }

                if($dateofservice != NULL && $bookingdate  == NULL){
                    $b[$key+1]  = 'Booking Date field is required before the date of service.';
                    $bookresult = false;
                }

                if($bookresult == true){
                    if($bookingdate != null && $bookingdate < $duedate){
                        $b[$key+1]  = 'Booking Date should be smaller than booking due date.';
                    }
                }

                if($dofresult == true){
                    if ($bookingdate != null && $bookingdate > $dateofservice) {
                        $c[$key+1]  = 'Date of service should be smaller than booking date.';
                    }
                }

                $error['date_of_service'] = (isset($c) && count($c) >0 )? (object) $c : NULL;
                $error['booking_date'] = (isset($b) && count($b) >0 )? (object) $b : NULL;
                $error['booking_due_date'] = (isset($a) && count($a) >0 )? (object) $a : NULL;

                $errors = $error;
            }

            if(count($errors) > 0){
              if($error['date_of_service'] != NULL || $error['date_of_service'] != NULL || $error['date_of_service'] != NULL){
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
                }
            }

            $qoute = new Qoute;
            $qoute->ref_no           =  $request->ref_no;
            $qoute->reference_name   =  $request->reference;
            $qoute->quotation_no     =  $request->quotation_no;
            $qoute->dinning_preferences  = $request->dinning_preferences;
            $qoute->lead_passenger_name =  $request->lead_passenger_name;
            $qoute->brand_name       =  $request->brand_name;
            $qoute->type_of_holidays =  $request->type_of_holidays;
            $qoute->sale_person      =  $request->sale_person;
            $qoute->season_id        =  $request->season_id;
            $qoute->agency_booking   =  $request->agency_booking;
            $qoute->agency_name       =  $request->agency_name;
            $qoute->agency_contact_no =  $request->agency_contact_no;
            $qoute->currency          =  $request->currency;
            $qoute->convert_currency  =  $request->convert_currency;
            $qoute->group_no          =  $request->group_no;
            $qoute->net_price         =  $request->net_price;
            $qoute->markup_amount     =  $request->markup_amount;
            $qoute->selling           =  $request->selling;
            $qoute->gross_profit      =  $request->gross_profit;
            $qoute->markup_percent    =  $request->markup_percent;
            $qoute->show_convert_currency =  $request->show_convert_currency;
            $qoute->per_person       =  $request->per_person;
            $qoute->save();

            if(!empty($request->cost)){
                foreach($request->cost as $key => $cost){

                    $qouteDetail = new QouteDetail;
                    $qouteDetail->qoute_id = $qoute->id;
                    $qouteDetail->date_of_service   = $request->date_of_service[$key] ?date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->service_details   = $request->service_details[$key];
                    $qouteDetail->category_id       = $request->category[$key];
                    $qouteDetail->supplier          = $request->supplier[$key];
                    $qouteDetail->product           = $request->product[$key];
                    $qouteDetail->booking_date      = $request->booking_date[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->booking_due_date  = $request->booking_due_date[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d'))): null;
                    $qouteDetail->booking_method    = $request->booking_method[$key];
                    $qouteDetail->booked_by         = $request->booked_by[$key];
                    $qouteDetail->booking_refrence  = $request->booking_refrence[$key];
                    $qouteDetail->booking_type      = $request->booking_type[$key];
                    $qouteDetail->comments          = $request->comments[$key];
                    $qouteDetail->supplier_currency = $request->supplier_currency[$key];
                    $qouteDetail->cost              = $request->cost[$key];
                    $qouteDetail->supervisor_id     = $request->supervisor[$key];
                    $qouteDetail->added_in_sage     = ($request->has('added_in_sage') && isset($request->added_in_sage[$key]))? $request->added_in_sage[$key] : NULL;
                    $qouteDetail->qoute_base_currency     = $request->qoute_base_currency[$key];
                    $qouteDetail->save();
                }
            }

            return response()->json(['success_message'=>'Quote Successfully Created!!']);

        }

        $get_user_branche = Cache::remember('get_user_branche', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output, true);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.create')->with([
            'get_user_branche' => $get_user_branche,
            'get_holiday_type' => $get_holiday_type,
            'categories'       => Category::all()->sortBy('name'),
            'products'         => Product::all()->sortBy('name'),
            'seasons'          => season::all(),
            'users'            => User::all()->sortBy('name'),
            'supervisors'      => User::where('role_id',5)->orderBy('name','ASC')->get(),
            'suppliers'        => Supplier::all()->sortBy('name'),
            'booking_methods'  => BookingMethod::all()->sortBy('id'),
            'currencies'       => Currency::all()->sortBy('name'),
            'templates'        => Template::all()->sortBy('name'),
            // 'sale_person' => User::where('role_id',2)->orderBy('name', 'asc')->get(),
        ]);
    }

  
    public function view_quote(){

        return view('qoute.view')->with(['quotes' => $results = Qoute::orderBy('created_at', 'desc')->get() ]);
    }


    public function booking(Request $request,$id){

        if($request->isMethod('post')){

            $this->validate($request, ['ref_no'           => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['lead_passenger_name' => 'required'], ['required' => 'Lead Passenger Name is required']);
            $this->validate($request, ['brand_name'       => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person'      => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['season_id'        => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_name'       => 'required_if:agency_booking,2'], ['required_if' => 'Agency Name is required']);
            $this->validate($request, ['agency_contact_no' => 'required_if:agency_booking,2'], ['required_if' => 'Agency No is required']);
            $this->validate($request, ['agency_booking'    => 'required'], ['required' => 'Agency is required']);
            $this->validate($request, ['currency'          => 'required'], ['required' => 'Booking Currency is required']);
            $this->validate($request, ['group_no'          => 'required'], ['required' => 'Pax No is required']);
            $this->validate($request, ['dinning_preferences' => 'required'], ['required' => 'Dinning Preferences is required']);
            $this->validate($request, [ "booking_due_date"    => "required|array", "booking_due_date.*"  => "required" ]);
            $this->validate($request, [ "cost"    => "required|array", "cost.*"  => "required"]);

            $season = season::find($request->season_id);

            // if(!empty($request->date_of_service)){
            //     $error_array = [];
            //     foreach($request->date_of_service as $key => $date){

            //         $start = date('Y-m-d', strtotime($season->start_date));
            //         $end   = date('Y-m-d', strtotime($season->end_date));

            //         if(!is_null($date)){
            //             $date  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($date) && !is_null($start)  && !is_null($end)){
            //             if( !(($date >= $start) && ($date <= $end)) ){
            //                 $error_array[$key+1] = "Date of service should be season date range.";
            //             }
            //         }

            //     }
            // }

            // if(!empty($error_array)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'date_of_service' =>  (object) $error_array
            //     ]);
            // }

            // $booking_error = [];
            // if(!empty($request->booking_date)){
            //     foreach($request->booking_date as $key => $date){

            //         if(!is_null($date)){
            //             $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($request->booking_due_date[$key])){
            //             $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
            //         }else{
            //             $booking_due_date  = null;
            //         }

            //         if(!is_null($request->date_of_service[$key])){
            //             $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
            //         }else{
            //             $date_of_service  = null;
            //         }

            //         if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( ($date > $booking_due_date ) ){
            //                 $booking_error[$key+1] = "Booking Date should be smaller than due date";
            //             }
            //         }

            //         if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
            //                 $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
            //             }
            //         }

            //     }
            // }

            // if(!empty($booking_error)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'booking_date' => (object) $booking_error
            //     ]);
            // }


            $errors = [];
            foreach ($request->booking_due_date as $key => $duedate) {
                $duedate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $duedate))->format('Y-m-d')));

                $startDate = date('Y-m-d', strtotime($season->start_date));
                $endDate   = date('Y-m-d', strtotime($season->end_date));

                $bookingdate     = (isset($request->booking_date) && !empty($request->booking_date[$key]))? $request->booking_date[$key] : NULL;
                if($bookingdate != NULL){
                    $bookingdate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
                }
                $dateofservice   = (isset($request->date_of_service) && !empty($request->date_of_service[$key]))? $request->date_of_service[$key] : NULL;
                if ($dateofservice != null) {
                    $dateofservice   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $dateofservice))->format('Y-m-d')));
                }
                $error = [];
                $dueresult = false;
                $dofresult = false;
                $bookresult = false;

                if($this->checkInSession($duedate, $season) == false){
                    $a[$key+1] = 'Due Date should be season date range.';
                }else{
                    $dueresult = true;
                }
                if($bookingdate != NULL && $this->checkInSession($bookingdate, $season) == false){
                    $b[$key+1]  = 'Booking Date should be season date range.';
                }else{
                    $bookresult = true;
                }
                if($dateofservice != NULL && $this->checkInSession($dateofservice, $season) == false){
                    $c[$key+1]  = 'Date of service should be season date range.';
                }else{
                    $dofresult = true;
                }

                if($dateofservice != NULL && $bookingdate  == NULL){
                    $b[$key+1]  = 'Booking Date field is required before the date of service.';
                    $bookresult = false;
                }

                if($bookresult == true){
                    if($bookingdate != null && $bookingdate < $duedate){
                        $b[$key+1]  = 'Booking Date should be smaller than booking due date.';
                    }
                }

                if($dofresult == true){
                    if ($bookingdate != null && $bookingdate > $dateofservice) {
                        $c[$key+1]  = 'Date of service should be smaller than booking date.';
                    }
                }


                $error['date_of_service'] = (isset($c) && count($c) >0 )? (object) $c : NULL;
                $error['booking_date'] = (isset($b) && count($b) >0 )? (object) $b : NULL;
                $error['booking_due_date'] = (isset($a) && count($a) >0 )? (object) $a : NULL;

                $errors = $error;
            }

            if(count($errors) > 0){
              if($error['date_of_service'] != NULL || $error['date_of_service'] != NULL || $error['date_of_service'] != NULL){
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
                }
            }

            $booking = Booking::updateOrCreate(
                [ 'quotation_no' => $request->quotation_no ],

                [
                    'ref_no'           =>  $request->ref_no,
                    'reference_name'   => $request->reference,
                    'qoute_id'          => $request->qoute_id,
                    'quotation_no'     =>  $request->quotation_no,
                    'dinning_preferences'   => $request->dinning_preferences,
                    'lead_passenger_name'=>  $request->lead_passenger_name,
                    'brand_name'       =>  $request->brand_name,
                    'type_of_holidays' =>  $request->type_of_holidays,
                    'sale_person'      =>  $request->sale_person,
                    'season_id'        =>  $request->season_id,
                    'agency_booking'   =>  $request->agency_booking,
                    'agency_name'       =>  $request->agency_name,
                    'agency_contact_no' =>  $request->agency_contact_no,
                    'currency'          =>  $request->currency,
                    'convert_currency'  =>  $request->convert_currency,
                    'group_no'          =>  $request->group_no,
                    'net_price'         =>  $request->net_price,
                    'markup_amount'     =>  $request->markup_amount,
                    'selling'           =>  $request->selling,
                    'gross_profit'      =>  $request->gross_profit,
                    'markup_percent'    =>  $request->markup_percent,
                    'show_convert_currency' =>  $request->show_convert_currency,
                    'per_person'       =>  $request->per_person,

                ]
            );

            if(!empty($request->actual_cost)){
                foreach($request->actual_cost as $key => $cost){

                    if(!is_null($request->qoute_invoice)){

                        if(array_key_exists($key,$request->qoute_invoice))
                        {

                            $oldFileName = $request->qoute_invoice_record[$key];


                            $newFile = $request->qoute_invoice[$key];
                            $filename = $newFile->getClientOriginalName();

                            $folder = public_path('booking/' . $request->qoute_id );

                            if (!File::exists($folder)) {
                                File::makeDirectory($folder, 0775, true, true);
                            }

                            $destinationPath = public_path('booking/'. $request->qoute_id .'/'.  $oldFileName);
                            File::delete($destinationPath);

                            $newFile->move(public_path('booking/' . $request->qoute_id ), $filename);

                        }
                        else{
                            $filename = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
                        }
                    }
                    else{

                        $filename = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
                    }

                    $bookingDetail = BookingDetail::updateOrCreate(
                        [
                            'quotation_no' => $request->quotation_no,
                            'row' => $key+1,
                        ],

                        [
                            'qoute_id'          => $request->qoute_id,
                            'booking_id'        => $booking->id,
                            'quotation_no'      => $request->quotation_no,
                            'row'               => $key+1,
                            'date_of_service'   => $request->date_of_service[$key] ? Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d') : null,
                            'service_details'   => $request->service_details[$key],
                            'category_id'       => $request->category[$key],
                            'supplier'          => $request->supplier[$key],
                            'booking_date'      => $request->booking_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d') : null,
                            'booking_due_date'  => $request->booking_due_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d') : null,
                            // 'booking_method'    => $request->booking_method[$key],
                            'booked_by'         => $request->booked_by[$key],
                            'booking_refrence'  => $request->booking_refrence[$key],
                            'booking_type'      => $request->booking_type[$key],
                            'comments'          => $request->comments[$key],
                            'supplier_currency' => $request->supplier_currency[$key],
                            'cost'              => $request->cost[$key],
                            'actual_cost'       => $request->actual_cost[$key],
                            'supervisor_id'     => $request->supervisor[$key],
                            'added_in_sage'     => $request->added_in_sage[$key],
                            'qoute_base_currency' => $request->qoute_base_currency[$key],
                            'qoute_invoice'     => $filename,
                        ]
                    );

                    foreach($request->deposit_due_date[$key] as $ikey => $deposit_due_date){

                        if($request->upload_calender[$key][$ikey]  == true && $deposit_due_date != NULL){
                            $event = new Event;
                            $event->name        = "To Pay ".$request->deposit_amount[$key][$ikey].' '.$request->supplier_currency[$key]." to Supplier";
                            $event->description = 'Event description';
                            $event->startDate   = ($deposit_due_date != NULL)? Carbon::parse(str_replace('/', '-', $deposit_due_date))->startOfDay(): NULL;
                            $event->endDate     = ($deposit_due_date != NULL)? Carbon::parse(str_replace('/', '-', $deposit_due_date))->endOfDay(): NULL;
                            // $event->addAttendee(['email' => 'kashan.kingdomvision@gmail.com']);
                            $event->save();

                        }
                        FinanceBookingDetail::updateOrCreate(
                            [
                                'booking_detail_id' => $bookingDetail->id,
                                'row' => $ikey+1,
                            ],
                            [
                                'upload_to_calender' => $request->upload_calender[$key][$ikey],
                                'deposit_amount'   =>  !empty($request->deposit_amount[$key][$ikey]) ? $request->deposit_amount[$key][$ikey] : null,
                                'deposit_due_date' =>  $request->deposit_due_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'paid_date'        =>  $request->paid_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'payment_method'   =>  $request->payment_method[$key][$ikey]??NULL,
                            ]

                        );

                    }

                }
            }

            return response()->json(['success_message' => 'Successfully Converted To Booked']);
        }

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $booking = Booking::where('qoute_id',$id)->first();

        if(!is_null($booking)){
            $quote = $booking;
        }else{
            $quote = Qoute::find($id);
        }

        $bookingDetail = BookingDetail::where('qoute_id',$id)->get();

        if($bookingDetail->count()){
            $quote_details = $bookingDetail;
        }else{
            $quote_details = QouteDetail::where('qoute_id',$id)->get();
        }

        return view('qoute.booking.edit')->with([
            'quote' => $quote,
            'quote_details' => $quote_details,
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
            'categories' => Category::all()->sortBy('name'),
            // 'seasons' => season::where('default_season',1)->first(),
            'seasons' => season::all(),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role_id',5)->orderBy('name','ASC')->get(),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('id'),
            'payment_method' => payment::all()->sortBy('name'),
            'currencies' => Currency::all()->sortBy('name'),
            'qoute_logs' => QouteLog::where('qoute_id',$id)->get(),
        ]);

            // $booking->ref_no           =  $request->ref_no;
            // $booking->quotation_no     =  $request->quotation_no;
            // $booking->brand_name       =  $request->brand_name;
            // $booking->type_of_holidays =  $request->type_of_holidays;
            // $booking->sale_person      =  $request->sale_person;
            // $booking->season_id        =  $request->season_id;
            // $booking->agency_booking   =  $request->agency_booking;
            // $booking->agency_name       =  $request->agency_name;
            // $booking->agency_contact_no =  $request->agency_contact_no;
            // $booking->currency          =  $request->currency;
            // $booking->convert_currency  =  $request->convert_currency;
            // $booking->group_no          =  $request->group_no;
            // $booking->net_price         =  $request->net_price;
            // $booking->markup_amount     =  $request->markup_amount;
            // $booking->selling           =  $request->selling;
            // $booking->markup_percent    =  $request->markup_percent;
            // $booking->show_convert_currency =  $request->show_convert_currency;
            // $booking->per_person       =  $request->per_person;
            // $booking->save();

            // $bookingDetail = BookingDetail::where('qoute_id', $id)->get();

            // $qouteDetailLog = new QouteDetailLog;

            // foreach($qouteDetails as $key => $qouteDetail){

            //     $QouteDetailLog = new QouteDetailLog;
            //     $QouteDetailLog->qoute_id          = $qouteDetail->qoute_id;
            //     $QouteDetailLog->date_of_service   = $qouteDetail->date_of_service;
            //     $QouteDetailLog->service_details   =  $qouteDetail->service_details;
            //     $QouteDetailLog->category_id       =  $qouteDetail->category_id;
            //     $QouteDetailLog->supplier          =  $qouteDetail->supplier;
            //     $QouteDetailLog->booking_date      =  $qouteDetail->booking_date;
            //     $QouteDetailLog->booking_due_date  =  $qouteDetail->booking_due_date;
            //     $QouteDetailLog->booking_method    =  $qouteDetail->booking_method;
            //     $QouteDetailLog->booked_by         =  $qouteDetail->booked_by;
            //     $QouteDetailLog->booking_refrence  =  $qouteDetail->booking_refrence;
            //     $QouteDetailLog->comments          =  $qouteDetail->comments;
            //     $QouteDetailLog->supplier_currency =  $qouteDetail->supplier_currency;
            //     $QouteDetailLog->cost              =  $qouteDetail->cost;
            //     $QouteDetailLog->supervisor_id     =  $qouteDetail->supervisor_id;
            //     $QouteDetailLog->added_in_sage     =  $qouteDetail->added_in_sage;
            //     $QouteDetailLog->qoute_base_currency =  $qouteDetail->qoute_base_currency;
            //     $QouteDetailLog->log_no = $qouteDetailLogNumber;
            //     $QouteDetailLog->save();
            // }

            // Delete old qoute
            // QouteDetail::where('qoute_id',$id)->delete();

            // if(!empty($request->cost)){
            //     foreach($request->cost as $key => $cost){

            //         $qouteDetail = new QouteDetail;
            //         $qouteDetail->qoute_id = $qoute->id;
            //         $qouteDetail->date_of_service   = $request->date_of_service[$key] ? Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d') : null;
            //         $qouteDetail->service_details   = $request->service_details[$key];
            //         $qouteDetail->category_id       = $request->category[$key];
            //         $qouteDetail->supplier          = $request->supplier[$key];
            //         $qouteDetail->booking_date      = $request->booking_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d') : null;
            //         $qouteDetail->booking_due_date  = $request->booking_due_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d') : null;
            //         $qouteDetail->booking_method    = $request->booking_method[$key];
            //         $qouteDetail->booked_by         = $request->booked_by[$key];
            //         $qouteDetail->booking_refrence  = $request->booking_refrence[$key];
            //         $qouteDetail->comments          = $request->comments[$key];
            //         $qouteDetail->supplier_currency = $request->supplier_currency[$key];
            //         $qouteDetail->cost              = $request->cost[$key];
            //         $qouteDetail->supervisor_id     = $request->supervisor[$key];
            //         $qouteDetail->added_in_sage     = $request->added_in_sage[$key];
            //         $qouteDetail->qoute_base_currency     = $request->qoute_base_currency[$key];

            //         // if(!is_null($request->qoute_invoice)){

            //         //     if(array_key_exists($key,$request->qoute_invoice))
            //         //     {

            //         //         $file = $request->qoute_invoice[$key];


            //         //         $folder = public_path('quote/' . $qoute->id );
            //         //         $filename = $file->getClientOriginalName();


            //         //         if (!File::exists($folder)) {
            //         //             File::makeDirectory($folder, 0775, true, true);
            //         //         }

            //         //         $destinationPath = public_path('quote/'. $id .'/'.  $filename  );
            //         //         File::delete($destinationPath);

            //         //         $file->move(public_path('quote/' . $qoute->id ), $filename);

            //         //         $qouteDetail->qoute_invoice  = $filename ? $filename : null;

            //         //     }
            //         //     else{
            //         //         $qouteDetail->qoute_invoice = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
            //         //     }
            //         // }else{

            //         //     $qouteDetail->qoute_invoice = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
            //         // }

            //         $qouteDetail->save();

            //     }
            // }

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.view')->with(['quotes' => Qoute::all()]);
    }

    public function upload_to_calendar(Request $request){

        if($request->isMethod('post')){

            // dd($request->all());

            // $title = "To Pay $request->deposit_amount $request->supplier_currency to Supplier";

            // $dynamic_text_area = "$request->details";

            // $calendar_start_date = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->format('Ymd');
            // $calendar_end_date = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->format('Ymd');

            // $location = "";
            // $description = "test";
            // // $guests = "kashan.mehmood13@gmail.com";
            // $message_url ="https://www.google.com/calendar/render?action=TEMPLATE&text=".$title."&dates=".$calendar_start_date."/".$calendar_end_date."&details=".$dynamic_text_area."&location=".$location."&sf=true&output=xml";
            // return $message_url;

            $event = new Event;
            $event->name = "To Pay $request->depositAmount $request->supplier_currency to Supplier";
            $event->description = 'Event description';
            $event->startDate = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->startOfDay();
            $event->endDate = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->endOfDay();
            // $event->addAttendee(['email' => 'kashan.kingdomvision@gmail.com']);
            $event->save();

            dd($request->all());
        }

    }


    public function edit_quote(Request $request,$id){

        if($request->isMethod('post')){

            $this->validate($request, ['ref_no'           => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['lead_passenger_name' => 'required'], ['required' => 'Lead Passenger Name is required']);
            $this->validate($request, ['brand_name'       => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['type_of_holidays' => 'required'], ['required' => 'Please select Type Of Holidays']);
            $this->validate($request, ['sale_person'      => 'required'], ['required' => 'Please select Sale Person']);
            $this->validate($request, ['season_id'        => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_name'       => 'required_if:agency_booking,2'], ['required_if' => 'Agency Name is required']);
            $this->validate($request, ['agency_contact_no' => 'required_if:agency_booking,2'], ['required_if' => 'Agency No is required']);
            $this->validate($request, ['agency_booking'    => 'required'], ['required' => 'Agency is required']);
            $this->validate($request, ['currency'          => 'required'], ['required' => 'Booking Currency is required']);
            $this->validate($request, ['group_no'          => 'required'], ['required' => 'Pax No is required']);
            $this->validate($request, ['dinning_preferences'          => 'required'], ['required' => 'Dinning Preferences is required']);
            $this->validate($request, [ "booking_due_date"    => "required|array", "booking_due_date.*"  => "required" ]);
            $this->validate($request, [ "cost"    => "required|array", "cost.*"  => "required"]);
            $season = season::findOrFail($request->season_id);

            // if(!empty($request->date_of_service)){
            //     $error_array = [];
            //     foreach($request->date_of_service as $key => $date){

            //         $start = date('Y-m-d', strtotime($season->start_date));
            //         $end   = date('Y-m-d', strtotime($season->end_date));

            //         if(!is_null($date)){
            //             $date  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($date) && !is_null($start)  && !is_null($end)){
            //             if( !(($date >= $start) && ($date <= $end)) ){
            //                 $error_array[$key+1] = "Date of service should be season date range.";
            //             }
            //         }

            //     }
            // }

            // if(!empty($error_array)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'date_of_service' =>  (object) $error_array
            //     ]);
            // }

            // $booking_error = [];
            // if(!empty($request->booking_date)){
            //     foreach($request->booking_date as $key => $date){

            //         if(!is_null($date)){
            //             $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
            //         }else{
            //             $date  = null;
            //         }

            //         if(!is_null($request->booking_due_date[$key])){
            //             $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
            //         }else{
            //             $booking_due_date  = null;
            //         }

            //         if(!is_null($request->date_of_service[$key])){
            //             $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
            //         }else{
            //             $date_of_service  = null;
            //         }

            //         if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( ($date > $booking_due_date ) ){
            //                 $booking_error[$key+1] = "Booking Date should be smaller than due date";
            //             }
            //         }

            //         if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
            //             if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
            //                 $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
            //             }
            //         }

            //     }
            // }

            // if(!empty($booking_error)){
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'booking_date' => (object) $booking_error
            //     ]);
            // }

            $errors = [];
            foreach ($request->booking_due_date as $key => $duedate) {
                $duedate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $duedate))->format('Y-m-d')));
                
                $startDate =  date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $season->start_date))->format('Y-m-d')));
                $endDate   =  date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $season->end_date))->format('Y-m-d')));

                $bookingdate     = (isset($request->booking_date) && !empty($request->booking_date[$key]))? $request->booking_date[$key] : NULL;
                if($bookingdate != NULL){
                    $bookingdate   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
                    // date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $bookingdate))->format('Y-m-d')));
                }
                $dateofservice   = (isset($request->date_of_service) && !empty($request->date_of_service[$key]))? $request->date_of_service[$key] : NULL;
                if ($dateofservice != null) {
                    $dateofservice   = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $dateofservice))->format('Y-m-d')));
                }
                $error = [];
                $dueresult = false;
                $dofresult = false;
                $bookresult = false;

                if($this->checkInSession($duedate, $season) == false){
                    $a[$key+1] = 'Due Date should be season date range.';
                }else{
                    $dueresult = true;
                }
                if($bookingdate != NULL && $this->checkInSession($bookingdate, $season) == false){
                    $b[$key+1]  = 'Booking Date should be season date range.';
                }else{
                    $bookresult = true;
                }
                if($dateofservice != NULL && $this->checkInSession($dateofservice, $season) == false){
                    $c[$key+1]  = 'Date of service should be season date range.';
                }else{
                    $dofresult = true;
                }

                if($dateofservice != NULL && $bookingdate  == NULL){
                    $b[$key+1]  = 'Booking Date field is required before the date of service.';
                    $bookresult = false;
                }

                if($bookresult == true){
                    if($bookingdate != null && $bookingdate < $duedate){
                        $b[$key+1]  = 'Booking Date should be smaller than booking due date.';
                    }
                }

                if($dofresult == true){
                    if ($bookingdate != null && $bookingdate > $dateofservice) {
                        $c[$key+1]  = 'Date of service should be smaller than booking date.';
                    }
                }


                $error['date_of_service'] = (isset($c) && count($c) >0 )? (object) $c : NULL;
                $error['booking_date'] = (isset($b) && count($b) >0 )? (object) $b : NULL;
                $error['booking_due_date'] = (isset($a) && count($a) >0 )? (object) $a : NULL;

                $errors = $error;
            }

            if(count($errors) > 0){
              if($error['date_of_service'] != NULL || $error['date_of_service'] != NULL || $error['date_of_service'] != NULL){
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
                }
            }


            $qoute = Qoute::findOrFail($id);

            $qoute_log = new QouteLog;

            $qouteDetailLogNumber = $this->increment_log_no($this->get_log_no('QouteLog',$id));
            $qoute_log->qoute_id          =  $id;
            $qoute_log->ref_no            =  $qoute->ref_no;
            $qoute_log->reference_name    =  $qoute->reference_name;
            $qoute_log->quotation_no      =  $qoute->quotation_no;
            $qoute_log->dinning_preferences     = $qoute->dinning_preferences;
            $qoute_log->lead_passenger_name =  $qoute->lead_passenger_name;
            $qoute_log->brand_name        =  $qoute->brand_name;
            $qoute_log->type_of_holidays  =  $qoute->type_of_holidays;
            $qoute_log->sale_person       =  $qoute->sale_person;
            $qoute_log->season_id         =  $qoute->season_id;
            $qoute_log->agency_booking    =  $qoute->agency_booking;
            $qoute_log->agency_name       =  $qoute->agency_name;
            $qoute_log->agency_contact_no =  $qoute->agency_contact_no;
            $qoute_log->currency          =  $qoute->currency;
            $qoute_log->convert_currency  =  $qoute->convert_currency;
            $qoute_log->group_no          =  $qoute->group_no;
            $qoute_log->net_price         =  $qoute->net_price;
            $qoute_log->markup_amount     =  $qoute->markup_amount;
            $qoute_log->selling           =  $qoute->selling;
            $qoute_log->gross_profit      =  $qoute->gross_profit;
            $qoute_log->markup_percent    =  $qoute->markup_percent;
            $qoute_log->show_convert_currency =  $qoute->show_convert_currency;
            $qoute_log->per_person        =  $qoute->per_person;
            $qoute_log->created_date      =  date("Y-m-d");
            $qoute_log->log_no            =  $qouteDetailLogNumber;
            $qoute_log->user_id           =  Auth::user()->id;
            $qoute_log->save();


            $qoute->ref_no           =  $request->ref_no;
            $qoute->quotation_no     =  $request->quotation_no;
            $qoute->reference_name    =  $request->reference;
            $qoute->dinning_preferences     = $request->dinning_preferences;
            $qoute->lead_passenger_name =  $request->lead_passenger_name;
            $qoute->brand_name       =  $request->brand_name;
            $qoute->type_of_holidays =  $request->type_of_holidays;
            $qoute->sale_person      =  $request->sale_person;
            $qoute->season_id        =  $request->season_id;
            $qoute->agency_booking   =  $request->agency_booking;
            $qoute->agency_name       =  $request->agency_name;
            $qoute->agency_contact_no =  $request->agency_contact_no;
            $qoute->currency          =  $request->currency;
            $qoute->convert_currency  =  $request->convert_currency;
            $qoute->group_no          =  $request->group_no;
            $qoute->net_price         =  $request->net_price;
            $qoute->markup_amount     =  $request->markup_amount;
            $qoute->selling           =  $request->selling;
            $qoute->gross_profit      =  $request->gross_profit;
            $qoute->markup_percent    =  $request->markup_percent;
            $qoute->show_convert_currency =  $request->show_convert_currency;
            $qoute->per_person       =  $request->per_person;
            $qoute->save();


            $qouteDetails = QouteDetail::where('qoute_id', $id)->get();

            $qouteDetailLog = new QouteDetailLog;

            foreach($qouteDetails as $key => $qouteDetail){

                $QouteDetailLog = new QouteDetailLog;
                $QouteDetailLog->qoute_id          = $qouteDetail->qoute_id;
                $QouteDetailLog->date_of_service   = $qouteDetail->date_of_service;
                $QouteDetailLog->service_details   =  $qouteDetail->service_details;
                $QouteDetailLog->category_id       =  $qouteDetail->category_id;
                $QouteDetailLog->product           = $qouteDetail->product;
                $QouteDetailLog->supplier          =  $qouteDetail->supplier;
                $QouteDetailLog->booking_date      =  $qouteDetail->booking_date;
                $QouteDetailLog->booking_due_date  =  $qouteDetail->booking_due_date;
                $QouteDetailLog->booking_method    =  $qouteDetail->booking_method;
                $QouteDetailLog->booked_by         =  $qouteDetail->booked_by;
                $QouteDetailLog->booking_refrence  =  $qouteDetail->booking_refrence;
                $QouteDetailLog->booking_type         =  $qouteDetail->booking_type;
                $QouteDetailLog->comments          =  $qouteDetail->comments;
                $QouteDetailLog->supplier_currency =  $qouteDetail->supplier_currency;
                $QouteDetailLog->cost              =  $qouteDetail->cost;
                $QouteDetailLog->supervisor_id     =  $qouteDetail->supervisor_id;
                $QouteDetailLog->added_in_sage     =  $qouteDetail->added_in_sage;
                $QouteDetailLog->qoute_base_currency =  $qouteDetail->qoute_base_currency;
                $QouteDetailLog->log_no = $qouteDetailLogNumber;
                $QouteDetailLog->save();
            }

            // Delete old qoute
            QouteDetail::where('qoute_id',$id)->delete();

            if(!empty($request->cost)){
                foreach($request->cost as $key => $cost){
                    $qouteDetail = new QouteDetail;
                    $qouteDetail->qoute_id = $qoute->id;
                    $qouteDetail->date_of_service   = $request->date_of_service[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->service_details   = $request->service_details[$key];
                    $qouteDetail->category_id       = $request->category[$key];
                    $qouteDetail->supplier          = $request->supplier[$key];
                    $qouteDetail->product           = $request->product[$key];
                    $qouteDetail->booking_date      = $request->booking_date[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d'))) : null;
                    $qouteDetail->booking_due_date  = $request->booking_due_date[$key] ? date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d'))): null;
                    $qouteDetail->booking_method    = $request->booking_method[$key];
                    $qouteDetail->booked_by         = $request->booked_by[$key];
                    $qouteDetail->booking_refrence  = $request->booking_refrence[$key];
                    $qouteDetail->booking_type      = $request->booking_type[$key];
                    $qouteDetail->comments          = $request->comments[$key];
                    $qouteDetail->supplier_currency = $request->supplier_currency[$key];
                    $qouteDetail->cost              = $request->cost[$key];
                    $qouteDetail->supervisor_id     = $request->supervisor[$key];
                    $qouteDetail->added_in_sage     = $request->added_in_sage[$key];
                    $qouteDetail->qoute_base_currency     = $request->qoute_base_currency[$key];

                    // if(!is_null($request->qoute_invoice)){

                    //     if(array_key_exists($key,$request->qoute_invoice))
                    //     {

                    //         $file = $request->qoute_invoice[$key];


                    //         $folder = public_path('quote/' . $qoute->id );
                    //         $filename = $file->getClientOriginalName();


                    //         if (!File::exists($folder)) {
                    //             File::makeDirectory($folder, 0775, true, true);
                    //         }

                    //         $destinationPath = public_path('quote/'. $id .'/'.  $filename  );
                    //         File::delete($destinationPath);

                    //         $file->move(public_path('quote/' . $qoute->id ), $filename);

                    //         $qouteDetail->qoute_invoice  = $filename ? $filename : null;

                    //     }
                    //     else{
                    //         $qouteDetail->qoute_invoice = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
                    //     }
                    // }else{

                    //     $qouteDetail->qoute_invoice = isset($request->qoute_invoice_record[$key])  ? $request->qoute_invoice_record[$key] : null;
                    // }

                    $qouteDetail->save();

                }
            }

            return response()->json(['success_message'=>'Quote Successfully Updated!!']);
        }

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.edit')->with([
            'quote'             => Qoute::find($id),
            'quote_details'     => QouteDetail::where('qoute_id',$id)->orderBy('date_of_service', 'ASC')->get(),
            'get_user_branches' => $get_user_branches,
            'get_holiday_type'  => $get_holiday_type,
            'categories'        => Category::all()->sortBy('name'),
            'products'          => Product::all()->sortBy('name'),
            // 'seasons' => season::where('default_season',1)->first(),
            'seasons'           => season::all(),
            'users'             => User::all()->sortBy('name'),
            'supervisors'       => User::where('role_id',5)->orderBy('name','ASC')->get(),
            'suppliers'         => Supplier::all()->sortBy('name'),
            'booking_methods'   => BookingMethod::all()->sortBy('id'),
            'currencies'        => Currency::all()->sortBy('name'),
            'qoute_logs'        => QouteLog::where('qoute_id',$id)->orderBy('log_no', 'DESC')->get(),
        ]);
    }


    public function view_version($quote_id, $log_no){

        $qoute_log = QouteLog::where('qoute_id',$quote_id)
        ->where('log_no',$log_no)
        ->first();



        $qoute_detail_logs = QouteDetailLog::where('qoute_id',$quote_id)
        ->where('log_no',$log_no)
        ->get();



        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.view-version')->with([
            'qoute_log'         => $qoute_log,
            'qoute_detail_logs' => $qoute_detail_logs,
            'seasons'           =>  season::all(),
            'currencies'        => Currency::all()->sortBy('name'),
            'categories'       => Category::all()->sortBy('name'),
            'suppliers'        => Supplier::all()->sortBy('name'),
            'products'         => Product::all()->sortBy('name'),
            'booking_methods'  => BookingMethod::all()->sortBy('id'),
            'users'            => User::all()->sortBy('name'),
            'supervisors'      => User::where('role_id',5)->orderBy('name','ASC')->get(),
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type
        ]);

    }

    public function recall_version($quote_id, $log_no){

        $qoute_log = QouteLog::where('qoute_id',$quote_id)
        ->where('log_no',$log_no)
        ->first();

        $qoute_detail_logs = QouteDetailLog::where('qoute_id',$quote_id)
        ->where('log_no',$log_no)
        ->get();

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.recall-version')->with([
            'quote'             => $qoute_log,
            'quote_details'     => $qoute_detail_logs,
            'get_user_branches' => $get_user_branches,
            'get_holiday_type'  => $get_holiday_type,
            'categories'        => Category::all()->sortBy('name'),
            'products'          => Product::all()->sortBy('name'),
            // 'seasons' => season::where('default_season',1)->first(),
            'seasons'           => season::all(),
            'users'             => User::all()->sortBy('name'),
            'supervisors'       => User::where('role_id',5)->orderBy('name','ASC')->get(),
            'suppliers'         => Supplier::all()->sortBy('name'),
            'booking_methods'   => BookingMethod::all()->sortBy('id'),
            'currencies'        => Currency::all()->sortBy('name'),
            'qoute_logs'        => QouteLog::where('qoute_id',$quote_id)->orderBy('log_no', 'DESC')->get(),
        ]);

    }


    public function get_log_no($table,$qoute_id)  {

        $modelName = "App\\$table";
        $qoute_log =  $modelName::where('qoute_id',$qoute_id)->orderBy('created_at','DESC')->first();

        if(is_null($qoute_log)){
            return 0;
        }else{
            return $qoute_log->log_no;
        }

    }

    public function increment_log_no($number)  {
        return  $number =  $number + 1;
    }

    // public function view_code()
    // {
    //     return view('code.view-code')->with(['codes' => code::all()]);
    // }

    public function booking_method(Request $request){

        if($request->isMethod('post')){

            $this->validate($request, ['booking_method_name'  => 'required'], ['required' => 'Booking Method is required']);

            $booking_method = new BookingMethod;
            $booking_method->name = $request->booking_method_name;
            $booking_method->save();

            return Redirect::route('view-booking-method')->with('success_message', 'Created Successfully');
        }

        return view('booking_method.create');
    }


    public function view_booking_method(){

        $booking_methods = BookingMethod::all();
        return view('booking_method.view')->with('booking_methods' , $booking_methods);
    }

    public function edit_booking_method(Request $request,$id){

        if($request->isMethod('post')){

            $this->validate($request, ['booking_method_name'  => 'required'], ['required' => 'Booking Method is required']);

            $booking_method = BookingMethod::find($id);
            $booking_method->name = $request->booking_method_name;
            $booking_method->save();

            return Redirect::route('view-booking-method')->with('success_message', 'Successfully Updated!!');
        }

        $booking_method = BookingMethod::find($id);
        return view('booking_method.edit')->with('booking_method' , $booking_method);
    }

    public function del_booking_method(Request $request,$id){

        BookingMethod::destroy('id', '=', $id);
        return Redirect::route('view-booking-method')->with('success_message', 'Delete Successfully');
    }

    public function update_code(Request $request,$id){

        // dd($request->all());

        $this->validate($request, ['ref_no'         => 'required'], ['required' => 'Reference number is required']);
        $this->validate($request, ['brand_name'                 => 'required'], ['required' => 'Please select Brand Name']);
        $this->validate($request, ['type_of_holidays'                 => 'required'], ['required' => 'Please select Type Of Holidays']);
        $this->validate($request, ['sale_person'                 => 'required'], ['required' => 'Please select Sale Person']);
        $this->validate($request, ['category'                 => 'required'], ['required' => 'Please select Category']);
        $this->validate($request, ['product'                 => 'required'], ['required' => 'Please select Product']);
        $this->validate($request, ['season_id'                  => 'required|numeric'], ['required' => 'Please select Booking Season']);
        $this->validate($request, ['agency_booking'             => 'required'], ['required' => 'Please select Agency']);
        $this->validate($request, ['pax_no'                     => 'required'], ['required' => 'Please select PAX No']);
        $this->validate($request, ['date_of_travel'             => 'required'], ['required' => 'Please select date of travel']);

        $this->validate($request, ['supplier'                 => 'required'], ['required' => 'Please select Supplier']);


        // $this->validate($request, ['flight_booked'              => 'required'], ['required' => 'Please select flight booked']);

        // $this->validate($request, ['fb_airline_name_id'         => 'required_if:flight_booked,yes'], ['required_if' => 'Please select flight airline name']);

        // $this->validate($request, ['fb_payment_method_id'       => 'required_if:flight_booked,yes'], ['required_if' => 'Please select payment method']);

        // $this->validate($request, ['fb_booking_date'            => 'required_if:flight_booked,yes'], ['required_if' => 'Please select booking date']);

        // $this->validate($request, ['fb_airline_ref_no'          => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter airline reference number']);

        // $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter flight booking details']);
        // //
        // // $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'],['required_if' => 'Please select booked person']);
        // $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'], ['required_if' => 'Plesse enter flight booking date']);

        // // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
        // $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'], ['required_if' => 'Plesse enter transfer date']);
        // // $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'],['required_if' => 'Please select document person']);
        // $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'], ['required_if' => 'Plesse enter document sent date']);
        // // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
        // $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'], ['required_if' => 'Plesse enter document sent date']);
        // //
        // // $this->validate($request, ['asked_for_transfer_details' => 'required'], ['required' => 'Please select asked for transfer detail box']);
        // $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'], ['required_if' => 'Please transfer detail']);
        // $this->validate($request, ['form_sent_on'               => 'required'], ['required' => 'Please select form sent on']);


        // // $this->validate($request, ['transfer_info_received'     => 'required'],['required' => 'Please select transfer info received']);
        // // $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'],['required_if' => 'Please transfer info detail']);

        // $this->validate($request, ['itinerary_finalised'        => 'required'], ['required' => 'Please select itinerary finalised']);
        // $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Please enter itinerary finalised details']);



        // // $this->validate($request, ['itf_person'                => 'required_if:itinerary_finalised,no'],['required_if' => 'Please select itinerary person']);
        // $this->validate($request, ['itf_last_date'              => 'required_if:itinerary_finalised,no'], ['required_if' => 'Plesse enter itinerary sent date']);

        // $this->validate($request, ['documents_sent'             => 'required'], ['required' => 'Please select documents sent']);
        // $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'], ['required_if' => 'Please enter document sent details']);

        // $this->validate($request, ['electronic_copy_sent'       => 'required'], ['required' => 'Please select electronic copy sent']);
        // $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Please enter electronic copy details']);

        // $this->validate($request, ['transfer_organised'         => 'required'], ['required' => 'Please select transfer organised']);
        // $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Please enter transfer organised details']);
        // $this->validate($request, ['type_of_holidays'           => 'required'], ['required' => 'Please select type of holidays']);
        // $this->validate($request, ['sale_person'                => 'required'], ['required' => 'Please select type of sale person']);
        // $this->validate($request, ['tdp_current_date'              => 'required_if:document_prepare,yes'], ['required_if' => 'Plesse enter Travel Document Prepared Date']);


        if ($request->form_received_on == '0000-00-00') {
            $form_received_on = NULL;
        } else {
            $form_received_on = $request->form_received_on;
        }
        //
        if ($request->app_login_date == '0000-00-00') {
            $app_login_date = NULL;
        } else {
            $app_login_date = $request->app_login_date;
}


        $product = code::where('id', $id)->update(array(
            'ref_no' => $request->ref_no,
            'brand_name'                  => $request->brand_name,
            'season_id'                   => $request->season_id,
            'agency_booking'              => $request->agency_booking,
            'pax_no'                      => $request->pax_no,
            'date_of_travel'              => Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d'),
            'category'                    => $request->category,
            'supplier'                    => $request->supplier,
            'product'                     => $request->product,
            'flight_booked'               => $request->flight_booked,
            'fb_airline_name_id'          => $request->fb_airline_name_id,
            'fb_payment_method_id'        => $request->fb_payment_method_id,
            'fb_booking_date'             => Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d'),
            'fb_airline_ref_no'           => $request->fb_airline_ref_no,
            'fb_last_date'                => Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d'),
            'fb_person'                   => $request->fb_person,
            //
            'aft_last_date'                => Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d'),
            'aft_person'                   => $request->aft_person,
            'ds_last_date'                 => Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d'),
            'ds_person'                    => $request->ds_person,
            'to_last_date'                 => Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d'),
            'to_person'                    => $request->to_person,
            //
            'document_prepare'             => $request->document_prepare,
            'dp_last_date'                 => Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d'),
            'dp_person'                    => $request->dp_person,
            //
            //
            'flight_booking_details'      => $request->flight_booking_details,
            'asked_for_transfer_details'  => $request->asked_for_transfer_details,
            'transfer_details'            => $request->transfer_details,
            'form_sent_on'                => Carbon::parse(str_replace('/', '-', $request->form_sent_on))->format('Y-m-d'),
            'form_received_on'            => $form_received_on,
            'app_login_date'              => $app_login_date,
            // 'transfer_info_received'      => $request->transfer_info_received,
            // 'transfer_info_details'       => $request->transfer_info_details,
            'itinerary_finalised'         => $request->itinerary_finalised,
            'itinerary_finalised_details' => $request->itinerary_finalised_details,
            'itf_last_date'               => Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d'),
            'itf_person'                  => $request->itf_person,
            'documents_sent'              => $request->documents_sent,
            'documents_sent_details'      => $request->documents_sent_details,
            'electronic_copy_sent'        => $request->electronic_copy_sent,
            'electronic_copy_details'     => $request->electronic_copy_details,
            'transfer_organised'          => $request->transfer_organised,
            'transfer_organised_details'  => $request->transfer_organised_details,
            'type_of_holidays'            => $request->type_of_holidays,
            'sale_person'                 => $request->sale_person,
            'deposit_received'            => $request->deposit_received == '' ? 0 : $request->deposit_received,
            'deposit_received'            => isset($request->deposit_received) ? $request->deposit_received : 0 ,
            // 'remaining_amount_received'   => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
            'remaining_amount_received'   => isset($request->remaining_amount_received) ? $request->remaining_amount_received : 0 ,
            'fso_person'                  => $request->fso_person,
            'fso_last_date'               => Carbon::parse(str_replace('/', '-', $request->fso_last_date))->format('Y-m-d'),
            'aps_person'                  => $request->aps_person,
            'aps_last_date'               => Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d'),
            'finance_detail'              => $request->finance_detail,
            'destination'                 => $request->destination,
            'user_id'                     => Auth::user()->id,
            'itf_current_date'            => Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d'),
            'tdp_current_date'            => Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d'),
            'tds_current_date'            => Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d'),
        ));



        // $code = code::find($id);
        // $code->ref_no =  $request->ref_no;
        // $code->brand_name =  $request->brand_name;
        // $code->season_id         = $request->season_id;
        // $code->agency_booking    = $request->agency_booking;
        // $code->pax_no            = $request->pax_no;
        // $code->date_of_travel    = Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d');
        // $code->category             = $request->category;
        // $code->supplier             = $request->supplier;
        // $code->product              = $request->product;
        // $code->flight_booked        = $request->flight_booked;
        // $code->fb_airline_name_id   = $request->fb_airline_name_id;
        // $code->fb_payment_method_id = $request->fb_payment_method_id;
        // $code->fb_booking_date      = Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d');
        // $code->fb_airline_ref_no    = $request->fb_airline_ref_no;
        // $code->fb_last_date         = Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d');
        // $code->fb_person            = $request->fb_person;
        // $code->aft_last_date        = Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d');
        // $code->aft_person          = $request->aft_person;
        // $code->ds_last_date        = Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d');
        // $code->ds_person           = $request->ds_person;
        // $code->to_last_date        = Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d');
        // $code->to_person           = $request->to_person;
        // $code->document_prepare    = $request->document_prepare;
        // $code->dp_last_date        = Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d');
        // $code->dp_person           = $request->dp_person;
        // $code->save();


        // $booking_id = code::update([
        //     'ref_no'                      => $request->ref_no,
        //     'brand_name'                  => $request->brand_name,
        //     'season_id'                   => $request->season_id,
        //     'agency_booking'              => $request->agency_booking,
        //     'pax_no'                      => $request->pax_no,
        //     'date_of_travel'              => Carbon::parse(str_replace('/', '-', $request->date_of_travel))->format('Y-m-d'),
        //     'category'                    => $request->category,
        //     'supplier'                    => $request->supplier,
        //     'product'                     => $request->product,
        //     'flight_booked'               => $request->flight_booked,
        //     'fb_airline_name_id'          => $request->fb_airline_name_id,
        //     'fb_payment_method_id'        => $request->fb_payment_method_id,
        //     'fb_booking_date'             => Carbon::parse(str_replace('/', '-', $request->fb_booking_date))->format('Y-m-d'),
        //     'fb_airline_ref_no'           => $request->fb_airline_ref_no,
        //     'fb_last_date'                => Carbon::parse(str_replace('/', '-', $request->fb_last_date))->format('Y-m-d'),
        //     'fb_person'                   => $request->fb_person,
        //     //
        //     'aft_last_date'                => Carbon::parse(str_replace('/', '-', $request->aft_last_date))->format('Y-m-d'),
        //     'aft_person'                   => $request->aft_person,
        //     'ds_last_date'                 => Carbon::parse(str_replace('/', '-', $request->ds_last_date))->format('Y-m-d'),
        //     'ds_person'                    => $request->ds_person,
        //     'to_last_date'                 => Carbon::parse(str_replace('/', '-', $request->to_last_date))->format('Y-m-d'),
        //     'to_person'                    => $request->to_person,
        //     //
        //     'document_prepare'             => $request->document_prepare,
        //     'dp_last_date'                 => Carbon::parse(str_replace('/', '-', $request->dp_last_date))->format('Y-m-d'),
        //     'dp_person'                    => $request->dp_person,
        //     //
        //     //
        //     'flight_booking_details'      => $request->flight_booking_details,
        //     'asked_for_transfer_details'  => $request->asked_for_transfer_details,
        //     'transfer_details'            => $request->transfer_details,
        //     'form_sent_on'                => Carbon::parse(str_replace('/', '-', $request->form_sent_on))->format('Y-m-d'),
        //     'form_received_on'            => $form_received_on,
        //     'app_login_date'              => $app_login_date,
        //     // 'transfer_info_received'      => $request->transfer_info_received,
        //     // 'transfer_info_details'       => $request->transfer_info_details,
        //     'itinerary_finalised'         => $request->itinerary_finalised,
        //     'itinerary_finalised_details' => $request->itinerary_finalised_details,
        //     'itf_last_date'               => Carbon::parse(str_replace('/', '-', $request->itf_last_date))->format('Y-m-d'),
        //     'itf_person'                  => $request->itf_person,
        //     'documents_sent'              => $request->documents_sent,
        //     'documents_sent_details'      => $request->documents_sent_details,
        //     'electronic_copy_sent'        => $request->electronic_copy_sent,
        //     'electronic_copy_details'     => $request->electronic_copy_details,
        //     'transfer_organised'          => $request->transfer_organised,
        //     'transfer_organised_details'  => $request->transfer_organised_details,
        //     'type_of_holidays'            => $request->type_of_holidays,
        //     'sale_person'                 => $request->sale_person,
        //     'deposit_received'            => $request->deposit_received == '' ? 0 : $request->deposit_received,
        //     'remaining_amount_received'   => $request->remaining_amount_received == '' ? 0 : $request->remaining_amount_received,
        //     'fso_person'                  => $request->fso_person,
        //     'fso_last_date'               => Carbon::parse(str_replace('/', '-', $request->fso_last_date))->format('Y-m-d'),
        //     'aps_person'                  => $request->aps_person,
        //     'aps_last_date'               => Carbon::parse(str_replace('/', '-', $request->aps_last_date))->format('Y-m-d'),
        //     'finance_detail'              => $request->finance_detail,
        //     'destination'                 => $request->destination,
        //     'user_id'                     => Auth::user()->id,
        //     'itf_current_date'            => Carbon::parse(str_replace('/', '-', $request->itf_current_date))->format('Y-m-d'),
        //     'tdp_current_date'            => Carbon::parse(str_replace('/', '-', $request->tdp_current_date))->format('Y-m-d'),
        //     'tds_current_date'            => Carbon::parse(str_replace('/', '-', $request->tds_current_date))->format('Y-m-d'),

        // ])->where('id',$id);

        return Redirect::route('view-code')->with('success_message', 'Code Successfully Updated!!');

    }

    public function edit_code(Request $request,$id){

        $code = code::find($id);

        $get_user_branches = Cache::remember('get_user_branches', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_ref = Cache::remember('get_ref', $this->cacheTimeOut, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_ref';
            $output =  $this->curl_data($url);
            //   return json_decode($output)->data;
        });

        // return view('code.create-code')->with(['get_holiday_type' => $get_holiday_type, 'seasons' => season::all(), 'persons' => user::all(), 'get_refs' => $get_ref, 'get_user_branches' => $get_user_branches, 'booking_email' => $booking_email, 'payment' => payment::all(), 'airline' => airline::all(), 'categories' => Category::all(), 'products' => Product::all(),'suppliers' => Supplier::all()]);
        $booking_email = booking_email::where('booking_id', '=', 1)->get();

        return view('code.edit-code')->with([ 'code' => $code, 'get_user_branches' => $get_user_branches,  'get_holiday_type' => $get_holiday_type, 'get_user_branches' => $get_user_branches, 'codes' => $code, 'seasons' => season::all(), 'persons' => user::all(), 'payment' => payment::all(), 'airline' => airline::all(), 'categories' => Category::all(), 'products' => Product::all(),'suppliers' => Supplier::all(), 'booking_email' => $booking_email,  ]);
    }

    public function get_supplier(Request $request){

        $supplier_category = supplier_category::where('category_id',$request->category_id)
        ->select('suppliers.id','suppliers.name')
        ->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_categories.supplier_id')
        ->get();

        return $supplier_category;
    }

    public function get_product_details(Request $request){

        $product = Product::find($request->product_id);
        return $product;

        // $supplier_category = supplier_category::where('category_id',$request->category_id)
        // ->select('suppliers.id','suppliers.name')
        // ->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_categories.supplier_id')
        // ->get();
        // return $supplier_category;
    }


    public function get_supplier_currency(Request $request){

        $supplier_currency = Supplier::leftJoin('currencies', 'currencies.id', '=', 'suppliers.currency_id')->where('suppliers.id', $request->supplier_id)->first();
 
        $supplier_products = Supplier::find($request->supplier_id)->products;

        return array('supplier_currency' => $supplier_currency, 'supplier_products' => $supplier_products);
    }

    public function get_saleagent_supervisor(Request $request){

        $saleagent_supervisor = User::where('id',$request->booked_by)->first();
        return $saleagent_supervisor;
    }

    public function get_currency(Request $request){

        $test = CurrencyConversions::where('to',$request->to)->get(['from','value']);

        $arr = [];
        foreach($test as $test){
            $arr[$test->from] = $test->value;
        }

        return  $arr;
    }

    public function delete_code(Request $request,$id){
        code::destroy($id);
        return Redirect::route('view-code')->with('success_message', 'Code Successfully Deleted!!');
    }
}
