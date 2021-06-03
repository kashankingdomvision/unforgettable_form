@extends('content_layout.default')

@section('content')
<style type="text/css">
    .row.box-cus {
        width: 83%;
        border: solid 1px #000;
        padding: 20px;
        margin: 0 auto 15px;
        float: none;
        border-radius: 10px;
    }

    .row.box-cus .col-sm-offset-1 {
        margin: 0;
    }

    .finance-holiday-amount label {
        font-size: 18px;
        width: 100%;
    }

    .finance-holiday-amount input {
        width: 40%;
        height: 35px;
        text-align: center;
    }

    .deposit-remaining input {
        float: left;
    }

    .qoute{
        width: 83%;
        border: solid 1px #000;
        padding: 20px;
        margin: 0 auto 15px;
        float: none;
        border-radius: 10px;
    }

    .mb-2{
        margin-bottom: 1.5rem;
    }

    .mb-3{
        margin-bottom: 3rem;
    }

    .mt-3{
        margin-top: 3rem;
    }

    .mt-2{
        margin-top: 2rem;
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


    <div class="" id="qoute" hidden>

        <div class="qoute">

            <div class="row">
                <div class="col-sm-12" >
                    <button type="button" class="btn pull-right close"> x </button>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Date of Service</label> 
                    <div class="input-group">
                        <input type="text" name="date_of_service[]" autocomplete="off" class="form-control datepicker" placeholder="Date of Service" autocomplete="off" >
                    </div>
                    <div class="alert-danger date_of_service" style="text-align:center"></div>
                </div>

                <div class="col-sm-2" style="margin-bottom: 35px;">
                    <label for="inputEmail3" class="">Service Details</label> 
                    <textarea name="service_details[]"  class="form-control" cols="30" rows="1"></textarea>
                    <div class="alert-danger" style="text-align:center">{{ $errors->first('service_details') }}</div>
                </div>

                <div class="col-sm-2 " style="margin-bottom:15px;">
                    <label class="">Select Category</label> 
                    <select class="form-control category-select2" name="category[]" >
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category') == $category->id  ? "selected" : "" }}> {{ $category->name }} </option>
                        @endforeach
                    </select>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('category') }} </div>
                </div>

                <div class="col-sm-2" style="margin-bottom:15px">
                    <label class="">Select Supplier</label> 
                    <select class="form-control supplier-select2" name="supplier[]" >
                        <option value="">Select Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier') == $supplier->id  ? "selected" : "" }}> {{ $supplier->name }} </option>
                        @endforeach
                    </select>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('supplier') }} </div>
                </div>
                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booking Date</label>
                    <div class="input-group">
                        <input type="text" name="booking_date[]" value="" class="form-control datepicker" placeholder="Booking Date" autocomplete="off" value="{{old('booking_date')}}" >
                    </div>
                    <div class="alert-danger booking_date" style="text-align:center"> {{ $errors->first('booking_date') }} </div>
                </div>

                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booking Due Date <span style="color:red">*</span></label> 
                    <div class="input-group">
                        <input type="text" name="booking_due_date[]" value="" class="form-control datepicker" placeholder="Booking Due Date" autocomplete="off" required>
                    </div>
                    <div class="alert-danger booking_due_date" style="text-align:center; width: 160px; "> {{ $errors->first('booking_date') }} </div>
                </div>


            </div>

            <div class="row">
                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booking Method</label> 
                    <div class="input-group">
                        <select class="form-control booking-method-select2"  name="booking_method[]" >
                            <option value="">Select Booking Method</option>
                            @foreach ($booking_methods as $booking_method)
                            <option {{($booking_method->name == 'Supplier Own')? 'selected' : NULL}} value="{{$booking_method->id}}">{{$booking_method->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                </div>

                <div class="col-sm-2 " style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booked By</label> 
                    <div class="input-group">
                        <select class="form-control booked-by-select2" name="booked_by[]" >
                            <option value="">Select Person</option>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}" {{ !empty(Auth::user()->id) && Auth::user()->id == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                </div>

                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booking Reference</label>
                    <div class="input-group">
                        <input type="text" name="booking_refrence[]" value="" class="form-control"  placeholder="Booking Reference" value="{{old('booking_refrence')}}" >
                    </div>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_refrence') }} </div>
                </div>


                <div class="col-sm-2" style="margin-bottom: 35px;">
                    <label for="inputEmail3" class="">Comments</label> 
                    <textarea name="comments[]" id="" class="form-control" cols="30" rows="1"></textarea>
                    <div class="alert-danger" style="text-align:center">{{ $errors->first('service_details') }}</div>
                </div>

                <div class="col-sm-2" style="margin-bottom:15px;">
                    <label class="">Select Supplier Currency</label> 
                    <select class="form-control supplier-currency"  name="supplier_currency[]" required>
                        <option value="">Select Currency</option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->code }}"  > {{ $currency->name }} ({{ $currency->symbol }}) </option>
                        @endforeach
                    </select>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('category') }} </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booking Currency Conversion</label>
                    <label class="currency"></label>  
                    <input type="text" class="base-currency" name="qoute_base_currency[]" readonly><br>
                </div>

                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Added in Sage</label>
                    <div class="input-group">
                        <input type="hidden" name="added_in_sage[]" value="0"><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value">
                    </div>
                    
                </div>

                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Supervisor</label>
                    <div class="input-group">
                        <select  name="supervisor[]" class="form-control supervisor-select2" >
                            <option value="">Select Supervisor</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{$supervisor->id}}">{{$supervisor->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert-danger" style="text-align:center"> </div>
                </div>

                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Upload Invoice</label>
                    <div class="input-group">
                        {{-- <input type="hidden" name="qoute_invoice_record[]" value="" > --}}
                        <input type="file" name="qoute_invoice[]" value="" class="form-control">
                    </div>
                    <div class="alert-danger" style="text-align:center"> </div>
                </div>

            
            </div>


        </div>
    </div>


    <section class="content-header">
        <h1> Booking </h1>
    </section>

    <section class="content">
        <div id="divLoading"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Booking</h3>
                        <span class="pull-right">
                            <button type="button" target="_blank" id="export-to-csv"> Export CSV</button></span>
                    </div>
                    <div class="col-sm-6 col-sm-offset-3" style="text-align: center;">
                        @if (Session::has('success_message'))
                        <div class="alert alert-success">
                            {{ Session::get('success_message') }}
                        </div>
                        @endif
                    </div>

                    {{-- <form action="{{ route('creat-quote') }}" method="POST" class="form-horizontal" id="user_form"> --}}


                        <form method="POST" id="user_form" action="javascript:void(0)" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" value="{{$quote->id}}" name="qoute_id">

                        <div class="row">
                            <div class="row">
                                <div class="col-md-5 col-sm-offset-1 mb-2">
                                    <label>Select the reference <span style="color:red">*</span></label> <br />
                                    <label class="radio-inline"><input type="radio" {{ ($quote->reference_name == 'zoho')? 'checked': NULL }} name="reference" value="zoho" checked>Zoho Reference</label>
                                    <label class="radio-inline"><input type="radio" {{ ($quote->reference_name == 'tas')? 'checked': NULL }} name="reference" value="tas" >TAS Reference</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5 col-sm-offset-1 mb-2">
                                    <label for="inputEmail3" id="referencename">{{ ($quote->reference_name == 'zoho')? 'Zoho': 'TAS' }} Reference</label> <span style="color:red">*</span>
                                    <div class="input-group">
                                        <input type="text" name="ref_no" value="{{ $quote->ref_no }}"  class="form-control" placeholder='Enter Reference Number' >
                                        <span  id="link">
                                        </span>
                                        <span class="input-group-addon">
                                            <button id="sendReference" type="button" class="btn-link"> Search </button>
                                        </span>
                                    </div>
                                    <div class="alert-danger" style="text-align:center" id="error_ref_no"></div>
                                </div>

                            <div class="col-sm-5">
                                <label for="inputEmail3" class="">Quote Reference</label> <span style="color:red">*</span>
                                <div class="input-group">
                                    <input type="text" name="quotation_no"  class="form-control" value="{{ $quote->quotation_no }}" required>
                                    <span class="input-group-addon"></span>
                                </div>
                                <div class="alert-danger" style="text-align:center" id="error_quotation_no"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-5 mb-2 col-sm-offset-1 mb-2">
                                <label for="inputEmail3" class="">Lead Passenger Name</label> <span style="color:red">*</span>
                                <div class="input-group">
                                    <input type="text" name="lead_passenger_name" class="form-control" value="{{ $quote->lead_passenger_name }}" >
                                    <span class="input-group-addon"></span>
                                </div>
                                <div class="alert-danger" style="text-align:center" id="error_lead_passenger_name"></div>
                            </div>


                            <div class="col-sm-5"  style="margin-bottom:15px">
                                <label class="">Brand Name</label> <span style="color:red">*</span>
                                <select class="form-control select2" name="brand_name" >
                                    <option value="">Select Brand</option>
                                    @foreach ($get_user_branches->branches as $branche)
                                    <option value="{{ $branche->name }}" {{$quote->brand_name == $branche->name ? 'selected' : ''}} >{{ $branche->name }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_brand_name"></div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px;">
                                <label class="">Type Of Holidays</label> <span style="color:red">*</span>
                                <select class="form-control select2" id="type_of_holidays" name="type_of_holidays" >
                                    <option value="">Select Holiday</option>
                                    @foreach ($get_holiday_type->holiday_type as $holiday)
                                    <option value="{{ $holiday->name }}" {{$quote->type_of_holidays == $holiday->name ? 'selected' : ''}}>{{ $holiday->name }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_type_of_holidays"></div>
                            </div>
    
                            <div class="col-sm-5" style="margin-bottom:15px;">
                                <label class="">Sales Person</label> <span style="color:red">*</span>
                                <select class="form-control select2" id="sales_person" name="sale_person" >
                                    <option value="">Select Person</option>
                                    @foreach ($get_user_branches->users as $user)
                                    <option value="{{ $user->email }}" {{ $quote->sale_person == $user->email ? 'selected' : ''}}> {{ $user->email }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_sale_person"> </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px;">
                                <label class="">Booking Season</label> 
                                <span style="color:red">*</span>
                                {{-- <input type="text" name="season_id" class="form-control"   readonly> --}}
                                <select class="form-control dropdown_value" name="season_id" >
                                    <option value="">Select Season</option>
                                    @foreach ($seasons as $sess)
                                    <option value="{{ $sess->id }}" {{ $quote->season_id == $sess->id ? 'selected' : ''}} >{{ $sess->name }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_season_id"> </div>
                            </div>

                            <div class="col-sm-1" style="margin-bottom: 35px; width:145px;">
                                <label for="inputEmail3" class="">Agency Booking</label> <span style="color:red"> *</span><br>
                                <input type="radio" name="agency_booking" value="2" id="ab_yes" {{ $quote->agency_booking == "2" ? 'checked' : ''}}> <label for="ab_yes"> Yes</label>
                                <input type="radio" name="agency_booking" value="1"  id="ab_no" {{ $quote->agency_booking == "1" ? 'checked' : ''}}> <label for="ab_no"> No</label>
                          
                                {{-- {!! Form::radio('agency_booking', 2, null, ['id' => 'ab_yes', 'required' => 'true']) !!}&nbsp<label for="ab_yes">Yes</label>
                                {!! Form::radio('agency_booking', 1, null, ['id' => 'ab_no', 'required' => 'true']) !!}&nbsp<label for="ab_no">No</label> --}}
                                <div class="alert-danger" style="text-align:center" > </div>
                            </div>
                            <div class="row" style="{{ $quote->agency_booking == 2 ? 'display:block' : 'display:none' }}" id="agency-detail">
                                <div class="col-sm-2" style="width:175px;">
                                    <label for="inputEmail3" class="">Agency Name</label> <span style="color:red"> *</span>
                                    <input type="text" name="agency_name" value="{{ $quote->agency_name }}"  class="form-control">
                                    <div class="alert-danger" style="text-align:center" id="error_agency_name"> </div>
                                    
                                </div>

                                <div class="col-sm-2">
                                    <label for="inputEmail3" class="">Agency Contact No.</label> <span style="color:red"> *</span>
                                    <input type="text" name="agency_contact_no" value="{{ $quote->agency_contact_no }}" class="form-control">
                                    <div class="alert-danger" style="text-align:center" id="error_agency_contact_no"> </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px;">
                                <label> Booking Currency</label> <span style="color:red">*</span>
                                <select name="currency" class="form-control select2">
                                    <option value="">Select Currency</option>
                                    @foreach ($currencies as $currency)
                                    {{-- {{ $currency->code == 'GBP' ? 'selected' : '' }}/ --}}
                                        <option value="{{ $currency->code }}" {{ $quote->currency == $currency->code ? 'selected' : ''}} > {{ $currency->name }} ({{ $currency->symbol }}) </option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_currency"></div>
                            </div>

                            <div class="col-sm-5" style="margin-bottom:15px">
                                <label class="">Pax No.</label> <span style="color:red">*</span>
                                  <select class="form-control dropdown_value select2" name="group_no">
                                    {{-- <option value="">Select Pax No.</option> --}}
                                    @for($i=1;$i<=30;$i++)
                                    <option value={{$i}} {{ $quote->group_no == $i ? 'selected' : ''}} >{{$i}}</option>
                                    @endfor
                                  </select>
                                <div class="alert-danger" style="text-align:center" id="error_group_no"></div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1 mb-2">
                                <label> Dinning Preferences</label> <span style="color:red">*</span>
                                <input type="text" name="dinning_preferences" value="{{ $quote->dinning_preferences }}" class="form-control">
                                <div class="alert-danger" style="text-align:center" id="error_dinning_preferences"></div>
                            </div>
                        </div>


                        
                        <br><br>

                        <div class="parent" id="parent">

                            {{-- {{ dd($quote_details) }} --}}
                            @foreach ($quote_details as $key => $quote_detail)
                                
                            <div class="qoute">
                                <div class="row">
                                    <div class="col-sm-12" >
                                        <button type="button" class="btn  pull-right close"> x </button>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Date of Service</label> 
                                        <div class="input-group">
                                            <input type="text" name="date_of_service[]" autocomplete="off" value="{{ !empty($quote_detail->date_of_service) ? date('d/m/Y', strtotime($quote_detail->date_of_service)) : "" }}"  class="form-control datepicker date_of_service" placeholder="Date of Service"  >
                                        </div>
                                        <div class="alert-danger date_of_service" style="text-align:center"></div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 35px;">
                                        <label for="inputEmail3" class="">Service Details</label> 
                                        <textarea name="service_details[]"   class="form-control" cols="30" rows="1">{{ $quote_detail->service_details }}</textarea>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom:15px;">
                                        <label class="">Select Category</label> 
                                        <select class="form-control category-select2"  name="category[]" >
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ $quote_detail->category_id == $category->id  ? "selected" : "" }}> {{ $category->name }} </option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('category') }} </div>
                                    </div>
        
                                    <div class="col-sm-2" style="margin-bottom:15px">
                                        <label class="test">Select Supplier</label> 
                                        <select class="form-control supplier-select2 supplier-select2"  name="supplier[]" >
                                            <option value="">Select Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ $quote_detail->supplier == $supplier->id  ? "selected" : "" }}> {{ $supplier->name }} </option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>
        
                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Date</label>
                                        <div class="input-group">
                                            <input type="text" name="booking_date[]" value="{{ !empty($quote_detail->booking_date) ? date('d/m/Y', strtotime($quote_detail->booking_date)) : "" }}" class="form-control datepicker" autocomplete="off" placeholder="Booking Date" >
                                        </div>
                                        <div class="alert-danger booking_date" style="text-align:center"> {{ $errors->first('booking_date') }} </div>
                                    </div>
        
                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Due Date <span style="color:red">*</span></label> 
                                        <div class="input-group">
                                            <input type="text" name="booking_due_date[]"  value="{{ !empty($quote_detail->booking_due_date) ? date('d/m/Y', strtotime($quote_detail->booking_due_date)) : "" }}" class="form-control datepicker" autocomplete="off" placeholder="Booking Date" required>
                                        </div>
                                        <div class="alert-danger booking_due_date" style="text-align:center; width: 160px;"></div>
                                    </div>
        
                    
                                </div>
        
                                <div class="row">

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booked By </label>
                                        <div class="input-group">
                                            <select class="form-control booked-by-select2"  name="booked_by[]"   class="form-control" >
                                                <option value="">Select Person</option>
                                                @foreach ($users as $user)
                                                    <option value="{{$user->id}}" {{ $quote_detail->booked_by == $user->id ? "selected" : "" }}>{{$user->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Method</label>
                                        <div class="input-group">
                                            <select class="form-control booking-method-select2" name="booking_method[]" class="form-control">
                                                <option value="">Select Booking Method</option>
                                                @foreach ($booking_methods as $booking_method)
                                                    <option value="{{$booking_method->id}}" {{ $quote_detail->booking_method == $booking_method->id  ? "selected" : (($booking_method->name == 'Supplier Own')? 'selected' : NULL) }}>{{$booking_method->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                    </div>
                                 
        
                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Reference</label>
                                        <div class="input-group">
                                            <input type="text" name="booking_refrence[]" value="{{ $quote_detail->booking_refrence }}" class="form-control" placeholder="Booking Refrence"  >
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> </div>
                                    </div>

                                    <div class="col-sm-2 " style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Type</label> 
                                        <div class="input-group">
                                            <select class="form-control booked-by-select2" name="booking_type[]" >
                                                <option value="">Select Booking Type</option>
                                                <option {{  ($quote_detail->booking_type == 'refundable')? 'selected' : '' }} value="refundable">Refundable</option>
                                                <option {{  ($quote_detail->booking_type == 'non_refundable')? 'selected' : '' }} value="non_refundable">Non-Refundable</option>
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_type') }} </div>
                                    </div>
        
        
                                    <div class="col-sm-2" style="margin-bottom: 35px;">
                                        <label for="inputEmail3" class="">Comments</label> 
                                        <textarea name="comments[]"   class="form-control" cols="30" rows="1">{{ $quote_detail->comments }}</textarea>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom:15px;">
                                        <label class="">Select Supplier Currency</label> 
                                        <select class="form-control supplier-currency"   name="supplier_currency[]" required >
                                            <option value="">Select Currency</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->code }}" {{ $quote_detail->supplier_currency == $currency->code  ? "selected" : "" }}> {{ $currency->name }} ({{ $currency->symbol }}) </option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>
                                    
                                </div>

                                <div class="row">

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Estimated Cost</label> <span style="color:red">*</span>
                                        <div class="input-group">
                                            <span class="input-group-addon" >{{ $quote_detail->supplier_currency }}</span>
                                            <input type="number" name="cost[]" class="form-control" value="{{ $quote_detail->cost }}"  placeholder="Cost" min="0" required readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Actual Cost</label> <span style="color:red">*</span>
                                        <div class="input-group">
                                            <span class="input-group-addon symbol" >{{ $quote_detail->supplier_currency }}</span>
                                            <input type="number" data-code="{{$quote_detail->supplier_currency}}" name="actual_cost[]" class="form-control cost" value="{{ $quote_detail->actual_cost }}"  placeholder="Cost" min="0" required>
                                        </div>
                                        <div class="alert-danger error-cost" style="text-align:center" ></div>
                                    </div>
                                    
                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Currency Conversion</label>
                                        <label class="currency"></label>  
                                        <input type="text" class="base-currency"  name="qoute_base_currency[]" value="{{ $quote_detail->actual_cost != 0 ? $quote_detail->qoute_base_currency : '' }}" readonly><br>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Added in Sage </label>
                                        <div class="input-group">
                                            <input type="hidden" name="added_in_sage[]" value="{{ $quote_detail->added_in_sage == 1 ? 1 : 0 }}"  ><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value" {{ $quote_detail->added_in_sage == 1 ? 'checked' : '' }}>
                                        </div>
                                        
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Supervisor</label>
                                        <div class="input-group">
                                            <select  name="supervisor[]" class="form-control supervisor-select2" >
                                                <option value="">Select Supervisor</option>
                                                @foreach ($supervisors as $supervisor)
                                                    <option value="{{$supervisor->id}}" {{ $quote_detail->supervisor_id == $supervisor->id ? "selected" : "" }}>{{$supervisor->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> </div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Upload Invoice</label>
                                        <div class="input-group">
                                            <input type="hidden" name="qoute_invoice_record[]" value="{{$quote_detail->qoute_invoice}}" >
                                            <input type="file" name="qoute_invoice[]" value="" class="form-control">
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> </div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px; padding-top: 3rem;">
                                        <a  target="_blank" href="{{ asset("booking/".$quote->id."/".$quote_detail->qoute_invoice) }}" style="">  {{ $quote_detail->qoute_invoice }}</a>
                                    </div>


                                </div>

                                <div class="row finance-row" hidden>
                                    <div class="row">
                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                            <label for="inputEmail3" class="">Deposit Amount</label>
                                            <div class="input-group">
                                                <input type="number" name="deposit_amount[{{$key}}][]"  class="form-control disable-feild deposit_amount" placeholder="Deposit Amount" min="0"  >
                                            </div>
                                            <div class="alert-danger" style="text-align:center"> </div>
                                        </div>
    
                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                            <label for="inputEmail3" class="">Deposit Due Date</label>
                                            <div class="input-group">
                                                <input type="text" name="deposit_due_date[{{$key}}][]" class="form-control datepicker disable-feild deposit_due_date" placeholder="Deposit Due Date"  autocomplete="off">
                                            </div>
                                            <div class="alert-danger" style="text-align:center"> </div>
                                        </div>
    
                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                            <label for="inputEmail3" class="">Paid Date</label>
                                            <div class="input-group">
                                                <input type="text" name="paid_date[{{$key}}][]" class="form-control datepicker disable-feild" placeholder="Paid Date"  autocomplete="off">
                                            </div>
                                            <div class="alert-danger" style="text-align:center"> </div>
                                        </div>
     
                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                            <label for="inputEmail3" class="">Payment Method </label>
                                            <div class="input-group">
                                         
                                                <select class="form-control booking-method-select2 disable-feild" name="payment_method[{{$key}}][]" class="form-control">
                                                    <option value="">Select Payment Method</option>
                                                    @foreach ($payment_method as $paymentm)
                                                        <option value="{{$paymentm->id}}">{{$paymentm->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="alert-danger" style="text-align:center"> {{ $errors->first('payment_method') }} </div>
                                        </div>

                                        {{-- <div class="col-sm-2" style="margin-bottom: 15px; margin-top: 2.5rem;">
                                            <button type="button" class="upload_to_google_calendar">Upload to Calendar</button>
                                        </div> --}}
                                        <div class="col-sm-2" style="margin-bottom: 15px; ">
                                            <label for="inputEmail3" class=""> Upload to Calender</label>
                                            <div class="form-check">
                                                <input type='hidden' class="disable-feild" value='false' name='upload_calender[{{$key}}][]'>
                                                <input class="form-check-input uploadCalender disable-feild" type="checkbox" value="false" name="upload_calender[{{$key}}][]" style="height: 20px; width:28px;">
                                            </div>
                                        </div>

                                        <div class="col-sm-2" style="margin-bottom: 15px; margin-top: 2.5rem;">
                                            <button type="button" class="remove_finance">-</button>
                                        </div>

                                    </div>
                                </div>

                                @php
                                    $finance_booking_details = \App\FinanceBookingDetail::where('booking_detail_id' , $quote_detail->id)->get()
                                @endphp

                                @if($finance_booking_details->count())

                                    @foreach ($finance_booking_details as $fkey => $finance_booking_detail)
                                        
                                    <div class="row">
                                
                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                            <label for="inputEmail3" class="">Deposit Amount</label>
                                            <div class="input-group">
                                                <input type="number" name="deposit_amount[{{$key}}][]"  value="{{ !empty($finance_booking_detail->deposit_amount) ? $finance_booking_detail->deposit_amount : '' }}" class="form-control deposit_amount" placeholder="Deposit Amount" min="0" >
                                            </div>
                                            <div class="alert-danger" style="text-align:center"> </div>
                                        </div>

                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                            <label for="inputEmail3" class="">Deposit Due Date</label>
                                            <div class="input-group">
                                                <input type="text"  name="deposit_due_date[{{$key}}][]" value="{{ !empty($finance_booking_detail->deposit_due_date) ? date('d/m/Y', strtotime($finance_booking_detail->deposit_due_date)) : '' }}"  class="form-control deposit_due_date datepicker" placeholder="Deposit Due Date" autocomplete="off">
                                            </div>
                                            <div class="alert-danger" style="text-align:center"> </div>
                                        </div>

                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                            <label for="inputEmail3" class="">Paid Date</label>
                                            <div class="input-group">
                                                <input type="text" name="paid_date[{{$key}}][]" value="{{ !empty($finance_booking_detail->paid_date) ? date('d/m/Y', strtotime($finance_booking_detail->paid_date)) : '' }}"  class="form-control datepicker" placeholder="Paid Date" autocomplete="off">
                                            </div>
                                            <div class="alert-danger" style="text-align:center"> </div>
                                        </div>

                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                            <label for="inputEmail3" class="">Payment Method</label>
                                            <div class="input-group">
                                                <select class="form-control booking-method-select2" name="payment_method[{{$key}}][]" class="form-control">
                                                    <option value="">Select Payment Method</option>
                                                    @foreach ($payment_method as $paymentm)
                                                        <option value="{{$paymentm->id}}" {{ $finance_booking_detail->payment_method == $paymentm->id  ? "selected" : "" }}>{{$paymentm->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="alert-danger" style="text-align:center"> {{ $errors->first('payment_method') }} </div>
                                        </div>

                                        {{-- <div class="col-sm-2" style="margin-bottom: 15px; margin-top: 2.5rem;">
                                            <button type="button" class="upload_to_google_calendar">Upload to Calendar</button>
                                        </div> --}}
                                       

                                        <div class="col-sm-2" style="margin-bottom: 15px; ">
                                            <label for="inputEmail3" class=""> Upload to Calender</label>
                                            <div class="form-check">
                                                <input type='hidden' class="disable-feild" {{ ($finance_booking_detail->upload_to_calender == 'false')?  '': 'disabled="disabled"'}}   value="false" name='upload_calender[{{$key}}][]'>
                                                <input class="form-check-input uploadCalender disable-feild"  type="checkbox" value="{{ $finance_booking_detail->upload_to_calender??'false'}} "  {{ ($finance_booking_detail->upload_to_calender == 'false')?  '': 'checked'}}  name="upload_calender[{{$key}}][]" style="height: 20px; width:28px;">
                                            </div>
                                        </div>
                                    
                                        @if($fkey == 0)
                                            <div class="col-sm-2" style="margin-bottom: 15px; margin-top: 2.5rem;">
                                                <button type="button" class="add_finance">+</button>
                                            </div>
                                        @endif


                                    </div>
                                    @endforeach
                                    
                                @else

                                    <div class="row">
                                        <div class="append">
                                            <div class="col-sm-2" style="margin-bottom: 15px;">
                                                <label for="inputEmail3" class="">Deposit Amount</label>
                                                <div class="input-group">
                                                    <input type="number" name="deposit_amount[{{$key}}][]"  value="{{ !empty($finance_booking_detail->deposit_amount) ? $finance_booking_detail->deposit_amount : '' }}" class="form-control deposit_amount" placeholder="Deposit Amount" min="0" >
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> </div>
                                            </div>

                                            <div class="col-sm-2" style="margin-bottom: 15px;">
                                                <label for="inputEmail3" class="">Deposit Due Date</label>
                                                <div class="input-group">
                                                    <input type="text"  name="deposit_due_date[{{$key}}][]" value="{{ !empty($finance_booking_detail->deposit_due_date) ? date('d/m/Y', strtotime($finance_booking_detail->deposit_due_date)) : '' }}"  class="form-control deposit_due_date datepicker" placeholder="Deposit Due Date" autocomplete="off">
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> </div>
                                            </div>

                                            <div class="col-sm-2" style="margin-bottom: 15px;">
                                                <label for="inputEmail3" class="">Paid Date</label>
                                                <div class="input-group">
                                                    <input type="text" name="paid_date[{{$key}}][]" class="form-control datepicker" placeholder="Paid Date" autocomplete="off">
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> </div>
                                            </div>

                                            <div class="col-sm-2" style="margin-bottom: 15px;">
                                                <label for="inputEmail3" class="">Payment Method</label>
                                                <div class="input-group">
                                                    <select class="form-control booking-method-select2" name="payment_method[{{$key}}][]" class="form-control">
                                                        <option value="">Select Payment Method</option>
                                                        @foreach ($payment_method as $paymentm)
                                                            <option value="{{$paymentm->id}}" {{ $quote_detail->payment_method == $paymentm->id  ? "selected" : "" }}>{{$paymentm->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('payment_method') }} </div>
                                            </div>

                                            {{-- <div class="col-sm-2" style="margin-bottom: 15px; margin-top: 2.5rem;">
                                                <button type="button" class="upload_to_google_calendar">Upload to Calendar</button>
                                            </div> --}}

                                            <div class="col-sm-2" style="margin-bottom: 15px; ">
                                                <label for="inputEmail3" class=""> Upload to Calender</label>
                                                <div class="form-check">
                                                    <input type='hidden' value='false' name='upload_calender[{{$key}}][]'>
                                                    <input class="form-check-input uploadCalender" type="checkbox" value="false" name="upload_calender[{{$key}}][]" style="height: 20px; width:28px;">
                                                </div>
                                            </div>
    
                                        </div>

                                        <div class="col-sm-2" style="margin-bottom: 15px; margin-top: 2.5rem;">
                                            <button type="button" class="add_finance">+</button>
                                        </div>
                                    </div>
                                @endif

                         
                                
                            </div>
                            @endforeach
                        </div>

                        {{-- <div class="row mt-2">
                            <div class="col-sm-10 col-sm-offset-1">
                                <button type="button" id="new" class="btn btn-info pull-right">+</button>
                            </div>
                        </div> --}}
                        <br><br>
                        
                        
                        <div class="row">
                            <div class="col-sm-2 col-sm-offset-1">
                                <div class="row">
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Net Price</label>
                                </div>
                       
                                <div class="row"> 
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Markup</label>
                                    
                                </div>
                                <div class="row"> 
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Selling</label>
                                </div>
                                <div class="row"> 
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Gross Profit Rate</label>
                                </div>


                                <br><br>
                            </div>

       
                       
                            <div class="col-sm-2">
                                <div class="row">
                                    <label class="">
                                        <label class="currency" ></label>
                                        <input type="number" name="net_price" step="any" min="0" class="net_price hide-arrows" value="{{$quote->net_price}}">
                                    </label>
                                </div>
                                <div class="row">
                                    <label class="">
                                        <label class="currency" ></label>
                                        <input type="number" class="markup-amount" step="any" min="0" name="markup_amount" value="{{$quote->markup_amount}}">
                                    </label>
                                </div>
                                <div class="row">
                                    <label class="">
                                        <label class="currency" ></label>
                                        <input type="number" class="selling hide-arrows" min="0"  step="any" name="selling" value="{{$quote->selling}}">
                                    </label>
                                </div>
                                <div class="row">
                                    <label class="">
                                        <label class="currency" ></label>
                                        <input type="number" class="gross-profit hide-arrows" min="0" step="any" name="gross_profit"  value="{{$quote->gross_profit}}">
                                        <span>%</span> 
                                    </label>
                                </div>
                    
                            </div>

                            
                            <div class="col-sm-2">
                                <br>
                                <div class="row">
                                    <label class="">
                                        <input type="number" class="markup-percent" name="markup_percent" min="0" value="{{$quote->markup_percent}}" style="width:70px;">
                                        <span>%</span> 
                                    </label>
                                </div>
                            </div>
            
                        </div>


                        <div class="row"> 
                            <div class="col-sm-2 col-sm-offset-1" style="margin-bottom:15px;">
                                <select class="form-control select2" id="convert-currency" name="convert_currency">
                                    <option value="">Select Currency</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->code }}" {{ $quote->convert_currency == $currency->code ? 'selected' : ''}}> {{ $currency->name }} ({{ $currency->symbol }}) </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2" style="margin-bottom:15px;">
                                <label class="convert-currency"></label>
                                    <input type="number" name="show_convert_currency" min="0" value="{{$quote->show_convert_currency}}" step="any" class="show-convert-currency hide-arrows" value="0">
                                </label>
                            </div>
                        </div>

                        <div class="row"> 
                            <div class="col-sm-2 col-sm-offset-1" style="margin-bottom:15px;">
                                <label class="" style="margin-right: 10px; margin-bottom: 10px;"> 
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Booking Amount Per Person</label>
                                </label> 
                            </div>

                            <div class="col-sm-2" style="margin-bottom:15px;">
                                <label class="convert-currency"></label>
                                <input type="number" class="per-person hide-arrows" min="0" value="{{$quote->per_person}}" step="any" name="per_person" value="0">
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right">Submit</button>
                        </div>

                    </form>

                </div> 
            </div>  
        </div>
    </section>
</div>

<!-- /.content-wrapper -->
<footer class="main-footer"></footer>

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
                            <h4 class="control-sidebar-subheading">Frodo Updated His Profile
                            </h4>

                            <p>New phone +1(800)555-1234</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Nora Joined Mailing List
                            </h4>

                            <p>nora@example.com</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-file-code-o bg-green"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Cron Job 254 Executed
                            </h4>

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


<script type="text/javascript">

    $(function(){
        $( ".datepicker" ).datepicker({ autoclose: true, format: 'dd/mm/yyyy' });
    });

    $(document).ready(function() {

        $('.currency').html($('select[name="currency"]').val());
        $('.convert-currency').html($('select[name="convert_currency"]').val());


        $('input[type=radio][name=reference]').on('change', function () {
                switch ($(this).val()) {
                    case 'zoho':
                        $('#referencename').text('Zoho Reference');
                    break;
                    
                    case 'tas':
                        $('#referencename').text('TAS Reference');
                    break;
                }
        });
           
           
        var typingTimer;                //timer identifier
        var doneTypingInterval = 2000;  //time in ms, 5 second for example
        var $input = $('input[name="ref_no"]');

        $(document).on('click', '#sendReference', function(){
            $('#link').html('');
            $('#link').removeAttr('class');
            $(this).text('searching');
            $(this).attr('disabled', 'disabled');
            $('#error_ref_no').text('');
            doneTyping();
            
        });
        
        

///tabraiz queries
$(document).on('change', '.uploadCalender', function() {
            
            if ($(this).is(':checked')) {
                $(this).parent().find('input[type=hidden]').attr('disabled', 'disabled');
                $(this).attr('value', 'true');
            } else {
                $(this).parent().find('input[type=hidden]').removeAttr('disabled');
                $(this).attr('value', 'false');
                
            }            
        });        
//tabraiz queries

        //on keyup, start the countdown
        // $input.on('keyup', function () {
        //   clearTimeout(typingTimer);
        //   typingTimer = setTimeout(doneTyping, doneTypingInterval);
        // });

        // //on keydown, clear the countdown 
        // $input.on('keydown', function () {
        //   clearTimeout(typingTimer);
        // });
 
        
        function doneTyping () {
            book_id = $('input[name="ref_no"]').val();
            referenceName = $('input[type=radio][name=reference]:checked').val();

            if(book_id) {
                token = $('input[name=_token]').val();
                data = {id: book_id, reference_name: referenceName};
                url = '{{route('get-ref-detail')}}';
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
                        console.log(data);
                        if(data.hasOwnProperty("response") == true){
                            $('select[name="type_of_holidays"]').val(data.response.internal_information.holiday_type).trigger('change');    
                        }else{
                            $('#error_ref_no').text('The Reference is not found');
                        }
                            $('#sendReference').text('Search');
                            $("#divLoading").removeClass('show');
                            $('#sendReference').removeAttr('disabled');
                    }
                });
            }
        }

        // Dynamically appened qoute 
        $('body').on('click', '#new', function (e) {
            var qoute = $('#qoute').html();
            $("#parent").append(qoute);
            reinitializedDynamicFeilds();
        });

        // Initialize all Select2 
        $('.select2, .category-select2, .supplier-select2, .booking-method-select2, .booked-by-select2, .supplier-currency, .supervisor-select2 ').select2();
        $( ".datepicker" ).datepicker({ autoclose: true, format: 'dd/mm/yyyy' });
        
        function reinitializedDynamicFeilds(){

            $(".supplier-currency, .booked-by-select2, .booking-method-select2, .category-select2, .supplier-select2, .supervisor-select2").removeClass('select2-hidden-accessible').next().remove();
            $(".supplier-currency, .booked-by-select2, .booking-method-select2, .category-select2, .supplier-select2, .supervisor-select2").select2();

            $(".datepicker").datepicker({ autoclose: true, format: 'dd/mm/yyyy'  });
        }

        $(document).on('change', 'select[name="category[]"]',function(){
            
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

                    // console.log(response);

                    options += '<option value="">Select Supplier</option>';

                    $.each(response,function(key,value){
                        options += '<option value="'+value.id+'">'+value.name+'</option>';
                    });

                    $selector.closest('.row').find('[class*="supplier-select2"]').html(options);
                }
            })
        });

        $(document).on('change', 'select[name="supplier_currency[]"]',function(){

            let $selector = $(this);
            let selected_currency_code = $(this).val();
            let currentCost = $selector.closest(".qoute").find('[class*="cost"]').val();

            let final = 0;
            let selectedMainCurrency = $("select[name='currency']").val();

            let costArray = [];
            let currencyArray = [];

            $selector.closest(".qoute").find('[class*="cost"]').attr("data-code",selected_currency_code);
            $selector.closest(".qoute").find('[class*="symbol"]').html(selected_currency_code);


            $('.cost').each(function(){

                cost = $(this).val();
                currency = $(this).attr("data-code");

                if(cost !== "" && cost !== '0'){
                    costArray.push(parseFloat(cost));
                }

                if(currency !== "" && cost !== '0'){
                    currencyArray.push(currency);
                }

            });


            $.ajax({
                type: 'POST',
                url: '{{ route('get-currency') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'to': selectedMainCurrency,
                    'from': selected_currency_code
                },
                success: function(response) {

                 

                    qoute_currency = currentCost * response[selected_currency_code];
                    $selector.closest(".qoute").find('[class*="base-currency"]').val((qoute_currency.toFixed(2)));

                    for(i=0 ; i < currencyArray.length; i++){
                        final += (costArray[i] * response[currencyArray[i]]);
                    }

                    $('.net_price').val(final.toFixed(2));

                    var net_price = parseFloat($('.net_price').val());
                    var markup_percent = parseFloat($('.markup-percent').val());
                    var markup_amount = parseFloat($('.markup-amount').val());
                    markupAmount = (net_price / 100) * markup_percent;
                    $('.markup-amount').val(markupAmount.toFixed(2));

                    var sellingPrice = (markupAmount + net_price);
                    $('.selling').val(sellingPrice.toFixed(2));

                    var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2) ) / sellingPrice.toFixed(2)) * 100);
                    $('.gross-profit').val(grossProfit.toFixed(2));

                }
            });

        });

        var final = 0;
        $(document).on('change', '.cost',function(){

            var cost = $(this).val();
            var currency = $(this).attr("data-code");
            var selectedMainCurrency = $("select[name='currency']").val();
            var final = 0;

            var $selector = $(this);
            // var selected_currency_code = $(this).val();
            // $selector.closest(".qoute").find('[class*="cost"]').attr("data-code",selected_currency_code);

            var costArray = [];
            var currencyArray = [];
      
            $('.cost').each(function(){

                cost = $(this).val();
                currency = $(this).attr("data-code");

                if(cost !== "" && cost !== '0'){
                    costArray.push(parseFloat(cost));
                }

                if(currency !== "" && cost !== '0'){
                    currencyArray.push(currency);
                }

            });

            // console.log(costArray);
            // console.log(currencyArray);
            
            $.ajax({
                type: 'POST',
                url: '{{ route('get-currency') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'to': selectedMainCurrency,
                    'from': currency
                },
                success: function(response) {

                    qoute_currency = $selector.val() * response[$selector.attr("data-code")];
                    $selector.closest(".qoute").find('[class*="base-currency"]').val((qoute_currency.toFixed(2)));
        
                    for(i=0 ; i < currencyArray.length; i++){
                        final += (costArray[i] * response[currencyArray[i]]);
                    }

                    $('.net_price').val(final.toFixed(2));


                    var net_price = parseFloat($('.net_price').val());
                    var markup_percent = parseFloat($('.markup-percent').val());
                    var markup_amount = parseFloat($('.markup-amount').val());
                    markupAmount = (net_price / 100) * markup_percent;
                    $('.markup-amount').val(markupAmount.toFixed(2));

                    var sellingPrice = (markupAmount + net_price);
                    $('.selling').val(sellingPrice.toFixed(2));

                    var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2) ) / sellingPrice.toFixed(2)) * 100);
                    $('.gross-profit').val(grossProfit.toFixed(2));

                    // console.log(last_convert_currency);
                    // var perPersonAmount = sellingPrice / $('select[name="group_no"]').val();
                    // $('.per-person').val(perPersonAmount);

                }
            });
      
        });

        $(document).on('change', '.markup-percent',function(){

            var net_price = parseFloat($('.net_price').val());
            var markup_percent = parseFloat($('.markup-percent').val());
            var markup_amount = parseFloat($('.markup-amount').val());
 
            markupAmount = (net_price / 100) * markup_percent;

            $('.markup-amount').val(markupAmount.toFixed(2));

            var sellingPrice = (markupAmount + net_price);
            $('.selling').val(sellingPrice.toFixed(2));
     
            var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2) ) / sellingPrice.toFixed(2)) * 100);
            $('.gross-profit').val(grossProfit.toFixed(2));     
        });

        $(document).on('change', '.markup-amount',function(){

            var net_price = parseFloat($('.net_price').val());
            var markup_percent = parseFloat($('.markup-percent').val());
            var markup_amount = parseFloat($('.markup-amount').val());

            markupPercentage = markup_amount / (net_price / 100);
            $('.markup-percent').val(parseInt(markupPercentage));

            var sellingPrice = markup_amount + net_price;
            $('.selling').val(sellingPrice.toFixed(2));

            var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2) ) / sellingPrice.toFixed(2)) * 100);
            $('.gross-profit').val(grossProfit.toFixed(2));

            // var perPersonAmount = sellingPrice / $('select[name="group_no"]').val();
            // $('.per-person').val(perPersonAmount);
    
        });

        // $(document).on('change', 'select[name="currency"]',function(){
        //     var selected_currency_code = $(this).val();
        //     $('.currency').html(selected_currency_code);
        // });

        $(document).on('change', 'select[name="currency"]',function(){

            var selected_currency_code = $(this).val();

            var costArray = [];
            var currencyArray = [];
            var selectedMainCurrency = $("select[name='currency']").val();
            var final = 0;
      
            $('.cost').each(function(){

                cost = $(this).val();
                currency = $(this).attr("data-code");

                if(cost !== "" && cost !== '0'){
                    costArray.push(parseFloat(cost));
                }

                if(currency !== "" && cost !== '0'){
                    currencyArray.push(currency);
                }

            });

            $.ajax({
                type: 'POST',
                url: '{{ route('get-currency') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'to': selectedMainCurrency,
                    'from': currency
                },
                success: function(response) {
           
                    for(i=0 ; i < currencyArray.length; i++){
                        $(".base-currency").eq(i+1).val((costArray[i] * response[currencyArray[i]]).toFixed(2));
                        final += (costArray[i] * response[currencyArray[i]]);
                    }

                    $('.net_price').val(final.toFixed(2));

                    var net_price = parseFloat($('.net_price').val());
                    var markup_percent = parseFloat($('.markup-percent').val());
                    var markup_amount = parseFloat($('.markup-amount').val());
                    markupAmount = (net_price / 100) * markup_percent;
                    $('.markup-amount').val(markupAmount.toFixed(2));

                    var sellingPrice = (markupAmount + net_price);
                    $('.selling').val(sellingPrice.toFixed(2));

                    var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2) ) / sellingPrice.toFixed(2)) * 100);
                    $('.gross-profit').val(grossProfit.toFixed(2));

                    // console.log(last_convert_currency);
                    // var perPersonAmount = sellingPrice / $('select[name="group_no"]').val();
                    // $('.per-person').val(perPersonAmount);

                }
            });

            $('.currency').html(selected_currency_code);
        });

        $(document).on('click', '#ab_no',function(){
            $('#agency-detail').css("display", "none");
            $("input[name='agency_name']").prop('required',false);
            $("input[name='agency_contact_no']").prop('required',false);
        });

        $(document).on('click', '#ab_yes',function(){
            $('#agency-detail').css("display", "block");
            $("input[name='agency_name']").prop('required',true);
            $("input[name='agency_contact_no']").prop('required',true);
        });

        // $(document).on('change', 'select[name="convert_currency"]',function(){
        $(document).on('change', '#convert-currency',function(){


            // var selected_currency = 'USD';
            var selected_currency =  $(this).val();
            var selectedMainCurrency = $("select[name='currency']").val();
            var sellingPrice =  $('.selling').val();

            $.ajax({
                type: 'POST',
                url: '{{ route('get-currency') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'to': selected_currency,
                    'from': selectedMainCurrency
                },
                success: function(response) {
             
                    final = sellingPrice * response[selectedMainCurrency];
                    $('.show-convert-currency').val(final.toFixed(2));
                    
                    var group_no = $("select[name='group_no']").val();
                    var perPersonAmount = final / group_no;
                    $('.per-person').val(perPersonAmount.toFixed(2));

                    // var perPersonAmount = parseFloat($('.show-convert-currency').val()) / parseFloat($('select[name="group_no"]').val());
                    // $('.per-person').val(perPersonAmount.toFixed(2));

                    // total_per_person = parseFloat($('.port-tax').val()) + perPersonAmount;
                    // $('.total').val(total_per_person.toFixed(2));

                    $('.convert-currency').text(selected_currency);
                }
            });
        });

        $(document).on('change', 'select[name="group_no"]',function(){

            var group_no = $(this).val();
            var show_convert_currency =  $('.show-convert-currency').val();

            var perPersonAmount = show_convert_currency / group_no;
            $('.per-person').val(perPersonAmount.toFixed(2));

            var port_tax = parseFloat($('.port-tax').val());
            total_per_person = port_tax + perPersonAmount;
            $('.total').val(total_per_person.toFixed(2));
        });

        $(document).on('change', '.port-tax',function(){

            var port_tax = parseFloat($(this).val());
            var perPersonAmount = parseFloat($('.per-person').val());

            total_per_person = port_tax + perPersonAmount;
            $('.total').val(total_per_person.toFixed(2));
        });

        $(document).on('submit','#user_form',function(){

            event.preventDefault();
            var formdata = $(this).serialize();

            $('#error_ref_no, #error_brand_name, #error_type_of_holidays, #error_sale_person, #error_season_id, #error_agency_name, #error_agency_contact_no, #error_currency, #error_group_no,  #error_dinning_preferences, .error-cost, .date_of_service, .booking_date, .booking_due_date').html('');

            jQuery(".finance-row").find(".disable-feild").attr("disabled", "disabled");

            $.ajax({
                type: 'POST',
                url: '{{ route('confirm-booking' , $quote->id  ) }}',
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false, 
                beforeSend: function() {
                    $("#divLoading").addClass('show');
                },
                success: function (data) {
                    $("#divLoading").removeClass('show');
                    alert(data.success_message);

                    // location.reload();
                },
                error: function (reject) {
                if( reject.status === 422 ) {

                    var errors = $.parseJSON(reject.responseText);

                    jQuery.each(errors.errors['date_of_service'], function( index, value ) {
                        jQuery.each(value, function( key, value ) {
                            jQuery(".date_of_service").eq(key).html(value);
                        });
                    });

                    jQuery.each(errors.errors['booking_date'], function( index, value ) {
                        jQuery.each(value, function( key, value ) {
                            jQuery(".booking_date").eq(key).html(value);
                        });
                    });

                    jQuery.each(errors.errors, function( index, value ) {
                        $('#error_'+ index).html(value);

                        if($('#error_'+ index).length){
                            $('html, body').animate({ scrollTop: $('#error_'+ index).offset().top }, 1000);
                        }
                    });

                    // Validating cost feild 
                    var rows = jQuery('.parent .qoute');
                    jQuery.each(rows, function( index, value ) {
                        var error_row = errors.errors['cost.' + index] || null;
                        if(error_row) {
                            jQuery(rows[index]).find('.input-group input.cost').parent().next('.alert-danger').html("Cost feild is required");
                            $('html, body').animate({ scrollTop: $(rows[index]).offset().top }, 1000);
                        }
                    });

                    // Validating booking feild
                    // jQuery.each(rows, function( index, value ) {
                    //     var error_row = errors.errors['booking_due_date.' + index] || null;
                    //     if(error_row) {
                    //         jQuery(rows[index]).find('.booking_due_date').html("Booking Due Date is required");
                    //         $('html, body').animate({ scrollTop: $(rows[index]).offset().top }, 1000);
                    //     }
                    // });

                    
                    jQuery.each(rows, function( index, value ) {
                        var error_row = errors.errors['booking_due_date.' + index]??null;
                        if(error_row == null){
                            if(errors.errors['booking_due_date'] !== undefined){
                                error_row = errors.errors['booking_due_date'][index]??null;
                            }else{
                                error_row = null;
                            }
                        }
                        if(error_row && Array.isArray(error_row) == true) {
                            jQuery(rows[index]).find('.booking_due_date').html("Booking Due Date is required");
                            $('html, body').animate({ scrollTop: $(rows[index]).offset().top }, 1000);
                        }else{
                            jQuery.each(error_row, function( key, value ) {
                                jQuery(".booking_due_date").eq(key).html(value);
                            });
                        }
                    });

                    $("#divLoading").removeClass('show');
                }
                }
            });

        });

        $(document).on('click', '.close',function(){
            $(this).closest(".qoute").remove();
        });
        
        $(document).on('click', '.add_finance',function(){


            // $(".disable-feild").attr( "disabled", "disabled" );
            // $(".disable-feild").prop("disabled", false);
            
            let $selector = $(this);
            let html = $selector.closest(".qoute").find('[class*="finance-row"]').html();

            $selector.closest(".qoute").append(html);

            $( ".datepicker" ).datepicker({ autoclose: true, format: 'dd/mm/yyyy' });

            
            $(".booking-method-select2").removeClass('select2-hidden-accessible').next().remove();
            $(".booking-method-select2").select2();

            console.log(html);
        });

        $(document).on('click', '.upload_to_google_calendar',function(){

let $selector = $(this);
let depositAmount     = $selector.closest(".row").find('[class*="deposit_amount"]').val();
let deposit_due_date  = $selector.closest(".row").find('[class*="deposit_due_date"]').val();
let supplier_currency = $selector.closest(".qoute").find('select[name="supplier_currency[]"]').val();

var data = {
    "_token": "{{ csrf_token() }}",
    "deposit_amount": depositAmount,
    "supplier_currency": supplier_currency,
    "deposit_due_date": deposit_due_date,
    "details": window.location.href,
}

$.ajax({
    type: 'POST',
    url: '{{ route('upload-to-calendar') }}',
    data: data ,
    beforeSend: function() {},
    success: function (data) {

        console.log(data);
        // window.open(data, "_blank");
        // $(this).attr("href", data); // Set herf value

    },
});

});

        $(document).on('click', '#export-to-csv',function(){

            // let $selector = $(this);

            // let depositAmount     = $selector.closest(".row").find('[class*="deposit_amount"]').val();
            // let deposit_due_date  = $selector.closest(".row").find('[class*="deposit_due_date"]').val();
            // let supplier_currency = $selector.closest(".qoute").find('select[name="supplier_currency[]"]').val();

            var data = {
                "_token": "{{ csrf_token() }}",
            }

            $.ajax({
                type: 'POST',
                url: '{{ route('export_to_csv' , $quote->id  ) }}',
                data: data ,
                beforeSend: function() {},
                success: function (data) {

                },
            });

        });

        $(document).on('click', '.remove_finance',function(){
            $(this).closest(".row").remove();
        });

        // auto select default currency of supplier
        $(document).on('change', 'select[name="supplier[]"]',function(){

            var $selector = $(this);
            var supplier_id = $(this).val();

            $.ajax({
                type: 'POST',
                url: '{{ route('get-supplier-currency') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'supplier_id': supplier_id
                },
                success: function(response) {

                    $selector.closest('.qoute').find('[class*="supplier-currency"]').val(response.code).change();
                }
            })
        });

        $(document).on('change', 'select[name="booked_by[]"]',function(){

            var $selector = $(this);
            var booked_by = $(this).val();

            $.ajax({
                type: 'POST',
                url: '{{ route('get-saleagent-supervisor') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'booked_by': booked_by
                },
                success: function(response) {

                    $selector.closest('.qoute').find('[class*="supervisor-select2"]').val(response.supervisor_id).change();
                }
            })
        });
        
    });
</script>




</body>

</html>
@endsection