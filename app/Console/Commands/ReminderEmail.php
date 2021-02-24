<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\booking;
use App\booking_email;
use Carbon\Carbon;

class ReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Sending email
           /*  $id = 1
             $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/1';
             //
             $template          .= "<img src='".config('app.url')."/update_email?v=". uniqid() ."&booking_email_id=".$id. "' width='1px' height='1px'>";
             //
               $data['to']        = 'haris.kalim@kingdom-vision.com';
               $data['name']      = config('app.name');
               $data['from']      = config('app.mail');
               $data['subject']   = "Form Query";
            try{
                \Mail::send("email_template.form_query", ['template'=>$template], function ($m) use ($data) {
                    $m->from($data['from'], $data['name']);
                    $m->to($data['to'])->subject($data['subject']);
                });
            }catch(Swift_RfcComplianceException $e) {
                return $e->getMessage();
            } */
        //Sending email
        
        $query = booking::where('bookings.fb_last_date','=',Carbon::now()->addHours(48)->format('Y-m-d'))->where('bookings.fb_48hr','=',NULL)->where('bookings.flight_booked','=','no')->get();
        
        foreach($query as $val){
           // email code here
                $supervisor_email = $this->get_user_email($val->user_id);

                $user_name = $this->get_user_email($val->user_id);
                $booking_email = booking_email::create(array(
                    'booking_id' => $val->id,
                    'user_id'    => $val->user_id,
                    'username'   => 'Supervisor'.$user_name->name,
                    'hour'       => '48',
                    'action'     => 'flight_booked'
                ));
                
               $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
               //
               $template          .= "<img src='".config('app.url')."/update_email?v=". uniqid() ."&booking_email_id=".$booking_email->id. "' width='1px' height='1px'>";
               //
               $data['to']        = $supervisor_email->email;
               $data['name']      = config('app.name');
               $data['from']      = config('app.mail');
               $data['subject']   = "Flight Booked Alert";
            try{
                \Mail::send("email_template.flight_booked_alert_48", ['template'=>$template], function ($m) use ($data) {
                    $m->from($data['from'], $data['name']);
                    $m->to($data['to'])->subject($data['subject']);
                });
            }catch(Swift_RfcComplianceException $e) {
                return $e->getMessage();
            }
           
            booking::where('id','=',$val->id)->update(array('fb_48hr' => 1));
        } 

        // flight booked for 24hr
        $query = booking::where('bookings.fb_last_date','=',Carbon::now()->addHours(24)->format('Y-m-d'))->where('bookings.fb_24hr','=',NULL)->where('bookings.flight_booked','=','no')->get();
        foreach($query as $val){
           // email code here
               $fb_person         = $val->fb_person != NULL ? $val->fb_person : $val->user_id; 
               $user_email        = $this->get_user_email($fb_person); 
               $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
               $data['to']        = $user_email->email;
               $data['name']      = config('app.name');
               $data['from']      = config('app.mail');
               $data['subject']   = "Flight Booked Alert";
            try{
                \Mail::send("email_template.flight_booked_alert_24", ['template'=>$template], function ($m) use ($data) {
                    $m->from($data['from'], $data['name']);
                    $m->to($data['to'])->subject($data['subject']);
                });
            }catch(Swift_RfcComplianceException $e) {
                return $e->getMessage();
            }
            $user_name = $this->get_user_email($fb_person);
            $sp_user   = $val->fb_person != NULL ? 'User' : 'Supervisor';

            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->fb_person,
                'username'   => $sp_user.$user_name->name,
                'hour'       => '24',
                'action'     => 'flight_booked'
            ));
            booking::where('id','=',$val->id)->update(array('fb_24hr' => 1));
        } 

        // flight booked for 0hr
        $query = booking::where('bookings.fb_last_date','=',Carbon::now()->addHours(0)->format('Y-m-d'))->where('bookings.fb_0hr','=',NULL)->where('bookings.flight_booked','=','no')->get();
        
        foreach($query as $val){
           // email code here
            $supervisor_email = $this->get_user_email($val->user_id); 

            if($val->fb_person != NULL ){
               $user_email  = $this->get_user_email($val->fb_person); 
               $user_email  = $user_email->email;
            }else{
              $user_email = '';
            }

              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = array($user_email,$supervisor_email->email);
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Flight Booked Alert";
           try{
               \Mail::send("email_template.flight_booked_alert_0", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }
            $user_name = $this->get_user_email($val->fb_person);
            
            if($val->fb_person != NULL ){
              booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->fb_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '0',
                'action'     => 'flight_booked'
              ));
            }
            $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '0',
                'action'     => 'flight_booked'
            ));
            booking::where('id','=',$val->id)->update(array('fb_0hr' => 1));
        } 


        // asked for transfer 48hr
        $query = booking::where('bookings.aft_last_date','=',Carbon::now()->addHours(48)->format('Y-m-d'))->where('bookings.aft_48hr','=',NULL)->where('bookings.asked_for_transfer_details','=','no')->get();
        
        foreach($query as $val){
            $supervisor_email = $this->get_user_email($val->user_id);
           // email code here
               $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
               $data['to']        = $supervisor_email->email;
               $data['name']      = config('app.name');
               $data['from']      = config('app.mail');
               $data['subject']   = "Asked For Transfer Alert";
            try{
                \Mail::send("email_template.asked_for_transfer_48", ['template'=>$template], function ($m) use ($data) {
                    $m->from($data['from'], $data['name']);
                    $m->to($data['to'])->subject($data['subject']);
                });
            }catch(Swift_RfcComplianceException $e) {
                return $e->getMessage();
            }
            $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '48',
                'action'     => 'asked_for_transfer'
            ));
            booking::where('id','=',$val->id)->update(array('aft_48hr' => 1));
        } 

        // asked for transfer 24hr
        $query = booking::where('bookings.aft_last_date','=',Carbon::now()->addHours(24)->format('Y-m-d'))->where('bookings.aft_24hr','=',NULL)->where('bookings.asked_for_transfer_details','=','no')->get();
        foreach($query as $val){
            $user_email       = $this->get_user_email($val->aft_person);
           // email code here
              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = $user_email->email;
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Asked For Transfer Alert";
           try{
               \Mail::send("email_template.asked_for_transfer_24", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }
           $user_name = $this->get_user_email($val->aft_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->aft_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '24',
                'action'     => 'asked_for_transfer'
            ));
            booking::where('id','=',$val->id)->update(array('aft_24hr' => 1));
        } 

        // asked for transfer 0hr
         

        $query = booking::where('bookings.aft_last_date','=',Carbon::now()->addHours(0)->format('Y-m-d'))->where('bookings.aft_0hr','=',NULL)->where('bookings.asked_for_transfer_details','=','no')->get();
        
        foreach($query as $val){
            $supervisor_email = $this->get_user_email($val->user_id); 
            $user_email       = $this->get_user_email($val->aft_person);
           // email code here
              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = array($supervisor_email->email,$user_email->email);
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Asked For Transfer Alert";
           try{
               \Mail::send("email_template.asked_for_transfer_0", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }
           $user_name = $this->get_user_email($val->aft_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->aft_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '0',
                'action'     => 'asked_for_transfer'
            ));
            $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '0',
                'action'     => 'asked_for_transfer'
            ));
            booking::where('id','=',$val->id)->update(array('aft_0hr' => 1));
        } 


        // asked for document 48hr
        $query = booking::where('bookings.ds_last_date','=',Carbon::now()->addHours(48)->format('Y-m-d'))->where('bookings.ds_48hr','=',NULL)->where('bookings.documents_sent','=','no')->get();
        
        foreach($query as $val){
           // email code here
           $supervisor_email = $this->get_user_email($val->user_id);
           // email code here
              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = $supervisor_email->email;
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Document Sent Alert";
           try{
               \Mail::send("email_template.document_sent_48", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }
           $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '48',
                'action'     => 'document_sent'
            ));
            booking::where('id','=',$val->id)->update(array('ds_48hr' => 1));
        } 

        // asked for document 24hr
        $query = booking::where('bookings.ds_last_date','=',Carbon::now()->addHours(24)->format('Y-m-d'))->where('bookings.ds_24hr','=',NULL)->where('bookings.documents_sent','=','no')->get();
        
        foreach($query as $val){
           // email code here
            $user_email       = $this->get_user_email($val->ds_person);
            // email code here
               $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
               $data['to']        = $user_email->email;
               $data['name']      = config('app.name');
               $data['from']      = config('app.mail');
               $data['subject']   = "Document Sent Alert";
            try{
                \Mail::send("email_template.document_sent_24", ['template'=>$template], function ($m) use ($data) {
                    $m->from($data['from'], $data['name']);
                    $m->to($data['to'])->subject($data['subject']);
                });
            }catch(Swift_RfcComplianceException $e) {
                return $e->getMessage();
            }

            $user_name = $this->get_user_email($val->ds_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->ds_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '24',
                'action'     => 'document_sent'
            ));
            booking::where('id','=',$val->id)->update(array('ds_24hr' => 1));
        } 

        // asked for document 0hr

        $query = booking::where('bookings.ds_last_date','=',Carbon::now()->addHours(0)->format('Y-m-d'))->where('bookings.ds_0hr','=',NULL)->where('bookings.documents_sent','=','no')->get();
        
        foreach($query as $val){
            $supervisor_email = $this->get_user_email($val->user_id); 
            $user_email       = $this->get_user_email($val->ds_person); 
           // email code here
              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = array($supervisor_email->email,$user_email->email);
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Document Sent Alert";
           try{
               \Mail::send("email_template.document_sent_0", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }
           $user_name = $this->get_user_email($val->ds_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->ds_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '0',
                'action'     => 'document_sent'
            ));
            $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '0',
                'action'     => 'document_sent'
            ));
            booking::where('id','=',$val->id)->update(array('ds_0hr' => 1));
        }


        // document prepare 48hr
        $query = booking::where('bookings.dp_last_date','=',Carbon::now()->addHours(48)->format('Y-m-d'))->where('bookings.dp_48hr','=',NULL)->where('bookings.document_prepare','=','no')->get();
        
        foreach($query as $val){
           // email code here
           $supervisor_email = $this->get_user_email($val->user_id); 
              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = $supervisor_email->email;
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Document Prepare Alert";
           try{
               \Mail::send("email_template.document_prepare_48", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }
           $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '48',
                'action'     => 'document_prepare'
            ));
            booking::where('id','=',$val->id)->update(array('dp_48hr' => 1));
        } 

        // document prepare 24hr
        $query = booking::where('bookings.dp_last_date','=',Carbon::now()->addHours(24)->format('Y-m-d'))->where('bookings.dp_24hr','=',NULL)->where('bookings.document_prepare','=','no')->get();
        
        foreach($query as $val){
           // email code here
              $user_email       = $this->get_user_email($val->dp_person); 
              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = $user_email->email;
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Document Prepare Alert";
           try{
               \Mail::send("email_template.document_prepare_24", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }
           $user_name = $this->get_user_email($val->dp_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->dp_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '24',
                'action'     => 'document_prepare'
            ));
            booking::where('id','=',$val->id)->update(array('dp_24hr' => 1));
        } 

        // document prepare 0hr
         

        $query = booking::where('bookings.dp_last_date','=',Carbon::now()->addHours(0)->format('Y-m-d'))->where('bookings.dp_0hr','=',NULL)->where('bookings.document_prepare','=','no')->get();
        
        foreach($query as $val){
             $supervisor_email = $this->get_user_email($val->user_id); 
             $user_email       = $this->get_user_email($val->dp_person);
           // email code here
              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = array($supervisor_email->email,$user_email->email);
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Document Prepare Alert";
           try{
               \Mail::send("email_template.document_prepare_0", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }
            $user_name = $this->get_user_email($val->dp_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->dp_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '0',
                'action'     => 'document_prepare'
            ));
            $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '0',
                'action'     => 'document_prepare'
            ));
            booking::where('id','=',$val->id)->update(array('dp_0hr' => 1));
        }

        // transfer organised 48hr
        $query = booking::where('bookings.to_last_date','=',Carbon::now()->addHours(48)->format('Y-m-d'))->where('bookings.to_48hr','=',NULL)->where('bookings.transfer_organised','=','no')->get();
        
        foreach($query as $val){
           // email code here
            $supervisor_email = $this->get_user_email($val->user_id); 

              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = $supervisor_email->email;
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Transfer Organised Alert";
           try{
               \Mail::send("email_template.transfer_organised_48", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }

           $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '48',
                'action'     => 'transfer_organised'
            ));
            booking::where('id','=',$val->id)->update(array('to_48hr' => 1));
        } 

        // transfer organised 24hr
        $query = booking::where('bookings.to_last_date','=',Carbon::now()->addHours(24)->format('Y-m-d'))->where('bookings.to_24hr','=',NULL)->where('bookings.transfer_organised','=','no')->get();
        
        foreach($query as $val){
           // email code here
            $user_email       = $this->get_user_email($val->to_person); 
               $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
               $data['to']        = $user_email->email;
               $data['name']      = config('app.name');
               $data['from']      = config('app.mail');
               $data['subject']   = "Transfer Organised Alert";
            try{
                \Mail::send("email_template.transfer_organised_24", ['template'=>$template], function ($m) use ($data) {
                    $m->from($data['from'], $data['name']);
                    $m->to($data['to'])->subject($data['subject']);
                });
            }catch(Swift_RfcComplianceException $e) {
                return $e->getMessage();
            }
            $user_name = $this->get_user_email($val->to_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->to_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '24',
                'action'     => 'transfer_organised'
            ));
            booking::where('id','=',$val->id)->update(array('to_24hr' => 1));
        } 

        // transfer organised 0hr

        $query = booking::where('bookings.to_last_date','=',Carbon::now()->addHours(0)->format('Y-m-d'))->where('bookings.to_0hr','=',NULL)->where('bookings.transfer_organised','=','no')->get();
        
        foreach($query as $val){
            $supervisor_email = $this->get_user_email($val->user_id); 
            $user_email       = $this->get_user_email($val->to_person); 
           // email code here
            $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
               $data['to']        = array($supervisor_email->email,$user_email->email);
               $data['name']      = config('app.name');
               $data['from']      = config('app.mail');
               $data['subject']   = "Transfer Organised Alert";
            try{
                \Mail::send("email_template.transfer_organised_0", ['template'=>$template], function ($m) use ($data) {
                    $m->from($data['from'], $data['name']);
                    $m->to($data['to'])->subject($data['subject']);
                });
            }catch(Swift_RfcComplianceException $e) {
                return $e->getMessage();
            }
            $user_name = $this->get_user_email($val->to_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->to_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '0',
                'action'     => 'transfer_organised'
            ));
            $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '0',
                'action'     => 'transfer_organised'
            ));
            booking::where('id','=',$val->id)->update(array('to_0hr' => 1));
        }

        // Itinerary Finalised 48hr
        $query = booking::where('bookings.itf_last_date','=',Carbon::now()->addHours(48)->format('Y-m-d'))->where('bookings.itf_48hr','=',NULL)->where('bookings.itinerary_finalised','=','no')->get();

        foreach($query as $val){
           // email code here
            $supervisor_email    = $this->get_user_email($val->user_id); 

              $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
              $data['to']        = $supervisor_email->email;
              $data['name']      = config('app.name');
              $data['from']      = config('app.mail');
              $data['subject']   = "Itinerary Finalised Alert";
           try{
               \Mail::send("email_template.itinerary_finalised_48", ['template'=>$template], function ($m) use ($data) {
                   $m->from($data['from'], $data['name']);
                   $m->to($data['to'])->subject($data['subject']);
               });
           }catch(Swift_RfcComplianceException $e) {
               return $e->getMessage();
           }

           $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '48',
                'action'     => 'itinerary_finalised'
            ));
            booking::where('id','=',$val->id)->update(array('itf_48hr' => 1));
        } 

        // Itinerary Finalised 24hr
        $query = booking::where('bookings.itf_last_date','=',Carbon::now()->addHours(24)->format('Y-m-d'))->where('bookings.itf_24hr','=',NULL)->where('bookings.itinerary_finalised','=','no')->get();

        foreach($query as $val){
           // email code here
            $user_email       = $this->get_user_email($val->itf_person); 
               $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
               $data['to']        = $user_email->email;
               $data['name']      = config('app.name');
               $data['from']      = config('app.mail');
               $data['subject']   = "Itinerary Finalised Alert";
            try{
                \Mail::send("email_template.itinerary_finalised_24", ['template'=>$template], function ($m) use ($data) {
                    $m->from($data['from'], $data['name']);
                    $m->to($data['to'])->subject($data['subject']);
                });
            }catch(Swift_RfcComplianceException $e) {
                return $e->getMessage();
            }
            $user_name = $this->get_user_email($val->itf_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->itf_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '24',
                'action'     => 'itinerary_finalised'
            ));
            booking::where('id','=',$val->id)->update(array('itf_24hr' => 1));
        } 

        // Itinerary Finalised 0hr

        $query = booking::where('bookings.itf_last_date','=',Carbon::now()->addHours(0)->format('Y-m-d'))->where('bookings.itf_0hr','=',NULL)->where('bookings.itinerary_finalised','=','no')->get();

        foreach($query as $val){
          $supervisor_email = $this->get_user_email($val->user_id); 
          $user_email       = $this->get_user_email($val->itf_person); 
           // email code here
            $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
               $data['to']        = array($supervisor_email->email,$user_email->email);
               $data['name']      = config('app.name');
               $data['from']      = config('app.mail');
               $data['subject']   = "Itinerary Finalised Alert";
            try{
                \Mail::send("email_template.itinerary_finalised_0", ['template'=>$template], function ($m) use ($data) {
                    $m->from($data['from'], $data['name']);
                    $m->to($data['to'])->subject($data['subject']);
                });
            }catch(Swift_RfcComplianceException $e) {
                return $e->getMessage();
            }
            $user_name = $this->get_user_email($val->itf_person);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->itf_person,
                'username'   => 'User'.$user_name->name,
                'hour'       => '0',
                'action'     => 'itinerary_finalised'
            ));
            $user_name = $this->get_user_email($val->user_id);
            booking_email::create(array(
                'booking_id' => $val->id,
                'user_id'    => $val->user_id,
                'username'   => 'Supervisor'.$user_name->name,
                'hour'       => '0',
                'action'     => 'itinerary_finalised'
            ));
            booking::where('id','=',$val->id)->update(array('itf_0hr' => 1));
        }
    
      $query = booking::where('bookings.fso_last_date','=',Carbon::now()->addHours(48)->format('Y-m-d'))->where('bookings.fso_48hr','=',NULL)->where('bookings.form_received_on','=',NULL)->get();

      foreach($query as $val){
         // email code here
              $supervisor_email = $this->get_user_email($val->user_id);
              $user_name        = $this->get_user_email($val->user_id);

              $booking_email = booking_email::create(array(
                  'booking_id' => $val->id,
                  'user_id'    => $val->user_id,
                  'username'   => 'Supervisor'.$user_name->name,
                  'hour'       => '48',
                  'action'     => 'form_sent_on'
              ));
              
             $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
             //
             $template          .= "<img src='".config('app.url')."/update_email?v=". uniqid() ."&booking_email_id=".$booking_email->id. "' width='1px' height='1px'>";
             //
             $data['to']        = $supervisor_email->email;
             $data['name']      = config('app.name');
             $data['from']      = config('app.mail');
             $data['subject']   = "Form Sent On Pending";
          try{
              \Mail::send("email_template.form_sent_on_48", ['template'=>$template], function ($m) use ($data) {
                  $m->from($data['from'], $data['name']);
                  $m->to($data['to'])->subject($data['subject']);
              });
          }catch(Swift_RfcComplianceException $e) {
              return $e->getMessage();
          }
         
          booking::where('id','=',$val->id)->update(array('fso_48hr' => 1));
      } 




      // flight booked for 24hr
      $query = booking::where('bookings.fso_last_date','=',Carbon::now()->addHours(24)->format('Y-m-d'))->where('bookings.fso_24hr','=',NULL)->where('bookings.form_received_on','=',NULL)->get();
      foreach($query as $val){
         // email code here
             $fso_person         = $val->fso_person != NULL ? $val->fso_person : $val->user_id; 
             $user_email        = $this->get_user_email($fso_person); 
             $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
             $data['to']        = $user_email->email;
             $data['name']      = config('app.name');
             $data['from']      = config('app.mail');
             $data['subject']   = "Form Sent On Pending";
          try{
              \Mail::send("email_template.form_sent_on_24", ['template'=>$template], function ($m) use ($data) {
                  $m->from($data['from'], $data['name']);
                  $m->to($data['to'])->subject($data['subject']);
              });
          }catch(Swift_RfcComplianceException $e) {
              return $e->getMessage();
          }
          $user_name = $this->get_user_email($fso_person);
          $sp_user   = $val->fso_person != NULL ? 'User' : 'Supervisor';

          booking_email::create(array(
              'booking_id' => $val->id,
              'user_id'    => $val->fso_person,
              'username'   => $sp_user.$user_name->name,
              'hour'       => '24',
              'action'     => 'form_sent_on'
          ));
          booking::where('id','=',$val->id)->update(array('fso_24hr' => 1));
      } 

      // flight booked for 0hr
      $query = booking::where('bookings.fso_last_date','=',Carbon::now()->addHours(0)->format('Y-m-d'))->where('bookings.fso_0hr','=',NULL)->where('bookings.form_received_on','=',NULL)->get();

      foreach($query as $val){
         // email code here
          $supervisor_email = $this->get_user_email($val->user_id); 

          if($val->fso_person != NULL ){
             $user_email  = $this->get_user_email($val->fso_person); 
             $user_email  = $user_email->email;
          }else{
            $user_email = '';
          }

            $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
            $data['to']        = array($user_email,$supervisor_email->email);
            $data['name']      = config('app.name');
            $data['from']      = config('app.mail');
            $data['subject']   = "Form Sent On Pending";
         try{
             \Mail::send("email_template.form_sent_on_0", ['template'=>$template], function ($m) use ($data) {
                 $m->from($data['from'], $data['name']);
                 $m->to($data['to'])->subject($data['subject']);
             });
         }catch(Swift_RfcComplianceException $e) {
             return $e->getMessage();
         }
          $user_name = $this->get_user_email($val->fso_person);
          
          if($val->fso_person != NULL ){
            booking_email::create(array(
              'booking_id' => $val->id,
              'user_id'    => $val->fso_person,
              'username'   => 'User'.$user_name->name,
              'hour'       => '0',
              'action'     => 'form_sent_on'
            ));
          }
          $user_name = $this->get_user_email($val->user_id);
          booking_email::create(array(
              'booking_id' => $val->id,
              'user_id'    => $val->user_id,
              'username'   => 'Supervisor'.$user_name->name,
              'hour'       => '0',
              'action'     => 'form_sent_on'
          ));
          booking::where('id','=',$val->id)->update(array('fso_0hr' => 1));
      }

      $query = booking::where('bookings.aps_last_date','=',Carbon::now()->addHours(48)->format('Y-m-d'))->where('bookings.aps_48hr','=',NULL)->where('bookings.electronic_copy_sent','=','no')->get();

      foreach($query as $val){
         // email code here
              $supervisor_email = $this->get_user_email($val->user_id);
              $user_name        = $this->get_user_email($val->user_id);

              $booking_email = booking_email::create(array(
                  'booking_id' => $val->id,
                  'user_id'    => $val->user_id,
                  'username'   => 'Supervisor'.$user_name->name,
                  'hour'       => '48',
                  'action'     => 'app_login_sent'
              ));
              
             $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
             //
             $template          .= "<img src='".config('app.url')."/update_email?v=". uniqid() ."&booking_email_id=".$booking_email->id. "' width='1px' height='1px'>";
             //
             $data['to']        = $supervisor_email->email;
             $data['name']      = config('app.name');
             $data['from']      = config('app.mail');
             $data['subject']   = "App login sent pending";
          try{
              \Mail::send("email_template.app_login_sent_48", ['template'=>$template], function ($m) use ($data) {
                  $m->from($data['from'], $data['name']);
                  $m->to($data['to'])->subject($data['subject']);
              });
          }catch(Swift_RfcComplianceException $e) {
              return $e->getMessage();
          }
         
          booking::where('id','=',$val->id)->update(array('aps_48hr' => 1));
      } 

      // flight booked for 24hr
      $query = booking::where('bookings.aps_last_date','=',Carbon::now()->addHours(24)->format('Y-m-d'))->where('bookings.aps_24hr','=',NULL)->where('bookings.electronic_copy_sent','=','no')->get();
      foreach($query as $val){
         // email code here
             $aps_person        = $val->aps_person != NULL ? $val->aps_person : $val->user_id; 
             $user_email        = $this->get_user_email($aps_person); 
             $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
             $data['to']        = $user_email->email;
             $data['name']      = config('app.name');
             $data['from']      = config('app.mail');
             $data['subject']   = "App login sent pending";
          try{
              \Mail::send("email_template.app_login_sent_24", ['template'=>$template], function ($m) use ($data) {
                  $m->from($data['from'], $data['name']);
                  $m->to($data['to'])->subject($data['subject']);
              });
          }catch(Swift_RfcComplianceException $e) {
              return $e->getMessage();
          }
          $user_name = $this->get_user_email($aps_person);
          $sp_user   = $val->aps_person != NULL ? 'User' : 'Supervisor';

          booking_email::create(array(
              'booking_id' => $val->id,
              'user_id'    => $val->aps_person,
              'username'   => $sp_user.$user_name->name,
              'hour'       => '24',
              'action'     => 'app_login_sent'
          ));
          booking::where('id','=',$val->id)->update(array('aps_24hr' => 1));
      } 

      // flight booked for 0hr
      $query = booking::where('bookings.aps_last_date','=',Carbon::now()->addHours(0)->format('Y-m-d'))->where('bookings.aps_0hr','=',NULL)->where('bookings.electronic_copy_sent','=','no')->get();

      foreach($query as $val){
         // email code here
          $supervisor_email = $this->get_user_email($val->user_id); 

          if($val->aps_person != NULL ){
             $user_email  = $this->get_user_email($val->aps_person); 
             $user_email  = $user_email->email;
          }else{
            $user_email = '';
          }

            $template          = 'https://unforgettabletravelcompany.com/unforgettable_form/public/update-booking/'.$val->id;
            $data['to']        = array($user_email,$supervisor_email->email);
            $data['name']      = config('app.name');
            $data['from']      = config('app.mail');
            $data['subject']   = "App login sent pending";
         try{
             \Mail::send("email_template.app_login_sent_0", ['template'=>$template], function ($m) use ($data) {
                 $m->from($data['from'], $data['name']);
                 $m->to($data['to'])->subject($data['subject']);
             });
         }catch(Swift_RfcComplianceException $e) {
             return $e->getMessage();
         }
          $user_name = $this->get_user_email($val->aps_person);
          
          if($val->aps_person != NULL ){
            booking_email::create(array(
              'booking_id' => $val->id,
              'user_id'    => $val->aps_person,
              'username'   => 'User'.$user_name->name,
              'hour'       => '0',
              'action'     => 'app_login_sent'
            ));
          }
          $user_name = $this->get_user_email($val->user_id);
          booking_email::create(array(
              'booking_id' => $val->id,
              'user_id'    => $val->user_id,
              'username'   => 'Supervisor'.$user_name->name,
              'hour'       => '0',
              'action'     => 'app_login_sent'
          ));
          booking::where('id','=',$val->id)->update(array('aps_0hr' => 1));
      }
    }  
    private function get_user_email($id){
            return User::select('email')->where('id', $id)->get()->first();
        }
}
