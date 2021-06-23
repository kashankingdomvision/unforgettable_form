@extends('content_layout.default')

@section('content')
<style type="text/css">
    td.day{
      position:relative;  
    }
    td.day.disabled:read-only {
        color: gray;
    }
    
    td.day{
        color: #000;
        font-weight: 700;
    }
    
  
        .qoute{
            width: 100%;
            border: solid 1px #000;
            padding: 20px;
            margin: 0 auto 15px;
            float: none;
            border-radius: 10px;
        }
    
    
        .hide-arrows::-webkit-inner-spin-button, .hide-arrows::-webkit-outer-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
    
        .hide-arrows {
            -moz-appearance:textfield !important;
        }
    </style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Edit Templates (Quote)</h1>
        <ol class="breadcrumb">
            <li>
              <a href="{{ route('template.index') }}" class="btn btn-primary btn-xs">View all template</a>
            </li>
          </ol>
    </section>
    <section class="content">
        <div id="divLoading"></div>
        <div class="row">
            <div class="col-xs-6 col-md-offset-3">
               @if(Session::has('success_message'))
                   <div class="alert alert-success" style="text-align: center;">{{Session::get('success_message')}}</div>
               @endif
               @if(Session::has('error_message'))
                   <div class="alert alert-danger" style="text-align: center;">{{Session::get('error_message')}}</div>
               @endif 
            </div>
        <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 appendColumn">
                        <form method="POST" action="{{ route('template.update', encrypt($template->id)) }}"> @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="template_title" >Template Tilte <span class="text-danger">*</span></label>
                                        <input id="template_title" class="form-control" type="text" value="{{ $template->title }}" name="template_name" placeholder="Enter the template title" required>
                                                                            <div class="alert-danger" style="text-align:center"> {{ $errors->first('template_name') }} </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bookingSeason" > Booking Season <span class="text-danger">*</span></label>
                                        <select class="form-control dropdown_value" name="season_id" id="bookingSeason"  >
                                            <option value="">Select Season</option>
                                            @foreach ($seasons as $sess)
                                                <option value="{{ $sess->id }}"  {{ $template->season_id == $sess->id? 'selected' : NULL }} >{{ $sess->name }}</option>
                                            @endforeach
                                        </select>
                                                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('season_id') }} </div>
                                    </div>
                                </div>
                            </div>
                            <div class="parent" id="parent">
                            @foreach ($template->getTemplateDetails as $key => $detail)   
                                <input type="hidden" name="quote[{{ $key }}][key]" value="{{ encrypt($detail->id) }}">                             
                                    <div class="qoute">
                                        <div class="row removeButton">
                                        @if($key > 0)
                                            <button type='button' class='remove btn btn-link pull-right'><i class='fa fa-times'  style='color:red' ></i></button>
                                        @endif
                                        </div>
                                        <div class="row" style="margin-top: 15px">
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Date of Service</label> 
                                                <div class="input-group">
                                                    <input type="text" data-name="date_of_service" name="quote[{{ $key }}][date_of_service]" value="{{ !empty($detail->date_of_service) ? date('d/m/Y', strtotime($detail->date_of_service)) : NULL }}" class="form-control datepicker checkDates bookingDateOfService" autocomplete="off" placeholder="Date of Service"  >
                                                </div>
                                                {{-- <div class="alert-danger date_of_service" style="text-align:center"></div> --}}
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Service Details</label> 
                                                <textarea name="quote[{{ $key }}][service_details]"  class="form-control" cols="30" rows="1">{{ $detail->service_details }}</textarea>
                                                {{-- <div class="alert-danger" style="text-align:center">{{ $errors->first('service_details') }}</div> --}}
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label class="">Select Category</label> 
                                                <select class="form-control category-select2" id="category-select2"  name="quote[{{ $key }}][category_id]" >
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ ($detail->category_id == $category->id)? 'selected' :(old('category') == $category->id  ? "selected" : "") }}> {{ $category->name }} </option>
                                                    @endforeach
                                                </select>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('category') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label class="test">Select Supplier</label> 
                                                <select class="form-control supplier-select2"  id="supplier-select2" name="quote[{{ $key }}][supplier_id]" >
                                                    <option value="">Select Supplier</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" {{ ($detail->supplier == $supplier->id)? 'selected': (old('supplier') == $supplier->id  ? "selected" : "") }}> {{ $supplier->name }} </option>
                                                    @endforeach
                                                </select>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('supplier') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="test222">Booking Date</label>
                                                <div class="input-group">
                                                    <input type="text" data-name="booking_date" name="quote[{{ $key }}][booking_date]" class="form-control datepicker bookingDate" placeholder="Booking Date" autocomplete="off" value="{{ !empty($detail->booking_date) ? date('d/m/Y', strtotime($detail->booking_date)) : NULL }}" >
                                                </div>
                                                <div class="alert-danger booking_date" value="" style="text-align:center"> {{ $errors->first('booking_date') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Booking Due Date <span style="color:red">*</span></label> 
                                                <div class="input-group">
                                                    <input type="text" data-name="booking_due_date"   name="quote[{{ $key }}][booking_due_date]"  value="{{ !empty($detail->booking_due_date) ? date('d/m/Y', strtotime($detail->booking_due_date)) : NULL }}""  class="form-control datepicker checkDates bookingDueDate" autocomplete="off" placeholder="Booking Due Date" >
                                                </div>
                                                <div class="alert-danger booking_due_date" style="text-align:center; width: 160px;"></div>
                                            </div>
                            
                                        </div>
                                        <div class="row" style="margin-top: 15px">
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Booking Method</label>
                                                <div class="input-group">
                                                    <select class="form-control select2"  name="quote[{{ $key }}][booking_method_id]" id="booking-method-select2" class="form-control" >
                                                        <option value="">Select Booking Method</option>
                                                        @foreach ($booking_methods as $booking_method)
                                                        <option value="{{ $booking_method->id }}" {{ ($detail->booking_method == $booking_method->id)? 'selected':($booking_method->name == 'Supplier Own' ? 'selected' : '') }}>{{$booking_method->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Booked By </label>
                                                <div class="input-group">
                                                    <select class="form-control select2"  name="quote[{{ $key }}][booked_by_id]" id="booked-by-select2" class="form-control" >
                                                        <option value="">Select Person</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{$user->id}}" {{ ($detail->booked_by == $user->id)? 'selected' :((!empty(Auth::user()->id) && Auth::user()->id == $user->id) ? 'selected' : '') }}>{{$user->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Booking Reference</label>
                                                <div class="input-group">
                                                    <input type="text" name="quote[{{ $key }}][booking_reference]" class="form-control" placeholder="Booking Reference" value="{{ $detail->booking_refrence }}" >
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> </div>
                                            </div>
                
                                            <div class="col-sm-2 " style="margin-bottom: 15px;">
                                                <label for="inputEmail3" class="">Booking Type</label> 
                                                <div class="input-group">
                                                    <select class="form-control select2" name="quote[{{ $key }}][booking_type]" >
                                                        <option value="">Select Booking Type</option>
                                                        <option {{ ($detail->booking_type == 'refundable')? 'selected': NULL }} value="refundable">Refundable</option>
                                                        <option {{ ($detail->booking_type == 'non_refundable')? 'selected': NULL }} value="non_refundable">Non-Refundable</option>
                                                    </select>
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_type') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Comments</label> 
                                                <textarea name="quote[{{ $key }}][comments]"   class="form-control" cols="30" rows="1">{{ $detail->comments }}</textarea>
                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label>Select Supplier Currency</label> 
                                                <select class="form-control supplier-currency"  name="quote[{{ $key }}][currency_id]" >
                                                    <option value="">Select Currency</option>
                                                    @foreach ($currencies as $currency)
                                                        <option value="{{ $currency->code }}"  {{ ($detail->supplier_currency == $currency->code)? 'selected': NULL }} > {{ $currency->name }} ({{ $currency->symbol }}) </option>
                                                    @endforeach
                                                </select>
                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 15px">
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Estimated Cost</label> <span style="color:red">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-addon symbol" ></span>
                                                    <input type="number" data-code=""  name="quote[{{ $key }}][cost]" class="form-control cost" min="0" value="{{ $detail->cost }}" placeholder="Cost" >
                                                </div>
                                                <div class="alert-danger error-cost" style="text-align:center" ></div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Currency Conversion</label>
                                                <label class="currency"></label>  
                                                <input type="text" class="base-currency" name="quote[{{ $key }}][currency_conversion]" readonly><br>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Added in Sage</label>
                                                <div class="input-group">
                                                    <input type="checkbox" {{ ($detail->added_in_sage == '1')? 'checked': NULL }} class="addsaga" name="quote[{{ $key }}][add_in_sag]" value="{{ ($detail->added_in_sage == '1')? 1: 0 }}">
                                                    {{-- <input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value"> --}}
                                                </div>
                                                
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Supervisor</label>
                                                <div class="input-group">
                                                    <select class="form-control supervisor-select2"  name="quote[{{ $key }}][supervisor_id]" id="supervisor-select2" class="form-control" >
                                                        <option value="">Select Supervisor</option>
                                                        @foreach ($supervisors as $supervisor)
                                                            <option value="{{$supervisor->id}}" {{ ($detail->supervisor_id == $supervisor->id)? 'selected' :((isset(Auth::user()->getSupervisor))? ((Auth::user()->getSupervisor->id == $supervisor->id)? 'selected': NULL) : NULL) }} >{{$supervisor->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> </div>
                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group pull-right" style="margin-top: 25px">
                                                <button type="button" id="createNEw" class="btn btn-info pull-right ">+ Add more </button>
                                                <button type="submit" class="btn btn-success pull-right" style="margin-right: 20px">Update Template</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- jQuery 2.2.3 -->

{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
<!-- Bootstrap 3.3.6 -->
{!! HTML::script('bootstrap/js/bootstrap.min.js') !!}
<!-- DataTables -->
{!! HTML::script('plugins/datatables/jquery.dataTables.min.js') !!}
{!! HTML::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
{!! HTML::script('plugins/datepicker/bootstrap-datepicker.js') !!}
<!-- SlimScroll -->
{!! HTML::script('plugins/slimScroll/jquery.slimscroll.min.js') !!}
{!! HTML::script('plugins/select2/select2.full.min.js') !!}
<!-- FastClick -->
{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}

<script>

function datePickerSetDate(y = 1) {
    var season_id  = $('#bookingSeason').val();
    console.log(season_id);
    var season  = {!! json_encode($seasons->toArray()) !!};
    var item = season.filter(function(a){ return a.id == season_id })[0];
    var startdate = new Date(item.start_date);
    var enddate = new Date(item.end_date);
    if(y != 1){
        $('.bookingDate:last').datepicker('remove').datepicker({  autoclose: true, format:'dd/mm/yyyy', startDate: startdate, endDate: enddate });
        $('.bookingDateOfService:last').datepicker('remove').datepicker({  autoclose: true, format:'dd/mm/yyyy',  startDate: startdate, endDate: enddate });
        $('.bookingDueDate:last').datepicker('remove').datepicker({  autoclose: true, format:'dd/mm/yyyy',  startDate: startdate, endDate: enddate });
    }else{                
        $('.datepicker').datepicker('remove').datepicker({  autoclose: true, format:'dd/mm/yyyy',  startDate: startdate, endDate: enddate});
    }
}

function convertDate(date) {
    var dateParts = date.split("/");
    return dateParts = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]);
}
    $(document).ready(function(){
        $(function(){
            datePickerSetDate();
        });
        $('.select2, .category-select2, .supplier-select2, .booking-method-select2, .booked-by-select2, .supplier-currency, .supervisor-select2, .booking-type-select2').select2();
        
        
        $('body').on('click', '#createNEw', function (e) {
            
                $(".qoute").eq(0).clone()
                    .find("input").val("") .each(function(){
                        this.name = this.name.replace(/\[(\d+)\]/, function(str,p1){                        
                            return '[' + ($('.qoute').size()) + ']';
                        });
                    }).end()
                    .find("textarea").val("").each(function(){
                        this.name = this.name.replace(/\[(\d+)\]/, function(str,p1){
                            return '[' + (parseInt($('.qoute').size())) + ']';
                        });
                    }).end()
                    .find("select").val("").each(function(){
                        this.name = this.name.replace(/\[(\d+)\]/, function(str,p1){
                            return '[' + ($('.qoute').size()) + ']';
                        });
                    }).end()
                    .show()
                    .insertAfter(".qoute:last");
                    $(".select2, .supplier-currency, .booked-by-select2, .booking-method-select2, .category-select2, .supplier-select2, .supervisor-select2, .booking-type-select2").removeClass('select2-hidden-accessible').next().remove();
                    $(".select2, .supplier-currency, .booked-by-select2, .booking-method-select2, .category-select2, .supplier-select2, .supervisor-select2, .booking-type-select2").select2();
                    $('.removeButton:last').append("<button type='button' class='remove btn btn-link pull-right'><i class='fa fa-times'  style='color:red' ></i></button>");   
                    datePickerSetDate();
                    

        });
        
        $(document).on("click", ".remove", function() {
            $($(this).parent().parent()).remove();
        });

    
        $(document).on('change', '.addsaga', function() {
            if(this.checked) {
                $(this).val(true);
            }else{
                $(this).val(false);
            }    
        });
        
        $(document).on('change', '.datepicker', function (event) {
                
                var season_id   = $('#bookingSeason').val();
                var season      = {!! json_encode($seasons->toArray()) !!};
                var item        = season.filter(function(a){ return a.id == season_id })[0];
                var startdate   = new Date(item.start_date);
                var enddate     = new Date(item.end_date);
                
                
                var date        = $(this).val();
                var name        = $(this).data("name");
                var $selector   = $(this);
                var inValBookingDate    = $selector.closest(".qoute").find("input[data-name='booking_date']").val();
                var inValDateOfService  = $selector.closest(".qoute").find("input[data-name='date_of_service']").val();
                var inValBookingDueDate = $selector.closest(".qoute").find("input[data-name='booking_due_date']").val();
                switch (name) {
                    case 'date_of_service':
                            var booking_due_date = (inValBookingDueDate != '')? convertDate(inValBookingDueDate): startdate;
                            var booking_dateofservice = (date != '')? convertDate(date): enddate;
                            if(booking_dateofservice > startdate ){
                                booking_dateofservice.setDate(booking_dateofservice.getDate() - 1);
                            }
                            
                            $selector.closest(".qoute").find("input[data-name='booking_date']").datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: booking_due_date, endDate: booking_dateofservice});
                            booking_dateofservice = (inValBookingDate != '')? convertDate(inValBookingDate): booking_dateofservice;
                            $selector.closest(".qoute").find("input[data-name='booking_due_date']").datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: startdate, endDate: booking_dateofservice});
                        
                        break;
                        
                    case 'booking_date':
                            var booking_date = (date != '')? convertDate(date): startdate;
                            $selector.closest(".qoute").find('[class*="bookingDateOfService"]').datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: booking_date, endDate: enddate});
                            booking_date = (date != '')? convertDate(date): (inValDateOfService != '')? convertDate(inValDateOfService) : enddate;
                            $selector.closest(".qoute").find('[class*="bookingDueDate"]').datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: startdate, endDate: booking_date});
                        break;
                        
                    case 'booking_due_date':
                    
                        var booking_due_date = convertDate(date);
                        booking_dateofservice = (inValDateOfService != '')? convertDate(inValDateOfService): enddate;
                        $selector.closest(".qoute").find('[class*="bookingDate"]').datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: booking_due_date, endDate: booking_dateofservice});
                        booking_due_date = (inValBookingDate != '')? convertDate(inValBookingDate): booking_due_date;
                    
                            $selector.closest(".qoute").find('[class*="bookingDateOfService"]').datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: booking_due_date, endDate: enddate});
                    break;
                }
            // });
        });
    
        $('#bookingSeason').change(function() {
                $('.datepicker').val("");
                datePickerSetDate();
        })
    });
        function datePickerSetDate(y = 1) {
            var season_id  = $('#bookingSeason').val();      
            var season  = {!! json_encode($seasons->toArray()) !!};
            var item = season.filter(function(a){ return a.id == season_id })[0];
            var startdate = new Date(item.start_date);
            var enddate = new Date(item.end_date);
            if(y != 1){
                $('.bookingDate:last').datepicker('remove').datepicker({  autoclose: true, format:'dd/mm/yyyy', startDate: startdate, endDate: enddate });
                $('.bookingDateOfService:last').datepicker('remove').datepicker({  autoclose: true, format:'dd/mm/yyyy',  startDate: startdate, endDate: enddate });
                $('.bookingDueDate:last').datepicker('remove').datepicker({  autoclose: true, format:'dd/mm/yyyy',  startDate: startdate, endDate: enddate });
            }else{
                $('.datepicker').datepicker('remove').datepicker({ defaultDate:'', autoclose: true, format:'dd/mm/yyyy',  startDate: startdate, endDate: enddate});
            }
        }

        function convertDate(date) {
            var dateParts = date.split("/");
            return dateParts = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]);
        }
</script>
</body>
</html>
@endsection