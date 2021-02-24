@extends('content_layout.default')

  @section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 style="text-align: center;margin-bottom: 20px">
        View All Bookings
        <!-- <small>advanced tables</small> -->
      </h1>

      <ol class="breadcrumb">
        <!-- <li>
          <a href="{{ URL::to('creat-season')}}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add"><span class="fa fa-plus">Add</span></a>
        </li> -->

      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        {!! Form::open(array(route('view-booking',array($book_id)),'class'=>'form-horizontal','id'=>'user_form','files'=> true,'method'=>'GET')) !!}
          <div class="col-md-2">
            {!! Form::text('form_sent_on',$form_sent_on,['autocomplete' => 'off','class'=>'form-control','id'=>'datepicker2','placeholder'=>'Form Sent On']) !!}
          </div>
          <div class="col-md-2">
            <select name="created_by" class="form-control select2">
                <option value="">Created By</option>
                @foreach($staffs as $staff)
                  <option value="{{$staff->id}}" {{$created_by==$staff->id? 'selected' : ''}}>{{$staff->name}}</option>
                @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-control dropdown_value select2" name="ref_no">
               <option value="">Select Reference Number</option>
               @foreach($get_refs as $get_ref)
               <option value="{{ $get_ref }}" {{$ref_no==$get_ref? 'selected' : ''}}>{{ $get_ref }}</option>
               @endforeach
            </select>
          </div>
          <div class="col-md-2" style="margin-bottom: 25px">
            {!! Form::text('date_of_travel',$date_of_travel,['autocomplete' => 'off','class'=>'form-control','id'=>'datepicker','placeholder'=>'Departure Date']) !!}
          </div>
          <div class="col-md-2">
            <select class="form-control select2" name="brand_name">
               <option value="">Select Brand</option>
               @foreach($get_user_branches->branches as $branche)
                 <option value="{{ $branche->name }}" {{$brand_name==$branche->name? 'selected' : ''}}>{{ $branche->name }}</option>
               @endforeach
            </select>
          </div>
      
          <div class="col-md-2" style="margin-bottom: 25px">
               <select class="form-control dropdown_value" name="season_id">
                 <option value="">Select Season</option>
                 @foreach($seasons as $sess)
                 <option value="{{ $sess->id }}" {{$session_id==$sess->id? 'selected' : ''}}>{{ $sess->name }}</option>
                 @endforeach
               </select>
          </div>
          
          <div class="col-md-2" style="margin-bottom: 25px;">
            {!! Form::text('created_at',$created_at,['autocomplete' => 'off','class'=>'form-control','id'=>'datepicker3','placeholder'=>'Created On']) !!}
          </div>

          
          <div class="col-md-2" style="margin-bottom: 25px;">
            <select class="form-control select2" name="type_of_holidays">
               <option value="">Select Holidays</option>
               @foreach($get_holiday_type->holiday_type as $holiday)
                 <option value="{{ $holiday->name }}" {{$type_of_holidays==$holiday->name ? 'selected' : ''}}>{{ $holiday->name }}</option>
               @endforeach
            </select>
          </div>
          <div class="col-md-2" style="margin-bottom: 25px;">
            <select class="form-control select2" name="fb_payment_method_id">
               <option value="">Select Payment Method</option>
               @foreach($payment as $payment)
                 <option value="{{ $payment->id }}" {{$fb_payment_method_id==$payment->id ? 'selected' : ''}} >{{ $payment->name }}</option>
               @endforeach
            </select>
          </div>
           <div class="col-md-2" style="margin-bottom: 25px;">
            <select class="form-control select2" name="fb_airline_name_id">
               <option value="">Select Airline Name</option>
               @foreach($airline as $airline)
                 <option value="{{ $airline->id }}" {{$fb_airline_name_id==$airline->id ? 'selected' : ''}} >{{ $airline->name }}</option>
               @endforeach
            </select>
          </div>

            <div class="col-md-2" style="margin-bottom: 25px;">
            <select class="form-control select2" name="fb_responsible_person">
               <option value="">Flight Booked Responsible Person</option>
           @foreach($staffs as $staff)
                  <option value="{{$staff->id}}" {{$fb_responsible_person==$staff->id? 'selected' : ''}}>{{$staff->name}}</option>
                @endforeach
            </select>
          </div>
           <div class="col-md-2" style="margin-bottom: 25px;">
            <select class="form-control select2" name="ti_responsible_person">
               <option value="">Transfer Info Responsible Person</option>
            @foreach($staffs as $staff)
                  <option value="{{$staff->id}}" {{$ti_responsible_person==$staff->id? 'selected' : ''}}>{{$staff->name}}</option>
                @endforeach
            </select>
          </div>
           <div class="col-md-2">
            <select class="form-control select2" name="to_responsible_person">
               <option value="">Transfer Organized Responsible Person</option>
            @foreach($staffs as $staff)
                  <option value="{{$staff->id}}" {{$to_responsible_person==$staff->id? 'selected' : ''}}>{{$staff->name}}</option>
                @endforeach
            </select>
          </div>
           <div class="col-md-2">
            <select class="form-control select2" name="itf_responsible_person">
               <option value="">Itinerary Finalised Responsible Person</option>
            @foreach($staffs as $staff)
                  <option value="{{$staff->id}}" {{$itf_responsible_person==$staff->id? 'selected' : ''}}>{{$staff->name}}</option>
                @endforeach
            </select>
          </div>
           <div class="col-md-2">
            <select class="form-control select2" name="dp_responsible_person">
               <option value="">Travel Document Prepared Responsible Person</option>
           @foreach($staffs as $staff)
                  <option value="{{$staff->id}}" {{$dp_responsible_person==$staff->id? 'selected' : ''}}>{{$staff->name}}</option>
                @endforeach
            </select>
          </div>
           <div class="col-md-2">
            <select class="form-control select2" name="ds_responsible_person">
               <option value="">Document Sent Responsible Person</option>
            @foreach($staffs as $staff)
                  <option value="{{$staff->id}}" {{$ds_responsible_person==$staff->id? 'selected' : ''}}>{{$staff->name}}</option>
                @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-control select2" name="pax_no" style="width: 189px">
               <option value="">Pax No</option>
                @for($i=1;$i<=30;$i++)
                  <option value="{{$i}}" {{$pax_no==$i? 'selected' : ''}} >{{$i}}</option>
                @endfor
            </select>
          </div>

          

           <div class="col-md-2" style="margin-top: 25px">
              <label for="inputEmail3" class="">Ask For Transfer</label>
              {!! Form::radio('asked_for_transfer_details', 'yes',$asked_for_transfer_details == 'yes' ? true : null,['id' => 'fb_yes']) !!}&nbsp<label>Yes</label>
              {!! Form::radio('asked_for_transfer_details', 'no',$asked_for_transfer_details == 'no' ? true : null,['id' => 'fb_no']) !!}&nbsp<label>No</label>
          </div>
            <div class="col-md-2" style="margin-top: 25px">
              <label for="inputEmail3" class="">Transfer Organized</label>
              {!! Form::radio('transfer_organised', 'yes',$transfer_organised == 'yes' ? true : null,['id' => 'fb_yes']) !!}&nbsp<label>Yes</label>
              {!! Form::radio('transfer_organised', 'no',$transfer_organised == 'no' ? true : null,['id' => 'fb_no']) !!}&nbsp<label>No</label>
            </div>
              <div class="col-md-2" style="margin-top: 25px">
              <label for="inputEmail3" class="">Itinerary Finalised</label>
              {!! Form::radio('itinerary_finalised', 'yes',$itinerary_finalised == 'yes' ? true : null,['id' => 'fb_yes']) !!}&nbsp<label>Yes</label>
              {!! Form::radio('itinerary_finalised', 'no',$itinerary_finalised == 'no' ? true : null,['id' => 'fb_no']) !!}&nbsp<label>No</label>
            </div>
            <div class="col-md-2" style="margin-top: 25px">
                <label for="inputEmail3" class="">Agency Booking</label>
                {!! Form::radio('agency_booking', 2,$agency_booking == 2 ? true : null,['id' => 'ab_yes']) !!}&nbsp<label for="ab_yes">Agency</label>
                {!! Form::radio('agency_booking', 1,$agency_booking == 1 ? true : null,['id' => 'ab_no']) !!}&nbsp<label for="ab_no">Client</label>
            </div>

            <div class="col-md-2" style="margin-top: 25px">
                <label for="inputEmail3" class="">Flight Booked</label>
                {!! Form::radio('flight_booked', 'yes',$flight_booked == 'yes' ? true : null,['id' => 'fb_yes']) !!}&nbsp<label for="fb_yes">Yes</label>
                {!! Form::radio('flight_booked', 'no',$flight_booked == 'no' ? true : null,['id' => 'fb_no']) !!}&nbsp<label for="fb_no">No</label>
            </div>

          
          <div class="col-md-2" style="margin-top: 25px">
            <span class="input-group-btn">
              <button style="margin-right: 10px" class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button> 
              <a class="btn btn-default" id="reset"><span class="fa fa-refresh"></span></a>
            </span>
          </div>
        <div class="col-xs-6 col-xs-offset-3">
          @if(Session::has('success_message'))
              <div style="text-align: center;" class="alert alert-success">{{Session::get('success_message')}}</div>
          @endif
          @if(Session::has('error_message'))
              <div style="text-align: center;" class="alert alert-danger">{{Session::get('error_message')}}</div>
          @endif
        </div>  
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3> Total Records ({{$data->total()}})</h3>
              <div class="col-xs-4" style="padding-left:0;">  
                <div class="dropdown">
                  <select name="action" id="action" class="form-control" style="width:30%;">
                     <option value="0">Action</option>
                     <option value="delete">Delete</option>
                  </select>
                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="overflow-x: scroll;">

            {!! Form::open(array('route' => array('del-multi-booking',$book_id),'class'=>'form-horizontal','id'=>'del-multi-booking','method'=>'POST')) !!}
            <input type="hidden" name="set_action" value="" id="set_action">
             <table id="example1" class="table table-bordered table-striped dataTable no-footer" role="grid">
                <thead>
                <tr>
                  <th style="width:1%">
                     <input id="select_all" type='checkbox' name="" value="" />
                  </th>
                  {{-- <th>Id</th> --}}
                  <th>Form Sent On</th>
                  <th>Created By</th>
                  <th>Ref.No</th>
                  <th>Departure Date</th>
                  <th>Brand Name</th>
                  <th>Booking Season</th>
                  <th>Agency Booking</th>
                  <th>Flight Booked</th>
                  <th>Created On</th>
                  <th>Type of Holidays</th>
                  <th>Airline Name</th>
                  <th>Payment Method</th>
                  <th>Flight Booked Responsible Person</th>
                  <th>Transfer Info Responsible Person</th>
                  <th>Transfer Organized Responsible Person</th>
                  <th>Itinerary Finalised Responsible Person</th>
                  <th>Travel Document Prepared Responsible Person</th>
                  <th>Document Sent Responsible Person</th>
                  <th>Pax No</th>
                  <th>Asked For Transfer</th>
                  <th>Transfer Organised</th>
                  <th>Itinerary Finalised</th>
                  


                  

                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($data as $value)
                <tr>
                  <td>
                      <input class="checkbox multi_val" type='checkbox' name='multi_val[]' value="<?php echo "$value->id";?>" />
                  </td>
                  {{-- <td>{{ $value->id }}</td> --}}
                  <td>{{\Carbon\Carbon::parse($value->form_sent_on)->format('d-m-Y')}}</td>
                  <td>{{$value->username}}</td>
                  <td>{{$value->ref_no}}</td>
                  <td>{{\Carbon\Carbon::parse($value->date_of_travel)->format('d-m-Y')}}</td>
                  <td>{{$value->brand_name}}</td>
                  <td>{{$value->name}}</td>
                  <td>
                    @if($value->agency_booking == 1)
                      Client
                    @else
                      Agency
                    @endif
                  </td>
                  <td>{{$value->flight_booked}}</td>
                  <td>{{\Carbon\Carbon::parse(substr($value->created_at,0,10))->format('d-m-Y') }}</td>
                  <td>{{$value->type_of_holidays}}</td>
                   <td>{{$value->airline_name}}</td>
                    <td>{{$value->payment_name}}</td>
                   <td>{{$value->fbusername}}</td>
                    <td>{{$value->tiusername}}</td>
                    <td>{{$value->tousername}}</td>
                    <td>{{$value->itfusername}}</td>
                    <td>{{$value->tdpusername}}</td>
                    <td>{{$value->dsusername}}</td>
                    <td>{{$value->pax_no}}</td>
                    <td>{{$value->asked_for_transfer_details}}</td>
                    <td>{{$value->transfer_organised}}</td>
                    <td>{{$value->itinerary_finalised}}</td>



                  <td>
                    <a href="{{ route('update-booking',$value->id)}}" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="left" title="Edit"><span class="fa fa-pencil"></span></a>
                    <a onclick="return confirm('Are you sure want to this');" href="{{ route('del-booking',array($book_id,$value->id))}}" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="right" data-title="Delete" data-target="#edit"><span class="fa fa-remove"></span></a>
                  </td>
                </tr>
                @endforeach
                </tbody>
              </table>
            </form>
              <div style="float:right">
                {{ $data->links() }}
              </div>
              <div style="margin-top:30px">
                Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} entries
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
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

<!-- jQuery 2.2.3 -->

{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
<!-- Bootstrap 3.3.6 -->
{!! HTML::script('bootstrap/js/bootstrap.min.js') !!}
<!-- DataTables -->
{!! HTML::script('plugins/daterangepicker/moment.min.js') !!}
{!! HTML::script('plugins/daterangepicker/daterangepicker.js') !!}

{!! HTML::script('plugins/datatables/jquery.dataTables.min.js') !!}
{!! HTML::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
<!-- SlimScroll -->
{!! HTML::script('plugins/slimScroll/jquery.slimscroll.min.js') !!}
<!-- FastClick -->
{!! HTML::script('plugins/select2/select2.full.min.js') !!}
{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}
<!-- page script -->
<script>
 $("#select_all").change(function(){  //"select all" change 
      var status = this.checked; // "select all" checked status
      $('.checkbox').each(function(){ //iterate all listed checkbox items
          this.checked = status; //change ".checkbox" checked status
      });
  });
 document.getElementById('action').onchange = function(){
   var id = this.value;
   if(id != 0){
     $('#set_action').val(id);
     if($('.multi_val:checkbox:checked').length == 0){
        alert('Please select at least one checkbox')
        return false;
     }
     $('#del-multi-booking').submit();
   }
 };
 $(function() {
   $('#datepicker3').daterangepicker({
       autoUpdateInput: false,
       locale: {
           cancelLabel: 'Clear'
       }
   });
   $('#datepicker3').on('apply.daterangepicker', function(ev, picker) {
       $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
   });

   $('#datepicker3').on('cancel.daterangepicker', function(ev, picker) {
       $(this).val('');
   });

   $('#datepicker2').daterangepicker({
       autoUpdateInput: false,
       locale: {
           cancelLabel: 'Clear'
       }
   });
   $('#datepicker2').on('apply.daterangepicker', function(ev, picker) {
       $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
   });

   $('#datepicker2').on('cancel.daterangepicker', function(ev, picker) {
       $(this).val('');
   });


   $('#datepicker').daterangepicker({
       autoUpdateInput: false,
       locale: {
           cancelLabel: 'Clear'
       }
   });
   $('#datepicker').on('apply.daterangepicker', function(ev, picker) {
       $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
   });

   $('#datepicker').on('cancel.daterangepicker', function(ev, picker) {
       $(this).val('');
   });

 });
 /*$('#datepicker3').daterangepicker({
   autoclose: true,
   autoUpdateInput: true,  
 });
 $('#datepicker3').val('');
 $('#datepicker2').daterangepicker({
   autoclose: true,
   autoUpdateInput: true,  
 });
 $('#datepicker2').val('');
 $('#datepicker').daterangepicker({
   autoclose: true,
   autoUpdateInput: true,  
 });
 $('#datepicker').val('');*/
 $('.select2').select2();
 $('#reset').click(function(event) {
   window.location.href = '{{route('view-booking',$book_id)}}';
 });
 $(function () {
   $('#example1').DataTable({
     "paging": false,
     "lengthChange": false,
     "searching": false,
     "ordering": true,
     "info": false,
     "autoWidth": false
   });
 });
</script>
</body>
</html>
@endsection