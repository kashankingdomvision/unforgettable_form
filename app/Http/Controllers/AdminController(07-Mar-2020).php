<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Request as Routerequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Requests;
use App\User;
use App\season;
use App\booking;
use App\airline;
use App\payment;
use App\supervisor;
use App\booking_email;
use Validator;
use Redirect;
use DB;
use Cache;
use Input;
use Hash;
use Session;
use Carbon\Carbon;

class AdminController extends Controller
{
  public function __construct(Request $request){
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
    public function index(){
    	return view('admin.index');
    }
    public function logout(){
     	if(Auth::check()){
        $id = Auth::user()->id;
     		Auth::logout();
        user::where('id','=',$id)->update(array('is_login' => 0));
        session()->flush();
     		return Redirect::route('login')->with('success_message','Your session has been ended!');
     	}
     	else{
     		return Redirect::route('admin');
     	}  
    }
    public function get_chapter(Request $request)
    {
         $matchThese    = ['book_id'=>$request->input('id')];
         $item_rec      = DB::table('chapters')->where($matchThese)->select('id','title')->get();
        if($request->ajax()){
           return response()->json([
               'item_rec' => $item_rec
           ]);
        }
    }

    
    public function create_user(Request $request){
        if ($request->isMethod('post')) {
            $this->validate($request, ['username'  => 'required']); 
            $this->validate($request, ['email'     => 'required|email|unique:users']); 
            $this->validate($request, ['password'  => 'required']); 

            user::create(array(
                'name'          => $request->username,
                'email'         => $request->email,
                'supervisor_id' => $request->supervisor_id,
                'password'      =>  bcrypt($request->password),
            ));
            return Redirect::route('creat-user')->with('success_message','Created Successfully');
        }else{
            return view('user.create_user')->with(['name'=>'','id'=>'','supervisor' => supervisor::all()]);
        }
    }
    public function view_user(Request $request){

        $data = user::leftjoin('supervisors', 'supervisors.id', '=', 'users.supervisor_id')->get(['users.*','supervisors.name as supervisor_name','supervisors.email as supervisor_email']);

        return view('user.view_user')->with('data',$data);   
    }
    public function update_user(Request $request,$id){
        if ($request->isMethod('post')) {
             $this->validate($request, ['username'  => 'required']); 
             if(user::select('email')->where('id', $id)->get()->first()->email != $request->email ){
              $this->validate($request, ['email'     => 'required|email|unique:users']); 
             }
            
            if($request->password != ''){
             user::where('id','=',$request->id)->update(
             array('name'          => $request->username,
                   'email'         => $request->email, 
                   'supervisor_id' => $request->supervisor_id,
                   'password'      => bcrypt($request->password)
              ));
            }else{
              user::where('id','=',$request->id)->update(
              array('name'          => $request->username,
                    'email'         => $request->email,
                    'supervisor_id' => $request->supervisor_id
               ));
            }
             return Redirect::route('view-user')->with('success_message','update Successfully');
        }else{
            return view('user.update_user')->with(['data' => user::find($id),'id' => $id ,'supervisor' => supervisor::all()]);
        }
    }
    public function delete_user($id){
        if(booking::where('user_id',$id)->count() == 1){
            return Redirect::route('view-user')->with('error_message','You can not delete this user because user already in use');
        }
         user::destroy('id','=',$id);
         return Redirect::route('view-user')->with('success_message','Delete Successfully');
    }

    // CRUD related to seasson
    public function create_season(Request $request){
      
        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required|unique:seasons']); 
            season::create(array(
                'name'      => $request->name
            ));
            return Redirect::route('creat-season')->with('success_message','Created Successfully');
        }else{
            return view('season.create_season')->with(['name'=>'','id'=>'']);
        }
    }
    public function view_season(Request $request){
        return view('season.view_season')->with('data',season::all());   
    }
    public function update_season(Request $request,$id){
        if ($request->isMethod('post')) {
            /* $this->validate($request, ['username'  => 'required']); 
             if(season::select('email')->where('id', $id)->get()->first()->email != $request->email ){
              $this->validate($request, ['email'     => 'required|email|unique:users']); 
             }
            
            if($request->password != ''){
             season::where('id','=',$request->id)->update(
             array('name'     => $request->username,
                   'email'    => $request->email, 
                   'password' => bcrypt($request->password)
              ));
            }else{
              season::where('id','=',$request->id)->update(
              array('name'     => $request->username,
                    'email'    => $request->email 
               ));
            }*/
             return Redirect::route('view-season')->with('success_message','update Successfully');
        }else{
            return view('season.update_season')->with(['data' => season::find($id),'id' => $id]);
        }
    }
    public function delete_season($id){
         if(booking::where('season_id',$id)->count() >= 1){
             return Redirect::route('view-season')->with('error_message','You can not delete this record because season already in use');
         }
         season::destroy('id','=',$id);
         return Redirect::route('view-season')->with('success_message','Delete Successfully');
    }
    //
    public function create_supervisor(Request $request){
      
        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required']); 
            $this->validate($request, ['email' => 'required|email|unique:supervisors']); 
            supervisor::create(array(
                'name'  => $request->name,
                'email' => $request->email
            ));
            return Redirect::route('create-supervisor')->with('success_message','Created Successfully');
        }else{
            return view('supervisor.create_supervisor')->with(['name' => '','id' => '','email' => '']);
        }
    }
    public function view_supervisor(Request $request){
        return view('supervisor.view_supervisor')->with('data',supervisor::all());   
    }
    public function update_supervisor(Request $request,$id){
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
             supervisor::where('id','=', $id)->update(
                 array(
                     'email' => $request->email,
                     'name'  => $request->name
                 ));
             return Redirect::route('view-supervisor')->with('success_message','update Successfully');
        }else{
            return view('supervisor.update_supervisor')->with(['data' => supervisor::find($id),'id' => $id]);
        }
    }
    public function delete_supervisor($id){
         if(User::where('supervisor_id',$id)->count() == 1){
             return Redirect::route('view-supervisor')->with('error_message','You can not delete this record because supervisor already in use');
         }
         supervisor::destroy('id','=',$id);
         return Redirect::route('view-supervisor')->with('success_message','Delete Successfully');
    }
    //
    public function create_booking(Request $request){
        if ($request->isMethod('post')) {
           $this->validate($request, ['ref_no'                     => 'required'],['required' => 'Reference number is required']); 
           $this->validate($request, ['brand_name'                 => 'required'],['required' => 'Please select Brand Name']); 
           $this->validate($request, ['season_id'                  => 'required|numeric'],['required' => 'Please select Booking Season']); 
           $this->validate($request, ['agency_booking'             => 'required'],['required' => 'Please select Agency']); 
           $this->validate($request, ['pax_no'                     => 'required'],['required' => 'Please select PAX No']); 
           $this->validate($request, ['date_of_travel'             => 'required'],['required' => 'Please select date of travel']); 
           $this->validate($request, ['flight_booked'              => 'required'],['required' => 'Please select flight booked']);

           $this->validate($request, ['fb_airline_name_id'         => 'required'],['required' => 'Please select flight airline name']);

           $this->validate($request, ['fb_payment_method_id'       => 'required'],['required' => 'Please select payment method']);

           $this->validate($request, ['fb_booking_date'            => 'required'],['required' => 'Please select booking date']);

           $this->validate($request, ['fb_airline_ref_no'          => 'required'],['required' => 'Please enter airline reference number']);

           $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'],['required_if' => 'Please enter flight booking details']); 
           //
           // $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'],['required_if' => 'Please select booked person']); 
           $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'],['required_if' => 'Plesse enter flight booking date']);
           //
           // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
           $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Plesse enter transfer date']);
           // $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'],['required_if' => 'Please select document person']);
           $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'],['required_if' => 'Plesse enter document sent date']);
           // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
           $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'],['required_if' => 'Plesse enter document sent date']);
           // 
           $this->validate($request, ['asked_for_transfer_details' => 'required'],['required' => 'Please select asked for transfer detail box']);
           $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'],['required_if' => 'Please transfer detail']); 
           $this->validate($request, ['form_sent_on'               => 'required'],['required' => 'Please select form sent on']); 
           // $this->validate($request, ['transfer_info_received'     => 'required'],['required' => 'Please select transfer info received']);
           // $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'],['required_if' => 'Please transfer info detail']); 
          
           $this->validate($request, ['itinerary_finalised'        => 'required'],['required' => 'Please select itinerary finalised']);
           $this->validate($request, ['itinerary_finalised_details'=> 'required_if:itinerary_finalised,yes'],['required_if' => 'Please enter itinerary finalised details']);

           // $this->validate($request, ['itf_person'                => 'required_if:itinerary_finalised,no'],['required_if' => 'Please select itinerary person']);
           $this->validate($request, ['itf_last_date'              => 'required_if:itinerary_finalised,no'],['required_if' => 'Plesse enter itinerary sent date']);

           $this->validate($request, ['documents_sent'             => 'required'],['required' => 'Please select documents sent']);
           $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'],['required_if' => 'Please enter document sent details']);  
           
           $this->validate($request, ['electronic_copy_sent'       => 'required'],['required' => 'Please select electronic copy sent']);
           $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'],['required_if' => 'Please enter electronic copy details']);             

           $this->validate($request, ['transfer_organised'         => 'required'],['required' => 'Please select transfer organised']);
           $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'],['required_if' => 'Please enter transfer organised details']);             
           $this->validate($request, ['type_of_holidays'           => 'required'],['required' => 'Please select type of holidays']); 
           $this->validate($request, ['sale_person'                => 'required'],['required' => 'Please select type of sale person']); 
           
           if($request->form_received_on == '0000-00-00'){
             $form_received_on = NULL;
           }else{
            $form_received_on = $request->form_received_on;
           }
           //
           if($request->app_login_date == '0000-00-00'){
             $app_login_date = NULL;
           }else{
            $app_login_date = $request->app_login_date;
           }
           //
           $booking_id = booking::create(array(
               'ref_no'                      => $request->ref_no,
               'brand_name'                  => $request->brand_name,
               'season_id'                   => $request->season_id,
               'agency_booking'              => $request->agency_booking,
               'pax_no'                      => $request->pax_no,
               'date_of_travel'              => Carbon::parse(str_replace('/','-',$request->date_of_travel))->format('Y-m-d'),
               'flight_booked'               => $request->flight_booked,
               'fb_airline_name_id'          => $request->fb_airline_name_id,
               'fb_payment_method_id'        => $request->fb_payment_method_id,
               'fb_booking_date'             => Carbon::parse(str_replace('/','-',$request->fb_booking_date))->format('Y-m-d'),
               'fb_airline_ref_no'           => $request->fb_airline_ref_no,
               'fb_last_date'                => Carbon::parse(str_replace('/','-',$request->fb_last_date))->format('Y-m-d'),
               'fb_person'                   => $request->fb_person,
               //
               'aft_last_date'                => Carbon::parse(str_replace('/','-',$request->aft_last_date))->format('Y-m-d'),
               'aft_person'                   => $request->aft_person,
               'ds_last_date'                 => Carbon::parse(str_replace('/','-',$request->ds_last_date))->format('Y-m-d'),
               'ds_person'                    => $request->ds_person,
               'to_last_date'                 => Carbon::parse(str_replace('/','-',$request->to_last_date))->format('Y-m-d'),
               'to_person'                    => $request->to_person,
               //
               'document_prepare'             => $request->document_prepare,
               'dp_last_date'                 => Carbon::parse(str_replace('/','-',$request->dp_last_date))->format('Y-m-d'),
               'dp_person'                    => $request->dp_person,
               //
               //
               'flight_booking_details'      => $request->flight_booking_details,
               'asked_for_transfer_details'  => $request->asked_for_transfer_details,
               'transfer_details'            => $request->transfer_details,
               'form_sent_on'                => Carbon::parse(str_replace('/','-',$request->form_sent_on))->format('Y-m-d'),
               'form_received_on'            => $form_received_on,
               'app_login_date'              => $app_login_date,
               // 'transfer_info_received'      => $request->transfer_info_received,
               // 'transfer_info_details'       => $request->transfer_info_details,
               'itinerary_finalised'         => $request->itinerary_finalised,
               'itinerary_finalised_details' => $request->itinerary_finalised_details,
               'itf_last_date'               => Carbon::parse(str_replace('/','-',$request->itf_last_date))->format('Y-m-d'),
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
               'fso_last_date'               => Carbon::parse(str_replace('/','-',$request->fso_last_date))->format('Y-m-d'),
               'aps_person'                  => $request->aps_person,
               'aps_last_date'               => Carbon::parse(str_replace('/','-',$request->aps_last_date))->format('Y-m-d'),
               'finance_detail'              => $request->finance_detail,
               'destination'                 => $request->destination,
               'user_id'                     => Auth::user()->id,
               'itf_current_date'            => Carbon::parse(str_replace('/','-',$request->itf_current_date))->format('Y-m-d'),
               'tdp_current_date'            => Carbon::parse(str_replace('/','-',$request->tdp_current_date))->format('Y-m-d'),
               'tds_current_date'            => Carbon::parse(str_replace('/','-',$request->tds_current_date))->format('Y-m-d'),

           ));

           if($request->flight_booked == 'yes'){
             //Sending email
                $template   = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$booking_id->id;
                $template   .= '<h1>Reference Number : '.$request->ref_no.'</h1>';
                $template   .= '<h1>Last Date Of Flight Booking : '.$request->fb_last_date.'</h1>';
                
                if($request->fb_person == ''){
                  $email = Auth::user()->email;
                  $template   .= '<h1>Responsible Person : '.Auth::user()->name.'</h1>';
                }else{
                  $record = User::where('id',$request->fb_person)->get()->first();
                  $email  = $record->email;
                  $name   = $record->name;
                  $template   .= '<h1>Responsible Person : '.$name.'</h1>';
                } 
                $data['to']        = $email;
                $data['name']      = config('app.name');
                $data['from']      = config('app.mail');
                $data['subject']   = "Task Flight Booked Alert";
             try{
                 \Mail::send("email_template.flight_booked_alert", ['template'=>$template], function ($m) use ($data) {
                     $m->from($data['from'], $data['name']);
                     $m->to($data['to'])->subject($data['subject']);
                 });
             }catch(Swift_RfcComplianceException $e) {
                 return $e->getMessage();
             }
             //Sending email
           }
           if($request->form_received_on == '0000-00-00'){
              //Sending email
                 $template     = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$booking_id->id;

                 $template   .= '<h1>Reference Number : '.$request->ref_no.'</h1>';
                 $template   .= '<h1>Reminder for sent on date : '.$request->fso_last_date.'</h1>';

                 if($request->fso_person == ''){
                   $email = Auth::user()->email;
                   $template   .= '<h1>Responsible Person : '.Auth::user()->name.'</h1>';
                 }else{
                   $record = User::where('id',$request->fso_person)->get()->first();
                   $email  = $record->email;
                   $name   = $record->name;
                   $template   .= '<h1>Responsible Person : '.$name.'</h1>';
                 } 
                 $data['to']        = $email;
                 $data['name']      = config('app.name');
                 $data['from']      = config('app.mail');
                 $data['subject']   = "Reminder for form sent on";
              try{
                  \Mail::send("email_template.form_sent_on", ['template'=>$template], function ($m) use ($data) {
                      $m->from($data['from'], $data['name']);
                      $m->to($data['to'])->subject($data['subject']);
                  });
              }catch(Swift_RfcComplianceException $e) {
                  return $e->getMessage();
              }
              //Sending email
           }

           if($request->electronic_copy_sent == 'no'){
              //Sending email
                 $template    = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$booking_id->id;

                 $template   .= '<h1>Reference Number : '.$request->ref_no.'</h1>';
                 $template   .= '<h1>App Reminder Sent Date : '.$request->aps_last_date.'</h1>';

                 if($request->aps_person == ''){
                   $email = Auth::user()->email;
                   $template   .= '<h1>Responsible Person : '.Auth::user()->name.'</h1>';
                 }else{
                   $record = User::where('id',$request->aps_person)->get()->first();
                   $email  = $record->email;
                   $name   = $record->name;
                   $template   .= '<h1>Responsible Person : '.$name.'</h1>';
                 } 
                 $data['to']        = $email;
                 $data['name']      = config('app.name');
                 $data['from']      = config('app.mail');
                 $data['subject']   = "Reminder for app login sent";
              try{
                  \Mail::send("email_template.app_login_sent", ['template'=>$template], function ($m) use ($data) {
                      $m->from($data['from'], $data['name']);
                      $m->to($data['to'])->subject($data['subject']);
                  });
              }catch(Swift_RfcComplianceException $e) {
                  return $e->getMessage();
              }
              //Sending email
           }

           return Redirect::route('create-booking')->with('success_message','Created Successfully');
       }else{

           $get_ref = Cache::remember('get_ref',60,function(){
             $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
             $output =  $this->curl_data($url); 
             // return json_decode($output)->data;
           });

           $get_user_branches = Cache::remember('get_user_branches',60,function(){
             $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
             $output =  $this->curl_data($url); 
             return json_decode($output);
           });

           $get_holiday_type = Cache::remember('get_holiday_type',60,function(){
           $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_holiday_type';
           $output =  $this->curl_data($url); 
              return json_decode($output);
           });

           $booking_email = booking_email::where('booking_id', '=', 1)->get();
           return view('booking.create_booking')->with(['get_holiday_type' => $get_holiday_type, 'seasons' => season::all(),'persons' => user::all(),'get_refs' => $get_ref,'get_user_branches' => $get_user_branches,'booking_email' => $booking_email,'payment' => payment::all(),'airline' => airline::all() ]);
       }
    }

    public function view_booking_season(Request $request){
        $group_by_seasons = season::join('bookings', 'bookings.season_id', '=', 'seasons.id')->orderBy('seasons.created_at','desc')->groupBy('seasons.id','seasons.name')->get(['seasons.id','seasons.name']);
        return view('booking.view_booking_season')->with('data',$group_by_seasons);   
    }

    public function delete_booking_season($id){
         season::destroy('id','=',$id);
         return Redirect::route('view-booking-season')->with('success_message','Deleted Successfully');
    }

    public function view_booking(Request $request,$id){
        //
        $staff = Cache::remember('staff',1140,function(){
          return User::orderBy('id', 'DESC')->get();
        });
        //
        $get_ref = Cache::remember('get_ref',60,function(){
          $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
          $output =  $this->curl_data($url); 
          return json_decode($output)->data;
        });
       //
        $get_user_branches = Cache::remember('get_user_branches',60,function(){
          $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
          $output =  $this->curl_data($url); 
          return json_decode($output);
        });
        //
         $get_holiday_type = Cache::remember('get_holiday_type',60,function(){
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
        ->leftjoin('payments', 'payments.id', '=', 'bookings.fb_payment_method_id')->where('bookings.season_id','=',$id);
       
        if($request->created_at !=''){
            $date  = explode('-', $request->created_at);
            $start_date = $date[0]; 
            $end_date   = $date[1];

            $start_created_at = Carbon::parse($start_date)->format('Y-m-d');
            $end_created_at   = Carbon::parse($end_date)->format('Y-m-d');
            $query =  $query->whereRaw('DATE(bookings.created_at) >= ?',$start_created_at);
            $query =  $query->whereRaw('DATE(bookings.created_at) <= ?',$end_created_at);
        }
        if($request->created_by !=''){
            $query =  $query->where('bookings.user_id', '=', $request->created_by);
        }
        if($request->ref_no !=''){
            $query =  $query->where('bookings.ref_no', '=', $request->ref_no);
        }
        if($request->date_of_travel !=''){
            $date  = explode('-', $request->date_of_travel);
            $start_date = $date[0]; 
            $end_date   = $date[1];

            $query =  $query->where('bookings.date_of_travel', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            $query =  $query->where('bookings.date_of_travel', '<=', Carbon::parse($end_date)->format('Y-m-d'));
        }
        if($request->brand_name !=''){
            $query =  $query->where('bookings.brand_name', '=', $request->brand_name);
        }
        if($request->season_id !=''){
            $query =  $query->where('bookings.season_id', '=', $request->season_id);
        }
        if($request->agency_booking !=''){
            $query =  $query->where('bookings.agency_booking', '=', $request->agency_booking);
        }
        if($request->flight_booked !=''){
            $query =  $query->where('bookings.flight_booked', '=', $request->flight_booked);
        }
        if($request->form_sent_on !=''){
            $date  = explode('-', $request->form_sent_on);
            $start_date = $date[0]; 
            $end_date   = $date[1];
            $query =  $query->where('bookings.form_sent_on', '>=', Carbon::parse($start_date)->format('Y-m-d'));
            $query =  $query->where('bookings.form_sent_on', '<=', Carbon::parse($end_date)->format('Y-m-d'));
        }
         if($request->type_of_holidays !=''){
            $query =  $query->where('bookings.type_of_holidays', '=', $request->type_of_holidays);
        }
         if($request->fb_payment_method_id !=''){
            $query =  $query->where('bookings.fb_payment_method_id', '=', $request->fb_payment_method_id);
        }
        if($request->fb_airline_name_id !=''){
            $query =  $query->where('bookings.fb_airline_name_id', '=', $request->fb_airline_name_id);
        }
        if($request->fb_responsible_person !=''){
            $query =  $query->where('bookings.fb_person', '=', $request->fb_responsible_person);
        }
        if($request->ti_responsible_person !=''){
            $query =  $query->where('bookings.aft_person', '=', $request->ti_responsible_person);
        }
        if($request->to_responsible_person !=''){
            $query =  $query->where('bookings.to_person', '=', $request->to_responsible_person);
        }
        if($request->itf_responsible_person !=''){
            $query =  $query->where('bookings.itf_person', '=', $request->itf_responsible_person);
        }
        if($request->dp_responsible_person !=''){
            $query =  $query->where('bookings.dp_person', '=', $request->dp_responsible_person);
        }
        if($request->ds_responsible_person !=''){
            $query =  $query->where('bookings.ds_person', '=', $request->ds_responsible_person);
        }
        if($request->pax_no !=''){
            $query =  $query->where('bookings.pax_no', '=', $request->pax_no);
        }
        if($request->asked_for_transfer_details !=''){
            $query =  $query->where('bookings.asked_for_transfer_details', '=', $request->asked_for_transfer_details);
        }
        if($request->transfer_organised !=''){
            $query =  $query->where('bookings.transfer_organised', '=', $request->transfer_organised);
        }
        if($request->itinerary_finalised !=''){
            $query =  $query->where('bookings.itinerary_finalised', '=', $request->itinerary_finalised);
        }
        $query = $query->orderBy('bookings.created_at','desc')->paginate(10,['bookings.*','airlines.name as airline_name','payments.name as payment_name','seasons.name','users.name as username','user_fb.name as fbusername','user_ti.name as tiusername','user_to.name as tousername','user_itf.name as itfusername','user_tdp.name as tdpusername','user_ds.name as dsusername'])->appends(Input::all());

        return view('booking.view_booking')->with(['data' => $query,'book_id' => $id,'staffs' => $staff,'get_refs' => $get_ref,'get_holiday_type'=>$get_holiday_type,'type_of_holidays' => $request->type_of_holidays,
        'get_user_branches' => $get_user_branches,'created_at' => $request->created_at,'created_by' => $request->created_by,'ref_no' => $request->ref_no ,'date_of_travel' => $request->date_of_travel,'brand_name' => $request->brand_name,'seasons' => season::all(),'session_id' => $request->season_id,'agency_booking' => $request->agency_booking,'flight_booked' => $request->flight_booked,'form_sent_on' => $request->form_sent_on,'payment' => payment::all(),'airline' => airline::all(),'fb_payment_method_id'=>$request->fb_payment_method_id,'fb_airline_name_id'=>$request->fb_airline_name_id,'fb_responsible_person'=>$request->fb_responsible_person,'ti_responsible_person'=>$request->ti_responsible_person,'to_responsible_person'=>$request->to_responsible_person,'itf_responsible_person'=>$request->itf_responsible_person,'dp_responsible_person'=>$request->dp_responsible_person,'ds_responsible_person'=>$request->ds_responsible_person,'pax_no'=>$request->pax_no,'asked_for_transfer_details' => $request->asked_for_transfer_details,'transfer_organised' => $request->transfer_organised,'itinerary_finalised' => $request->itinerary_finalised]);   
    }
    public function delete_booking($season_id,$booking_id){
         booking::destroy('id','=',$booking_id);
         return Redirect::route('view-booking',$season_id)->with('success_message','Deleted Successfully');
    }
    public function get_ref_detail(Request $request){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://unforgettabletravelcompany.com/staging/backend/api/payment/get_lead_info");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(array('reference' => $request->input('id'))));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);

        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://unforgettabletravelcompany.com/staging/backend/api/payment/get_lead_by_reference");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(array('ref_no' => $request->input('id'))));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output2 = curl_exec($ch);
        curl_close ($ch);
        //

        //
        $url ='https://unforgettabletravelcompany.com/ufg-form/Admin/Login/LoginCon/get_user_detail/'.$request->input('id');
        $output =  $this->curl_data($url); 
        //
        //
        $url2 ='https://unforgettabletravelcompany.com/ufg-form/Admin/Login/LoginCon/app_login_detail/'.$request->input('id');
        $output2 =  $this->curl_data($url2); 
        //
        if($request->ajax()){
           return response()->json([
               'item_rec'  => json_decode($server_output)->data,
               'item_rec2' => json_decode($output),
               'item_rec3' => json_decode($output2),
               'item_rec4' => json_decode($server_output2)->data
           ]);
        }
    }
    private function curl_data($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return $output = curl_exec($ch);
    }
    public function update_booking(Request $request,$id){
        if ($request->isMethod('post')) {
           $this->validate($request, ['ref_no'                     => 'required'],['required' => 'Reference number is required']); 
           $this->validate($request, ['brand_name'                 => 'required'],['required' => 'Please select Brand Name']); 
           $this->validate($request, ['season_id'                  => 'required|numeric'],['required' => 'Please select Booking Season']); 
           $this->validate($request, ['agency_booking'             => 'required'],['required' => 'Please select Agency']); 
           $this->validate($request, ['pax_no'                     => 'required'],['required' => 'Please select PAX No']); 
           $this->validate($request, ['date_of_travel'             => 'required'],['required' => 'Please select date of travel']); 
           $this->validate($request, ['flight_booked'              => 'required'],['required' => 'Please select flight booked']);
           $this->validate($request, ['flight_booking_details'     => 'required_if:flight_booked,yes'],['required_if' => 'Please enter flight booking details']);
           $this->validate($request, ['fb_person'                  => 'required_if:flight_booked,no'],['required_if' => 'Please select booked person']); 
           $this->validate($request, ['fb_last_date'               => 'required_if:flight_booked,no'],['required_if' => 'Plesse enter flight booking date']);
           //
           // $this->validate($request, ['aft_person'                 => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Please select asked for transfer person']);
           $this->validate($request, ['aft_last_date'              => 'required_if:asked_for_transfer_details,no'],['required_if' => 'Plesse enter transfer date']);
           $this->validate($request, ['ds_person'                 => 'required_if:documents_sent,no'],['required_if' => 'Please select document person']);
           $this->validate($request, ['ds_last_date'              => 'required_if:documents_sent,no'],['required_if' => 'Plesse enter document sent date']);
           // $this->validate($request, ['to_person'                 => 'required_if:transfer_organised,no'],['required_if' => 'Please select document person']);
           $this->validate($request, ['to_last_date'              => 'required_if:transfer_organised,no'],['required_if' => 'Plesse enter document sent date']);
           // 
           $this->validate($request, ['asked_for_transfer_details' => 'required'],['required' => 'Please select asked for transfer detail box']);
           $this->validate($request, ['transfer_details'           => 'required_if:asked_for_transfer_details,yes'],['required_if' => 'Please transfer detail']); 
           $this->validate($request, ['form_sent_on'               => 'required'],['required' => 'Please select form sent on']); 
           $this->validate($request, ['transfer_info_received'     => 'required'],['required' => 'Please select transfer info received']);
           $this->validate($request, ['transfer_info_details'      => 'required_if:transfer_info_received,yes'],['required_if' => 'Please transfer info detail']); 
           $this->validate($request, ['itinerary_finalised'        => 'required'],['required' => 'Please select itinerary finalised']);
           $this->validate($request, ['itinerary_finalised_details'=> 'required_if:itinerary_finalised,yes'],['required_if' => 'Please enter itinerary finalised details']);

           $this->validate($request, ['documents_sent'             => 'required'],['required' => 'Please select documents sent']);
           $this->validate($request, ['documents_sent_details'     => 'required_if:documents_sent,yes'],['required_if' => 'Please enter document sent details']);  

           $this->validate($request, ['electronic_copy_sent'       => 'required'],['required' => 'Please select electronic copy sent']);
           $this->validate($request, ['electronic_copy_details'    => 'required_if:electronic_copy_sent,yes'],['required_if' => 'Please enter electronic copy details']);             

           $this->validate($request, ['transfer_organised'         => 'required'],['required' => 'Please select transfer organised']);
           $this->validate($request, ['transfer_organised_details' => 'required_if:transfer_organised,yes'],['required_if' => 'Please enter transfer organised details']);             
           $this->validate($request, ['type_of_holidays'           => 'required'],['required' => 'Please select type of holidays']); 
           $this->validate($request, ['sale_person'                => 'required'],['required' => 'Please select type of sale person']); 
           
           if($request->form_received_on == '0000-00-00'){
             $form_received_on = NULL;
           }elseif($request->form_received_on == ''){
             $form_received_on = NULL;
           }else{
            $form_received_on = $request->form_received_on;
           }

           if($request->app_login_date == '0000-00-00'){
             $app_login_date = NULL;
           }elseif($request->app_login_date == ''){
             $app_login_date = NULL;
           }else{
            $app_login_date = $request->app_login_date;
           }

           booking::where('id','=',$id)->update(array(
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
           return Redirect::route('update-booking',$id)->with('success_message','Updated Successfully');
       }else{

           $get_ref = Cache::remember('get_ref',60,function(){
             $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_ref';
             $output =  $this->curl_data($url); 
             return json_decode($output)->data;
           });

           $get_user_branches = Cache::remember('get_user_branches',60,function(){
             $url    = 'https://unforgettabletravelcompany.com/staging/backend/api/payment/get_payment_settings';
             $output =  $this->curl_data($url); 
             return json_decode($output);
           });
           $booking_email = booking_email::where('booking_id', '=', $id)->get();
           return view('booking.update_booking')->with(['booking_email' => $booking_email,'persons' => user::all(),'seasons' => season::all(),'get_refs' => $get_ref,'get_user_branches' => $get_user_branches,'record' => booking::where('id', '=', $id)->get()->first(),'id' => $id ]);
       }
    }
    public function delete_multi_booking(Request $request,$id){
        $customMessages = ['required' => 'Please select at least one checkbox'];
        $this->validate($request, ['multi_val'    => 'required'], $customMessages);
        foreach ($request->multi_val as $val) {
          booking::destroy('id','=',$val);
        }
        return Redirect::route('view-booking',$id)->with('success_message','Action Perform Successfully');
    }

      public function create_airline(Request $request){
      
        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required']); 
           
            airline::create(array(
                'name'  => $request->name
              
            ));
            return Redirect::route('creat-airline')->with('success_message','Created Successfully');
        }else{
            return view('airline.create_airline')->with(['name' => '','id' => '']);
        }
    }
    public function view_airline(Request $request){
        return view('airline.view_airline')->with('data',airline::all());   
    }

 public function update_airline(Request $request,$id){
        if ($request->isMethod('post')) {

             $validator = Validator::make($request->all(), [
               
                 'name'  => 'required'
             ]);
             if ($validator->fails()) {
                 return back()
                     ->withErrors($validator)
                     ->withInput();
             }
             airline::where('id','=', $id)->update(
                 array(
                   
                     'name'  => $request->name
                 ));
             return Redirect::route('view-airline')->with('success_message','update Successfully');
        }else{
            return view('airline.update_airline')->with(['data' => airline::find($id),'id' => $id]);
        }
    }

       public function delete_airline($id){
         if(booking::where('fb_airline_name_id',$id)->count() >= 1){
                   return Redirect::route('view-airline')->with('error_message','You can not delete this record because season already in use');
               }
         airline::destroy('id','=',$id);
         return Redirect::route('view-airline')->with('success_message','Deleted Successfully');
    }








      public function create_payment(Request $request){
      
        if ($request->isMethod('post')) {
            $this->validate($request, ['name'  => 'required']); 
          
            payment::create(array(
                'name'  => $request->name,
                
            ));
            return Redirect::route('creat-payment')->with('success_message','Created Successfully');
        }else{
            return view('payment.create_payment')->with(['name' => '','id' => '','email' => '']);
        }
    }
    public function view_payment(Request $request){
        return view('payment.view_payment')->with('data',payment::all());   
    }

     public function update_payment(Request $request,$id){
        if ($request->isMethod('post')) {

             $validator = Validator::make($request->all(), [
               
                 'name'  => 'required'
             ]);
             if ($validator->fails()) {
                 return back()
                     ->withErrors($validator)
                     ->withInput();
             }
             payment::where('id','=', $id)->update(
                 array(
                   
                     'name'  => $request->name
                 ));
             return Redirect::route('view-payment')->with('success_message','update Successfully');
        }else{
            return view('payment.update_payment')->with(['data' => payment::find($id),'id' => $id]);
        }
    }

       public function delete_payment($id){
          if(booking::where('fb_payment_method_id',$id)->count() >= 1){
             return Redirect::route('view-payment')->with('error_message','You can not delete this record because season already in use');
         }
         payment::destroy('id','=',$id);
         return Redirect::route('view-payment')->with('success_message','Deleted Successfully');
    }
}
