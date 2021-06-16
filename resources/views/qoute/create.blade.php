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
                    <button type="button" class="btn  pull-right close"> x </button>
                </div>
            </div><br>
            <div class="row">
                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Date of Service</label> 
                    <div class="input-group">
                        <input type="text" name="date_of_service[]"  class="form-control datepicker bookingDateOfService" placeholder="Date of Service" autocomplete="off" >
                    </div>
                    <div class="alert-danger date_of_service" style="text-align:center">  </div>
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
                    <select class="form-control supplier-select2 " name="supplier[]" >
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
                        <input type="text" name="booking_date[]" value="" class="form-control datepicker bookingDate" placeholder="Booking Date" autocomplete="off" value="{{old('booking_date')}}" >
                    </div>
                    <div class="alert-danger booking_date" style="text-align:center"> {{ $errors->first('booking_date') }} </div>
                </div>
                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booking Due Date <span style="color:red">*</span></label> 
                    <div class="input-group">
                        <input type="text" name="booking_due_date[]" value="" class="form-control datepicker bookingDueDate" autocomplete="off" placeholder="Booking Due Date" >
                    </div>
                    <div class="alert-danger booking_due_date" style="text-align:center; width: 160px;"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booking Method</label> 
                    <div class="input-group">
                        <select class="form-control  booking-method-select2"  name="booking_method[]" >
                            <option value="">Select Booking Method</option>
                            @foreach ($booking_methods as $booking_method)
                                <option value="{{$booking_method->id}}" {{ $booking_method->name == 'Supplier Own' ? 'selected' : '' }}>{{$booking_method->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                </div>
                <div class="col-sm-2 " style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booked By </label> 
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
                <div class="col-sm-2 " style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booking Type</label> 
                    <div class="input-group">
                        <select class="form-control booking-type-select2" name="booking_type[]" >
                            <option value="">Select Booking Type</option>
                            <option value="refundable">Refundable</option>
                            <option value="non_refundable">Non-Refundable</option>
                        </select>
                    </div>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_type') }} </div>
                </div>
                <div class="col-sm-2" style="margin-bottom: 35px;">
                    <label for="inputEmail3" class="">Comments</label> 
                    <textarea name="comments[]" id="" class="form-control" cols="30" rows="1"></textarea>
                    <div class="alert-danger" style="text-align:center">{{ $errors->first('service_details') }}</div>
                </div>
                <div class="col-sm-2" style="margin-bottom:15px;">
                    <label class="">Select Supplier Currency</label> 
                    <select class="form-control supplier-currency"  name="supplier_currency[]" >
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
                    <label for="inputEmail3" class="">Estimsted Cost <span style="color:red">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon symbol"></span>
                        <input type="number" name="cost[]" data-code="" class="form-control cost" placeholder="Cost" min="0" value="0"  >
                    </div>
                    <div class="alert-danger error-cost" style="text-align:center"></div>
                </div>
                <div class="col-sm-2" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Booking Currency Conversion</label>
                    <label class="currency"></label>  
                    <input type="text" class="base-currency" name="qoute_base_currency[]" value="0.00" readonly><br>
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
                                <option value="{{$supervisor->id}}" {{ (isset(Auth::user()->getSupervisor))? ((Auth::user()->getSupervisor->id == $supervisor->id)? 'selected': NULL) : NULL }}   >{{$supervisor->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert-danger" style="text-align:center"> </div>
                </div>
            </div>
        </div>
    </div>
    <section class="content-header">
        <h1> Add Quote</h1>
    </section>
    <section class="content">
        <div id="divLoading"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border mb-2">
                        <h3 class="box-title">Create Quote</h3>
                    </div>
                    <div class="col-sm-6 col-sm-offset-3" style="text-align: center;">
                        @if (Session::has('success_message'))
                        <div class="alert alert-success">
                            {{ Session::get('success_message') }}</div>
                        @endif
                    </div>
                    <form method="POST" id="user_form" action="javascript:void(0)" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="row">
                                <div class="col-md-5 col-sm-offset-1 mb-2">
                                    <label>Select the reference <span style="color:red">*</span></label> <br />
                                    <label class="radio-inline"><input type="radio"  name="reference" value="zoho" checked>Zoho Reference</label>
                                    {{-- <label class="radio-inline"><input type="radio"  name="reference" value="tas" >TAS Reference</label> --}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5 col-sm-offset-1 mb-2">
                                    <label for="inputEmail3" id="referencename">Zoho Reference</label> <span style="color:red">*</span>
                                    <div class="input-group">
                                        <input type="text" name="ref_no"  class="form-control" placeholder='Enter Reference Number' >
                                        <span  id="link">
                                        </span>
                                        <span class="input-group-addon">
                                            <button id="sendReference" type="button" class="btn-link"> Search </button>
                                        </span>
                                    </div>
                                    <div class="alert-danger" style="text-align:center" id="error_ref_no"></div>
                                </div>
                                <div class="col-sm-5 mb-2">
                                    <label for="inputEmail3" class="">Quote Reference</label> <span style="color:red">*</span>
                                    <div class="input-group">
                                        <input type="text" name="quotation_no" class="form-control" value="{{ uniqid() }}" >
                                        <span class="input-group-addon"></span>
                                    </div>
                                    <div class="alert-danger" style="text-align:center" id="error_quotation_no"></div>
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-sm-5 mb-2 col-sm-offset-1 mb-2">
                                <label for="inputEmail3" class="">Lead Passenger Name</label> <span style="color:red">*</span>
                                <div class="input-group">
                                    <input type="text" name="lead_passenger_name" class="form-control"  >
                                    <span class="input-group-addon"></span>
                                </div>
                                <div class="alert-danger" style="text-align:center" id="error_lead_passenger_name"></div>
                            </div>
                            <div class="col-sm-5">
                                <label class="">Brand Name</label> <span style="color:red">*</span>
                                <select class="form-control select2" name="brand_name"  >
                                    <option value="">Select Brand</option>
                                    @foreach ($get_user_branche['branches'] as $branche)
                                    <option {{ (Auth::user()->brand_name == $branche['name'])? 'selected':'' }} value="{{ $branche['name'] }}" >{{ $branche['name'] }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_brand_name"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1 mb-2">
                                <label class="">Type Of Holidays</label> <span style="color:red">*</span>
                                <select class="form-control select2" id="type_of_holidays" name="type_of_holidays" >
                                    <option value="">Select Holiday</option>
                                    @foreach ($get_holiday_type->holiday_type as $holiday)
                                    <option value="{{ $holiday->name }}">{{ $holiday->name }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_type_of_holidays"></div>
                            </div>
                            <div class="col-sm-5 mb-2">
                                <label class="">Sales Person</label> <span style="color:red">*</span>
                                <select class="form-control select2" id="sales_person" name="sale_person"  >
                                    <option value="">Select Person</option>
                                    @foreach ($users as $user)
                                    <option {{ (Auth::user()->email == $user['email'])? 'selected':'' }} value="{{ $user['email'] }}" > {{ $user['name'] }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_sale_person"> </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1 mb-2">
                                <label class="">Booking Season</label> 
                                <span style="color:red">*</span>
                                {{-- <input type="text" name="season_id" class="form-control"   readonly> --}}
                                <select class="form-control dropdown_value" name="season_id" id="bookingSeason"  >
                                    <option value="">Select Season</option>
                                    @foreach ($seasons as $sess)
                                        <option value="{{ $sess->id }}"  {{ (old('season_id') == $sess->id)? 'selected' :(($sess->default_season == 1 )? 'selected': NULL) }}>{{ $sess->name }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_season_id"> </div>
                            </div>
                            <div class="col-sm-1 mb-2">
                                <label for="inputEmail3" class="">Agency Booking</label> <span style="color:red"> *</span><br>
                                <input type="radio" name="agency_booking" value="2" id="ab_yes"> <label for="ab_yes"> Yes</label>
                                <input type="radio" name="agency_booking" value="1"  id="ab_no" checked> <label for="ab_no"> No</label>
                                <div class="alert-danger" style="text-align:center" > </div>
                            </div>
                            <div class="row" style="display:none;" id="agency-detail">
                                <div class="col-sm-2" style="width:175px;">
                                    <label for="inputEmail3" class="">Agency Name</label> <span style="color:red"> *</span>
                                    <input type="text" name="agency_name" class="form-control">
                                    <div class="alert-danger" style="text-align:center" id="error_agency_name"> </div>
                                </div>
                                <div class="col-sm-2">
                                    <label for="inputEmail3" class="">Agency Contact No.</label> <span style="color:red"> *</span>
                                    <input type="text" name="agency_contact_no" class="form-control">
                                    <div class="alert-danger" style="text-align:center" id="error_agency_contact_no"> </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1 mb-2">
                                <label> Booking Currency</label> <span style="color:red">*</span>
                                <select name="currency" class="form-control select2" >
                                    <option value="">Select Currency</option>
                                    @foreach ($currencies as $currency)
                                        <option {{ (Auth::user()->currency_id == $currency['id'])? 'selected':'' }} value="{{ $currency->code }}"  > {{ $currency->name }} ({{ $currency->symbol }}) </option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_currency"></div>
                            </div>
                            <div class="col-sm-5 mb-2">
                                <label class="">Pax No.</label> <span style="color:red">*</span>
                                  <select class="form-control dropdown_value select2" name="group_no" >
                                    @for($i=1;$i<=30;$i++)
                                    <option value={{$i}} >{{$i}}</option>
                                    @endfor
                                  </select>
                                <div class="alert-danger" style="text-align:center" id="error_group_no"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1 mb-2">
                                <label> Dinning Preferences</label> <span style="color:red">*</span>
                                <input type="text" name="dinning_preferences" class="form-control">
                                <div class="alert-danger" style="text-align:center" id="error_dinning_preferences"></div>
                            </div>
                        </div>
                        <br><br>
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-10 col-sm-offset-1 mb-2">
                                    <div class="pull-right">
                                        <select class="form-control" id="template" >
                                            <option  selected >Select Any Template</option>
                                            @foreach ($templates as $temp)
                                            <option value="{{ $temp->id }}">{{ $temp->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="parent" id="parent">
                            <div class="qoute">
                                <div class="row mt-3">
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Date of Service</label> 
                                        <div class="input-group">
                                            <input type="text" name="date_of_service[]"  class="form-control datepicker checkDates bookingDateOfService" autocomplete="off" placeholder="Date of Service"  >
                                        </div>
                                        <div class="alert-danger date_of_service" style="text-align:center"></div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Service Details</label> 
                                        <textarea name="service_details[]"  class="form-control" cols="30" rows="1"></textarea>
                                        <div class="alert-danger" style="text-align:center">{{ $errors->first('service_details') }}</div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label class="">Select Category</label> 
                                        <select class="form-control category-select2"  name="category[]" >
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category') == $category->id  ? "selected" : "" }}> {{ $category->name }} </option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('category') }} </div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label class="test">Select Supplier</label> 
                                        <select class="form-control supplier-select2"  name="supplier[]" >
                                            <option value="">Select Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier') == $supplier->id  ? "selected" : "" }}> {{ $supplier->name }} </option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('supplier') }} </div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="test222">Booking Date</label>
                                        <div class="input-group">
                                            <input type="text" name="booking_date[]" value="" class="form-control datepicker bookingDate" placeholder="Booking Date" autocomplete="off" value="{{old('booking_date')}}" >
                                        </div>
                                        <div class="alert-danger booking_date" style="text-align:center"> {{ $errors->first('booking_date') }} </div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Booking Due Date <span style="color:red">*</span></label> 
                                        <div class="input-group">
                                            <input type="text" name="booking_due_date[]"   class="form-control datepicker checkDates bookingDueDate" autocomplete="off" placeholder="Booking Due Date" >
                                        </div>
                                        <div class="alert-danger booking_due_date" style="text-align:center; width: 160px;"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Booking Method</label>
                                        <div class="input-group">
                                            <select class="form-control  booking-method-select2"  name="booking_method[]" class="form-control" >
                                                <option value="">Select Booking Method</option>
                                                @foreach ($booking_methods as $booking_method)
                                                <option value="{{$booking_method->id}}" {{ $booking_method->name == 'Supplier Own' ? 'selected' : '' }}>{{$booking_method->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Booked By </label>
                                        <div class="input-group">
                                            <select class="form-control booked-by-select2"  name="booked_by[]"  class="form-control" >
                                                <option value="">Select Person</option>
                                                @foreach ($users as $user)
                                                    <option value="{{$user->id}}" {{ !empty(Auth::user()->id) && Auth::user()->id == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Booking Reference</label>
                                        <div class="input-group">
                                            <input type="text" name="booking_refrence[]" value="" class="form-control" placeholder="Booking Reference" value="{{old('booking_refrence')}}" >
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> </div>
                                    </div>
                                    <div class="col-sm-2 " style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Type</label> 
                                        <div class="input-group">
                                            <select class="form-control booking-type-select2"  name="booking_type[]" >
                                                <option value="">Select Booking Type</option>
                                                <option value="refundable">Refundable</option>
                                                <option value="non_refundable">Non-Refundable</option>
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_type') }} </div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Comments</label> 
                                        <textarea name="comments[]"   class="form-control" cols="30" rows="1"></textarea>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label>Select Supplier Currency</label> 
                                        <select class="form-control supplier-currency" name="supplier_currency[]" >
                                            <option value="">Select Currency</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->code }}"> {{ $currency->name }} ({{ $currency->symbol }}) </option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Estimated Cost</label> <span style="color:red">*</span>
                                        <div class="input-group">
                                            <span class="input-group-addon symbol" ></span>
                                            <input type="number" data-code="" name="cost[]" class="form-control cost" min="0" value="0" placeholder="Cost" >
                                        </div>
                                        <div class="alert-danger error-cost" style="text-align:center" ></div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Booking Currency Conversion</label>
                                        <label class="currency"></label>  
                                        <input type="text" class="base-currency" name="qoute_base_currency[]" value="0.00" readonly><br>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Added in Sage</label>
                                        <div class="input-group">
                                            <input type="hidden" name="added_in_sage[]" value="0"><input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value">
                                        </div>
                                    </div>
                                    <div class="col-sm-2 mb-3">
                                        <label for="inputEmail3" class="">Supervisor</label>
                                        <div class="input-group">
                                            <select class="form-control supervisor-select2"  name="supervisor[]" class="form-control" >
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
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-10 col-sm-offset-1">
                                <button type="button" id="new" class="btn btn-info pull-right">+ </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 col-sm-offset-1">
                                <div class="row">
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Net Price</label>
                                </div>
                                <div class="row"> 
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Markup</label>
                                </div>
                                <div class="row"> 
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Selling Price</label>
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
                                        <input type="number" name="net_price" step="any" min="0" class="net_price hide-arrows" value="0.00">
                                    </label>
                                </div>
                                <div class="row">
                                    <label class="">
                                        <label class="currency" ></label>
                                        <input type="number" class="markup-amount" step="any" min="0" name="markup_amount" value="0.00">
                                    </label>
                                </div>
                                <div class="row">
                                    <label class="">
                                        <label class="currency" ></label>
                                        <input type="number" class="selling hide-arrows" min="0" step="any" name="selling" value="0.00">
                                    </label>
                                </div>
                                <div class="row">
                                    <label class="">
                                        <label class="currency" ></label>
                                        <input type="number" class="gross-profit hide-arrows" min="0" step="any" name="gross_profit" value="0.00">
                                        <span>%</span> 
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <br>
                                <div class="row">
                                    <label class="">
                                        <input type="number" class="markup-percent" min="0" name="markup_percent" value="0" style="width:70px;">
                                        <span>%</span> 
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 col-sm-offset-1 mb-2">
                                <label for="">
                                    Selling Price in Other Currency
                                </label>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-sm-2 col-sm-offset-1 mb-2">
                                <label class="" style="margin-right: 10px; margin-bottom: 10px;"> 
                                    {{-- <button type="button" >Convert to USD</button> --}}
                                    <select class="form-control select2" id="convert-currency" name="convert_currency">
                                        <option value="">Select Currency</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->code }}"> {{ $currency->name }} ({{ $currency->symbol }}) </option>
                                        @endforeach
                                    </select>
                                </label> 
                                {{-- <select class="form-control convert-currency"  id="convert-currency" name="convert_currency" >
                                    <option value="">Select Currency</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->code }}" > {{ $currency->name }} ({{ $currency->symbol }}) </option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('category') }} </div> --}}
                            </div>
                            <div class="col-sm-2 mb-2">
                                <label class="convert-currency"></label>
                                    <input type="number" name="show_convert_currency" min="0" step="any" class="show-convert-currency hide-arrows" value="0.00">
                                </label>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-sm-2 col-sm-offset-1 mb-2">
                                <label class="" style="margin-right: 10px; margin-bottom: 10px;"> 
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Booking Amount Per Person</label>
                                </label> 
                            </div>
                            <div class="col-sm-2 mb-2">
                                <label class="convert-currency"></label>
                                <input type="number" class="per-person hide-arrows" step="any" min="0" name="per_person" value="0.00">
                            </div>
                        </div>
                        {{-- <div class="row"> 
                            <div class="col-sm-2 col-sm-offset-1" style="margin-bottom:15px;">
                                <label class="" style="margin-right: 10px; margin-bottom: 10px;"> 
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Include Port Charges</label>
                                </label> 
                            </div>
                            <div class="col-sm-2" style="margin-bottom:15px;">
                                <label class="convert-currency"></label>
                                <input type="number" class="port-tax" step="any" min="0" name="port_tax" value="0">
                            </div>
                        </div> --}}
                        {{-- <div class="row"> 
                            <div class="col-sm-2 col-sm-offset-1" style="margin-bottom:15px;">
                                <label class="" style="margin-right: 10px; margin-bottom: 10px;"> 
                                    <label style="margin-right: 10px; margin-bottom: 10px;">Total Per Person</label>
                                </label> 
                            </div>
                            <div class="col-sm-2" style="margin-bottom:15px;">
                                <label class="convert-currency"></label>
                                <input type="number" class="total hide-arrows" min="0" step="any" name="total_per_person" value="0">
                            </div>
                        </div> --}}
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
<!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
{{-- {!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!} --}}
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
    $(function(){
        datePickerSetDate();
    });
    $(document).ready(function() {
        $(document).on('change', '#template', function(e) {
            e.preventDefault();
            var id = $(this).val();
            
            if (confirm('Are you sure you want to lose your data into the template??' ,'yes')) {
                if(id != null){
                    var url = '{{ route("template.partial", ":id") }}';
                    url = url.replace(':id', id);
                    token = $('input[name=_token]').val();
                    data = {id: id};
                    $.ajax({
                        url: url,
                        headers: {'X-CSRF-TOKEN': token},
                        data : data,
                        type: 'get',
                        // dataType: "json",
                        success:function(data) {
                            console.log(data);
                            $('#parent').children( ".qoute" ).remove();
                            // $('.qoute').remove();
                            $('#parent').append(data.template_view);
                            // $('.select2').select2();
                            reinitializedDynamicFeilds()
                            datePickerSetDate();
                            $('#bookingSeason').val(data.template.season_id).trigger('change'); 
                        } //end success
                    });
                }
            }
        })
        $(document).on('change', '.datepicker', function (event) {
                var season_id   = $('#bookingSeason').val();
                var season      = {!! json_encode($seasons->toArray()) !!};
                var item        = season.filter(function(a){ return a.id == season_id })[0];
                var startdate   = new Date(item.start_date);
                var enddate     = new Date(item.end_date);
                var date        = $(this).val();
                var name        = $(this).attr("name");
                var $selector   = $(this);
                var inValBookingDate    = $selector.closest(".qoute").find("input[name='booking_date[]']").val();
                var inValDateOfService  = $selector.closest(".qoute").find("input[name='date_of_service[]']").val();
                var inValBookingDueDate = $selector.closest(".qoute").find("input[name='booking_due_date[]']").val();
                switch (name) {
                    case 'date_of_service[]':
                            var booking_due_date = (inValBookingDueDate != '')? convertDate(inValBookingDueDate): startdate;
                            var booking_dateofservice = (date != '')? convertDate(date): enddate;
                            if(booking_dateofservice > startdate ){
                                booking_dateofservice.setDate(booking_dateofservice.getDate() - 1);
                            }
                            $selector.closest(".qoute").find("input[name='booking_date[]']").datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: booking_due_date, endDate: booking_dateofservice});
                            booking_dateofservice = (inValBookingDate != '')? convertDate(inValBookingDate): booking_dateofservice;
                            $selector.closest(".qoute").find("input[name='booking_due_date[]']").datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: startdate, endDate: booking_dateofservice});
                        break;
                    case 'booking_date[]':
                        var booking_date = (date != '')? convertDate(date): startdate;
                        $selector.closest(".qoute").find('[class*="bookingDateOfService"]').datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: booking_date, endDate: enddate});
                        booking_date = (date != '')? convertDate(date): (inValDateOfService != '')? convertDate(inValDateOfService) : enddate;
                        $selector.closest(".qoute").find('[class*="bookingDueDate"]').datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: startdate, endDate: booking_date});
                        break;
                    case 'booking_due_date[]':
                        var booking_due_date = convertDate(date);
                        booking_dateofservice = (inValDateOfService != '')? convertDate(inValDateOfService): enddate;
                        $selector.closest(".qoute").find('[class*="bookingDate"]').datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: booking_due_date, endDate: booking_dateofservice});
                        booking_due_date = (inValBookingDate != '')? convertDate(inValBookingDate): booking_due_date;
                            $selector.closest(".qoute").find('[class*="bookingDateOfService"]').datepicker('remove').datepicker({ autoclose: true, format:'dd/mm/yyyy', startDate: booking_due_date, endDate: enddate});
                    break;
                }
            // });
        });
        // $('.checkDates').on('click', function() {
        //         var select = $(this);
        //         var name = $(this).attr("name");
        //         console.log(name);
        //         // select.closest('.row').find('[class*="supplier-select2"]').html(options);
        // });
        /////////////
        $(function () {
            $('#bookingSeason').change(function() {
                datePickerSetDate();
            })
        })
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
        $(document).on('click', '#sendReference', function(){
            $('#link').html('');
            $('#link').removeAttr('class');
            $(this).text('Searching');
            $(this).attr('disabled', 'disabled');
            $('#error_ref_no').text('');
            doneTyping();
        });
        var typingTimer;                //timer identifier
        var doneTypingInterval = 2000;  //time in ms, 5 second for example
        var $input = $('input[name="ref_no"]');
        //user is "finished typing," do something
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
                        if( Object.keys(data).length > 0 ){
                            $('select[name="type_of_holidays"]').val(data.holiday_type).trigger('change'); 
                            // $('select[name="sale_person"]').val(data.sale_person).trigger('change');  
                            // $('select[name="currency"]').val(data.currency).trigger('change');  
                            $('select[name="group_no"]').val(data.pax).trigger('change');  
                        }else{
                            $('#error_ref_no').text('The Reference is not found');
                        }
                        $('#sendReference').text('Search');
                        $("#divLoading").removeClass('show');
                        $('#sendReference').removeAttr('disabled');
                    } //end success
                });
            }
        }
        // $('.select2').select2();
        // $('#category-select2').select2();
        // $('#supplier-select2').select2();
        // $('#booking-method-select2').select2();
        // $('#booked-by-select2').select2();
        // $('#supplier-currency').select2();
        // $('#supervisor-select2').select2();
        // $('#booking-type-select2').select2();
        $('.select2, .category-select2, .supplier-select2, .booking-method-select2, .booked-by-select2, .supplier-currency, .supervisor-select2, .booking-type-select2').select2();
        // $('#convert-currency').select2();
        $('body').on('click', '#new', function (e) {
            var qoute = $('#qoute').html();
            $("#parent").append(qoute);
            reinitializedDynamicFeilds();
        });
        function reinitializedDynamicFeilds(){
            // $(".category-select2:last").select2();
            // $(".supplier-select2:last").select2();
            // $(".booking-method-select2:last").select2();
            // $(".booked-by-select2:last").select2();
            // $('.supplier-currency:last').select2();
            // $('.supervisor-select2:last').select2();
            // $('.booking-type-select2').select2();
            $(".supplier-currency, .booked-by-select2, .booking-method-select2, .category-select2, .supplier-select2, .supervisor-select2, .booking-type-select2").removeClass('select2-hidden-accessible').next().remove();
            $(".supplier-currency, .booked-by-select2, .booking-method-select2, .category-select2, .supplier-select2, .supervisor-select2, .booking-type-select2").select2();        
            // $(".datepicker").datepicker({ autoclose: true, format: 'dd-mm-yyyy'  });
            datePickerSetDate('reinitializedDynamicFeilds');
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
                    // $selector.closest(".qoute").find('[class*="base-currency"]').val((qoute_currency.toFixed(2)));
                    $selector.closest(".qoute").find('[class*="base-currency"]').val((!isNaN(parseFloat(qoute_currency)) ? parseFloat(qoute_currency).toFixed(2) : parseFloat(0).toFixed(2) ));
                    for(i=0 ; i < currencyArray.length; i++){
                        final += (costArray[i] * response[currencyArray[i]]);
                    }
                    // $('.net_price').val(final.toFixed(2));
                    $('.net_price').val((isNaN(parseFloat(final)) ?  parseFloat(0).toFixed(2) : parseFloat(final).toFixed(2) ));
                    var net_price = parseFloat($('.net_price').val());
                    var markup_percent = parseFloat($('.markup-percent').val());
                    var markup_amount = parseFloat($('.markup-amount').val());
                    markupAmount = (net_price / 100) * markup_percent;
                    // $('.markup-amount').val(markupAmount.toFixed(2));
                    $('.markup-amount').val((isNaN(parseFloat(markupAmount)) ?  parseFloat(0).toFixed(2) : parseFloat(markupAmount).toFixed(2) ));
                    var sellingPrice = (markupAmount + net_price);
                    // $('.selling').val(sellingPrice.toFixed(2));
                    $('.selling').val((isNaN(parseFloat(sellingPrice)) ?  parseFloat(0).toFixed(2) : parseFloat(sellingPrice).toFixed(2) ));
                    var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2) ) / sellingPrice.toFixed(2)) * 100);
                    $('.gross-profit').val((!isNaN(parseFloat(grossProfit)) ? parseFloat(grossProfit).toFixed(2) : parseFloat(0).toFixed(2) ));
                    // $('.gross-profit').val(grossProfit.toFixed(2));
                }
            });
        });
        $(document).on('change', '.cost',function(){
            var cost = $(this).val();
            var currency = $(this).attr("data-code");
            var selectedMainCurrency = $("select[name='currency']").val();
            var final = 0;
            var $selector = $(this);
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
            $.ajax({
                type: 'POST',
                url: '{{ route('get-currency') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'to': selectedMainCurrency,
                    'from': $selector.attr("data-code")
                },
                success: function(response) {
                    qoute_currency = $selector.val() * response[$selector.attr("data-code")];
                    $selector.closest(".qoute").find('[class*="base-currency"]').val((!isNaN(parseFloat(qoute_currency)) ? parseFloat(qoute_currency.toFixed(2)) : parseFloat(0).toFixed(2) ));
                    for(i=0 ; i < currencyArray.length; i++){
                        final += (costArray[i] * response[currencyArray[i]]);
                    }
                    // $('.net_price').val(final.toFixed(2));
                    $('.net_price').val((isNaN(parseFloat(final)) ?  parseFloat(0).toFixed(2) : parseFloat(final).toFixed(2) ));
                    var net_price = parseFloat($('.net_price').val());
                    var markup_percent = parseFloat($('.markup-percent').val());
                    var markup_amount = parseFloat($('.markup-amount').val());
                    markupAmount = (net_price / 100) * markup_percent;
                    // $('.markup-amount').val(markupAmount.toFixed(2));
                    $('.markup-amount').val((isNaN(parseFloat(markupAmount)) ?  parseFloat(0).toFixed(2) : parseFloat(markupAmount).toFixed(2) ));
                    var sellingPrice = (markupAmount + net_price);
                    // $('.selling').val(sellingPrice.toFixed(2));
                    $('.selling').val((isNaN(parseFloat(sellingPrice)) ?  parseFloat(0).toFixed(2) : parseFloat(sellingPrice).toFixed(2) ));
                    var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2) ) / sellingPrice.toFixed(2)) * 100);
                    // $('.gross-profit').val(grossProfit.toFixed(2));
                    $('.gross-profit').val((!isNaN(parseFloat(grossProfit)) ? parseFloat(grossProfit).toFixed(2) : parseFloat(0).toFixed(2) ));
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
            var grossProfit = (((sellingPrice - net_price ) / sellingPrice) * 100)
            $('.gross-profit').val(grossProfit.toFixed(2));
            // var perPersonAmount = sellingPrice / $('select[name="group_no"]').val();
            // $('.per-person').val(perPersonAmount);
        });
        $(document).on('change', '.markup-amount',function(){
            var net_price = parseFloat($('.net_price').val());
            var markup_percent = parseFloat($('.markup-percent').val());
            var markup_amount = parseFloat($('.markup-amount').val());
            markupPercentage = markup_amount / (net_price / 100);
            $('.markup-percent').val(parseInt(markupPercentage));
            var sellingPrice = markup_amount + net_price;
            $('.selling').val(sellingPrice.toFixed(2));
            var grossProfit = (((sellingPrice - net_price ) / sellingPrice) * 100)
            $('.gross-profit').val(grossProfit.toFixed(2));
            // var perPersonAmount = sellingPrice / $('select[name="group_no"]').val();
            // $('.per-person').val(perPersonAmount);
        });
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
                        // $('.net_price').val((isNaN((costArray[i] * response[currencyArray[i]]).toFixed(2)) ?  parseFloat(0).toFixed(2) : (costArray[i] * response[currencyArray[i]]).toFixed(2) ));
                        $(".base-currency").eq(i+1).val((isNaN((costArray[i] * response[currencyArray[i]]).toFixed(2)) ? parseFloat(0).toFixed(2) : (costArray[i] * response[currencyArray[i]]).toFixed(2) ));
                        final += (costArray[i] * response[currencyArray[i]]);
                    }
                    // $('.net_price').val(final.toFixed(2));
                    $('.net_price').val((isNaN(parseFloat(final)) ?  parseFloat(0).toFixed(2) : parseFloat(final).toFixed(2) ));
                    var net_price = parseFloat($('.net_price').val());
                    var markup_percent = parseFloat($('.markup-percent').val());
                    var markup_amount = parseFloat($('.markup-amount').val());
                    markupAmount = (net_price / 100) * markup_percent;
                    // $('.markup-amount').val(markupAmount.toFixed(2));
                    $('.markup-amount').val((isNaN(parseFloat(markupAmount)) ?  parseFloat(0).toFixed(2) : parseFloat(markupAmount).toFixed(2) ));
                    var sellingPrice = (markupAmount + net_price);
                    $('.selling').val((isNaN(parseFloat(sellingPrice)) ?  parseFloat(0).toFixed(2) : parseFloat(sellingPrice).toFixed(2) ));
                    // $('.selling').val(sellingPrice.toFixed(2));
                    var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2) ) / sellingPrice.toFixed(2)) * 100);
                    $('.gross-profit').val((!isNaN(parseFloat(grossProfit)) ? parseFloat(grossProfit).toFixed(2) : parseFloat(0).toFixed(2) ));
                    // $('.gross-profit').val(grossProfit.toFixed(2));
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
                    // console.log(response[selectedMainCurrency]);
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
            $('.total').val(total_per_person);
        });
        $(document).on('change', '.port-tax',function(){
            var port_tax = parseFloat($(this).val());
            var perPersonAmount = parseFloat($('.per-person').val());
            total_per_person = port_tax + perPersonAmount;
            $('.total').val(total_per_person);
        });
        $(document).on('submit','#user_form',function(){
            // $('#user_form').on('submit', function(event){
            event.preventDefault();
            var formdata = $(this).serialize();
            $('#error_ref_no, #error_brand_name, #error_lead_passenger_name , #error_type_of_holidays, #error_sale_person, #error_season_id, #error_agency_name, #error_agency_contact_no, #error_currency, #error_group_no, #error_dinning_preferences, .error-cost, .date_of_service, .booking_date, .booking_due_date').html('');
            $.ajax({
                type: 'POST',
                url: '{{ route('creat-quote') }}',
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
                   window.location.href = "{{ route('view-quote')}}";
                },
                error: function (reject) {
                if( reject.status === 422 ) {
                    var errors = $.parseJSON(reject.responseText);
                    jQuery.each(errors.errors, function( index, value ) {
                        $('#error_'+ index).html(value);
                        if($('#error_'+ index).length){
                            $('html, body').animate({ scrollTop: $('#error_'+ index).offset().top }, 1000);
                        }
                    });
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
                    // jQuery.each(errors.errors['booking_due_date'], function( index, value ) {
                    //     jQuery(".booking_due_date").eq(value+1).html(value);
                    // });
                    var rows = jQuery('.parent .qoute');
                    jQuery.each(rows, function( index, value ) {
                        var error_row = errors.errors['cost.' + index] || null;
                        if(error_row) {
                            jQuery(rows[index]).find('.input-group input.cost').parent().next('.alert-danger').html(error_row);
                            $('html, body').animate({ scrollTop: $(rows[index]).offset().top }, 1000);
                        }
                    });
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
                    // jQuery.each(rows, function( index, value ) {
                    //     var error_row = errors.errors['booking_due_date.' + index] || null;
                    //     if(error_row) {
                    //         jQuery(rows[index]).find('.booking_due_date').html("Booking Due Date is required");
                    //         $('html, body').animate({ scrollTop: $(rows[index]).offset().top }, 1000);
                    //     }
                    // });
                    $("#divLoading").removeClass('show');
                }
            }
            });
        });
        $(document).on('click', '.close',function(){
            $(this).closest(".qoute").remove();
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
        // var xc = mainCurrencyConverter('EUR',10,'USD');
        // console.log(xc);
        // function mainCurrencyConverter(mainCurrencyValue, currencyValue, currencyCode) {
        //     if(mainCurrencyValue == 'GBP'){
        //         mainCurrencyValue = 1.39480;
        //     }
        //     if(mainCurrencyValue == 'EUR'){
        //         mainCurrencyValue = 0.856068;
        //     }
        //     if(currencyCode == 'USD' && mainCurrencyValue == 'GBP'){
        //         var ans = (currencyValue / mainCurrencyValue);
        //     }
        //     if(currencyCode == 'USD' && mainCurrencyValue == 'EUR'){
        //         var ans = (currencyValue / mainCurrencyValue);
        //     }
        //     return ans;
        // }
    });
</script>
</body>
</html>
@endsection