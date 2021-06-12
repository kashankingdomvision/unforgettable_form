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
        <h1>View Templates Details(Quote)</h1>
        <ol class="breadcrumb">
            <li>
              <a href="{{ route('template.index') }}" class="btn btn-primary btn-xs">View all template</a>
            </li>
          </ol>
    </section>
<section class="content">
    <div id="divLoading"></div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 appendColumn">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="template_title" >Template Tilte <span class="text-danger">*</span></label>
                                        <input id="template_title" disabled  class="form-control" type="text" value="{{ $template->title }}" name="template_name" placeholder="Enter the template title" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bookingSeason" > Booking Season <span class="text-danger">*</span></label>
                                        <input id="template_title"  disabled class="form-control" type="text" value="{{ $template->getSeason->name??NULL }}" name="template_name" placeholder="Enter the template title" required>
                                    
                                    </div>
                                </div>
                            </div>
                            <div class="parent" id="parent">
                                @foreach ($template->getTemplateDetails as $detail)
                                    <div class="qoute">
                                            <div class="row removeButton"> </div>
                                            <div class="row" style="margin-top: 15px">
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Date of Service</label> 
                                                    <div class="input-group">
                                                        <input type="text" disabled value="{{ $detail->date_of_service }}" name="quote[0][date_of_service]"  class="form-control datepicker checkDates bookingDateOfService" autocomplete="off" placeholder="Date of Service"  >
                                                    </div>
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Service Details</label> 
                                                    <textarea disabled name="quote[0][service_details]" class="form-control" cols="30" rows="1">{{ $detail->service_details }}</textarea>
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label class="">Select Category</label> 
                                                    <input   class="form-control"disabled type="text" value="{{ $detail->getCategory->name??NULL }}"  required>
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label class="test">Select Supplier</label> 
                                                    <input  class="form-control"disabled type="text" value="{{ $detail->getSupplier->name??NULL }}"  required>
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="test222">Booking Date</label>
                                                    <input type="text"disabled value="{{ $detail->booking_date }}" name="quote[0][booking_date]" value="" class="form-control datepicker bookingDate" placeholder="Booking Date" autocomplete="off" value="{{old('booking_date')}}" >
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Booking Due Date <span style="color:red">*</span></label> 
                                                    <input type="text" disabled value="{{ $detail->booking_due_date }}"    class="form-control datepicker checkDates bookingDueDate" autocomplete="off" placeholder="Booking Due Date" >
                                                </div>
                                            </div>
                                            <div class="row" style="margin-top: 15px">
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Booking Method</label>
                                                    <div class="input-group">
                                                        <input type="text" disabled value="{{ $detail->getBookinMethod->name??NULL }}" class="form-control datepicker checkDates bookingDueDate" autocomplete="off" placeholder="Booking Due Date" >
                                                    </div>
                                                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Booked By </label>
                                                    <div class="input-group">
                                                        <input type="text" disabled value="{{ $detail->getBookedBy->name??NULL }}" class="form-control datepicker checkDates bookingDueDate" autocomplete="off" placeholder="Booking Due Date" >
                                                    </div>
                                                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Booking Reference</label>
                                                    <div class="input-group">
                                                        <input type="text" name="quote[0][booking_reference]" disabled value="{{ $detail->booking_refrence }}" class="form-control" placeholder="Booking Reference"  >
                                                    </div>
                                                    <div class="alert-danger" style="text-align:center"> </div>
                                                </div>
                    
                                                <div class="col-sm-2 " style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Booking Type</label> 
                                                    <div class="input-group">
                                                        <input type="text" disabled value="{{ $detail->booking_type }}" class="form-control" placeholder="Booking Reference"  >
                                                    </div>
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Comments</label> 
                                                    <textarea disabled class="form-control" cols="30" rows="1">{{ $detail->comments }}</textarea>
                                                    <div class="alert-danger" style="text-align:center"></div>
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label>Select Supplier Currency</label> 
                                                    <input type="text" disabled value="{{ $detail->supplier_currency }}" class="form-control" placeholder="Booking Reference"  >
                                                    <div class="alert-danger" style="text-align:center"></div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-top: 5px">
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Estimated Cost</label>
                                                     {{-- <span style="color:red">*</span> --}}
                                                    <input type="number" data-code="" disabled class="form-control cost" min="0" value="{{ $detail->cost }}" placeholder="Cost" >
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Currency Conversion</label>
                                                    <input type="text" disabled value="{{ $detail->qoute_base_currency }}" class="base-currency" name="quote[0][currency_conversion]" readonly><br>
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Added in Sage {{   $detail->added_in_sage }}</label>
                                                    <div class="input-group">
                                                        <input type="checkbox"  disabled {{ ($detail->added_in_sage == '1')? 'checked': NULL }} class="addsaga" name="quote[0][add_in_sag]" value="{{ ($detail->added_in_sage == '1')? true: false }} ">
                                                        {{-- <input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value"> --}}
                                                    </div>
                                                    
                                                </div>
                    
                                                <div class="col-sm-2">
                                                    <label for="inputEmail3" class="">Supervisor</label>
                                                        <input type="text" disabled value="{{ $detail->getSupervisor->name??NULL }}" class="base-currency" readonly><br>
                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                            </div>
                        </div>
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
<!-- FastClick -->
{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}

{{-- <script>
    $(document).ready(function(){
        $(function(){
            datePickerSetDate();
        });
        
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
                $('.datepicker').datepicker('remove').datepicker({  autoclose: true, format:'dd/mm/yyyy',  startDate: startdate, endDate: enddate});
            }
        }

        function convertDate(date) {
            var dateParts = date.split("/");
            return dateParts = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]);
        }
        
    });
</script> --}}
</body>
</html>
@endsection