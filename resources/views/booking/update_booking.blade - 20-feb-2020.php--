@extends('content_layout.default')

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Update Booking
      </h1>
      <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol> -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div id="divLoading"></div>
      <div class="row">
        <!-- left column -->
        
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Update Booking</h3>
            </div>
            <div class="col-sm-6 col-sm-offset-3" style="text-align: center;">
              @if(Session::has('success_message'))
                  <div class="alert alert-success">{{Session::get('success_message')}}</div>
              @endif
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(array('route'=> array('update-booking',$id),'class'=>'form-horizontal','id'=>'user_form')) !!}
              <div class="box-body">
                  <div class="col-sm-5 col-sm-offset-1">
                    <label for="inputEmail3" class="">Enter Reference Number</label>
                    <!-- <div class="input-group">
                       <span class="input-group-addon"></span>
                       {{-- {!! Form::text('ref_no',null,['class'=>'form-control','placeholder'=>'Enter Reference Number','required'=>'true']) !!} --}}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('ref_no')}}</div> -->
                       <select class="form-control dropdown_value select2" name="ref_no" required="required">
                          @foreach($get_refs as $get_ref)
                          <option value="{{ $get_ref }}" @if($get_ref == $record->ref_no){{'selected'}}@endif>{{ $get_ref }}</option>
                          @endforeach
                       </select>
                       <span id="link"></span>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('ref_no')}}</div>
                  </div>

                  <div class="col-sm-5" style="margin-bottom:25px">
                    <label class="">Brand Name</label>
                       <select class="form-control dropdown_value" name="brand_name" required="required">
                          <option value="">Select Brand</option>
                          @foreach($get_user_branches->branches as $branche)
                            <option value="{{ $branche->name }}" @if($branche->name == $record->brand_name){{'selected'}}@endif>{{ $branche->name }}</option>
                          @endforeach
                       </select>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('brand_name')}}</div>
                  </div>
                  
                   <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:25px">
                     <label class="">Type Of Holidays</label>
                        <select class="form-control dropdown_value" name="type_of_holidays" required="required">
                          <option value="">Select Holiday</option>
                          <option value="Deluxe" @if('Deluxe' == $record->type_of_holidays){{'selected'}}@endif>Deluxe</option>
                          <option value="Superior" @if('Superior' == $record->type_of_holidays){{'selected'}}@endif>Superior</option>
                          <option value="Signature" @if('Signature' == $record->type_of_holidays){{'selected'}}@endif>Signature</option>
                        </select>
                     <div class="alert-danger" style="text-align:center">{{$errors->first('type_of_holidays')}}</div>
                   </div>

                  <div class="col-sm-5" style="margin-bottom:25px">
                    <label class="">Sales Person</label>
                       <select class="form-control select2" name="sale_person" required="required">
                         <option value="">Select Person</option>
                         @foreach($get_user_branches->users as $user)
                           <option value="{{ $user->email }}" @if($user->email == $record->sale_person){{'selected'}}@endif>{{ $user->email }}</option>
                         @endforeach
                       </select>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('sale_person')}}</div>
                  </div>

                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:25px">
                    <label class="">Booking Season</label>
                       <select class="form-control dropdown_value" name="season_id" required="required">
                         <option value="">Select Season</option>
                         @foreach($seasons as $sess)
                         <option value="{{ $sess->id }}" @if($sess->id == $record->season_id){{'selected'}}@endif>{{ $sess->name }}</option>
                         @endforeach
                       </select>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('season_id')}}</div>
                  </div>

                  <div class="col-sm-5" style="margin-bottom: 35px;">
                      <label for="inputEmail3" class="">Agency Booking</label><br>
                      {!! Form::radio('agency_booking', 2,$record->agency_booking == 2 ? true : null,['id' => 'ab_yes']) !!}&nbsp<label for="ab_yes">Yes</label>
                      {!! Form::radio('agency_booking', 1,$record->agency_booking == 1 ? true : null,['id' => 'ab_no']) !!}&nbsp<label for="ab_no">No</label>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('agency_booking')}}</div>
                  </div>
                  
                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:25px">
                    <label class="">PAX NO</label>
                      <select class="form-control dropdown_value select2" name="pax_no" required="required">
                        @for($i=1;$i<=30;$i++)
                        <option value="{{$i}}" @if($i == $record->pax_no){{'selected'}}@endif>{{$i}}</option>
                        @endfor
                      </select>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('pax_no')}}</div>
                  </div>

                  <div class="col-sm-5" style="margin-bottom: 25px;">
                    <label for="inputEmail3" class="">Departure Date</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::text('date_of_travel',\Carbon\Carbon::parse($record->date_of_travel)->format('m/d/Y'),['class'=>'form-control','id'=>'datepicker','placeholder'=>'Departure Date','required'=>'true']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('date_of_travel')}}</div>
                  </div>
                  {{-- flight booked condition --}}
                  <div class="col-sm-5 col-sm-offset-1">
                     <label class="">Email finance to enter flight purchase details 
                       <input type='checkbox' name="email_finance" value="1" @if($record->email_finance == 1){{'checked'}} @endif style="margin:5px"/>
                     </label>
                     <div class="alert-danger" style="text-align:center">{{$errors->first('email_finance')}}</div>
                  </div>

                  <div class="col-sm-5">
                    <label for="inputEmail3" class="">Form Sent On</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::text('form_sent_on',\Carbon\Carbon::parse($record->form_sent_on)->format('m/d/Y'),['class'=>'form-control','id'=>'datepicker2','placeholder'=>'','required'=>'true']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('form_sent_on')}}</div><br>
                    <span id="form_received" style="color: #3c8dbc"></span>
                    <input id="form_received_on" type="hidden" name="form_received_on" value="{{$record->form_received_on}}">
                  </div>

                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom: 25px;">
                    <label for="inputEmail3" class="">Destination</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::text('destination',$record->destination,['autocomplete' => 'off','class'=>'form-control','id'=>'destination','placeholder'=>'Destination','required'=>'true']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('destination')}}</div>
                  </div>
                  <div class="col-sm-5" style="margin-bottom: 85px;">
                  </div>
                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:25px">
                      <label for="inputEmail3" class="">Flight Booked</label><br>
                     {!! Form::radio('flight_booked', 'yes',$record->flight_booked == 'yes' ? true : null,['id' => 'fb_yes']) !!}&nbsp<label for="fb_yes">Yes</label>
                      {!! Form::radio('flight_booked', 'no',$record->flight_booked == 'no' ? true : null,['id' => 'fb_no']) !!}&nbsp<label for="fb_no">No</label>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('flight_booked')}}</div>
                  </div>
                  
                  
                  

                  <div class="col-sm-5 flight_booking_details" style="display: none;">
                    <label for="inputEmail3" class="">Flight Booking Details</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::textarea('flight_booking_details', $record->flight_booking_details,['class'=>'form-control','placeholder'=>'Flight Booking Detail','style'=>'height:80px']) !!} 
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('flight_booking_details')}}</div>
                  </div>
                  {{-- end flight condition --}}

                  {{-- Asked For Transfer Details --}}
                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:75px">
                      <label for="inputEmail3" class="">Asked For Transfer</label><br>
                      {!! Form::radio('asked_for_transfer_details', 'yes',$record->asked_for_transfer_details == 'yes' ? true : null,['id' => 'td_yes']) !!}&nbsp<label for="td_yes">Yes</label>
                      {!! Form::radio('asked_for_transfer_details', 'no',$record->asked_for_transfer_details == 'no' ? true : null,['id' => 'td_no']) !!}&nbsp<label for="td_no">No</label>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('asked_for_transfer_details')}}</div>
                  </div>

                  <div class="col-sm-5 transfer_details" style="margin-bottom:25px;display: none;">
                    <label for="inputEmail3" class="">Asked For Transfer Details</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::textarea('transfer_details', $record->transfer_details,['class'=>'form-control','placeholder'=>'Asked For Transfer Details','style'=>'height:80px']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('transfer_details')}}</div>
                  </div>
                  {{-- end Transfer condition --}}
                  
                  
                  

                  {{-- Transfer Info Received --}}
                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:25px">
                      <label for="inputEmail3" class="">Transfer Info Received</label><br>
                      {!! Form::radio('transfer_info_received', 'yes',$record->transfer_info_received == 'yes' ? true : null,['id' => 'tir_yes']) !!}&nbsp<label for="tir_yes">Yes</label>
                      {!! Form::radio('transfer_info_received', 'no',$record->transfer_info_received == 'no' ? true : null,['id' => 'tir_no']) !!}&nbsp<label for="tir_no">No</label>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('transfer_info_received')}}</div>
                  </div>

                  <div class="col-sm-5 transfer_info_details" style="display: none;">
                    <label for="inputEmail3" class="">Transfer Info Details</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::textarea('transfer_info_details', $record->transfer_info_details,['class'=>'form-control','placeholder'=>'Transfer Info Details','style'=>'height:80px']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('transfer_info_details')}}</div>
                  </div>
                  {{-- end Transfer condition --}}
                  

                  {{-- Itinerary finalised --}}
                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:50px">
                      <label for="inputEmail3" class="">Itinerary Finalised</label><br>
                      {!! Form::radio('itinerary_finalised', 'yes',$record->itinerary_finalised == 'yes' ? true : null,['id' => 'itf_yes']) !!}&nbsp<label for="itf_yes">Yes</label>
                      {!! Form::radio('itinerary_finalised', 'no',$record->itinerary_finalised == 'no' ? true : null,['id' => 'itf_no']) !!}&nbsp<label for="itf_no">No</label>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('itinerary_finalised')}}</div>
                  </div>

                  <div class="col-sm-5 itinerary_finalised_details" style="display: none;">
                    <label for="inputEmail3" class="">Itinerary Finalised Details</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::textarea('itinerary_finalised_details', $record->itinerary_finalised_details,['class'=>'form-control','placeholder'=>'Itinerary Finalised Details','style'=>'height:80px']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('itinerary_finalised_details')}}</div>
                  </div>
                  {{-- end Itinerary finalised --}}

                  {{-- Document Sent --}}
                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:50px">
                      <label for="inputEmail3" class="">Document Sent</label><br>
                      {!! Form::radio('documents_sent', 'yes',$record->documents_sent == 'yes' ? true : null,['id' => 'ds_yes']) !!}&nbsp<label for="ds_yes">Yes</label>
                      {!! Form::radio('documents_sent', 'no',$record->documents_sent == 'no' ? true : null,['id' => 'ds_no']) !!}&nbsp<label for="ds_no">No</label>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('documents_sent')}}</div>
                  </div>

                  <div class="col-sm-5 documents_sent_details" style="display: none;">
                    <label for="inputEmail3" class="">Document Details</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::textarea('documents_sent_details', $record->documents_sent_details,['class'=>'form-control','placeholder'=>'Document Details','style'=>'height:80px']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('documents_sent_details')}}</div>
                  </div>
                  {{-- end Document Sent --}}

                  <!-- <div class="col-sm-5 col-sm-offset-1">
                    <label for="inputEmail3" class="">Details</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::textarea('details', null,['class'=>'form-control','placeholder'=>'Details','style'=>'height:80px']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('details')}}</div>
                  </div> -->

                  {{-- Electronic Copy Sent --}}
                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom: 50px">
                      <label for="inputEmail3" class="">App login Sent</label><br>
                      {!! Form::radio('electronic_copy_sent', 'yes',$record->electronic_copy_sent == 'yes' ? true : null,['id' => 'ecs_yes']) !!}&nbsp<label for="ecs_yes">Yes</label>
                      {!! Form::radio('electronic_copy_sent', 'no',$record->electronic_copy_sent == 'no' ? true : null,['id' => 'ecs_no']) !!}&nbsp<label for="ecs_no">No</label>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('electronic_copy_sent')}}</div>
                      <span id="app_login_detail" style="color: #3c8dbc"></span>
                      <input id="app_login_date" type="hidden" name="app_login_date" value="{{$record->app_login_date}}">
                  </div>

                  <div class="col-sm-5 electronic_copy_details" style="display: none;">
                    <label for="inputEmail3" class="">App login Sent Details</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::textarea('electronic_copy_details', $record->electronic_copy_details,['class'=>'form-control','placeholder'=>'Electronic Copy Details','style'=>'height:80px']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('electronic_copy_details')}}</div>
                  </div>
                  {{-- end Electronic Copy Sent --}}

                  {{-- Transfer Organised --}}
                  <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:50px">
                      <label for="inputEmail3" class="">Transfer Organised</label><br>
                      {!! Form::radio('transfer_organised', 'yes',$record->transfer_organised == 'yes' ? true : null,['id' => 'tro_yes']) !!}&nbsp<label for="tro_yes">Yes</label>
                      {!! Form::radio('transfer_organised', 'no',$record->transfer_organised == 'no' ? true : null,['id' => 'tro_no']) !!}&nbsp<label for="tro_no">No</label>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('transfer_organised')}}</div>
                  </div>

                  <div class="col-sm-5 transfer_organised_details" style="display: none;">
                    <label for="inputEmail3" class="">Transfer Organised Details</label>
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       {!! Form::textarea('transfer_organised_details', $record->transfer_organised_details,['class'=>'form-control','placeholder'=>'Transfer Organised Details','style'=>'height:80px']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('transfer_organised_details')}}</div>
                  </div>

                  <div class="col-sm-10 col-sm-offset-1" style="margin-bottom:49px">
                     <label class="">Deposit Received
                       <input type='checkbox' name="deposit_received" value="1" @if($record->deposit_received == 1){{'checked'}} @endif style="margin:5px"/>
                     </label>
                     <div class="alert-danger" style="text-align:center">{{$errors->first('deposit_received')}}</div>

                     <label class="">Remaining Amount Received
                       <input type='checkbox' name="remaining_amount_received" value="1" @if($record->remaining_amount_received == 1){{'checked'}} @endif style="margin:5px"/>
                     </label>
                     <div class="alert-danger" style="text-align:center">{{$errors->first('remaining_amount_received')}}</div>
                  </div>
                  <input type="hidden" name="finance_detail" value="{{$record->finance_detail}}" id="finance_detail">
                  {{-- end Transfer Organised --}}
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <!-- <button type="submit" class="btn btn-info pull-right">Sign in</button> -->
                {!! Form::submit('submit',['class'=>'btn btn-info pull-right']) !!}
              </div>
              <!-- /.box-footer -->
            </form>
            <div class="col-sm-10 col-sm-offset-1">
              <h1 style="text-align: center;">Finance Detail</h1>
              <table id="example2" class="table table-bordered table-striped dataTable no-footer" role="grid">
                <thead>
                  <tr>
                    <th>Travel Specialist</th>
                    <th>Departure Date</th>
                    <th>Passenger Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Holiday Amount</th>
                  </tr>
                </thead>
                <tbody>
                  {!!$record->finance_detail!!}
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.box -->
          <!-- general form elements disabled -->
          
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <!-- <b>Version</b> 2.3.7 -->
    </div>
    {{-- Copyright © 2017-2018 Almuftionline .Design & Developed by <a href="http://www.webfluorescent.com//">WebFluorescent </a> --}}
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Recent Activity</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-birthday-cake bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                <p>Will be 23 on April 24th</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-user bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                <p>New phone +1(800)555-1234</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                <p>nora@example.com</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-file-code-o bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                <p>Execution time 5 seconds</p>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="label label-danger pull-right">70%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Update Resume
                <span class="label label-success pull-right">95%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Laravel Integration
                <span class="label label-warning pull-right">50%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Back End Framework
                <span class="label label-primary pull-right">68%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Some information about this general settings option
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Allow mail redirect
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Other sets of options are available
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Expose author name in posts
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Allow the user to show his name in blog posts
            </p>
          </div>
          <!-- /.form-group -->

          <h3 class="control-sidebar-heading">Chat Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Show me as online
              <input type="checkbox" class="pull-right" checked>
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Turn off notifications
              <input type="checkbox" class="pull-right">
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Delete chat history
              <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
            </label>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}

<!-- jQuery 2.2.3 -->
{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
<!-- Bootstrap 3.3.6 -->
{!! HTML::script('bootstrap/js/bootstrap.min.js') !!}

{!! HTML::script('plugins/datepicker/bootstrap-datepicker.js') !!}

{!! HTML::script('plugins/datatables/jquery.dataTables.min.js') !!}
{!! HTML::script('plugins/datatables/dataTables.bootstrap.min.js') !!}

{!! HTML::script('plugins/select2/select2.full.min.js') !!}

<!-- FastClick -->
{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}
<script>
  var date = new Date();
  date.setDate(date.getDate()+1);
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2();
    //Date picker
        $('#datepicker').datepicker({
          autoclose: true,
          startDate: date
        });
        $('#datepicker2').datepicker({
          autoclose: true,
          // startDate: date
        })
  })
</script>

<script type="text/javascript">
  function submitForm(btn) {
      // disable the button
      btn.disabled = true;
      // submit the form    
      btn.form.submit();
  }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('select[name="ref_no"]').on('change', function() {
            //
            $('#link').html('');
            $('#form_received').html('');
            $('#app_login_date').val('0000-00-00');
            $('#form_received_on').val('0000-00-00');
            $('select[name="sale_person"]').val('').trigger('change');
            $('select[name="brand_name"]').val('');
            $('input:radio[name=agency_booking]').prop('checked', false); 
            $('input[name="date_of_travel"]').datepicker("setDate" ,'');
            $('input[name="form_sent_on"]').datepicker("setDate" ,'');
            //
            book_id = $(this).val();
            if(book_id) {
              token = $('input[name=_token]').val();
              data  = {id: book_id};
              url   = '{{route('get-ref-detail')}}';
                $.ajax({
                    url: url,
                    headers: {'X-CSRF-TOKEN': token},
                    data : data,
                    beforeSend: function() {
                            $("#divLoading").addClass('show');
                        },
                    type: 'POST',
                    dataType: "json",
                    success:function(data) {
                        if(data.item_rec != ''){
                          $('select[name="brand_name"]').val(data.item_rec.branch_name);
                          $('select[name="sale_person"]').val(data.item_rec.created_by).trigger('change');
                          // $('select[name="brand_name"]').empty();
                          // $('select[name="brand_name"]').append('<option value="'+ data.item_rec.branch_name +'">'+ data.item_rec.branch_name +'</option>');
                           $('input:radio[name=agency_booking]').filter('[value='+data.item_rec.client_type+']').prop('checked', true);
                           //
                           if("departure_date" in data.item_rec){
                            var year  = data.item_rec.departure_date.substr(0, 4);
                            var month = data.item_rec.departure_date.substr(5, 2);
                            var day   = data.item_rec.departure_date.substr(8, 2);
                            var convert_date = month+'/'+day+'/'+year;
                            $('input[name="date_of_travel"]').datepicker("setDate" , convert_date);
                          }
                           //
                            var employee_data = '';
                            employee_data += '<tr>';
                            employee_data += '<td>'+data.item_rec.travel_specialist+'</td>';
                            employee_data += '<td>'+data.item_rec.departure_date+'</td>';
                            employee_data += '<td>'+data.item_rec.passenger_name+'</td>';
                            employee_data += '<td>'+data.item_rec.remaining_amount+'</td>';
                            employee_data += '<td>'+data.item_rec.status_name+'</td>';
                            employee_data += '<td>'+data.item_rec.holiday_amount+'</td>';
                            employee_data += '<tr>';

                            $("#example2").on("draw.dt", function () {
                                $(this).find(".dataTables_empty").parents('tbody').empty();
                              }).DataTable();
                            var table = $('#example2').DataTable();
                            table.clear().draw();
                            $('#example2').append(employee_data);  
                            $('#finance_detail').val(employee_data);    
                        }
                        if(data.item_rec2 != null){
                          if("id" in data.item_rec2){
                            var id = data.item_rec2.id;
                            $('#link').html('<strong><a href="https://unforgettabletravelcompany.com/ufg-form/user/'+id+'" target="_blank">View Reference Detail</a></strong>');
                            var detail_rec_date = data.item_rec2.detail_rec_date;
                            if(detail_rec_date == '0000-00-00'){
                              $('#form_received').html('<strong>Form Status (Pending) </strong>');
                              $('#form_received_on').val('0000-00-00');
                            }else{
                              $('#form_received').html('<strong>Received On- ' + detail_rec_date + '</strong>');
                              $('#form_received_on').val(detail_rec_date);
                            }
                          }
                          if("date" in data.item_rec2){
                            var year  = data.item_rec2.date.substr(0, 4);
                            var month = data.item_rec2.date.substr(5, 2);
                            var day   = data.item_rec2.date.substr(8, 2);
                            var convert_date = month+'/'+day+'/'+year;
                            // $('input[name="form_sent_on"]').val(convert_date);
                            $('input[name="form_sent_on"]').datepicker("setDate" , convert_date);
                          }
                        }
                        if(data.item_rec3 != null){
                          if(data.item_rec3.lognum == null){
                            $('#app_login_detail').html('<strong>Status - App login not created </strong>');
                            $('#app_login_date').val('0000-00-00');
                          }else{
                            $('input:radio[name=electronic_copy_sent]').filter('[value=yes]').prop('checked', true);
                            $('textarea[name=electronic_copy_details]').prop('required',true);
                            $('.electronic_copy_details').show(200);
                            $('#app_login_detail').html('<strong>App login created on '+ data.item_rec3.created_at.substr(0, 10) + ' - User loggedin atleast once : yes</strong>')
                            $('#app_login_date').val(data.item_rec3.created_at.substr(0, 10));
                          }
                        }
                        /*$.each(data.item_rec, function(key, value) {
                            $('select[name="brand_name"]').append('<option value="'+ value.id +'">'+ value.title +'</option>');
                        });*/
                        $("#divLoading").removeClass('show');
                    }
                });
            }else{
                // $('select[name="brand_name"]').empty();
            }
        });
    });

    $('input:radio[name=flight_booked]').click(function(){
       if($('input:radio[name=flight_booked]:checked').val() == 'yes'){
          $('.flight_booking_details').show(200);
          $('textarea[name=flight_booking_details]').prop('required',true);
       }else{
          $('.flight_booking_details').hide(200);
          $('textarea[name=flight_booking_details]').prop('required',false);
       }
    });

    $('input:radio[name=asked_for_transfer_details]').click(function(){
       if($('input:radio[name=asked_for_transfer_details]:checked').val() == 'yes'){
          $('.transfer_details').show(200);
          $('textarea[name=transfer_details]').prop('required',true);
       }else{
          $('.transfer_details').hide(200);
          $('textarea[name=transfer_details]').prop('required',false);
       }
    });

    $('input:radio[name=transfer_info_received]').click(function(){
       if($('input:radio[name=transfer_info_received]:checked').val() == 'yes'){
          $('.transfer_info_details').show(200);
          $('textarea[name=transfer_info_details]').prop('required',true);
       }else{
          $('.transfer_info_details').hide(200);
          $('textarea[name=transfer_info_details]').prop('required',false);
       }
    });

    $('input:radio[name=itinerary_finalised]').click(function(){
       if($('input:radio[name=itinerary_finalised]:checked').val() == 'yes'){
          $('.itinerary_finalised_details').show(200);
          $('textarea[name=itinerary_finalised_details]').prop('required',true);
       }else{
          $('.itinerary_finalised_details').hide(200);
          $('textarea[name=itinerary_finalised_details]').prop('required',false);
       }
    });

    $('input:radio[name=documents_sent]').click(function(){
       if($('input:radio[name=documents_sent]:checked').val() == 'yes'){
          $('.documents_sent_details').show(200);
          $('textarea[name=documents_sent_details]').prop('required',true);
       }else{
          $('.documents_sent_details').hide(200);
          $('textarea[name=documents_sent_details]').prop('required',false);
       }
    });

    $('input:radio[name=electronic_copy_sent]').click(function(){
       if($('input:radio[name=electronic_copy_sent]:checked').val() == 'yes'){
          $('.electronic_copy_details').show(200);
          $('textarea[name=electronic_copy_details]').prop('required',true);
       }else{
          $('.electronic_copy_details').hide(200);
          $('textarea[name=electronic_copy_details]').prop('required',false);
       }
    });

    $('input:radio[name=transfer_organised]').click(function(){
       if($('input:radio[name=transfer_organised]:checked').val() == 'yes'){
          $('.transfer_organised_details').show(200);
          $('textarea[name=transfer_organised_details]').prop('required',true);
       }else{
          $('.transfer_organised_details').hide(200);
          $('textarea[name=transfer_organised_details]').prop('required',false);
       }
    });

$(function () {
if( $('#form_received_on').val() == '') {
  $('#form_received').html('<strong>Form Status (Pending) </strong>');
} else if( $('#form_received_on').val() == '0000-00-00' ){
  $('#form_received').html('<strong>Form Status (Pending) </strong>');
}else{
  $('#form_received').html('<strong>Received On- ' + $('#form_received_on').val() + '</strong>');
}

//
if( $('#app_login_date').val() == '') {
  $('#app_login_detail').html('<strong>Status - App login not created </strong>');
} else if( $('#app_login_date').val() == '0000-00-00' ){
  $('#app_login_detail').html('<strong>Status - App login not created </strong>');
}else{
  $('#app_login_detail').html('<strong>App login created on '+ $('#app_login_date').val() + ' - User loggedin atleast once : yes</strong>');
}
//
if('{{$record->flight_booked == "yes"}}'){
  $('.flight_booking_details').show();
  $('textarea[name=flight_booking_details]').prop('required',true);
}
if('{{$record->asked_for_transfer_details == "yes"}}'){
  $('.transfer_details').show();
  $('textarea[name=transfer_details]').prop('required',true);
}
if('{{$record->transfer_info_received == "yes"}}'){
  $('.transfer_info_details').show();
  $('textarea[name=transfer_info_details]').prop('required',true);
}
if('{{$record->itinerary_finalised == "yes"}}'){
  $('.itinerary_finalised_details').show();
  $('textarea[name=itinerary_finalised_details]').prop('required',true);
}
if('{{$record->documents_sent == "yes"}}'){
  $('.documents_sent_details').show();
  $('textarea[name=documents_sent_details]').prop('required',true);
}
if('{{$record->electronic_copy_sent == "yes"}}'){
  $('.electronic_copy_details').show();
  $('textarea[name=electronic_copy_details]').prop('required',true);
}
if('{{$record->transfer_organised == "yes"}}'){
  $('.transfer_organised_details').show();
  $('textarea[name=transfer_organised_details]').prop('required',true);
}

$('#example2').DataTable({
 "columnDefs": [{
     "defaultContent": "-",
     "targets": "_all"
   }], 
"paging": false,
"lengthChange": false,
"searching": false,
"ordering": false,
"info": false,
"autoWidth": false
});
});

</script>




</body>
</html>
@endsection