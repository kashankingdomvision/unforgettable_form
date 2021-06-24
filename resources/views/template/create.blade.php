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

        .pl-3{
            padding-left: 3rem;
        }

        .pr-3{
            padding-right: 3rem;
        }

        .mt-2{
        margin-top: 2rem;
    }
    </style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Create Quote Template </h1>
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
        <div class="box  box-info">
            <div class="box-body">
                <div class="box-header with-border">
                    <h3 class="box-title">Template Form</h3>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 appendColumn">
                        <form method="POST" action="{{ route('template.store') }}"> @csrf
                            <div class="row pl-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="template_title" >Template Tilte <span class="text-danger">*</span></label>
                                        <input id="template_title" class="form-control" type="text"  name="template_name" placeholder="Enter the Template Title" required>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('template_name') }} </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bookingSeason" > Booking Season <span class="text-danger">*</span></label>
                                         <select class="form-control dropdown_value"  name="season_id" id="bookingSeason"  required>
                                            <option value="">Select Season</option>
                                            @foreach ($seasons as $sess)
                                                <option value="{{ $sess->id }}"  {{ (old('season_id') == $sess->id)? 'selected' :(($sess->default_season == 1 )? 'selected': NULL) }} >{{ $sess->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('season_id') }} </div>
                                    </div>
                                </div>
                            </div>
                            <div class="parent pl-3 pr-3 mt-2" id="parent">
                                <div class="qoute">
                                        <div class="row removeButton"> </div>
                                        <div class="row" style="margin-top: 15px">
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Date of Service</label> 
                                                <div class="input-group">
                                                    <input type="text" data-name="date_of_service" name="quote[0][date_of_service]"  class="form-control datepicker checkDates bookingDateOfService" autocomplete="off" placeholder="Date of Service"  >
                                                </div>
                                                {{-- <div class="alert-danger date_of_service" style="text-align:center"></div> --}}
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label class="">Category</label> 
                                                <select class="form-control category-select2"  name="quote[0][category_id]" >
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category') == $category->id  ? "selected" : "" }}> {{ $category->name }} </option>
                                                    @endforeach
                                                </select>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('category') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label class="test">Supplier</label> 
                                                <select class="form-control supplier-select2"  name="quote[0][supplier_id]" >
                                                    <option value="">Select Supplier</option>
                                                </select>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('supplier') }} </div>
                                            </div>

                                            <div class="col-sm-2 mb-3">
                                                <label class="">Product</label> 
                                                <select class="form-control product-select2"  name="quote[0][product]" >
                                                    <option value="">Select Product</option>
                                                </select>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('product') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="test222">Booking Date</label>
                                                <div class="input-group">
                                                    <input type="text" data-name="booking_date" name="quote[0][booking_date]" value="" class="form-control datepicker bookingDate" placeholder="Booking Date" autocomplete="off" value="{{old('booking_date')}}" >
                                                </div>
                                                <div class="alert-danger booking_date" style="text-align:center"> {{ $errors->first('booking_date') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3"  class="">Booking Due Date <span style="color:red">*</span></label> 
                                                <div class="input-group">
                                                    <input type="text" data-name="booking_due_date" name="quote[0][booking_due_date]"   class="form-control datepicker checkDates bookingDueDate" autocomplete="off" placeholder="Booking Due Date" >
                                                </div>
                                                <div class="alert-danger booking_due_date" style="text-align:center; width: 160px;"></div>
                                            </div>
                            
                                        </div>
                                        <div class="row" style="margin-top: 15px">

                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Service Details</label> 
                                                <textarea name="quote[0][service_details]"  class="form-control service-detail" cols="30" rows="1"></textarea>
                                                {{-- <div class="alert-danger" style="text-align:center">{{ $errors->first('service_details') }}</div> --}}
                                            </div>

                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Booking Method</label>
                                                <div class="input-group">
                                                    <select class="form-control select2"  name="quote[0][booking_method_id]" >
                                                        <option value="">Select Booking Method</option>
                                                        @foreach ($booking_methods as $booking_method)
                                                        <option value="{{$booking_method->id}}" {{ $booking_method->name == 'Supplier Own' ? 'selected' : '' }}>{{$booking_method->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Booked By </label>
                                                <div class="input-group">
                                                    <select class="form-control select2"  name="quote[0][booked_by_id]">
                                                        <option value="">Select Person</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{$user->id}}" {{ !empty(Auth::user()->id) && Auth::user()->id == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Booking Reference</label>
                                                <div class="input-group">
                                                    <input type="text" name="quote[0][booking_reference]" value="" class="form-control" placeholder="Booking Reference" value="{{old('booking_refrence')}}" >
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> </div>
                                            </div>
                
                                            <div class="col-sm-2 " style="margin-bottom: 15px;">
                                                <label for="inputEmail3" class="">Booking Type</label> 
                                                <div class="input-group">
                                                    <select class="form-control select2"  name="quote[0][booking_type]" >
                                                        <option value="">Select Booking Type</option>
                                                        <option value="refundable">Refundable</option>
                                                        <option value="non_refundable">Non-Refundable</option>
                                                    </select>
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_type') }} </div>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Comments</label> 
                                                <textarea name="quote[0][comments]"   class="form-control" cols="30" rows="1"></textarea>
                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>

                                        </div>
                                        <div class="row" style="margin-top: 15px">

                                            <div class="col-sm-2">
                                                <label>Supplier Currency</label> 
                                                <select class="form-control supplier-currency"  name="quote[0][currency_id]" >
                                                    <option value="">Select Currency</option>
                                                    @foreach ($currencies as $currency)
                                                        <option value="{{ $currency->code }}" data-image="data:image/png;base64, {{$currency->flag}}"> &nbsp; {{$currency->code}} - {{$currency->name}} </option>
                                                    @endforeach
                                                </select>
                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>

                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Estimated Cost</label>
                                                 {{-- <span style="color:red">*</span> --}}
                                                <div class="input-group">
                                                    <span class="input-group-addon symbol" ></span>
                                                    <input type="number" data-code="" name="quote[0][cost]" class="form-control cost" min="0" value="0" placeholder="Cost" >
                                                </div>
                                                <div class="alert-danger error-cost" style="text-align:center" ></div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Currency Conversion</label>
                                                <label class="currency"></label>  
                                                <input type="text" class="base-currency" name="quote[0][currency_conversion]" readonly><br>
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Added in Sage</label>
                                                <div class="input-group">
                                                    <input type="checkbox" class="addsaga" name="quote[0][add_in_sag]" value="0">
                                                    {{-- <input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value"> --}}
                                                </div>
                                                
                                            </div>
                
                                            <div class="col-sm-2">
                                                <label for="inputEmail3" class="">Supervisor</label>
                                                <div class="input-group">
                                                    <select class="form-control supervisor-select2 select2"  name="quote[0][supervisor_id]"  class="form-control" >
                                                        <option value="">Select Supervisor</option>
                                                        @foreach ($supervisors as $supervisor)
                                                            <option value="{{$supervisor->id}}" {{ (isset(Auth::user()->getSupervisor))? ((Auth::user()->getSupervisor->id == $supervisor->id)? 'selected': NULL) : NULL }} >{{$supervisor->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-group pull-right" style="margin-top: 25px">
                                                <button type="button" id="createNEw" class="btn btn-info pull-right ">+ Add more </button>
                                                <button type="submit" class="btn btn-success pull-right" style="margin-right: 20px">Save Template</button>
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
<!-- FastClick -->
{!! HTML::script('plugins/select2/select2.full.min.js') !!}

{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}

<script>
    $(document).ready(function(){
 
        datePickerSetDate();
        $('.select2, .category-select2, .supplier-select2, .product-select2').select2();

        $('.supplier-currency').select2({
            templateResult: formatState,
            templateSelection: formatState
        });

        function formatState(opt) {
            if (!opt.id) {
                return opt.text;
            }

            var optimage = $(opt.element).attr('data-image');

            if (!optimage) {
                return opt.text ;
            } else {
                var $opt = $(
                    '<span><img height="20" width="20" src="' + optimage + '" width="60px" /> ' + opt.text + '</span>'
                );
                return $opt;
            }
        };
            
  
        
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
                    $('.select2, .supplier-currency, .category-select2, .supplier-select2, .product-select2').removeClass('select2-hidden-accessible').next().remove()
                    $('.select2, .category-select2, .supplier-select2, .product-select2').select2();

                    $('.supplier-currency').select2({
                        templateResult: formatState,
                        templateSelection: formatState
                    });

                    $('.removeButton:last').append("<button type='button' class='remove btn btn-link pull-right'><i class='fa fa-times'  style='color:red' ></i></button>");   
                    datePickerSetDate();
        });
        
        $(document).on("click", ".remove", function() {
            // $($(this).parent().parent()).remove();
            $(this).closest(".qoute").remove();
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
        
        $(document).on('change', '.category-select2',function(){
            
            var $selector = $(this);
            var category_id = $(this).val();
            
            var options = '';
            $.ajax({
                type: 'POST',
                url: '{{ route('get-supplier') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'category_id': category_id
                },
                success: function(response) {
                    options += '<option value="">Select Supplier</option>';
                    $.each(response,function(key,value){
                        options += '<option value="'+value.id+'">'+value.name+'</option>';
                    });
                    
                    $selector.closest('.row').find('[class*="supplier-select2"]').html(options);
                    $selector.closest('.row').find('[class*="product-select2"]').html('<option value="">Select Product</option>');
                    $selector.closest('.qoute').find('[class*="service-detail"]').val('');
                }
            })
        });

        // set supplier's default & supplier's product list
        $(document).on('change', '.supplier-select2',function(){

            var $selector = $(this);
            var supplier_id = $(this).val();
            var options = '';

            $.ajax({
                type: 'POST',
                url: '{{ route('get-supplier-currency') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'supplier_id': supplier_id
                },
                success: function(response) {

                    // set supplier's product 
                    options += '<option value="">Select Product</option>';
                    $.each(response.supplier_products,function(key,value){
                        options += '<option value="'+value.id+'">'+value.name+'</option>';
                    });
                    $selector.closest('.row').find('[class*="product-select2"]').html(options);

                    // set supplier's currency 
                    $selector.closest('.qoute').find('[class*="supplier-currency"]').val(response.supplier_currency.code).change();
                }
            })
        });

        // get product's details for service details
        $(document).on('change', '.product-select2',function(){

            var $selector = $(this);
            var product_id = $(this).val();

            $.ajax({
                type: 'POST',
                url: '{{ route('get-product-details') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'product_id': product_id
                },
                success: function(response) {

                    $selector.closest('.qoute').find('[class*="service-detail"]').val(response.description);
                }
            })
        });
        


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