<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Request as Routerequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Requests;
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
use File;
use Image;
use Response;

use App\Mail\DueDateMail;
use Illuminate\Support\Facades\Mail;

use Validator;
use Redirect;
use DB;
use Cache;
use Input;
use Hash;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect as FacadesRedirect;

use Spatie\GoogleCalendar\Event;

 


class AdminController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
        /*Session::set('curr_route', explode('/', Route::getFacadeRoot()->current()->uri())[0]);
      echo Session::get('id');die();
      if(explode('/', Route::getFacadeRoot()->current()->uri())[0] != 'logout' ){
        if(!$request->isMethod('post')){
          Redirect::to('permission')->send();
        }
      }*/
        // return Redirect::route('user-permission')->with('curr_route',explode('/', Route::getFacadeRoot()->current()->uri())[0]);
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
            $this->validate($request, ['username'  => 'required']);
            $this->validate($request, ['email'     => 'required|email|unique:users']);
            $this->validate($request, ['role'      => 'required']);
            $this->validate($request, ['password'  => 'required']);
            $request->role = (int)$request->role;
            user::create(array(
                'name'          => $request->username,
                'role'          => $request->role,
                'email'         => $request->email,
                'supervisor_id' => $request->supervisor,
                'password'      =>  bcrypt($request->password),
            ));
            return Redirect::route('creat-user')->with('success_message', 'Created Successfully');
        } else {
            return view('user.create_user')->with(['name' => '', 'id' => '', 'roles' => role::all(), 'supervisors' => User::where('role',5)->orderBy('name','ASC')->get() ]);
        }
    }
    public function view_user(Request $request)
    {

        $data = user::leftjoin('roles', 'roles.id', '=', 'users.role')->get(['users.*', 'roles.name as role']);

        return view('user.view_user')->with('data', $data);
    }
    public function update_user(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['username'  => 'required']);
            if (user::select('email')->where('id', $id)->get()->first()->email != $request->email) {
                $this->validate($request, ['email'     => 'required|email|unique:users']);
            }

            if ($request->password != '') {
                user::where('id', '=', $request->id)->update(
                    array(
                        'name'          => $request->username,
                        'role'          => (int)$request->role,
                        'email'         => $request->email,
                        'supervisor_id' => $request->supervisor,
                        'password'      => bcrypt($request->password)
                    )
                );
            } else {
                user::where('id', '=', $request->id)->update(
                    array(
                        'name'          => $request->username,
                        'role'          => (int)$request->role,
                        'email'         => $request->email,
                        'supervisor_id' => $request->supervisor,
                    )
                );
            }
            return Redirect::route('view-user')->with('success_message', 'Update Successfully');
        } else {
            return view('user.update_user')->with(['data' => user::find($id), 'id' => $id, 'roles' => role::all(), 'supervisors' => User::where('role',5)->orderBy('name','ASC')->get(),]);
        }
    }
    public function delete_user($id)
    {
        if (booking::where('user_id', $id)->count() == 1) {
            return Redirect::route('view-user')->with('error_message', 'You can not delete this user because user already in use');
        }
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

            $get_ref = Cache::remember('get_ref', 60, function () {
                $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_ref';
                $output =  $this->curl_data($url);
                //   return json_decode($output)->data;
            });

            $get_user_branches = Cache::remember('get_user_branches', 60, function () {
                $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });

            $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
                $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });

            $booking_email = booking_email::where('booking_id', '=', 1)->get();
            return view('booking.create_booking')->with(['get_holiday_type' => $get_holiday_type, 'seasons' => season::all(), 'persons' => user::all(), 'get_refs' => $get_ref, 'get_user_branches' => $get_user_branches, 'booking_email' => $booking_email, 'payment' => payment::all(), 'airline' => airline::all()]);
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
        $staff = Cache::remember('staff', 1140, function () {
            return User::orderBy('id', 'DESC')->get();
        });
        //
        $get_ref = Cache::remember('get_ref', 60, function () {
            $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
            $output =  $this->curl_data($url);
            return json_decode($output)->data;
        });
        //
        $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });
        //
        $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
            $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });
        $query = booking::join('seasons', 'seasons.id', '=', 'bookings.season_id')
            ->join('users', 'users.id', '=', 'bookings.user_id')
            ->leftjoin('users as user_fb', 'user_fb.id', '=', 'bookings.fb_person')
            ->leftjoin('users as user_ti', 'user_ti.id', '=', 'bookings.aft_person')
            ->leftjoin('users as user_to', 'user_to.id', '=', 'bookings.to_person')
            ->leftjoin('users as user_itf', 'user_itf.id', '=', 'bookings.itf_person')
            ->leftjoin('users as user_tdp', 'user_tdp.id', '=', 'bookings.dp_person')
            ->leftjoin('users as user_ds', 'user_ds.id', '=', 'bookings.ds_person')
            ->leftjoin('airlines', 'airlines.id', '=', 'bookings.fb_airline_name_id')
            ->leftjoin('payments', 'payments.id', '=', 'bookings.fb_payment_method_id')->where('bookings.season_id', '=', $id);

        if ($request->created_at != '') {
            $date  = explode('-', $request->created_at);
            $start_date = $date[0];
            $end_date   = $date[1];

            $start_created_at = Carbon::parse($start_date)->format('Y-m-d');
            $end_created_at   = Carbon::parse($end_date)->format('Y-m-d');
            $query =  $query->whereRaw('DATE(bookings.created_at) >= ?', $start_created_at);
            $query =  $query->whereRaw('DATE(bookings.created_at) <= ?', $end_created_at);
        }
        if ($request->created_by != '') {
            $query =  $query->where('bookings.user_id', '=', $request->created_by);
        }
        if ($request->ref_no != '') {
            $query =  $query->where('bookings.ref_no', '=', $request->ref_no);
        }
        if ($request->date_of_travel != '') {
            $date  = explode('-', $request->date_of_travel);
            $start_date = $date[0];
            $end_date   = $date[1];

            $query =  $query->where('bookings.date_of_travel', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            $query =  $query->where('bookings.date_of_travel', '<=', Carbon::parse($end_date)->format('Y-m-d'));
        }
        if ($request->brand_name != '') {
            $query =  $query->where('bookings.brand_name', '=', $request->brand_name);
        }
        if ($request->season_id != '') {
            $query =  $query->where('bookings.season_id', '=', $request->season_id);
        }
        if ($request->agency_booking != '') {
            $query =  $query->where('bookings.agency_booking', '=', $request->agency_booking);
        }
        if ($request->flight_booked != '') {
            $query =  $query->where('bookings.flight_booked', '=', $request->flight_booked);
        }
        if ($request->form_sent_on != '') {
            $date  = explode('-', $request->form_sent_on);
            $start_date = $date[0];
            $end_date   = $date[1];
            $query =  $query->where('bookings.form_sent_on', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            $query =  $query->where('bookings.form_sent_on', '<=', Carbon::parse($end_date)->format('Y-m-d'));
        }
        if ($request->type_of_holidays != '') {
            $query =  $query->where('bookings.type_of_holidays', '=', $request->type_of_holidays);
        }
        if ($request->fb_payment_method_id != '') {
            $query =  $query->where('bookings.fb_payment_method_id', '=', $request->fb_payment_method_id);
        }
        if ($request->fb_airline_name_id != '') {
            $query =  $query->where('bookings.fb_airline_name_id', '=', $request->fb_airline_name_id);
        }
        if ($request->fb_responsible_person != '') {
            $query =  $query->where('bookings.fb_person', '=', $request->fb_responsible_person);
        }
        if ($request->ti_responsible_person != '') {
            $query =  $query->where('bookings.aft_person', '=', $request->ti_responsible_person);
        }
        if ($request->to_responsible_person != '') {
            $query =  $query->where('bookings.to_person', '=', $request->to_responsible_person);
        }
        if ($request->itf_responsible_person != '') {
            $query =  $query->where('bookings.itf_person', '=', $request->itf_responsible_person);
        }
        if ($request->dp_responsible_person != '') {
            $query =  $query->where('bookings.dp_person', '=', $request->dp_responsible_person);
        }
        if ($request->ds_responsible_person != '') {
            $query =  $query->where('bookings.ds_person', '=', $request->ds_responsible_person);
        }
        if ($request->pax_no != '') {
            $query =  $query->where('bookings.pax_no', '=', $request->pax_no);
        }
        if ($request->asked_for_transfer_details != '') {
            $query =  $query->where('bookings.asked_for_transfer_details', '=', $request->asked_for_transfer_details);
        }
        if ($request->transfer_organised != '') {
            $query =  $query->where('bookings.transfer_organised', '=', $request->transfer_organised);
        }
        if ($request->itinerary_finalised != '') {
            $query =  $query->where('bookings.itinerary_finalised', '=', $request->itinerary_finalised);
        }
        $query = $query->orderBy('bookings.created_at', 'desc')->paginate(10, ['bookings.*', 'airlines.name as airline_name', 'payments.name as payment_name', 'seasons.name', 'users.name as username', 'user_fb.name as fbusername', 'user_ti.name as tiusername', 'user_to.name as tousername', 'user_itf.name as itfusername', 'user_tdp.name as tdpusername', 'user_ds.name as dsusername'])->appends(Input::all());

        return view('booking.view_booking')->with([
            'data' => $query, 'book_id' => $id, 'staffs' => $staff, 'get_refs' => $get_ref, 'get_holiday_type' => $get_holiday_type, 'type_of_holidays' => $request->type_of_holidays,
            'get_user_branches' => $get_user_branches, 'created_at' => $request->created_at, 'created_by' => $request->created_by, 'ref_no' => $request->ref_no, 'date_of_travel' => $request->date_of_travel, 'brand_name' => $request->brand_name, 'seasons' => season::all(), 'session_id' => $request->season_id, 'agency_booking' => $request->agency_booking, 'flight_booked' => $request->flight_booked, 'form_sent_on' => $request->form_sent_on, 'payment' => payment::all(), 'airline' => airline::all(), 'fb_payment_method_id' => $request->fb_payment_method_id, 'fb_airline_name_id' => $request->fb_airline_name_id, 'fb_responsible_person' => $request->fb_responsible_person, 'ti_responsible_person' => $request->ti_responsible_person, 'to_responsible_person' => $request->to_responsible_person, 'itf_responsible_person' => $request->itf_responsible_person, 'dp_responsible_person' => $request->dp_responsible_person, 'ds_responsible_person' => $request->ds_responsible_person, 'pax_no' => $request->pax_no, 'asked_for_transfer_details' => $request->asked_for_transfer_details, 'transfer_organised' => $request->transfer_organised, 'itinerary_finalised' => $request->itinerary_finalised
        ]);
    }
    public function delete_booking($season_id, $booking_id)
    {
        booking::destroy('id', '=', $booking_id);
        return Redirect::route('view-booking', $season_id)->with('success_message', 'Deleted Successfully');
    }
    public function get_ref_detail(Request $request)
    {
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://localhost/unforgettable_payment/backend/api/payment/get_lead_info");
        curl_setopt($ch, CURLOPT_URL, "http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_lead_info");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('reference' => $request->input('id'))));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);

        //
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://localhost/unforgettable_payment/backend/api/payment/get_lead_by_reference");
        curl_setopt($ch, CURLOPT_URL, "http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_lead_by_reference");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('ref_no' => $request->input('id'))));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output2 = curl_exec($ch);
        curl_close($ch);
        //

        //
        $url = 'https://unforgettabletravelcompany.com/ufg-form/Admin/Login/LoginCon/get_user_detail/' . $request->input('id');
        $output =  $this->curl_data($url);
        //
        //
        $url2 = 'https://unforgettabletravelcompany.com/ufg-form/Admin/Login/LoginCon/app_login_detail/' . $request->input('id');
        $output2 =  $this->curl_data($url2);
        //
        if ($request->ajax()) {
            return response()->json([
                'item_rec'  => json_decode($server_output) == '' ? json_decode($server_output) : json_decode($server_output),
                'item_rec2' => json_decode($output),
                'item_rec3' => json_decode($output2),
                'item_rec4' => json_decode($server_output2)->data
            ]);
        }
    }
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
            $this->validate($request, ['ref_no'                     => 'required'], ['required' => 'Reference number is required']);
            $this->validate($request, ['brand_name'                 => 'required'], ['required' => 'Please select Brand Name']);
            $this->validate($request, ['season_id'                  => 'required|numeric'], ['required' => 'Please select Booking Season']);
            $this->validate($request, ['agency_booking'             => 'required'], ['required' => 'Please select Agency']);
            $this->validate($request, ['pax_no'                     => 'required'], ['required' => 'Please select PAX No']);
            $this->validate($request, ['date_of_travel'             => 'required'], ['required' => 'Please select date of travel']);
            $this->validate($request, ['flight_booked'              => 'required'], ['required' => 'Please select flight booked']);
            $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'], ['required_if' => 'Please enter flight booking details']);
            $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'], ['required_if' => 'Please select booked person']);
            $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'], ['required_if' => 'Plesse enter flight booking date']);
            //
            // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
            $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'], ['required_if' => 'Plesse enter transfer date']);
            $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'], ['required_if' => 'Please select document person']);
            $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'], ['required_if' => 'Plesse enter document sent date']);
            // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
            $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'], ['required_if' => 'Plesse enter document sent date']);
            // 
            $this->validate($request, ['asked_for_transfer_details' => 'required'], ['required' => 'Please select asked for transfer detail box']);
            $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'], ['required_if' => 'Please transfer detail']);
            $this->validate($request, ['form_sent_on'               => 'required'], ['required' => 'Please select form sent on']);
            $this->validate($request, ['transfer_info_received'     => 'required'], ['required' => 'Please select transfer info received']);
            $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'], ['required_if' => 'Please transfer info detail']);
            $this->validate($request, ['itinerary_finalised'        => 'required'], ['required' => 'Please select itinerary finalised']);
            $this->validate($request, ['itinerary_finalised_details' => 'required_if:itinerary_finalised,yes'], ['required_if' => 'Please enter itinerary finalised details']);

            $this->validate($request, ['documents_sent'             => 'required'], ['required' => 'Please select documents sent']);
            $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'], ['required_if' => 'Please enter document sent details']);

            $this->validate($request, ['electronic_copy_sent'       => 'required'], ['required' => 'Please select electronic copy sent']);
            $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'], ['required_if' => 'Please enter electronic copy details']);

            $this->validate($request, ['transfer_organised'         => 'required'], ['required' => 'Please select transfer organised']);
            $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'], ['required_if' => 'Please enter transfer organised details']);
            $this->validate($request, ['type_of_holidays'           => 'required'], ['required' => 'Please select type of holidays']);
            $this->validate($request, ['sale_person'                => 'required'], ['required' => 'Please select type of sale person']);

            if ($request->form_received_on == '0000-00-00') {
                $form_received_on = NULL;
            } elseif ($request->form_received_on == '') {
                $form_received_on = NULL;
            } else {
                $form_received_on = $request->form_received_on;
            }

            if ($request->app_login_date == '0000-00-00') {
                $app_login_date = NULL;
            } elseif ($request->app_login_date == '') {
                $app_login_date = NULL;
            } else {
                $app_login_date = $request->app_login_date;
            }

            booking::where('id', '=', $id)->update(array(
                'ref_no'                      => $request->ref_no,
                'brand_name'                  => $request->brand_name,
                'season_id'                   => $request->season_id,
                'agency_booking'              => $request->agency_booking,
                'pax_no'                      => $request->pax_no,
                'date_of_travel'              => Carbon::parse($request->date_of_travel)->format('Y-m-d'),
                'flight_booked'               => $request->flight_booked,
                'flight_booking_details'      => $request->flight_booking_details,
                'asked_for_transfer_details'  => $request->asked_for_transfer_details,
                'transfer_details'            => $request->transfer_details,
                'form_sent_on'                => Carbon::parse($request->form_sent_on)->format('Y-m-d'),
                'form_received_on'            => $form_received_on,
                'app_login_date'              => $app_login_date,
                'transfer_info_received'      => $request->transfer_info_received,
                'transfer_info_details'       => $request->transfer_info_details,
                'itinerary_finalised'         => $request->itinerary_finalised,
                'itinerary_finalised_details' => $request->itinerary_finalised_details,
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
                'finance_detail'              => $request->finance_detail,
                'destination'                 => $request->destination
            ));
            return Redirect::route('update-booking', $id)->with('success_message', 'Updated Successfully');
        } else {

            $get_ref = Cache::remember('get_ref', 60, function () {
                $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
                $output =  $this->curl_data($url);
                return json_decode($output)->data;
            });

            $get_user_branches = Cache::remember('get_user_branches', 60, function () {
                $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });
            $booking_email = booking_email::where('booking_id', '=', $id)->get();
            return view('booking.update_booking')->with(['booking_email' => $booking_email, 'persons' => user::all(), 'seasons' => season::all(), 'get_refs' => $get_ref, 'get_user_branches' => $get_user_branches, 'record' => booking::where('id', '=', $id)->get()->first(), 'id' => $id]);
        }
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
            $this->validate($request, ['name'  => 'required']);

            airline::create(array(
                'name'  => $request->name

            ));
            return Redirect::route('creat-airline')->with('success_message', 'Created Successfully');
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

            $validator = Validator::make($request->all(), [

                'name'  => 'required'
            ]);
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
        if (booking::where('fb_airline_name_id', $id)->count() >= 1) {
            return Redirect::route('view-airline')->with('error_message', 'You can not delete this record because season already in use');
        }
        airline::destroy('id', '=', $id);
        return Redirect::route('view-airline')->with('success_message', 'Deleted Successfully');
    }
    public function create_payment(Request $request)
    {

        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required']);

            payment::create(array(
                'name'  => $request->name,

            ));
            return Redirect::route('creat-payment')->with('success_message', 'Created Successfully');
        } else {
            return view('payment.create_payment')->with(['name' => '', 'id' => '', 'email' => '']);
        }
    }
    public function view_payment(Request $request)
    {
        return view('payment.view_payment')->with('data', payment::all());
    }

    public function update_payment(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [

                'name'  => 'required'
            ]);
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
        if (booking::where('fb_payment_method_id', $id)->count() >= 1) {
            return Redirect::route('view-payment')->with('error_message', 'You can not delete this record because season already in use');
        }
        payment::destroy('id', '=', $id);
        return Redirect::route('view-payment')->with('success_message', 'Deleted Successfully');
    }

    public function add_role(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required']);

            role::create(array(
                'name' => $request->name
            ));
            return Redirect::route('add-role')->with('success_message', 'Role Added Successfully');
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
        return Redirect::route('view-role')->with('success_message', 'Role Successfully Deleted!!');
    }

    public function update_role(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['name' => 'required']);

            role::where('id', '=', $id)->update(array(
                'name' => $request->name
            ));

            return Redirect::route('view-role')->with('success_message', 'Role Successfully Updated!!');
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

    public function add_supplier(Request $request)
    {
        if ($request->isMethod('post')) {

            // dd($request->all());

            $this->validate($request, ['username' => 'required'], ['required' => 'Name is required']);
            $this->validate($request, ['email' => 'required|unique:suppliers'], ['required' => 'Email is required']);
            $this->validate($request, ['phone' => 'required|unique:suppliers'], ['required' => 'Phone Number is required']);
            $this->validate($request, ['categories' => 'required'], ['required' => 'Category is required']);
            $this->validate($request, ['products' => 'required'], ['required' => 'Product is required']);
            $this->validate($request, ['currency' => 'required'], ['required' => 'Currency is required']);

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
        supplier::destroy('id', '=', $id);
        supplier_product::where('supplier_id', $id)->delete();
        supplier_category::where('supplier_id', $id)->delete();
        return Redirect::route('view-supplier')->with('success_message', 'Supplier Successfully Deleted!!');
    }

    public function update_supplier(Request $request, $id)
    {
        if($request->isMethod('post')) {

            $this->validate($request, ['username' => 'required'], ['required' => 'Name is required']);
            $this->validate($request, ['email' => 'required|email|unique:suppliers,email,'.$id], ['required' => 'Email is required']);
            $this->validate($request, ['phone' => 'required|unique:suppliers,phone,'.$id, ], ['required' => 'Phone Number is required']);
            $this->validate($request, ['categories' => 'required'], ['required' => 'Product is required']);
            $this->validate($request, ['products' => 'required'], ['required' => 'Currency is required']);
    
            $supplier = Supplier::find($id);
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
            $get_ref = Cache::remember('get_ref', 60, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_ref';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_ref';
                $output =  $this->curl_data($url);
                //   return json_decode($output)->data;
            });
    
            $get_user_branches = Cache::remember('get_user_branches', 60, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });
    
            $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
                $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
                // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
                $output =  $this->curl_data($url);
                return json_decode($output);
            });
    
            $booking_email = booking_email::where('booking_id', '=', 1)->get();
            return view('code.create-code')->with(['get_holiday_type' => $get_holiday_type, 'seasons' => season::all(), 'persons' => user::all(), 'get_refs' => $get_ref, 'get_user_branches' => $get_user_branches, 'booking_email' => $booking_email, 'payment' => payment::all(), 'airline' => airline::all(), 'categories' => Category::all(), 'products' => Product::all(),'suppliers' => Supplier::all()]);   
        }
    }



    public function create_quote(Request $request){

        if($request->isMethod('post')){

            // dd($request->all());

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
            $this->validate($request, [ "booking_due_date"    => "required|array", "booking_due_date.*"  => "required" ]);
            $this->validate($request, [ "cost"    => "required|array", "cost.*"  => "required"]);

            $season = season::find($request->season_id);

            if(!empty($request->date_of_service)){
                $error_array = [];
                foreach($request->date_of_service as $key => $date){
        
                    $start = date('Y-m-d', strtotime($season->start_date));
                    $end   = date('Y-m-d', strtotime($season->end_date));

                    if(!is_null($date)){
                        $date  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
                    }else{
                        $date  = null;
                    }

                    if(!is_null($date) && !is_null($start)  && !is_null($end)){
                        if( !(($date >= $start) && ($date <= $end)) ){
                            $error_array[$key+1] = "Date of service should be season date range.";
                        }
                    }
         
                }
            }

            if(!empty($error_array)){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'date_of_service' =>  (object) $error_array
                ]);
            }

            $booking_error = [];
            if(!empty($request->booking_date)){
                foreach($request->booking_date as $key => $date){

                    if(!is_null($date)){
                        $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
                    }else{
                        $date  = null;
                    }

                    if(!is_null($request->booking_due_date[$key])){
                        $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
                    }else{
                        $booking_due_date  = null;
                    }

                    if(!is_null($request->date_of_service[$key])){
                        $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
                    }else{
                        $date_of_service  = null;
                    }

                    if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
                        if( ($date > $booking_due_date ) ){
                            $booking_error[$key+1] = "Booking Date should be smaller than due date";
                        }
                    }

                    if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
                        if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
                            $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
                        }
                    }

                }
            }

            if(!empty($booking_error)){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'booking_date' => (object) $booking_error
                ]);
            }

            $qoute = new Qoute;
            $qoute->ref_no           =  $request->ref_no;
            $qoute->quotation_no     =  $request->quotation_no;
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
                    $qouteDetail->date_of_service   = $request->date_of_service[$key] ? Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d') : null;
                    $qouteDetail->service_details   = $request->service_details[$key];
                    $qouteDetail->category_id       = $request->category[$key];
                    $qouteDetail->supplier          = $request->supplier[$key];
                    $qouteDetail->booking_date      = $request->booking_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d') : null;
                    $qouteDetail->booking_due_date  = $request->booking_due_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d') : null;
                    $qouteDetail->booking_method    = $request->booking_method[$key];
                    $qouteDetail->booked_by         = $request->booked_by[$key];
                    $qouteDetail->booking_refrence  = $request->booking_refrence[$key];
                    $qouteDetail->comments          = $request->comments[$key];
                    $qouteDetail->supplier_currency = $request->supplier_currency[$key];
                    $qouteDetail->cost              = $request->cost[$key];
                    $qouteDetail->supervisor_id     = $request->supervisor[$key];
                    $qouteDetail->added_in_sage     = $request->added_in_sage[$key];
                    $qouteDetail->qoute_base_currency     = $request->qoute_base_currency[$key];
                    $qouteDetail->save();
                }
            }

            return response()->json(['success_message'=>'Quote Successfully Created!!']);
  
        }

        $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        
        return view('qoute.create')->with([
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
            'categories' => Category::all()->sortBy('name'),
            'seasons' => season::all(),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role',5)->orderBy('name','ASC')->get(),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('name'),
            'currencies' => Currency::all()->sortBy('name')
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
            $this->validate($request, [ "booking_due_date"    => "required|array", "booking_due_date.*"  => "required" ]);
            $this->validate($request, [ "cost"    => "required|array", "cost.*"  => "required"]);

            $season = season::find($request->season_id);
            
            if(!empty($request->date_of_service)){
                $error_array = [];
                foreach($request->date_of_service as $key => $date){
        
                    $start = date('Y-m-d', strtotime($season->start_date));
                    $end   = date('Y-m-d', strtotime($season->end_date));

                    if(!is_null($date)){
                        $date  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
                    }else{
                        $date  = null;
                    }

                    if(!is_null($date) && !is_null($start)  && !is_null($end)){
                        if( !(($date >= $start) && ($date <= $end)) ){
                            $error_array[$key+1] = "Date of service should be season date range.";
                        }
                    }
         
                }
            }

            if(!empty($error_array)){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'date_of_service' =>  (object) $error_array
                ]);
            }

            $booking_error = [];
            if(!empty($request->booking_date)){
                foreach($request->booking_date as $key => $date){

                    if(!is_null($date)){
                        $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
                    }else{
                        $date  = null;
                    }

                    if(!is_null($request->booking_due_date[$key])){
                        $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
                    }else{
                        $booking_due_date  = null;
                    }

                    if(!is_null($request->date_of_service[$key])){
                        $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
                    }else{
                        $date_of_service  = null;
                    }

                    if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
                        if( ($date > $booking_due_date ) ){
                            $booking_error[$key+1] = "Booking Date should be smaller than due date";
                        }
                    }

                    if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
                        if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
                            $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
                        }
                    }

                }
            }

            if(!empty($booking_error)){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'booking_date' => (object) $booking_error
                ]);
            }


            $booking = Booking::updateOrCreate(
                [ 'quotation_no' => $request->quotation_no ],

                [
                    'ref_no'           =>  $request->ref_no,
                    'qoute_id'          => $request->qoute_id,
                    'quotation_no'     =>  $request->quotation_no,
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

                        FinanceBookingDetail::updateOrCreate(
                            [ 
                                'booking_detail_id' => $bookingDetail->id,
                                'row' => $ikey+1,
                            ],
    
                            [
                                'deposit_amount'   =>  !empty($request->deposit_amount[$key][$ikey]) ? $request->deposit_amount[$key][$ikey] : null,
                                'deposit_due_date' =>  $request->deposit_due_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'paid_date'        =>  $request->paid_date[$key][$ikey] ? Carbon::parse(str_replace('/', '-', $request->deposit_due_date[$key][$ikey]))->format('Y-m-d') : null,
                                'booking_method'   =>  $request->booking_method[$key][$ikey] ? $request->booking_method[$key][$ikey] : null,
                            ]
    
                        );

                    }

                }
            }

            return response()->json(['success_message'=>' Changes Save Successfully ']);
        }

        $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
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
            'supervisors' => User::where('role',5)->orderBy('name','ASC')->get(),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('name'),
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

        $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.view')->with(['quotes' => Qoute::all()]);
    }

    public function upload_to_calendar(Request $request){

        if($request->isMethod('post')){

            $title = "To Pay $request->deposit_amount $request->supplier_currency to Supplier";

            $dynamic_text_area = "$request->details";

            $calendar_start_date =  Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->format('Ymd');
		    $calendar_end_date   = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->format('Ymd');

            $location = "";
            $description = "test";
            // $guests = "kashan.mehmood13@gmail.com";
            $message_url ="https://www.google.com/calendar/render?action=TEMPLATE&text=".$title."&dates=".$calendar_start_date."/".$calendar_end_date."&details=".$dynamic_text_area."&location=".$location."&sf=true&output=xml";
            return $message_url;

            // $event = new Event;
            // $event->name        = "To Pay $request->actualCost $request->supplier_currency to Supplier";
            // $event->description = 'Event description';
            // $event->startDate   = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->startOfDay();
            // $event->endDate     = Carbon::parse(str_replace('/', '-', $request->deposit_due_date))->endOfDay();
            // $event->addAttendee(['email' => 'kashan.kingdomvision@gmail.com']);
            // $event->save();
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
            $this->validate($request, [ "booking_due_date"    => "required|array", "booking_due_date.*"  => "required" ]);
            $this->validate($request, [ "cost"    => "required|array", "cost.*"  => "required"]);

            $season = season::find($request->season_id);
            
            if(!empty($request->date_of_service)){
                $error_array = [];
                foreach($request->date_of_service as $key => $date){
        
                    $start = date('Y-m-d', strtotime($season->start_date));
                    $end   = date('Y-m-d', strtotime($season->end_date));

                    if(!is_null($date)){
                        $date  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
                    }else{
                        $date  = null;
                    }

                    if(!is_null($date) && !is_null($start)  && !is_null($end)){
                        if( !(($date >= $start) && ($date <= $end)) ){
                            $error_array[$key+1] = "Date of service should be season date range.";
                        }
                    }
         
                }
            }

            if(!empty($error_array)){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'date_of_service' =>  (object) $error_array
                ]);
            }

            $booking_error = [];
            if(!empty($request->booking_date)){
                foreach($request->booking_date as $key => $date){

                    if(!is_null($date)){
                        $date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $date))->format('Y-m-d')));
                    }else{
                        $date  = null;
                    }

                    if(!is_null($request->booking_due_date[$key])){
                        $booking_due_date = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d')));
                    }else{
                        $booking_due_date  = null;
                    }

                    if(!is_null($request->date_of_service[$key])){
                        $date_of_service  = date('Y-m-d', strtotime(Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d')));
                    }else{
                        $date_of_service  = null;
                    }

                    if(is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
                        if( ($date > $booking_due_date ) ){
                            $booking_error[$key+1] = "Booking Date should be smaller than due date";
                        }
                    }

                    if(!is_null($date_of_service) && !is_null($date) && !is_null($booking_due_date) ){
                        if( !(($date >= $date_of_service) && ($date <= $booking_due_date)) ){
                            $booking_error[$key+1] = "Booking Date should be greater Date of service and smaller than Booking Due Date";
                        }
                    }

                }
            }

            if(!empty($booking_error)){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'booking_date' => (object) $booking_error
                ]);
            }

        
            $qoute = Qoute::find($id);

            $qoute_log = new QouteLog;

            $qouteDetailLogNumber = $this->increment_log_no($this->get_log_no('QouteLog',$id));
            $qoute_log->qoute_id          =  $id;
            $qoute_log->ref_no            =  $qoute->ref_no;
            $qoute_log->quotation_no      =  $request->quotation_no;
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
                $QouteDetailLog->supplier          =  $qouteDetail->supplier;
                $QouteDetailLog->booking_date      =  $qouteDetail->booking_date;
                $QouteDetailLog->booking_due_date  =  $qouteDetail->booking_due_date;
                $QouteDetailLog->booking_method    =  $qouteDetail->booking_method;
                $QouteDetailLog->booked_by         =  $qouteDetail->booked_by;
                $QouteDetailLog->booking_refrence  =  $qouteDetail->booking_refrence;
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
                    $qouteDetail->date_of_service   = $request->date_of_service[$key] ? Carbon::parse(str_replace('/', '-', $request->date_of_service[$key]))->format('Y-m-d') : null;
                    $qouteDetail->service_details   = $request->service_details[$key];
                    $qouteDetail->category_id       = $request->category[$key];
                    $qouteDetail->supplier          = $request->supplier[$key];
                    $qouteDetail->booking_date      = $request->booking_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_date[$key]))->format('Y-m-d') : null;
                    $qouteDetail->booking_due_date  = $request->booking_due_date[$key] ? Carbon::parse(str_replace('/', '-', $request->booking_due_date[$key]))->format('Y-m-d') : null;
                    $qouteDetail->booking_method    = $request->booking_method[$key];
                    $qouteDetail->booked_by         = $request->booked_by[$key];
                    $qouteDetail->booking_refrence  = $request->booking_refrence[$key];
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

        $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.edit')->with([
            'quote' => Qoute::find($id),
            'quote_details' => QouteDetail::where('qoute_id',$id)->get(),
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
            'categories' => Category::all()->sortBy('name'),
            // 'seasons' => season::where('default_season',1)->first(),
            'seasons' => season::all(),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role',5)->orderBy('name','ASC')->get(),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('name'),
            'currencies' => Currency::all()->sortBy('name'),
            'qoute_logs' => QouteLog::where('qoute_id',$id)->get(),
        ]);
    }
    

    public function view_version($quote_id, $log_no){
        // $qoute_log = QouteLog::where('qoute_id',$quote_id)->where('log_no',$log_no)->get();
        // return $qoute_log;

        $qoute_log = QouteLog::where('qoute_id',$quote_id)
        ->where('log_no',$log_no)
        ->first();



        $qoute_detail_logs = QouteDetailLog::where('qoute_id',$quote_id)
        ->where('log_no',$log_no)
        ->get();


        
        $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.view-version')->with([
            'qoute_log' => $qoute_log,
            'qoute_detail_logs' => $qoute_detail_logs,
            'seasons' =>  season::all(), 
            'currencies' => Currency::all()->sortBy('name'),

            'categories' => Category::all()->sortBy('name'),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('name'),
            'users' => User::all()->sortBy('name'),

            'supervisors' => User::where('role',5)->orderBy('name','ASC')->get(),

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

        $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            // $url    = 'http://localhost/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        return view('qoute.recall-version')->with([
            'quote' => $qoute_log,
            'quote_details' => $qoute_detail_logs,
            'get_user_branches' => $get_user_branches,
            'get_holiday_type' => $get_holiday_type,
            'categories' => Category::all()->sortBy('name'),
            // 'seasons' => season::where('default_season',1)->first(),
            'seasons' => season::all(),
            'users' => User::all()->sortBy('name'),
            'supervisors' => User::where('role',5)->orderBy('name','ASC')->get(),
            'suppliers' => Supplier::all()->sortBy('name'),
            'booking_methods' => BookingMethod::all()->sortBy('name'),
            'currencies' => Currency::all()->sortBy('name'),
            'qoute_logs' => QouteLog::where('qoute_id',$quote_id)->get(),
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

            return view('booking_method.create');
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

            return Redirect::route('view-booking-method')->with('success_message', 'Booking Method Successfully Updated!!');
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

        $get_user_branches = Cache::remember('get_user_branches', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_payment_settings';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });
        
        $get_holiday_type = Cache::remember('get_holiday_type', 60, function () {
            $url    = 'http://whipplewebdesign.com/php/unforgettable_payment/backend/api/payment/get_holiday_type';
            $output =  $this->curl_data($url);
            return json_decode($output);
        });

        $get_ref = Cache::remember('get_ref', 60, function () {
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

    
    public function get_supplier_currency(Request $request){

        $supplier_currency = Supplier::leftJoin('currencies', 'currencies.id', '=', 'suppliers.currency_id')
        ->where('suppliers.id', $request->supplier_id)
        ->first();

        return $supplier_currency;
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
        code::destroy('id','=',$id);
        return Redirect::route('view-code')->with('success_message', 'Code Successfully Deleted!!');
    }
}