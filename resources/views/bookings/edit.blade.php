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

    .qoute {
        width: 83%;
        border: solid 1px #000;
        padding: 20px;
        margin: 0 auto 15px;
        float: none;
        border-radius: 10px;
    }

    .outline{
        width: 83%;
        border: solid 1px #000;
        padding: 20px;
        margin: 0 auto 15px;
        float: none;
        border-radius: 10px;
    }

     {
        margin-bottom: 1.5rem;
    }

    .mb-3 {
        margin-bottom: 3rem;
    }

    .mt-3 {
        margin-top: 3rem;
    }

    .mt-2 {
        margin-top: 2rem;
    }

    .hide-arrows::-webkit-inner-spin-button,
    .hide-arrows::-webkit-outer-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }

    .hide-arrows {
        -moz-appearance: textfield !important;
    }

    .additional_date{
        width: 80%;
    }

</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Update Booking</h1>
        <div class="row">
            <div class="col-md-6">
                @if($booking->getBookingLogs && count($booking->getBookingLogs) > 0)
                    <h4> <a href="#" class="view-booking-version">View Booking Versions {{ $booking->getBookingLogs()->count() }}</a></h4>
                    <div id="booking-version" hidden>
                        @foreach ($booking->getBookingLogs as $key => $booking_log)
                            <p> 
                                <a href="{{ route('bookings.logs', encrypt($booking_log->id)) }}" class="version" target="_blank">
                                    fadfasfdsas   {{-- Booking Version {{ $booking_log->log_no }}: {{ $booking_log->quotation_no }} / {{ $booking_log->created_date ? \Carbon\Carbon::parse(str_replace('/', '-', $booking_log->created_date))->format('d/m/Y') : ""}}  {{ isset(\App\User::find($booking_log->user_id)->name) ? "By ".\App\User::find($booking_log->user_id)->name : ""}} --}}
                                </a>
                            </p>
                        @endforeach
                    </div>
                @else
                    <p>No Booking Versions Available</p>
                @endif
            </div>
            <div class="col-md-6 text-right">
                @if($booking->getQuote->getQuoteLogs && count($booking->getQuote->getQuoteLogs) > 0)
                <h4><a href="" class="view-quotation-version"> View Quotation Versions {{ $booking->getQuote->getQuoteLogs()->count() }} </a></h4>
                <div id="quotation-version">
                    @foreach ($booking->getQuote->getQuoteLogs as $key => $qoute_log)
                        <p> <a href="{{ route('quote.logs',encrypt($qoute_log->id)) }}" class="version" target="_blank">
                            Quote version    {{-- Quotation Version {{ $qoute_log->log_no }}: {{ $qoute_log->quotation_no }} / {{ $qoute_log->created_date ? \Carbon\Carbon::parse(str_replace('/', '-', $qoute_log->created_date))->format('d/m/Y') : ""}} {{ isset(\App\User::find($qoute_log->user_id)->name) ? "By ".\App\User::find($qoute_log->user_id)->name : ""}} --}}
                            </a> </p>
                            @endforeach
                        </div>
                @else 
                    <p>No Quotation Versions Available</p>
                @endif
            </div>
        </div>
    </section>
    <section class="content">
        <div id="divLoading"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Booking Form</h3>
                            <a target="_blank" href=""> <h3 class="box-title pull-right"> View Final Quotation</h3> </a>
                        </div>
                        <div class="col-sm-6 col-sm-offset-3" style="text-align: center;">
                            @if(Session::has('success_message'))
                                <div class="alert alert-success">{{ Session::get('success_message') }}</div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('bookings.update', encrypt($booking->id)) }}" accept-charset="UTF-8" class="form-horizontal" id="user_form" >
                            @csrf
                            <div class="box-body">
                                <section id="upper-section">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-offset-1">
                                            <label for="inputEmail3" id="referencename">Zoho Reference</label> <span style="color:red">*</span>
                                            <div class="input-group">
                                                <input type="text" name="ref_no" value="{{ $booking->ref_no }}" class="form-control" placeholder='Enter Reference Number'>
                                                <span id="link">
                                                </span>
                                                <span class="input-group-addon">
                                                    <button id="sendReference" type="button" class="btn-link"> Search </button>
                                                </span>
                                            </div>
                                            <div class="alert-danger" style="text-align:center" id="error_ref_no"></div>
                                        </div>

                                        <div class="col-sm-5">
                                            <label for="inputEmail3" class="">Quote Reference</label> <span class="badge badge-secondary">default</span>
                                            <div class="input-group">
                                                <input type="text" name="quotation_no" class="form-control" value="{{ $booking->quote_ref }}" required readonly>
                                            </div>
                                            <div class="alert-danger" style="text-align:center" id="error_quotation_no"></div>
                                        </div>
                                    </div>

                                <div class="row">
                                    <div class="col-sm-5 col-sm-offset-1">
                                        <label for="inputEmail3" class="">Lead Passenger Name</label> <span
                                            style="color:red">*</span>
                                        <div class="input-group">
                                            <input type="text" name="lead_passenger_name" class="form-control" value="{{ $booking->lead_passenger }}">
                                        </div>
                                        <div class="alert-danger" style="text-align:center" id="error_lead_passenger_name"></div>
                                    </div>


                                    <div class="col-sm-5">
                                        <label class="">Brand Name</label> <span style="color:red">*</span>
                                        <select class="form-control " name="brand_name">
                                            <option value="">Select Brand</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{$brand->id}}" {{ $booking->getBrand->name == $brand->id ? 'selected' : '' }} >{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center" id="error_brand_name"></div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px;">
                                        <label class="">Type Of Holidays</label> <span style="color:red">*</span>
                                        <select class="form-control" id="type_of_holidays" name="type_of_holidays">
                                            <option value="">Select Holiday</option>
                                            @foreach ($holiday_types as $holiday_type)
                                                <option value="{{ $holiday_type->id }}" {{  $booking->holiday_type_id == $holiday_type->id ? 'selected' : '' }} >{{ $holiday_type->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center" id="error_type_of_holidays">
                                        </div>
                                    </div>

                                    <div class="col-sm-5" style="margin-bottom:15px;">
                                        <label class="">Sales Person</label> <span style="color:red">*</span>
                                        <select class="form-control" id="sales_person" name="sale_person">
                                            <option value="">Select Person</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->email }}" {{ $user->email == $booking->sale_person ? 'selected' : '' }}> {{ $user->email }}</option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center" id="error_sale_person"> </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-5 col-sm-offset-1">
                                        <label class="">Booking Season</label>
                                        <span style="color:red">*</span>
                                        {{-- <input type="text" name="season_id" class="form-control"   readonly> --}}
                                        <select class="form-control dropdown_value" name="season_id">
                                            <option value="">Select Season</option>
                                            @foreach ($seasons as $sess)
                                                <option value="{{ $sess->id }}"
                                                    {{ $booking->season_id == $sess->id ? 'selected' : '' }}>
                                                    {{ $sess->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center" id="error_season_id"> </div>
                                    </div>

                                    <div class="col-sm-5" >
                                        <label for="inputEmail3" class="">Agency Booking</label> <span style="color:red">
                                            *</span><br>
                                        <div class="inline-flex">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="agency_booking" value="1" {{  ($booking->agency == 1)? 'checked': NULL }} id="ab_yes">
                                                <label class="form-check-label" for="inlineRadio1">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="agency_booking" value="0" {{  ($booking->agency == 0)? 'checked': NULL }} id="ab_no">
                                                <label class="form-check-label" for="inlineRadio2">No</label>
                                            </div>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> </div>
                                    </div>
                                    <div class="row"
                                        style="{{ $booking->agency == 1 ? 'display:block' : 'display:none' }}"
                                        id="agency-detail"> --}}
                                        <div class="col-sm-2" style="width:175px;">
                                            <label for="inputEmail3" class="">Agency Name</label> <span style="color:red">
                                                *</span>
                                            <input type="text" name="agency_name" value="{{ $booking->agency_name }}"
                                                class="form-control">
                                            <div class="alert-danger" style="text-align:center" id="error_agency_name">
                                            </div>

                                        </div>

                                         <div class="col-sm-2">
                                            <label for="inputEmail3" class="">Agency Contact No.</label> <span
                                                style="color:red"> *</span>
                                            <input type="text" name="agency_contact_no"
                                                value="{{ $booking->agency_contact }}" class="form-control">
                                            <div class="alert-danger" style="text-align:center"
                                                id="error_agency_contact_no"> </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-5 col-sm-offset-1">
                                        <label> Dinning Preferences</label> <span style="color:red">*</span>
                                        <input type="text" name="dinning_preferences" value="{{ $booking->dinning_preference }}" required class="form-control">
                                        <div class="alert-danger" style="text-align:center" id="error_dinning_preferences">
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <label>Bedding Preferences</label> <span style="color:red">*</span>
                                        <input type="text" name="bedding_preference"  value="{{ $booking->bedding_preference }}" required  class="form-control" >
                                        <div class="alert-danger" style="text-align:center" id="error_bedding_preference"></div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px;">
                                        <label> Booking Currency</label> <span style="color:red">*</span>
                                        <select name="currency" class="form-control">
                                            <option value="">Select Currency</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->code }}" {{ $booking->currency_id == $currency->id ? 'selected' : '' }}  data-image="data:image/png;base64, {{$currency->flag}}"> &nbsp; {{$currency->code}} - {{$currency->name}} </option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center" id="error_currency"></div>
                                    </div>
                                    <div class="col-sm-5 ">
                                        <label class="">Pax No.</label> <span style="color:red">*</span>
                                        <select class="form-control dropdown_value paxNumber" name="group_no">
                                            <option selected disabled value="">Select Pax No.</option>
                                            @for ($i = 1; $i <= 30; $i++)
                                                <option value="{{ $i }}" {{ $booking->pax_no == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <div class="alert-danger" style="text-align:center" id="error_group_no"></div>
                                    </div>
                                </div>
                            </section> 
                            {{-- /// UpperSection --}}
                            <section id="passenger-section">
                                <div class="row">
                                    <div class=" col-md-12 col-sm-offset-1" id="appendPaxName">
                                        @if($booking->pax_no > 1)
                                            @foreach ($booking->getBookingPaxDetail as $paxKey => $pax )
                                            @php
                                                 $count = $paxKey +1;
                                            @endphp
                                            <div class= appendCount" id="appendCount{{$count}}">
                                                <div class="row" >
                                                    <div class="col-md-4">
                                                        <label >Passenger #{{ $count+1 }} Full Name</label> 
                                                        <input type="text" name="pax[{{$paxKey}}][full_name]" value="{{ $pax->full_name }}" class="form-control" placeholder="PASSENGER #2 FULL NAME" >
                                                        <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label >Email Address</label> 
                                                        <input type="email" name="pax[{{$paxKey}}][email_address]" value="{{ $pax->email }}" class="form-control" placeholder="EMAIL ADDRESS" >
                                                        <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label >Contact Number</label> 
                                                        <input type="number" name="pax[{{$paxKey}}][contact_number]" value="{{ $pax->contact }}" class="form-control" placeholder="CONTACT NUMBER" >
                                                        <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>Date Of Birth</label> 
                                                        <input type="date" max="{{  date("Y-m-d") }}" name="pax[{{$paxKey}}][date_of_birth]" value="{{ $pax->date_of_birth }}" class="form-control" placeholder="CONTACT NUMBER" >
                                                        <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Bedding Preference</label> 
                                                        <input type="text" name="pax[{{$paxKey}}][bedding_preference]" value="{{ $pax->bedding_preference }}" class="form-control" placeholder="BEDDING PREFERENCES" >
                                                        <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                                    </div>
                                                    
                                                    <div class="col-md-3">
                                                        <label>Dinning Preference</label> 
                                                        <input type="text" name="pax[{{$paxKey}}][dinning_preference]" value="{{ $pax->dinning_preference }}" class="form-control" placeholder="DINNING PREFERENCES" >
                                                        <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                    @endif
                                    </div>
                                </div>
                            </section>
                            <br />
                            <br />
                            <div class="parent" id="parent">
                                @foreach ($booking->getBookingDetail as $key => $booking_detail)
                                    <div class="qoute">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <button type="button" class="btn  pull-right close"> x </button>
                                            </div>
                                        </div>      <br />
                                        <div class="row">
                                            <div class="col-sm-2" style="margin-bottom: 15px;">
                                                <label for="inputEmail3" class="">Date of Service</label>
                                                <div class="input-group">
                                                    <input type="text" name="date_of_service[]" autocomplete="off"
                                                        value="{{ !empty($booking_detail->date_of_service) ? date('d/m/Y', strtotime($booking_detail->date_of_service)) : '' }}"
                                                        class="form-control datepicker date_of_service"
                                                        placeholder="Date of Service">
                                                </div>
                                                <div class="alert-danger date_of_service" style="text-align:center">
                                                </div>
                                            </div>

                                                <div class="col-sm-2" style="margin-bottom:15px;">
                                                    <label class="">Category</label>
                                                    <select class="form-control category-select2" name="category[]">
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ $booking_detail->category_id == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }} </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="alert-danger" style="text-align:center">
                                                        {{ $errors->first('category') }} </div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom:15px">
                                                    <label class="test">Supplier</label>
                                                    <select class="form-control supplier-select2 supplier-select2"
                                                        name="supplier[]">
                                                        <option value="">Select Supplier</option>
                                                        @if(!empty($booking_detail->getCategory->getSupplier))
                                                            @foreach ($booking_detail->getCategory->getSupplier as $supplier)
                                                                <option value="{{ $supplier->id }}" {{ $booking_detail->supplier == $supplier->id  ? "selected" : "" }}> {{ $supplier->name }} </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <div class="alert-danger" style="text-align:center"></div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom:15px;">
                                                    <label class="">Product</label>
                                                    <select class="form-control product-select2" name="product[]">
                                                        <option value="">Select Product</option>
                                                        @if(!empty($booking_detail->getSupplier->products))
                                                            @foreach ($booking_detail->getSupplier->products as $product)
                                                                <option value="{{ $product->id }}" {{ $booking_detail->product == $product->id  ? "selected" : "" }}> {{ $product->name }} </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <div class="alert-danger" style="text-align:center">{{ $errors->first('category') }} </div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Booking Date</label>
                                                    <div class="input-group">
                                                        <input type="text" name="booking_date[]"
                                                            value="{{ !empty($booking_detail->booking_date) ? date('d/m/Y', strtotime($booking_detail->booking_date)) : '' }}"
                                                            class="form-control datepicker" autocomplete="off"
                                                            placeholder="Booking Date">
                                                    </div>
                                                    <div class="alert-danger booking_date" style="text-align:center">
                                                        {{ $errors->first('booking_date') }} </div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Booking Due Date <span
                                                            style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="booking_due_date[]"
                                                            value="{{ !empty($booking_detail->booking_due_date) ? date('d/m/Y', strtotime($booking_detail->booking_due_date)) : '' }}"
                                                            class="form-control datepicker" autocomplete="off"
                                                            placeholder="Booking Date" required>
                                                    </div>
                                                    <div class="alert-danger booking_due_date"
                                                        style="text-align:center; width: 160px;"></div>
                                                </div>


                                            </div>

                                            <div class="row">

                                                <div class="col-sm-2" style="margin-bottom: 35px;">
                                                    <label for="inputEmail3" class="">Service Details</label>
                                                    <textarea name="service_details[]" class="form-control" cols="30"
                                                        rows="1">{{ $booking_detail->service_details }}</textarea>
                                                    <div class="alert-danger" style="text-align:center"></div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Booked By </label>
                                                    <div class="input-group">
                                                        <select class="form-control booked-by-select2" name="booked_by[]"
                                                            class="form-control">
                                                            <option value="">Select Person</option>
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $booking_detail->booked_by == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="alert-danger" style="text-align:center"></div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Booking Method</label>
                                                    <div class="input-group">
                                                        <select class="form-control booking-method-select2"
                                                            name="booking_method[]" class="form-control">
                                                            <option value="">Select Booking Method</option>
                                                            @foreach ($booking_methods as $booking_method)
                                                                <option value="{{ $booking_method->id }}"
                                                                    {{ $booking_detail->booking_method == $booking_method->id ? 'selected' : ($booking_method->name == 'Supplier Own' ? 'selected' : null) }}>
                                                                    {{ $booking_method->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="alert-danger" style="text-align:center">
                                                        {{ $errors->first('booking_method') }} </div>
                                                </div>


                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Booking Reference</label>
                                                    <div class="input-group">
                                                        <input type="text" name="booking_refrence[]"
                                                            value="{{ $booking_detail->booking_refrence }}"
                                                            class="form-control" placeholder="Booking Refrence">
                                                    </div>
                                                    <div class="alert-danger" style="text-align:center"> </div>
                                                </div>

                                                <div class="col-sm-2 " style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Booking Type</label>
                                                    <div class="input-group">
                                                        <select class="form-control booking-type-select2"
                                                            name="booking_type[]">
                                                            <option value="">Select Booking Type</option>
                                                            <option
                                                                {{ $booking_detail->booking_type == 'refundable' ? 'selected' : '' }}
                                                                value="refundable">Refundable</option>
                                                            <option
                                                                {{ $booking_detail->booking_type == 'non_refundable' ? 'selected' : '' }}
                                                                value="non_refundable">Non-Refundable</option>
                                                        </select>
                                                    </div>
                                                    <div class="alert-danger" style="text-align:center">
                                                        {{ $errors->first('booking_type') }} </div>
                                                </div>


                                                <div class="col-sm-2" style="margin-bottom: 35px;">
                                                    <label for="inputEmail3" class="">Comments</label>
                                                    <textarea name="comments[]" class="form-control" cols="30"
                                                        rows="1">{{ $booking_detail->comments }}</textarea>
                                                    <div class="alert-danger" style="text-align:center"></div>
                                                </div>



                                            </div>

                                            <div class="row">

                                                <div class="col-sm-2" style="margin-bottom:15px;">
                                                    <label class="">Supplier Currency <span style="color:red">*</span> </label>
                                                    <select class="form-control supplier-currency"  disabled
                                                        name="supplier_currency[]" required>
                                                        <option value="">Select Currency</option>
                                                        @foreach ($currencies as $currency)
                                                            <option value="{{ $currency->code }}"
                                                                {{ $booking_detail->supplier_currency == $currency->code ? 'selected' : '' }} data-image="data:image/png;base64, {{$currency->flag}}">
                                                                &nbsp; {{$currency->code}} - {{$currency->name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="alert-danger" style="text-align:center"></div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Estimated Cost</label> <span
                                                        style="color:red">*</span>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">{{ $booking_detail->supplier_currency }}</span>
                                                        <input type="number" name="cost[]" class="form-control" value="{{ $booking_detail->cost }}" placeholder="Cost" min="0" step="any" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Actual Cost</label> <span
                                                        style="color:red">*</span>
                                                    <div class="input-group">
                                                        <span
                                                            class="input-group-addon symbol">{{ $booking_detail->supplier_currency }}</span>
                                                        <input type="number" data-code="{{ $booking_detail->supplier_currency }}" name="actual_cost[]" class="form-control cost cost{{ $key }} " data-key="cost{{$key}}" value="{{ $booking_detail->actual_cost }}" placeholder="Cost" min="0" step="any" required>
                                                    </div>
                                                    <div class="alert-danger error-cost" style="text-align:center"></div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Booking Currency Conversion</label>
                                                    <label class="currency"></label>
                                                    <input type="text" class="base-currency" name="qoute_base_currency[]"
                                                        value="{{ $booking_detail->actual_cost != 0 ? number_format($booking_detail->qoute_base_currency, 2, '.', '') : '0.00' }}"
                                                        readonly><br>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Added in Sage </label>
                                                    <div class="input-group">
                                                        <input type="hidden" name="added_in_sage[]"
                                                            value="{{ $booking_detail->added_in_sage == 1 ? 1 : 0 }}"><input
                                                            type="checkbox"
                                                            onclick="this.previousSibling.value=1-this.previousSibling.value"
                                                            {{ $booking_detail->added_in_sage == 1 ? 'checked' : '' }}>
                                                    </div>

                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Supervisor</label>
                                                    <div class="input-group">
                                                        <select name="supervisor[]" class="form-control supervisor-select2">
                                                            <option value="">Select Supervisor</option>
                                                            @foreach ($supervisors as $supervisor)
                                                                <option value="{{ $supervisor->id }}"
                                                                    {{ $booking_detail->supervisor_id == $supervisor->id ? 'selected' : '' }}>
                                                                    {{ $supervisor->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="alert-danger" style="text-align:center"> </div>
                                                </div>



                                            </div>

                                            <div class="row">

                                                <div class="col-sm-2" style="margin-bottom: 15px;">
                                                    <label for="inputEmail3" class="">Upload Invoice</label>
                                                    <div class="input-group">
                                                        <input type="hidden" name="qoute_invoice_record[]"
                                                            value="{{ $booking_detail->qoute_invoice }}">
                                                        <input type="file" name="qoute_invoice[]" value=""
                                                            class="form-control">
                                                    </div>
                                                    <div class="alert-danger" style="text-align:center"> </div>
                                                </div>

                                                <div class="col-sm-2" style="margin-bottom: 15px; padding-top: 3rem;">
                                                    <label for="inputEmail3" class="">Uploaded Invoice</label>
                                                    <div class="input-group">
                                                            @if(!empty($booking_detail->qoute_invoice))
                                                                <a  target="_blank" href="{{ asset("booking/".$booking->qoute_id."/".$booking_detail->qoute_invoice) }}" >  {{$booking_detail->qoute_invoice}}</a>

                                                                @else

                                                                N/A
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>

                                            <div class="row finance-row" hidden>
                                                <div class="row">
                                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                                        <label for="inputEmail3" class="title">Payment {{ $key }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">{{ $booking_detail->supplier_currency }}</span>
                                                        
                                                            <input type="number"
                                                                name="deposit_amount[{{ $key }}][]"
                                                                class="form-control disable-feild deposit_amount depositeAmount" data-key="{{$key}}"
                                                                placeholder="Deposit Amount" min="0" step="any">
                                                        </div>
                                                        <div class="alert-danger" style="text-align:center"> </div>
                                                    </div>

                                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                                        <label for="inputEmail3" class="">Deposit Due Date</label>
                                                        <div class="input-group">
                                                            <input type="text"
                                                                name="deposit_due_date[{{ $key }}][]"
                                                                class="form-control datepicker disable-feild deposit_due_date"
                                                                placeholder="Deposit Due Date" autocomplete="off">
                                                        </div>
                                                        <div class="alert-danger" style="text-align:center"> </div>
                                                    </div>

                                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                                        <label for="inputEmail3" class="">Paid Date</label>
                                                        <div class="input-group">
                                                            <input type="text" name="paid_date[{{ $key }}][]"
                                                                class="form-control datepicker disable-feild"
                                                                placeholder="Paid Date" autocomplete="off">
                                                        </div>
                                                        <div class="alert-danger" style="text-align:center"> </div>
                                                    </div>

                                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                                        <label for="inputEmail3" class="">Payment Method </label>
                                                        <div class="input-group">

                                                            <select
                                                                class="form-control booking-method-select2 disable-feild"
                                                                name="payment_method[{{ $key }}][]"
                                                                class="form-control">
                                                                <option value="">Select Payment Method</option>
                                                                @foreach ($payment_method as $paymentm)
                                                                    <option value="{{ $paymentm->id }}">
                                                                        {{ $paymentm->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="alert-danger" style="text-align:center">
                                                            {{ $errors->first('payment_method') }} </div>
                                                    </div>

                                                    <div class="col-sm-1" style="margin-bottom: 15px; ">
                                                        <label for="inputEmail3" class=""> Upload to Calender</label>
                                                        <div class="input-group">
                                                            <input type='hidden' class="disable-feild" value='false' name='upload_calender[{{ $key }}][]'>
                                                            <input class="form-check-input uploadCalender disable-feild"
                                                                type="checkbox" value="false"
                                                                name="upload_calender[{{ $key }}][]"
                                                                style="height: 20px; width:28px;">
                                                                <label for="inputEmail3" class=""></label>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <label for="inputEmail3" class="">No of Days</label>
                                                        <div class="input-group additional_date">
                                                            <a href="#" class="input-group-addon minus increment">
                                                                <i class="fa fa-minus" aria-hidden="true"></i>
                                                            </a>

                                                            <input type="text" name="additional_date[{{ $key }}][]" class="form-control adults disable-feild" size="10" value="0" >
                                                            
                                                            <a href="#" class="input-group-addon plus increment">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-1" style="margin-bottom: 15px; margin-top: 2.5rem;">
                                                        <button type="button" class="btn btn-info remove_finance">-</button>
                                                    </div>

                                                </div>
                                            </div>

                                            @if ($booking_detail->getBookingFinance))

                                                @foreach ($booking_detail->getBookingFinance as $fkey => $finance_booking_detail)
                                                    <div class="row" data-title="title{{$key}}">

                                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                                            <label for="inputEmail3" class="title{{$key}}">Payment #{{ $fkey + 1}}</label>
                                                            <div class="input-group">
                                                            <span class="input-group-addon">{{ $booking_detail->supplier_currency }}</span>
                                                            
                                                                <input type="number"
                                                                    name="deposit_amount[{{ $key }}][]" {{ (Auth::user()->role_id != 1  && !empty($finance_booking_detail->deposit_amount))? 'disabled': '' }}
                                                                    value="{{ !empty($finance_booking_detail->deposit_amount) ? $finance_booking_detail->deposit_amount : '' }}"
                                                                    class="form-control deposit_amount depositecost{{$key}}" data-key="{{ $key }}"
                                                                    placeholder="Deposit Amount" min="0" step="any">
                                                            </div>
                                                            <div class="alert-danger" style="text-align:center"> </div>
                                                        </div>

                                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                                            <label for="inputEmail3" class="">Deposit Due Date</label>
                                                            <div class="input-group">
                                                                <input type="text"
                                                                    name="deposit_due_date[{{ $key }}][]" 
                                                                    value="{{ !empty($finance_booking_detail->deposit_due_date) ? date('d/m/Y', strtotime($finance_booking_detail->deposit_due_date)) : '' }}"
                                                                    class="form-control deposit_due_date datepicker"
                                                                    placeholder="Deposit Due Date" autocomplete="off">
                                                            </div>
                                                            <div class="alert-danger" style="text-align:center"> </div>
                                                        </div>

                                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                                            <label for="inputEmail3" class="">Paid Date</label>
                                                            <div class="input-group">
                                                                <input type="text" name="paid_date[{{ $key }}][]"
                                                                    value="{{ !empty($finance_booking_detail->paid_date) ? date('d/m/Y', strtotime($finance_booking_detail->paid_date)) : '' }}"
                                                                    class="form-control datepicker" placeholder="Paid Date"
                                                                    autocomplete="off">
                                                            </div>
                                                            <div class="alert-danger" style="text-align:center"> </div>
                                                        </div>

                                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                                            <label for="inputEmail3" class="">Payment Method</label>
                                                            <div class="input-group">
                                                                <select class="form-control booking-method-select2"
                                                                    name="payment_method[{{ $key }}][]"
                                                                    class="form-control">
                                                                    <option value="">Select Payment Method</option>
                                                                    @foreach ($payment_method as $paymentm)
                                                                        <option value="{{ $paymentm->id }}"
                                                                            {{ $finance_booking_detail->payment_method == $paymentm->id ? 'selected' : '' }}>
                                                                            {{ $paymentm->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="alert-danger" style="text-align:center">
                                                                {{ $errors->first('payment_method') }} </div>
                                                        </div>

                                                        <div class="col-sm-1" style="margin-bottom: 15px; ">
                                                            <label for="inputEmail3" class="">Upload to Calender</label>
                                                            <div class="input-group">
                                                                <input type='hidden' class="disable-feild"
                                                                    {{ $finance_booking_detail->upload_to_calender == 'false' ? '' : 'disabled="disabled"' }}
                                                                    value="false"
                                                                    name='upload_calender[{{ $key }}][]'>
                                                                <input class="form-check-input uploadCalender disable-feild"
                                                                    type="checkbox"
                                                                    value="{{ $finance_booking_detail->upload_to_calender ?? 'false' }} "
                                                                    {{ $finance_booking_detail->upload_to_calender == 'false' ? '' : 'checked' }}
                                                                    name="upload_calender[{{ $key }}][]"
                                                                    style="height: 20px; width:28px;">

                                                                        {{-- <button style=" font-size:22px; margin-top: -13px;" class=" btn-dark btn btn-sm form-check-input">-</button>
                                                                        <lable style="margin-top: -13px;"><strong>2</strong></lable>
                                                                        <button style=" font-size:22px; margin-top: -13px;" class=" btn-dark btn btn-sm form-check-input">+</button> --}}
                                                                       
                                                            </div>
                                                        </div>


                                                        <div class="col-sm-2" style="margin-bottom: 15px; ">
                                                            <label for="inputEmail3" class="">No of Days</label>
                                                            <div class="input-group additional_date">
                                                                <a href="#" class="input-group-addon minus increment"><i class="fa fa-minus" aria-hidden="true"></i></a>
                                                                <input type="text" name="additional_date[{{ $key }}][]" class="form-control adults disable-feild" size="10" value="{{ !empty($finance_booking_detail->additional_date) ? $finance_booking_detail->additional_date : 0 }} ">
                                                                <a href="#" class="input-group-addon plus increment"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                            </div>
                                                        </div>
                                                
                                                        {{-- <div class="col-md-2">
                                                            <label for="inputEmail3" class="">Add Event</label>
                                                            <div class="form-check">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                                <div class="input-group-append">
                                                                </div>
                                                              </div>
                                                            </div>
                                                        </div> --}}

                                                        @if ($fkey == 0)
                                                            <div class="col-sm-1" style="margin-bottom: 15px; margin-top: 2.5rem;">
                                                                <button type="button" class="add_finance btn btn-info">+</button>
                                                            </div>
                                                        @endif


                                                    </div>
                                                @endforeach

                                            @else
                                            @php $count = 1 @endphp
                                                <div class="row" data-title="title{{$key+1}}">
                                                    <div class="append">
                                                        <div class="col-sm-2" style="margin-bottom: 15px;"> 
                                                            <label for="inputEmail3" class="title{{$key+1}}">Payments #{{ $count  }}</label>
                                                            <div class="input-group">
                                                                <span class="input-group-addon">{{ $booking_detail->supplier_currency }}</span>
                                                                <input type="number" 
                                                                {{-- {{ (!empty($finance_booking_detail->deposit_amount) && ) }} --}}
                                                                    name="deposit_amount[{{ $key }}][]"
                                                                    value="{{ !empty($finance_booking_detail->deposit_amount) ? $finance_booking_detail->deposit_amount : '' }}"
                                                                    class="form-control deposit_amount depositecost{{$key}}" data-key="{{$key}}"
                                                                    placeholder="Deposit Amount" min="0" step="any">
                                                            </div>
                                                            <div class="alert-danger" style="text-align:center"> </div>
                                                        </div>

                                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                                            <label for="inputEmail3" class="">Deposit Due Date</label>
                                                            <div class="input-group">
                                                                <input type="text"
                                                                    name="deposit_due_date[{{ $key }}][]"
                                                                    value="{{ !empty($finance_booking_detail->deposit_due_date) ? date('d/m/Y', strtotime($finance_booking_detail->deposit_due_date)) : '' }}"
                                                                    class="form-control deposit_due_date datepicker"
                                                                    placeholder="Deposit Due Date" autocomplete="off">
                                                            </div>
                                                            <div class="alert-danger" style="text-align:center"> </div>
                                                        </div>

                                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                                            <label for="inputEmail3" class="">Paid Date</label>
                                                            <div class="input-group">
                                                                <input type="text" name="paid_date[{{ $key }}][]"
                                                                    class="form-control datepicker" placeholder="Paid Date"
                                                                    autocomplete="off">
                                                            </div>
                                                            <div class="alert-danger" style="text-align:center"> </div>
                                                        </div>

                                                        <div class="col-sm-2" style="margin-bottom: 15px;">
                                                            <label for="inputEmail3" class="">Payment Method</label>
                                                            <div class="input-group">
                                                                <select class="form-control booking-method-select2"
                                                                    name="payment_method[{{ $key }}][]"
                                                                    class="form-control">
                                                                    <option value="">Select Payment Method</option>
                                                                    @foreach ($payment_method as $paymentm)
                                                                        <option value="{{ $paymentm->id }}"
                                                                            {{ $booking_detail->payment_method == $paymentm->id ? 'selected' : '' }}>
                                                                            {{ $paymentm->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="alert-danger" style="text-align:center">
                                                                {{ $errors->first('payment_method') }} </div>
                                                        </div>


                                                        <div class="col-sm-1" style="margin-bottom: 15px; ">
                                                            <label for="inputEmail3" class=""> Upload to Calender</label>
                                                            <div class="input-group">
                                                                <input type='hidden' value='false'
                                                                    name='upload_calender[{{ $key }}][]'>
                                                                <input class="form-check-input uploadCalender"
                                                                    type="checkbox" value="false"
                                                                    name="upload_calender[{{ $key }}][]"
                                                                    style="height: 20px; width:28px;">
                                                             
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-2" style="margin-bottom: 15px; ">
                                                            <label for="inputEmail3" class="">No of Days</label>
                                                            <div class="input-group additional_date">
                                                                <a href="#" class="input-group-addon minus increment"><i class="fa fa-minus" aria-hidden="true"></i></a>
                                                                <input type="text" name="additional_date[{{ $key }}][]" class="form-control adults" size="10" value="0">
                                                                <a href="#" class="input-group-addon plus increment"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="col-sm-1" style="margin-bottom: 15px; margin-top: 2.5rem;">
                                                        <button type="button" class="add_finance btn btn-info">+</button>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    @endforeach
                                </div>

                                <br><br>
                            @if($booking->getBookingData)
                                <div>
                                    <h2 class="col-sm-offset-1">Transfer Info</h2>
                                    <div class="outline">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="inputEmail3" class="">Asked For Transfer</label><br>
                                                <input id="td_yes" name="asked_for_transfer_details" type="radio" value="yes" {{ $booking->getBookingData->ask_for_transfer == 'yes' ? 'checked' : ''}}>&nbsp;
                                                <label for="td_yes">Yes</label>

                                                <input id="td_no"  name="asked_for_transfer_details" type="radio" value="no" {{ $booking->getBookingData->ask_for_transfer == 'no' ? 'checked' : ''}}>&nbsp;
                                                <label for="td_no">No</label>

                                                <input id="td_NA" name="asked_for_transfer_details" type="radio" value="NA" {{ $booking->getBookingData->ask_for_transfer == 'NA' ? 'checked' : ''}}>&nbsp;
                                                <label for="td_NA">NA</label>

                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>

                                            <div class="col-sm-4 aft_depend" style="display: {{ $booking->getBookingData->ask_for_transfer == NULL ? 'none' : 'block' }};"> 
                                                <label class="">Responsible Person</label>
                                                   <select class="form-control responsible_person_depend" name="aft_person">
                                                        <option value="">Select Person</option>
                                                        @foreach($users as $user)
                                                            @if(Auth::user()->id != $user->id)
                                                                @if($user->id != 1)
                                                                    <option value="{{ $user->id }}" {{ !empty($booking->getBookingData->responsible_person_ti) && $booking->ask_for_transfer_details != 'NA' && $booking->getBookingData->responsible_person_ti ==  $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                   </select>
                                                <div class="alert-danger" style="text-align:center">{{$errors->first('aft_person')}}</div>
                                            </div>

                                            
                                            <div class="col-sm-5 aft_depend" style="display: {{ $booking->getBookingData->asked_for_transfer == 'NA' ? 'none' : 'block' }};"> 
                                                <label for="inputEmail3" class="">Last Date</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"></span>
                                                    <input autocomplete="off" class="form-control datepicker" placeholder="Last Date" name="aft_last_date" type="text" value="{{ !empty($booking->aft_last_date) && $booking->getBookingData->ask_for_transfer != NULL ? date('d/m/Y', strtotime($booking->getBookingData->last_date_ti)) : "" }}">
                                                </div>
                                                <div class="alert-danger" style="text-align:center">{{$errors->first('aft_last_date')}}</div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-sm-4"> 
                                                <div class="transfer_details" style="margin-bottom:25px;display: none;">
                                                    <label for="inputEmail3" class="">Asked For Transfer Details <span style="color:red"> * </span></label>
                                                    <div class="input-group">
                                                       <span class="input-group-addon"></span>
                                                       <textarea class="form-control" placeholder="Asked For Transfer Details" style="height:60px" name="transfer_details" cols="50" rows="10" required="">{{ $booking->getBookingData->transfer_detail }}</textarea>
                                                       <div class="alert-danger" id="error_transfer_details" style="text-align:center">{{$errors->first('transfer_details')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div>
                                    <h2 class="col-sm-offset-1">Transfers Organised</h2>
                                    <div class="outline">

                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="inputEmail3" class="">Transfers Organised</label><br>
                                                <input id="tro_yes" name="transfer_organised" type="radio" value="yes" {{ $booking->getBookingData->transfer_organize == 'yes' ? 'checked' : ''}}>&nbsp;
                                                <label for="tro_yes">Yes</label>

                                                <input id="tro_no" name="transfer_organised" type="radio" value="no" {{ $booking->getBookingData->transfer_organize == 'no' ? 'checked' : ''}}>&nbsp;
                                                <label for="tro_no">No</label>

                                                <input id="tro_NA" name="transfer_organised" type="radio" value="NA" {{ $booking->getBookingData->transfer_organize == 'NA' ? 'checked' : ''}}>&nbsp;
                                                <label for="tro_NA">NA</label>

                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>


                                            <div class="col-sm-4 to_depend" style="display: {{ $booking->transfer_organised == 'NA' ? 'none' : 'block' }};"> 
                                                <label class="">Responsible Person</label>
                                                <select class="form-control to_rp" name="to_person">
                                                    <option value="">Select Person</option>
                                                    @foreach($users as $user)
                                                        @if(Auth::user()->id != $user->id)
                                                            @if($user->id != 1)
                                                                <option value="{{ $user->id }}" {{ $booking->getBookingData->transfer_organize != 'NA' && $booking->getBookingData->reponsible_person_to ==  $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                   </select>
                                                <div class="alert-danger" style="text-align:center">{{$errors->first('to_person')}}</div>
                                            </div>

                                            <div class="col-sm-5 to_depend" style="display: {{ $booking->transfer_organize == NULL ? 'none' : 'block' }};"> 
                                                <label for="inputEmail3" class="">Last Date Of Transfer Organised</label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <input autocomplete="off" class="form-control datepicker"  placeholder="Last Date Of Transfer Organised" name="to_last_date" type="text" value="{{ !empty($booking->getBookingData->last_date_to) && $booking->getBookingData->transfer_organize != 'NA' ? date('d/m/Y', strtotime($booking->last_date_to)) : "" }}">
                                                </div>
                                                <div class="alert-danger"  style="text-align:center"></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="transfer_organised_details col-sm-4" style="display: {{ $booking->transfer_organised == 'yes' ? 'block' : 'none' }};"> 
                                                <label for="inputEmail3" class="">Transfer Organised Details <span style="color:red"> * </span></label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <textarea class="form-control" placeholder="Transfer Organised Details" style="height:60px" name="transfer_organised_details" cols="50" rows="10">{{ !empty($booking->getBookingData->transfer_organize_details) && $booking->getBookingData->transfer_organize_details == 'yes' ? $booking->transfer_organised_details : ''}}</textarea>
                                                </div>
                                                <div class="alert-danger" id="error_transfer_organised_details" style="text-align:center"></div>
                                            </div>
                                        </div>
                           
                                    </div>
                                </div>
                                @endif

                                {{-- <div>
                                    <h2 class="col-sm-offset-1">Itinerary Finalised</h2>
                                    <div class="outline">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label for="inputEmail3" class="">Itinerary Finalised</label><br>
                                                <input id="itf_yes" name="itinerary_finalised" type="radio" value="yes" {{ $booking->itinerary_finalised == 'yes' ? 'checked' : '' }}>&nbsp;
                                                <label for="itf_yes">Yes</label>

                                                <input id="itf_no"  name="itinerary_finalised" type="radio" value="no" {{ $booking->itinerary_finalised  == 'no' ? 'checked' : ''}}>&nbsp;
                                                <label for="itf_no">No</label>

                                                <input id="itf_NA"  name="itinerary_finalised" type="radio" value="NA" {{ $booking->itinerary_finalised  == 'NA' ? 'checked' : '' }}>&nbsp;
                                                <label for="itf_NA">NA</label>
                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>
                            
                                            <div class="col-sm-4 itf_depend" style="display: {{ $booking->itinerary_finalised == 'NA' ? 'none' : 'block' }};">  
                                                <label class="">Responsible Person</label>
                                                <select class="form-control if_rp" name="itf_person">
                                                    <option value="">Select Person</option>
                                                    @foreach($users as $user)
                                                        @if(Auth::user()->id != $user->id)
                                                            @if($user->id != 1)
                                                            <option value="{{ $user->id }}" {{ !empty($booking->itf_person) && $booking->itinerary_finalised != 'NA' && $booking->itf_person ==  $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div class="alert-danger" style="text-align:center">{{$errors->first('itf_person')}}</div>
                                            </div>

                                            <div class="col-sm-5 itf_depend" style="display: {{ $booking->itinerary_finalised == 'NA' ? 'none' : 'block' }};">  
                                                <label for="inputEmail3" class="">Last Date Of Itinerary Finalised </label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <input autocomplete="off" class="form-control datepicker" placeholder="Last Date Of Itinerary Finalised" name="itf_last_date" type="text" value="{{ !empty($booking->itf_last_date) && $booking->itinerary_finalised != 'NA' ? date('d/m/Y', strtotime($booking->itf_last_date)) : "" }}">
                                                </div>
                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>
                                        </div>

                                        <div class="row itinerary_finalised_details" style="display: {{ $booking->itinerary_finalised == 'yes' ? 'block' : 'none' }};"> 
                                            <div class=" col-sm-9">
                                                <label for="inputEmail3" class="">Itinerary Finalised Details <span style="color:red"> * </span> </label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <textarea class="form-control" placeholder="Itinerary Finalised Details" style="height:60px" name="itinerary_finalised_details" cols="50" rows="10" >{{ !empty($booking->itinerary_finalised_details) && $booking->itinerary_finalised == 'yes' ? $booking->itinerary_finalised_details : ''}}</textarea>
                                                   <div class="alert-danger" id="error_itinerary_finalised_details" style="text-align:center"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row itinerary_finalised_details" style="display: {{ $booking->itinerary_finalised == 'yes' ? 'block' : 'none' }};"> 
                                            <div class="col-sm-5 " >
                                                <label for="inputEmail3" class="">Itinerary Finalised Date <span style="color:red"> * </span></label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <input autocomplete="off" class="form-control datepicker" placeholder="Itinerary Finalised Date"  name="itf_current_date" type="text" value="{{ !empty($booking->itf_current_date) && $booking->itinerary_finalised == 'yes' ? date('d/m/Y', strtotime($booking->itf_current_date)) : ''}}">
                                                   <div class="alert-danger" id="error_itf_current_date" style="text-align:center"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h2 class="col-sm-offset-1">Travel Document Prepared</h2>
                                    <div class="outline">
                                        <div class="row">

                                            <div class="col-sm-3">
                                                <label for="inputEmail3">Travel Document Prepared</label><br>
                                                <input id="dp_yes" name="document_prepare" type="radio" value="yes" {{ $booking->document_prepare == 'yes' ? 'checked' : '' }}>&nbsp;
                                                <label for="dp_yes">Yes</label>

                                                <input id="dp_no" name="document_prepare" type="radio" value="no" {{ $booking->document_prepare == 'no' ? 'checked' : '' }}>&nbsp;
                                                <label for="dp_no">No</label>

                                                <input id="dp_NA" name="document_prepare" type="radio" value="NA" {{ $booking->document_prepare == 'NA' ? 'checked' : '' }}>&nbsp;
                                                <label for="dp_NA">NA</label>
                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>

                                            <div class="col-sm-4 dp_depend" style="display: {{ $booking->document_prepare == 'NA' ? 'none' : 'block' }};"> 
                                                <label class="">Responsible Person</label>
                                                   <select class="form-control tdp_rp" name="dp_person">
                                                        <option value="">Select Person</option>
                                                        @foreach($users as $user)
                                                            @if(Auth::user()->id != $user->id)
                                                                @if($user->id != 1)
                                                                    <option value="{{ $user->id }}" {{ !empty($booking->dp_person) && $booking->document_prepare != 'NA' && $booking->dp_person ==  $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                   </select>
                                                <div class="alert-danger" style="text-align:center">{{$errors->first('dp_person')}}</div>
                                            </div>

                                            <div class="col-sm-5 dp_depend" style="display: {{ $booking->document_prepare == 'NA' ? 'none' : 'block' }};">
                                                <label for="inputEmail3" class="">Last Date Of Document Prepared</label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <input autocomplete="off" class="form-control datepicker" placeholder="Last Date Of Document Prepared" name="dp_last_date" type="text" value="{{ !empty($booking->dp_last_date) && $booking->document_prepare != 'NA' ? date('d/m/Y', strtotime($booking->dp_last_date)) : "" }}">
                                             
                                                </div>
                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>

                                        </div>

                                        <div class="row tdp_current_date" style="display: {{ $booking->document_prepare == 'yes' ?  'block' : 'none' }};">
                                            <div class="col-sm-5 ">
                                                <div >
                                                    <label for="inputEmail3" class="">Travel Document Prepared Date <span style="color:red"> * </span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"></span>
                                                        <input autocomplete="off" class="form-control datepicker" placeholder="Travel Document Prepared Date" name="tdp_current_date" type="text" value="{{ !empty($booking->tdp_current_date) && $booking->document_prepare == 'yes' ? date('d/m/Y', strtotime($booking->tdp_current_date)) : "" }}">
                                                    </div>
                                                    <div class="alert-danger" id="error_tdp_current_date" style="text-align:center"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h2 class="col-sm-offset-1">Travel Document Sent</h2>
                                    <div class="outline">
                                        <div class="row">

                                            <div class="col-sm-3">
                        
                                                <label for="inputEmail3" class="">Travel Document Sent</label><br>
                                                <input id="ds_yes" name="documents_sent" type="radio" value="yes" {{ $booking->documents_sent == 'yes' ? 'checked' : ''}}>&nbsp;
                                                <label for="ds_yes">Yes</label>

                                                <input id="ds_no" name="documents_sent" type="radio" value="no" {{ $booking->documents_sent == 'no' ? 'checked' : ''}}>&nbsp;
                                                <label for="ds_no">No</label>

                                                <input id="ds_NA" name="documents_sent" type="radio" value="NA" {{ $booking->documents_sent == 'NA' ? 'checked' : ''}}>&nbsp;
                                                <label for="ds_NA">NA</label>

                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>

                                            <div class="col-sm-4 ds_depend" style="display: {{ $booking->documents_sent == 'NA' ? 'none' : 'block' }};"> 
                                                <label class="">Responsible Person</label>
                                                   <select class="form-control tds_rp" name="ds_person">
                                                     <option value="">Select Person</option>
                                                        @foreach($users as $user)
                                                            @if(Auth::user()->id != $user->id)
                                                                @if($user->id != 1)
                                                                    <option value="{{ $user->id }}" 
                                                                        {{ !empty($booking->ds_person) && $booking->documents_sent != 'NA' && $booking->ds_person ==  $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                   </select>
                                                <div class="alert-danger" style="text-align:center">{{$errors->first('dp_person')}}</div>
                                            </div>

                                            <div class="col-sm-5 ds_depend" style="display: {{ $booking->documents_sent == 'NA' ? 'none' : 'block' }};"> 
                                                <label for="inputEmail3" class="">Last Date Of Document Prepared</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"></span>
                                                    <input autocomplete="off" class="form-control datepicker" placeholder="Last Date Of Document Sent"  name="ds_last_date" type="text" 
                                                    value="{{ !empty($booking->ds_last_date) && $booking->documents_sent != 'NA' ? date('d/m/Y', strtotime($booking->ds_last_date)) : "" }}">
                                                </div>
                                                <div class="alert-danger"  style="text-align:center">{{$errors->first('dp_last_date')}}</div>
                                            </div>
                                            
                                        </div>


                                        <div class="row">
                                            <div class="col-md-4 documents_sent_details" style="display: none;">
                                                <label for="inputEmail3" class="">Document Details <span style="color:red"> * </span></label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <textarea class="form-control" placeholder="Document Details" style="height:60px" name="documents_sent_details" cols="50" rows="10" >{{ !empty($booking->documents_sent_details) && $booking->documents_sent == 'yes' ? $booking->documents_sent_details : ''}}</textarea>
                                                </div>
                                                <div class="alert-danger" id="error_documents_sent_details" style="text-align:center">{{$errors->first('documents_sent_details')}}</div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-5 tds_current_date" style="display: none;"> 
                                                <label for="inputEmail3" class="">Travel Document Sent Date <span style="color:red"> * </span></label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <input autocomplete="off" class="form-control datepicker"  placeholder="Travel Document Sent Date"  name="tds_current_date" type="text" 
                                                   value="{{ !empty($booking->tds_current_date) && $booking->documents_sent == 'yes' ? date('d/m/Y', strtotime($booking->tds_current_date)) : "" }}">
                                                </div>
                                                <div class="alert-danger" id="error_tds_current_date" style="text-align:center"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div>
                                    <h2 class="col-sm-offset-1">App login Sent</h2>
                                    <div class="outline">
                                        <div class="row">

                                            <div class="col-sm-3">
                                                <label for="inputEmail3" class="">App login Sent</label><br>
                                                <input id="ecs_yes" name="electronic_copy_sent" type="radio" value="yes"  {{ $booking->electronic_copy_sent == 'yes' ? 'checked' : ''}}>&nbsp;
                                                <label for="ecs_yes">Yes</label>
    
                                                <input id="ecs_no"  name="electronic_copy_sent" type="radio" value="no"  {{ $booking->electronic_copy_sent == 'no' ? 'checked' : ''}}>&nbsp;
                                                <label for="ecs_no">No</label>
                                                <div class="alert-danger" style="text-align:center"></div>
                                            </div>
                                        </div>

                                        <div class="row electronic_copy_details" style="display: {{ $booking->electronic_copy_sent == 'yes' ? 'block' : 'none' }};">
                                            <div class="col-sm-4 aps_depend"> 
                                                <label class="">Responsible Person <span style="color:red"> * </span></label>
                                                    <select class="form-control als_rp" name="aps_person">
                                                        <option value="">Select Person</option>
                                                        @foreach($users as $user)
                                                            @if(Auth::user()->id != $user->id)
                                                                @if($user->id != 1)
                                                                    <option value="{{ $user->id }}" {{ !empty($booking->aps_person) && $booking->electronic_copy_sent == 'yes' && $booking->aps_person ==  $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                <div class="alert-danger" id="error_aps_person" style="text-align:center">{{$errors->first('aps_person')}}</div>
                                            </div>

                                            <div class="col-sm-5 electronic_copy_details" style="display: {{ $booking->electronic_copy_sent == 'yes' ? 'block' : 'none' }};">
                                                <label for="inputEmail3" class="">Date <span style="color:red"> * </span></label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <input autocomplete="off" class="form-control datepicker" placeholder="Date" name="aps_last_date" type="text" value="{{ !empty($booking->aps_last_date) && $booking->electronic_copy_sent == 'yes' ? date('d/m/Y', strtotime($booking->aps_last_date)) : "" }}">
                                                </div>
                                                <div class="alert-danger" id="error_aps_last_date" style="text-align:center"></div>
                                            </div>
                                        </div>

                                        <div class="row electronic_copy_details" style="display: {{ $booking->electronic_copy_sent == 'yes' ? 'block' : 'none' }};">
                                            <div class="col-sm-5" >
                                                <label for="inputEmail3" class="">App Login Sent Details <span style="color:red"> * </span></label>
                                                <div class="input-group">
                                                   <span class="input-group-addon"></span>
                                                   <textarea class="form-control" placeholder="App Login Sent Details" style="height:60px" name="electronic_copy_details" cols="50" rows="10" >{{ !empty($booking->electronic_copy_details) && $booking->electronic_copy_sent == 'yes' ? $booking->electronic_copy_details : ''}}</textarea>
                                                </div>
                                                <div class="alert-danger" id="error_electronic_copy_details" style="text-align:center"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
 
                                <br><br><br>

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
                                                <input type="number" name="net_price" step="any" min="0" class="net_price hide-arrows" value="{{ number_format($booking->net_price, 2, '.', '') }}">
                                            </label>
                                        </div>
                                        <div class="row">
                                            <label class="">
                                                <label class="currency" ></label>
                                                <input type="number" class="markup-amount" step="any" min="0" name="markup_amount" value="{{ number_format($booking->markup_amount, 2, '.', '') }}">
                                            </label>
                                        </div>
                                        <div class="row">
                                            <label class="">
                                                <label class="currency" ></label>
                                                <input type="number" class="selling hide-arrows" min="0"  step="any" name="selling" value="{{ number_format($booking->selling, 2, '.', '') }}">
                                            </label>
                                        </div>
                                        <div class="row">
                                            <label class="">
                                                <label class="currency" ></label>
                                                <input type="number" class="gross-profit hide-arrows" min="0" step="any" name="gross_profit" value="{{ number_format($booking->gross_profit, 2, '.', '') }}">
                                                <span>%</span>
                                            </label>
                                        </div>

                                    </div>


                                    <div class="col-sm-2">
                                        <br>
                                        <div class="row">
                                            <label class="">
                                                <input type="number" class="markup-percent" name="markup_percent" min="0" value="{{ number_format($booking->markup_percent, 2, '.', '') }}" style="width:70px;">
                                                <span>%</span>
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-2 col-sm-offset-1">
                                        <label for="">
                                            Selling Price in Other Currency
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-2 col-sm-offset-1" style="margin-bottom:15px;">
                                        <select class="form-control convert-currency-select2" id="convert-currency" name="convert_currency">
                                            <option value="">Select Currency</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->code }}" data-image="data:image/png;base64, {{$currency->flag}}" {{ $booking->convert_currency == $currency->code ? 'selected' : ''}}> &nbsp; {{$currency->code}} - {{$currency->name}}  </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom:15px;">
                                        <label class="convert-currency"></label>
                                            <input type="number" name="show_convert_currency" min="0" value="{{ number_format($booking->show_convert_currency, 2, '.', '') }}" step="any" class="show-convert-currency hide-arrows" value="0">
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
                                        <input type="number" class="per-person hide-arrows" min="0" value="{{ number_format($booking->per_person, 2, '.', '') }}" step="any" name="per_person" value="0">
                                    </div>
                                </div>

                                <div class="box-footer">
                                    {!! Form::submit('Submit', ['class' => 'btn btn-info pull-right']) !!}
                                </div> --}}
                            </form>

                            <div class="col-sm-10 col-sm-offset-1">
                                <h1 style="text-align: center;">Finance Detail</h1>
                                <table id="example2" class="table table-bordered table-striped dataTable no-footer"
                                    role="grid">
                                    <thead>
                                        <tr>
                                            {{-- <th>Travel Specialist</th> --}}
                                            {{-- <th>Departure Date</th> --}}
                                            <th>Agency Name</th>
                                            <th>Agency Contact Name</th>
                                            <th>Passenger Name</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Type</th>
                                            {{-- <th>Holiday Amount</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- {!! $record->finance_detail !!} --}}
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-sm-10 col-sm-offset-1" style="margin-top: 100px">
                                <h1 style="text-align: center;">Email Details</h1>
                                <table id="example3" class="table table-bordered table-striped dataTable no-footer"
                                    role="grid">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Hour</th>
                                            <th>Is Read</th>
                                            <th>Is Read Date</th>
                                            <th>Alert For</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @foreach ($booking_email as $value)
                                            <tr>
                                                <td>{{ $value->username }}</td>
                                                <td>{{ $value->hour }}</td>
                                                <td>{{ $value->is_read == '' ? '-' : $value->is_read }}</td>
                                                <td>{{ $value->is_read_date == '' ? '-' : $value->is_read_date }}</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $value->action)) }}</td>
                                                <td>{{ \Carbon\Carbon::parse(substr($value->created_at, 0, 10))->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                        @endforeach --}}
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
        </section>

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

$(document).ready(function() {
    // Initialize all Select2 
    // $('.select2, .category-select2, .supplier-select2, .product-select2, .booking-method-select2, .booked-by-select2, .supplier-currency, .supervisor-select2, .booking-type-select2').select2();
    


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

    // $('.currency-select2, .supplier-currency, .convert-currency-select2').select2({
    //     templateResult: formatState,
    //     templateSelection: formatState
    // });
    
    // $(".datepicker").datepicker({
    //     autoclose: true,
    //     format: 'dd/mm/yyyy'
    // });

    // document.getElementById('ds_yes').addEventListener('click', function() {
    //     if((document.getElementById('deposit').checked ==false) || (document.getElementById('remain').checked ==false)){
    //     confirm('Full Payment may not have been received--Please confirm you like to Send Documnet to client');
    //     }
    // },false);

    // Flight Booked

    // if($('input:radio[name=flight_booked]:checked').val() == 'yes'){
    //     $('.flight_booking_details').show();
    //     $('.flight_booking_details').show(200);
    //     $('.fb_airline_ref_no').show(200);
    //     $('.fb_booking_date').show(200);
    //     $('.fb_airline_name_id').show(200);
    //     $('.fb_payment_method_id').show(200);
    //     $("#airline").css({"margin-top": "25px"});

    //     $('textarea[name=flight_booking_details]').prop('required',true);
    //     $('input[name=fb_airline_ref_no]').prop('required',true);
    //     $('input[name=fb_booking_date]').prop('required',true);
    //     $('select[name="fb_airline_name_id"]').prop('required',true);
    //     $('select[name="fb_payment_method_id"]').prop('required',true);
    //     $(".fb_select2").select2({ width: '100%' }); 
    // }
    // else{
    //     $('.flight_booking_details').hide(200);
    //     $('.fb_airline_ref_no').hide(200);
    //     $('.fb_booking_date').hide(200);
    //     $('.fb_airline_name_id').hide(200);
    //     $('.fb_payment_method_id').hide(200);
    //     $(".fb_select2").select2({ width: '100%' }); 

    //     $("#airline").css({"margin-top": "0px"});

    //     $('textarea[name=flight_booking_details]').prop('required',false);
    //     $('input[name=fb_airline_ref_no]').prop('required',false);
    //     $('input[name=fb_booking_date]').prop('required',false);
    //     $('select[name="fb_airline_name_id"]').prop('required',false);
    //     $('select[name="fb_payment_method_id"]').prop('required',false);
    // }
    // if($('input:radio[name=flight_booked]:checked').val() == 'NA'){
    //     $('.fb_depend').hide(200);
    // }else{
    //     $('.fb_depend').show(200);
    // }


    // $('input:radio[name=flight_booked]').click(function(){
    //     if($('input:radio[name=flight_booked]:checked').val() == 'yes'){

    //         $('.flight_booking_details').show(200);
    //         $('.fb_airline_ref_no').show(200);
    //         $('.fb_booking_date').show(200);
    //         $('.fb_airline_name_id').show(200);
    //         $('.fb_payment_method_id').show(200);
    //         $(".fb_select2").select2({ width: '100%' });      

    //         $("#airline").css({"margin-top": "25px"});

    //         $('textarea[name=flight_booking_details]').prop('required',true);
    //         $('input[name=fb_airline_ref_no]').prop('required',true);
    //         $('input[name=fb_booking_date]').prop('required',true);
    //         $('select[name="fb_airline_name_id"]').prop('required',true);
    //         $('select[name="fb_payment_method_id"]').prop('required',true);
    //     }
    //     else{
    //         $('.flight_booking_details').hide(200);
    //         $('.fb_airline_ref_no').hide(200);
    //         $('.fb_booking_date').hide(200);
    //         $('.fb_airline_name_id').hide(200);
    //         $('.fb_payment_method_id').hide(200);

    //         $("#airline").css({"margin-top": "0px"});

    //         $('textarea[name=flight_booking_details]').prop('required',false);
    //         $('input[name=fb_airline_ref_no]').prop('required',false);
    //         $('input[name=fb_booking_date]').prop('required',false);
    //         $('select[name="fb_airline_name_id"]').prop('required',false);
    //         $('select[name="fb_payment_method_id"]').prop('required',false);
    //     }

    //     if($('input:radio[name=flight_booked]:checked').val() == 'NA'){
    //         $('.fb_depend').hide(200);
    //     }else{
    //         $('.fb_depend').show(200);
    //     }
    // });
    // Flight Booked

    // Transfer Info
    if($('input:radio[name=asked_for_transfer_details]:checked').val() == 'yes'){

        $('.transfer_details').show(200);
        $('textarea[name=transfer_details]').prop('required',true);
        $('input[name=aft_last_date]').prop('required',false);
        // $(".responsible_person_depend").select2({ width: '100%' }); 

    }else{
        $('.transfer_details').hide(200);
        $('textarea[name=transfer_details]').prop('required',false);
        // $('input[name=aft_last_date]').prop('required',true);
        // $(".responsible_person_depend").select2({ width: '100%' }); 
    }

    if($('input:radio[name=asked_for_transfer_details]:checked').val() == 'NA'){

        $('.aft_depend').hide(200);
        // $('select[name="aft_person"]').prop('required',false);
        // $('input[name=aft_last_date]').prop('required',false);

    }else{
        $('.aft_depend').show(200);
    }

    $('input:radio[name=asked_for_transfer_details]').click(function(){
        if($('input:radio[name=asked_for_transfer_details]:checked').val() == 'yes'){

            $('.transfer_details').show(200);
            $('textarea[name=transfer_details]').prop('required',true);
            $('input[name=aft_last_date]').prop('required',false);
            // $(".responsible_person_depend").select2({ width: '100%' }); 

        }else{
            $('.transfer_details').hide(200);

            $('textarea[name=transfer_details]').prop('required',false);
            // $('input[name=aft_last_date]').prop('required',true);
            // $(".responsible_person_depend").select2({ width: '100%' }); 
        }

        if($('input:radio[name=asked_for_transfer_details]:checked').val() == 'NA'){
            
            $('.aft_depend').hide(200);
            // $('select[name="aft_person"]').prop('required',false);
            // $('input[name=aft_last_date]').prop('required',false);

        }else{
            $('.aft_depend').show(200);
        }
    });
    // Transfer Info

    // Transfers Organised

    if($('input:radio[name=transfer_organised]:checked').val() == 'yes'){
        $('.transfer_organised_details').show(200);
        $('textarea[name=transfer_organised_details]').prop('required',true);
        // $('input[name=to_last_date]').prop('required',false);

        
        // $(".to_rp").select2({ width: '100%' });
    }else{
        $('.transfer_organised_details').hide(200);
        $('textarea[name=transfer_organised_details]').prop('required',false);
        // $('input[name=to_last_date]').prop('required',true);

        // $(".to_rp").select2({ width: '100%' });
    }
    if($('input:radio[name=transfer_organised]:checked').val() == 'NA'){
        $('.to_depend').hide(200);
        // $('input[name=to_last_date]').prop('required',false);
    }else{
        $('.to_depend').show(200);
    }

    $('input:radio[name=transfer_organised]').click(function(){
        if($('input:radio[name=transfer_organised]:checked').val() == 'yes'){
            $('.transfer_organised_details').show(200);
            $('textarea[name=transfer_organised_details]').prop('required',true);
            // $('input[name=to_last_date]').prop('required',false);

            // $(".to_rp").select2({ width: '100%' });
        }else{
            $('.transfer_organised_details').hide(200);
            $('textarea[name=transfer_organised_details]').prop('required',false);
            // $('input[name=to_last_date]').prop('required',true);

            // $(".to_rp").select2({ width: '100%' });
        }
        if($('input:radio[name=transfer_organised]:checked').val() == 'NA'){
            $('.to_depend').hide(200);
            // $('input[name=to_last_date]').prop('required',false);
        }else{
            $('.to_depend').show(200);
        }
    });
    // Transfers Organised

    // Itinerary Finalised
    if($('input:radio[name=itinerary_finalised]:checked').val() == 'yes'){
        
        $('.itinerary_finalised_details').show(200);
        $('.itf_current_date').show(200);
        $('textarea[name=itinerary_finalised_details]').prop('required',true);
        $('input[name=itf_current_date]').prop('required',true);
        // $(".if_rp").select2({ width: '100%' });

    }else{
        $('.itinerary_finalised_details').hide(200);
        $('textarea[name=itinerary_finalised_details]').prop('required',false);
        $('input[name=itf_current_date]').prop('required',false);
        // $(".if_rp").select2({ width: '100%' });
    }
    if($('input:radio[name=itinerary_finalised]:checked').val() == 'NA'){
        $('.itf_depend').hide(200);
        // $('input[name=itf_last_date]').prop('required',false);
    }else{
        $('.itf_depend').show(200);
    }

    $('input:radio[name=itinerary_finalised]').click(function(){
        if($('input:radio[name=itinerary_finalised]:checked').val() == 'yes'){
        
            $('.itinerary_finalised_details').show(200);
            $('.itf_current_date').show(200);
            $('textarea[name=itinerary_finalised_details]').prop('required',true);
            $('input[name=itf_current_date]').prop('required',true);
            // $(".if_rp").select2({ width: '100%' });

        }else{
            $('.itinerary_finalised_details').hide(200);
            $('textarea[name=itinerary_finalised_details]').prop('required',false);
            $('input[name=itf_current_date]').prop('required',false);
            // $(".if_rp").select2({ width: '100%' });
        }
        if($('input:radio[name=itinerary_finalised]:checked').val() == 'NA'){
            $('.itf_depend').hide(200);
            // $('input[name=itf_last_date]').prop('required',false);
        }else{
            $('.itf_depend').show(200);
        }
    });

    // Itinerary Finalised


    // Travel Document Prepared
    if($('input:radio[name=document_prepare]:checked').val() == 'yes'){

        $('.tdp_current_date').show(200);
        $('textarea[name=tdp_current_date]').prop('required',true);
        $(".tdp_rp").select2({ width: '100%' });
    }else
    {
        $('.tdp_current_date').hide(200);
        $('textarea[name=tdp_current_date]').prop('required',false);
        $(".tdp_rp").select2({ width: '100%' });
    }

    if($('input:radio[name=document_prepare]:checked').val() == 'NA'){
        $('.dp_depend').hide(200);
    }else{
        $('.dp_depend').show(200);
    }

    $('input:radio[name=document_prepare]').click(function(){
        if($('input:radio[name=document_prepare]:checked').val() == 'yes'){

        $('.tdp_current_date').show(200);
        $('textarea[name=tdp_current_date]').prop('required',true);
        // $(".tdp_rp").select2({ width: '100%' });
    }else
    {
        $('.tdp_current_date').hide(200);
        $('textarea[name=tdp_current_date]').prop('required',false);
        // $(".tdp_rp").select2({ width: '100%' });
    }

    if($('input:radio[name=document_prepare]:checked').val() == 'NA'){
        $('.dp_depend').hide(200);
    }else{
        $('.dp_depend').show(200);
    }
    });
    // Travel Document Prepared

    // Travel Document Sent
    if($('input:radio[name=documents_sent]:checked').val() == 'yes'){
            // confirm('Full Payment may not have been received--Please confirm you like to Send Documnet to client');
            $('.documents_sent_details').show(200);
            $('.tds_current_date').show(200);

            $('textarea[name=documents_sent_details]').prop('required',true);
            $('input[name=tds_current_date]').prop('required',true);

            // $('.tds_rp').select2({ width: '100%' });
        }else{
            $('.documents_sent_details').hide(200);
            $('.tds_current_date').hide(200);

            $('textarea[name=documents_sent_details]').prop('required',false);
            $('input[name=tds_current_date]').prop('required',false);

            // $('.tds_rp').select2({ width: '100%' });
        }
        if($('input:radio[name=documents_sent]:checked').val() == 'NA'){
            $('.ds_depend').hide(200);
            // $('input[name=ds_last_date]').prop('required',false);
        }else{
            $('.ds_depend').show(200);
        }

    $('input:radio[name=documents_sent]').click(function(){
        if($('input:radio[name=documents_sent]:checked').val() == 'yes'){
            confirm('Full Payment may not have been received--Please confirm you like to Send Documnet to client');
            $('.documents_sent_details').show(200);
            $('.tds_current_date').show(200);

            $('textarea[name=documents_sent_details]').prop('required',true);
            $('input[name=tds_current_date]').prop('required',true);

            // $('.tds_rp').select2({ width: '100%' });
        }else{
            $('.documents_sent_details').hide(200);
            $('.tds_current_date').hide(200);

            $('textarea[name=documents_sent_details]').prop('required',false);
            $('input[name=tds_current_date]').prop('required',false);

            // $('.tds_rp').select2({ width: '100%' });
        }
        if($('input:radio[name=documents_sent]:checked').val() == 'NA'){
            $('.ds_depend').hide(200);
            // $('input[name=ds_last_date]').prop('required',false);
        }else{
            $('.ds_depend').show(200);
        }
    });
    // Travel Document Sent

    // App login Sent
    if($('input:radio[name=electronic_copy_sent]:checked').val() == 'yes'){
        $('.electronic_copy_details').show(200);
        $('.set_reminder_app').show(200);

        $('select[name=aps_person]').prop('required',true);
        $('input[name=aps_last_date]').prop('required',true);
        $('textarea[name=electronic_copy_details]').prop('required',true);

        // $('.als_rp').select2({ width: '100%' });

    }else{
        $('.electronic_copy_details').hide(200);
        $('.set_reminder_app').hide(200);

        $('select[name=aps_person]').prop('required',false);
        $('input[name=aps_last_date]').prop('required',false);
        $('textarea[name=electronic_copy_details]').prop('required',false);

        // $('.als_rp').select2({ width: '100%' });
    }

    $('input:radio[name=electronic_copy_sent]').click(function(){
        if($('input:radio[name=electronic_copy_sent]:checked').val() == 'yes'){
            $('.electronic_copy_details').show(200);
            $('.set_reminder_app').show(200);

            $('select[name=aps_person]').prop('required',true);
            $('input[name=aps_last_date]').prop('required',true);
            $('textarea[name=electronic_copy_details]').prop('required',true);

            // $('.als_rp').select2({ width: '100%' });

        }else{
            $('.electronic_copy_details').hide(200);
            $('.set_reminder_app').hide(200);

            $('select[name=aps_person]').prop('required',false);
            $('input[name=aps_last_date]').prop('required',false);
            $('textarea[name=electronic_copy_details]').prop('required',false);

            // $('.als_rp').select2({ width: '100%' });
        }
    });
    // App login Sent
});  

</script>

    <script type="text/javascript">
   

        $(document).ready(function() {

            $('.currency').html($('select[name="currency"]').val());
            $('.convert-currency').html($('select[name="convert_currency"]').val());


            $('input[type=radio][name=reference]').on('change', function() {
                switch ($(this).val()) {
                    case 'zoho':
                        $('#referencename').text('Zoho Reference');
                        break;

                    case 'tas':
                        $('#referencename').text('TAS Reference');
                        break;
                }
            });


            var typingTimer; //timer identifier
            var doneTypingInterval = 2000; //time in ms, 5 second for example
            var $input = $('input[name="ref_no"]');

            $(document).on('click', '#sendReference', function() {
                $('#link').html('');
                $('#link').removeAttr('class');
                $(this).text('Searching');
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

            function doneTyping() {
                book_id = $('input[name="ref_no"]').val();
                referenceName = $('input[type=radio][name=reference]:checked').val();

                if (book_id) {
                    token = $('input[name=_token]').val();
                    data = {
                        id: book_id,
                        reference_name: referenceName
                    };
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        data: data,
                        beforeSend: function() {
                            $("#divLoading").addClass('show');
                        },
                        type: 'POST',
                        dataType: "json",
                        success: function(data) {
                            // if( Object.keys(data).length > 0 ){
                            //     $('select[name="type_of_holidays"]').empty();
                            //         $.each( data.holidayTypes, function( key, value ) {
                            //         var selected = (value.id == data.holiday_type.id)? true: false;
                            //         var newOption = new Option(value.name, value.id, selected, true);
                            //         $('select[name="type_of_holidays"]').append(newOption);
                            //     });
                            //         console.log(data.passengers.lead_passenger);
                            //     $('select[name="brand_name"]').val(data.holiday_type.brand_id).trigger('change');
                            //     $('select[name="group_no"]').val(data.pax).trigger('change');  
                            //     $('select[name="currency"]').val(data.currency).trigger('change');
                            //     $('#sales_person').val(data.sale_person).trigger('change');
                            //     $('input[name="lead_passenger_name"]').val(data.passengers.lead_passenger.passenger_name);
                            //     $('input[name="dinning_preferences"]').val(data.passengers.lead_passenger.dinning_prefrences);
                            //     $('input[name="bedding_preference"]').val(data.passengers.lead_passenger.bedding_prefrences);
                            //     $('select[name="type_of_holidays"]').val(data.holiday_type.id).trigger('change'); 
                            // }else{
                            //     $('#error_ref_no').text('The Reference is not found');
                            // }
                            if(data.status == true){  
                                var data = data.response;                          
                                $('select[name="type_of_holidays"]').empty();
                                    $.each( data.holidayTypes, function( key, value ) {
                                    var selected = (value.id == data.holiday_type.id)? true: false;
                                    var newOption = new Option(value.name, value.id, selected, true);
                                    $('select[name="type_of_holidays"]').append(newOption);
                                });
                                    
                                $('select[name="brand_name"]').val(data.holiday_type.brand_id).trigger('change');
                                $('select[name="group_no"]').val(data.pax).trigger('change');  
                                $('select[name="currency"]').val(data.currency).trigger('change');
                                $('#sales_person').val(data.sale_person).trigger('change');
                                $('input[name="lead_passenger_name"]').val(data.passengers.lead_passenger.passenger_name);
                                $('input[name="dinning_preferences"]').val(data.passengers.lead_passenger.dinning_prefrences);
                                $('input[name="bedding_preference"]').val(data.passengers.lead_passenger.bedding_prefrences);
                                $('select[name="type_of_holidays"]').val(data.holiday_type.id).trigger('change'); 
                                
                                if(data.passengers.passengers.length > 0){
                                    data.passengers.passengers.forEach(($_value, $key) => {
                                        var $_count = $key + 1;
                                        $('input[name="pax['+$_count+'][full_name]"]').val($_value.passenger_name);
                                        $('input[name="pax['+$_count+'][email_address]"]').val($_value.passenger_email);
                                        $('input[name="pax['+$_count+'][contact_number]"]').val($_value.passenger_contact);
                                        $('input[name="pax['+$_count+'][date_of_birth]"]').val($_value.passenger_dbo);
                                        $('input[name="pax['+$_count+'][bedding_preference]"]').val($_value.bedding_prefrences);
                                        $('input[name="pax['+$_count+'][dinning_preference]"]').val($_value.dinning_prefrences);
                                    });
                                }
                            }else{
                                $('#error_ref_no').text(data.error);
                            }
                            $('#sendReference').text('Search');
                            $("#divLoading").removeClass('show');
                            $('#sendReference').removeAttr('disabled');
                        } //end success
                    });
                }
            }

            // Dynamically appened qoute 
            $('body').on('click', '#new', function(e) {
                var qoute = $('#qoute').html();
                $("#parent").append(qoute);
                reinitializedDynamicFeilds();
            });



            function reinitializedDynamicFeilds() {

                // $(".supplier-currency, .booked-by-select2, .booking-method-select2, .category-select2, .supplier-select2, .product-select2, .supervisor-select2, .booking-type-select2")
                //     .removeClass('select2-hidden-accessible').next().remove();
                // $(" .booked-by-select2, .booking-method-select2, .category-select2, .supplier-select2, .product-select2, .supervisor-select2, .booking-type-select2")
                //     .select2();

                // $('.supplier-currency').select2({
                //     templateResult: formatState,
                //     templateSelection: formatState
                // });

                // $(".datepicker").datepicker({
                //     autoclose: true,
                //     format: 'dd/mm/yyyy'
                // });
            }

            $(document).on('change', 'select[name="category[]"]', function() {

                var $selector = $(this);
                var category_id = $(this).val();

                var options = '';
                $.ajax({
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'category_id': category_id
                    },
                    success: function(response) {

                        // console.log(response);

                        options += '<option value="">Select Supplier</option>';

                        $.each(response, function(key, value) {
                            options += '<option value="' + value.id + '">' + value.name + '</option>';
                        });

                        $selector.closest('.row').find('[class*="supplier-select2"]').html(options);
                        $selector.closest('.row').find('[class*="product-select2"]').html('<option value="">Select Product</option>');
                        $selector.closest('.qoute').find('[name="service_details[]"]').val('');
                    }
                })
            });

            $(document).on('change', 'select[name="supplier_currency[]"]', function() {

                let $selector = $(this);
                let selected_currency_code = $(this).val();
                let currentCost = $selector.closest(".qoute").find('[class*="cost"]').val();

                let final = 0;
                let selectedMainCurrency = $("select[name='currency']").val();

                let costArray = [];
                let currencyArray = [];

                $selector.closest(".qoute").find('[class*="cost"]').attr("data-code",
                    selected_currency_code);
                $selector.closest(".qoute").find('[class*="symbol"]').html(selected_currency_code);


                $('.cost').each(function() {
                    console.log('count');   
                    cost = $(this).val();
                    currency = $(this).attr("data-code");

                    if (cost !== "" && cost !== '0') {
                        costArray.push(parseFloat(cost));
                    }

                    if (currency !== "" && cost !== '0') {
                        currencyArray.push(currency);
                    }

                });


                $.ajax({
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'to': selectedMainCurrency,
                        'from': selected_currency_code
                    },
                    success: function(response) {

                        qoute_currency = currentCost * response[selected_currency_code];
                        // $selector.closest(".qoute").find('[class*="base-currency"]').val((qoute_currency.toFixed(2)));
                        $selector.closest(".qoute").find('[class*="base-currency"]').val((!
                            isNaN(parseFloat(qoute_currency)) ? parseFloat(
                                qoute_currency).toFixed(2) : parseFloat(0).toFixed(2)));

                        for (i = 0; i < currencyArray.length; i++) {
                            final += (costArray[i] * response[currencyArray[i]]);
                        }

                        // $('.net_price').val(final.toFixed(2));
                        $('.net_price').val((isNaN(parseFloat(final)) ? parseFloat(0).toFixed(
                            2) : parseFloat(final).toFixed(2)));

                        var net_price = parseFloat($('.net_price').val());
                        var markup_percent = parseFloat($('.markup-percent').val());
                        var markup_amount = parseFloat($('.markup-amount').val());
                        markupAmount = (net_price / 100) * markup_percent;
                        // $('.markup-amount').val(markupAmount.toFixed(2));
                        $('.markup-amount').val((isNaN(parseFloat(markupAmount)) ? parseFloat(0)
                            .toFixed(2) : parseFloat(markupAmount).toFixed(2)));

                        var sellingPrice = (markupAmount + net_price);
                        // $('.selling').val(sellingPrice.toFixed(2));
                        $('.selling').val((isNaN(parseFloat(sellingPrice)) ? parseFloat(0)
                            .toFixed(2) : parseFloat(sellingPrice).toFixed(2)));

                        var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2)) /
                            sellingPrice.toFixed(2)) * 100);
                        $('.gross-profit').val((!isNaN(parseFloat(grossProfit)) ? parseFloat(
                            grossProfit).toFixed(2) : parseFloat(0).toFixed(2)));
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
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'to': selectedMainCurrency,
                        'from': $selector.attr("data-code")
                    },
                    success: function(response) {

                        qoute_currency = $selector.val() * response[$selector.attr("data-code")];
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
                        // $('.gross-profit').val(grossProfit.toFixed(2));
                        $('.gross-profit').val((!isNaN(parseFloat(grossProfit)) ? parseFloat(grossProfit).toFixed(2) : parseFloat(0).toFixed(2) ));

                        // console.log(last_convert_currency);
                        // var perPersonAmount = sellingPrice / $('select[name="group_no"]').val();
                        // $('.per-person').val(perPersonAmount);

                    }
                });

            });
    
            $(document).on('change', '.markup-percent', function() {

                var net_price = parseFloat($('.net_price').val());
                var markup_percent = parseFloat($('.markup-percent').val());
                var markup_amount = parseFloat($('.markup-amount').val());

                markupAmount = (net_price / 100) * markup_percent;

                $('.markup-amount').val(markupAmount.toFixed(2));

                var sellingPrice = (markupAmount + net_price);
                $('.selling').val(sellingPrice.toFixed(2));

                var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2)) / sellingPrice.toFixed(
                    2)) * 100);
                $('.gross-profit').val(grossProfit.toFixed(2));
            });

            $(document).on('change', '.markup-amount', function() {

                var net_price = parseFloat($('.net_price').val());
                var markup_percent = parseFloat($('.markup-percent').val());
                var markup_amount = parseFloat($('.markup-amount').val());

                markupPercentage = markup_amount / (net_price / 100);
                $('.markup-percent').val(parseInt(markupPercentage));

                var sellingPrice = markup_amount + net_price;
                $('.selling').val(sellingPrice.toFixed(2));

                var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2)) / sellingPrice.toFixed(
                    2)) * 100);
                $('.gross-profit').val(grossProfit.toFixed(2));

                // var perPersonAmount = sellingPrice / $('select[name="group_no"]').val();
                // $('.per-person').val(perPersonAmount);

            });

            $(document).on('change', 'select[name="currency"]', function() {

                var selected_currency_code = $(this).val();
                var costArray = [];
                var currencyArray = [];
                var selectedMainCurrency = $("select[name='currency']").val();
                var final = 0;

                $('.cost').each(function() {

                    cost = $(this).val();
                    currency = $(this).attr("data-code");

                    if (cost !== "" && cost !== '0') {
                        costArray.push(parseFloat(cost));
                    }

                    if (currency !== "" && cost !== '0') {
                        currencyArray.push(currency);
                    }

                });

                $.ajax({
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'to': selectedMainCurrency,
                        'from': currency
                    },
                    success: function(response) {

                        for (i = 0; i < currencyArray.length; i++) {

                            // $('.net_price').val((isNaN((costArray[i] * response[currencyArray[i]]).toFixed(2)) ?  parseFloat(0).toFixed(2) : (costArray[i] * response[currencyArray[i]]).toFixed(2) ));


                            $(".base-currency").eq(i + 1).val((isNaN((costArray[i] * response[currencyArray[i]]).toFixed(2)) ? parseFloat(0).toFixed(2) : (costArray[i] * response[currencyArray[i]]).toFixed(2)));
                            final += (costArray[i] * response[currencyArray[i]]);
                        }

                        // $('.net_price').val(final.toFixed(2));
                        $('.net_price').val((isNaN(parseFloat(final)) ? parseFloat(0).toFixed(
                            2) : parseFloat(final).toFixed(2)));

                        var net_price = parseFloat($('.net_price').val());
                        var markup_percent = parseFloat($('.markup-percent').val());
                        var markup_amount = parseFloat($('.markup-amount').val());
                        markupAmount = (net_price / 100) * markup_percent;
                        // $('.markup-amount').val(markupAmount.toFixed(2));
                        $('.markup-amount').val((isNaN(parseFloat(markupAmount)) ? parseFloat(0).toFixed(2) : parseFloat(markupAmount).toFixed(2)));

                        var sellingPrice = (markupAmount + net_price);
                        $('.selling').val((isNaN(parseFloat(sellingPrice)) ? parseFloat(0).toFixed(2) : parseFloat(sellingPrice).toFixed(2)));
                        // $('.selling').val(sellingPrice.toFixed(2));

                        var grossProfit = (((sellingPrice.toFixed(2) - net_price.toFixed(2)) / sellingPrice.toFixed(2)) * 100);
                        $('.gross-profit').val((!isNaN(parseFloat(grossProfit)) ? parseFloat(grossProfit).toFixed(2) : parseFloat(0).toFixed(2)));
                        // $('.gross-profit').val(grossProfit.toFixed(2));

                        // console.log(last_convert_currency);
                        // var perPersonAmount = sellingPrice / $('select[name="group_no"]').val();
                        // $('.per-person').val(perPersonAmount);

                    }
                });

                $('.currency').html(selected_currency_code);
            });

            $(document).on('click', '#ab_no', function() {
                $('#agency-detail').css("display", "none");
                $("input[name='agency_name']").prop('required', false);
                $("input[name='agency_contact_no']").prop('required', false);
            });

            $(document).on('click', '#ab_yes', function() {
                $('#agency-detail').css("display", "block");
                $("input[name='agency_name']").prop('required', true);
                $("input[name='agency_contact_no']").prop('required', true);
            });

            // $(document).on('change', 'select[name="convert_currency"]',function(){
            $(document).on('change', '#convert-currency', function() {


                // var selected_currency = 'USD';
                var selected_currency = $(this).val();
                var selectedMainCurrency = $("select[name='currency']").val();
                var sellingPrice = $('.selling').val();

                $.ajax({
                    type: 'POST',
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

            $(document).on('change', 'select[name="group_no"]', function() {

                var group_no = $(this).val();
                var show_convert_currency = $('.show-convert-currency').val();

                var perPersonAmount = show_convert_currency / group_no;
                $('.per-person').val(perPersonAmount.toFixed(2));

                var port_tax = parseFloat($('.port-tax').val());
                total_per_person = port_tax + perPersonAmount;
                $('.total').val(total_per_person.toFixed(2));
            });

            $(document).on('change', '.port-tax', function() {

                var port_tax = parseFloat($(this).val());
                var perPersonAmount = parseFloat($('.per-person').val());

                total_per_person = port_tax + perPersonAmount;
                $('.total').val(total_per_person.toFixed(2));
            });

            $(document).on('submit', '#user_form', function() {

                event.preventDefault();
                var formdata = $(this).serialize();

                $('#error_ref_no, #error_brand_name, #error_lead_passenger_name , #error_type_of_holidays, #error_sale_person, #error_season_id, #error_agency_name, #error_agency_contact_no, #error_currency, #error_group_no, #error_dinning_preferences, .error-cost, .date_of_service, .booking_date, .booking_due_date').html('');

                $('#error_fb_airline_name_id, #error_fb_payment_method_id, #error_fb_booking_date, #error_fb_airline_ref_no, #error_flight_booking_details, #error_transfer_details, #error_transfer_organised_details, #error_itinerary_finalised_details, #error_itf_current_date, #error_tdp_current_date, #error_documents_sent_details, #error_tds_current_date, #error_aps_person, #error_aps_last_date, #error_electronic_copy_details ').html('');

                jQuery(".finance-row").find(".disable-feild").attr("disabled", "disabled");

                $.ajax({
                    type: 'POST',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $("#divLoading").addClass('show');
                    },
                    success: function(data) {
                        $("#divLoading").removeClass('show');
                        alert(data.success_message);
                        window.history.back();
                        // location.reload();
                    },
                    error: function(reject) {
                        if (reject.status === 422) {
                            var errors = $.parseJSON(reject.responseText);
                            jQuery.each(errors.errors['date_of_service'], function(index,
                                value) {
                                jQuery.each(value, function(key, value) {
                                    jQuery(".date_of_service").eq(key).html(
                                        value);
                                });
                            });

                            jQuery.each(errors.errors['booking_date'], function(index, value) {
                                jQuery.each(value, function(key, value) {
                                    jQuery(".booking_date").eq(key).html(value);
                                });
                            });

                            jQuery.each(errors.errors, function(index, value) {
                                $('#error_' + index).html(value);

                                if ($('#error_' + index).length) {
                                    $('html, body').animate({
                                        scrollTop: $('#error_' + index).offset()
                                            .top
                                    }, 1000);
                                }
                            });

                            // Validating cost feild 
                            var rows = jQuery('.parent .qoute');
                            jQuery.each(rows, function(index, value) {
                                var error_row = errors.errors['cost.' + index] || null;
                                if (error_row) {
                                    jQuery(rows[index]).find('.input-group input.cost')
                                        .parent().next('.alert-danger').html(
                                            "Cost feild is required");
                                    $('html, body').animate({
                                        scrollTop: $(rows[index]).offset().top
                                    }, 1000);
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


                            jQuery.each(rows, function(index, value) {
                                var error_row = errors.errors['booking_due_date.' +
                                    index] ?? null;
                                if (error_row == null) {
                                    if (errors.errors['booking_due_date'] !==
                                        undefined) {
                                        error_row = errors.errors['booking_due_date'][
                                            index
                                        ] ?? null;
                                    } else {
                                        error_row = null;
                                    }
                                }
                                if (error_row && Array.isArray(error_row) == true) {
                                    jQuery(rows[index]).find('.booking_due_date').html(
                                        "Booking Due Date is required");
                                    $('html, body').animate({
                                        scrollTop: $(rows[index]).offset().top
                                    }, 1000);
                                } else {
                                    jQuery.each(error_row, function(key, value) {
                                        jQuery(".booking_due_date").eq(key)
                                            .html(value);
                                    });
                                }
                            });

                            $("#divLoading").removeClass('show');
                        }
                    }
                });

            });

            $(document).on('click', '.close', function() {
                $(this).closest(".qoute").remove();
            });


            $(document).on('change', '.deposit_amount', function () {
                var key     =   $(this).data('key');
                console.log('deposite key',key);
                var getclass   =   '.depositecost'+key;
                var actualcost  =  $('.cost'+key).val();
                console.log('actual cost '+actualcost, '.cost'+key);
                // console.log(getclass);
                var sum = 0;
                $(getclass).each(function(){
                    if($(this).val() != null){
                        sum += +$(this).val();
                    }
                });
               
              console.log('sum'+ sum)
                // console.log($(this).val(), sum);
                
                if(sum > actualcost){
                    alert('Payment amount should not be bigger than Actual cost');
                    $(this).val('');
                }
            });
            $(document).on('click', '.add_finance', function() {


                // $(".disable-feild").attr( "disabled", "disabled" );
                // $(".disable-feild").prop("disabled", false);
                var getClass = $(this).closest('.row').data('title');
                var classs = $(this).closest('.row').find('.deposit_amount').data('key');
                console.log(classs);
                $('input[name="deposit_amount['+classs+'][]"]').addClass('depositecost'+classs);
                // $('.finance-row').find('.row').find('.deposit_amount').addClass('depositecost'+classs);
                var count = $('.'+getClass).length + 1;
                var $v_text = 'Payment #'+count;
                console.log($v_text);
                $('.finance-row').find('.row').attr("data-title", getClass);
                $('.finance-row').find('.title').text($v_text);
                var title =  $('.finance-row').find('.title').addClass(getClass);
                let $selector = $(this);
                let html = $selector.closest(".qoute").find('[class*="finance-row"]').html();

                $selector.closest(".qoute").append(html);
                $('.finance-row').find('.row').removeAttr("data-title");
                $('.finance-row').find('.row').find('.title').removeClass(getClass);
                
                // $(".datepicker").datepicker({
                //     autoclose: true,
                //     format: 'dd/mm/yyyy'
                // });


                // $(".booking-method-select2").removeClass('select2-hidden-accessible').next().remove();
                // $(".booking-method-select2").select2();

            });

            $(document).on('click', '.upload_to_google_calendar', function() {

                let $selector = $(this);
                let depositAmount = $selector.closest(".row").find('[class*="deposit_amount"]').val();
                let deposit_due_date = $selector.closest(".row").find('[class*="deposit_due_date"]').val();
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
                    data: data,
                    beforeSend: function() {},
                    success: function(data) {

                        console.log(data);
                        // window.open(data, "_blank");
                        // $(this).attr("href", data); // Set herf value

                    },
                });

            });

            $(document).on('click', '.remove_finance', function() {
                $(this).closest(".row").remove();
            });

            $('#brand_name').on('select2:select', function (e) { 
            let brand_id = $(this).val();
            var options = '';
            $.ajax({
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'brand_id': brand_id
                },
                success: function(response) {

                    options += '<option value="">Select Holiday Type</option>';
                    $.each(response,function(key,value){
                    options += '<option value="'+value.id+'">'+value.name+'</option>';
                    });

                    $('select[name="type_of_holidays"]').html(options);
                    
                }
            });
        });
   

            // auto select default currency of supplier
            $(document).on('change', 'select[name="supplier[]"]', function() {

                var $selector = $(this);
                var supplier_id = $(this).val();
                var options = '';

                $.ajax({
                    type: 'POST',
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
                        // $selector.closest('.qoute').find('[class*="supplier-currency"]').val(response.supplier_currency.code).change();
                    }
                })
            });

            $(document).on('change', 'select[name="booked_by[]"]', function() {

                var $selector = $(this);
                var booked_by = $(this).val();

                $.ajax({
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'booked_by': booked_by
                    },
                    success: function(response) {

                        $selector.closest('.qoute').find('[class*="supervisor-select2"]').val(
                            response.supervisor_id).change();
                    }
                })
            });

            $(document).on('click', '.view-booking-version', function(e) {
                e.preventDefault();
                $('#booking-version').toggle();
            });

            $(document).on('click', '.view-quotation-version', function(e) {
                e.preventDefault();
                $('#quotation-version').toggle();
            });
            
            
            $(document).on('click', '.increment', function() {
                
                var close = $(this).closest('.row');
                
                    var valueElement = close.find('.adults');
                //     var dueDate = close.find('.deposit_due_date').val();
                //     var nowDate  =todayDate();
                //     const firstDate = convertDate(dueDate);
                //     const secondDate = convertDate(nowDate);
                //     const oneDay = 24 * 60 * 60 * 1000; 
                //     const diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));
                //    if(firstDate > secondDate){
                //         $(this).props('disabled', true);
                //    }else{
                //     console.log('less than');
                       
                //    }

                    // console.log(diffDays);
                    // console.log(close , valueElement);
                    if($(this).hasClass('plus')) 
                    {
                        valueElement.val(Math.max(parseInt(valueElement.val()) + 1));
                    } 
                    else if (valueElement.val() > 0) // Stops the value going into negatives
                    {
                        valueElement.val(Math.max(parseInt(valueElement.val()) - 1));
                    } 

                return false;
            });

            $(document).on('change', '.cost', function () {
                    var val = $(this).val();
                    var data = $(this).data('key');
                    var sum = 0;
                    
                    var sum = 0;
                    $('.deposite'+data).each(function(){
                        sum += +$(this).val();
                    });
                    
                    $('.deposite'+data).attr('max', val);
                    
                    // console.log('.deposite'+data);
                    
                    // if(sum > val){
                    //     $('.deposite'+data).val('');
                    // }
            });
            
            // get product's details for service details
            $(document).on('change', 'select[name="product[]"]',function(){

                var $selector = $(this);
                var product_id = $(this).val();

                $.ajax({
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'product_id': product_id
                    },
                    success: function(response) {

                        $selector.closest('.qoute').find('[name="service_details[]"]').val(response.description);
                    }
                })
            });
            
            $(document).on('change', '.paxNumber',function () {
                var $_val = $(this).val();
                console.log($_val);
                var currentDate = curday('-');
                if($_val > $('.appendCount').length){
                    var countable = ($_val - $('.appendCount').length) - 1;
                    for (i = 1; i <= countable; ++i) {
                        var count = $('.appendCount').length + 1;
                        const $_html = `<div class= appendCount" id="appendCount${count}">
                                    <div class="row" >
                                        <div class="col-md-3">
                                            <label>Passenger #${ count + 1 } Full Name</label> 
                                            <input type="text" name="pax[${count}][full_name]" class="form-control" placeholder="PASSENGER #2 FULL NAME" >
                                        </div>
                                        <div class="col-md-3">
                                            <label>Email Address</label> 
                                            <input type="email" name="pax[${count}][email_address]" class="form-control" placeholder="EMAIL ADDRESS" >
                                        </div>
                                        <div class="col-md-3">
                                            <label>Contact Number</label> 
                                            <input type="number" name="pax[${count}][contact_number]" class="form-control" placeholder="CONTACT NUMBER" >
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Date Of Birth</label> 
                                            <input type="date" max="${currentDate}" name="pax[${count}][date_of_birth]" class="form-control" placeholder="CONTACT NUMBER" >
                                        </div>
                                        <div class="col-md-3">
                                            <label>Bedding Preference</label> 
                                            <input type="text" name="pax[${count}][bedding_preference]" class="form-control" placeholder="BEDDING PREFERENCES" >
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label>Dinning Preference</label> 
                                            <input type="text" name="pax[${count}][dinning_preference]" class="form-control" placeholder="DINNING PREFERENCES" >
                                        </div>
                                    </div>
                                </div> `;
                            $('#appendPaxName').append($_html);
                    }
                }else{
                var countable = $('.appendCount').length + 1;
                console.log();
                for (var i = countable - 1; i >= $_val; i--) {
                        $("#appendCount"+i).remove();
                    }
                }
            });

    });

    var curday = function(sp){
        today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //As January is 0.
        var yyyy = today.getFullYear();

        if(dd<10) dd='0'+dd;
        if(mm<10) mm='0'+mm;
        return (yyyy+sp+mm+sp+dd);
    }; 
        // function convertDate(date) {
        //     var dateParts = date.split("/");
        //     return dateParts = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]);
        // }

        // function todayDate() {
        //     var today = new Date();
        //     var dd = String(today.getDate()).padStart(2, '0');
        //     var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        //     var yyyy = today.getFullYear();
        //     return today = dd + '/' + mm + '/' + yyyy;
        // }
    </script>

    


    </body>

    </html>
@endsection
