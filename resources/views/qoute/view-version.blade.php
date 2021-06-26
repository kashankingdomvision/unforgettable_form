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

    .disable{
        pointer-events:none;
        background:#eee;
    }


    .box-header>.fa, .box-header>.glyphicon, .box-header>.ion, .box-header .box-title{

    display: inline-block;
    font-size: 18px;
    margin: 0;
    line-height: 2;
}

</style>

<div class="content-wrapper">



    <section class="content-header">
        <h1>  </h1>
    </section>

    <section class="content">
        <div id="divLoading"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Quotation Version {{ $qoute_log->log_no }}  : {{ $qoute_log->quotation_no }} / {{ $qoute_log->created_date ? \Carbon\Carbon::parse(str_replace('/', '-', $qoute_log->created_date))->format('d/m/Y') : ""}}  By {{\App\User::findOrFail($qoute_log->user_id)->name}}
                        </h3>
                        {{-- <h3 > Quote</h3> --}}
                        <div class="pull-right">
                            <a onclick="return confirm('Are you sure you want to Recall this Quotation?');"  href="{{ route('recall-version',['quote_id'=>$qoute_log->qoute_id, 'log_no'=>$qoute_log->log_no]) }}">
                                <button class="btn btn-info pull-right"> Recall Quotation</button>
                            </a>
                        </div>
                    </div>

                    
                    
                    <div class="col-sm-6 col-sm-offset-3" style="text-align: center;">
                        @if (Session::has('success_message'))
                        <div class="alert alert-success">
                            {{ Session::get('success_message') }}
                        </div>
                        @endif
                    </div>

                    <form action="{{ route('creat-quote') }}" method="POST" class="form-horizontal" id="user_form">
                        @csrf

                        <div class="row">

                            <div class="row mt-2">
                                <div class="col-md-5 col-sm-offset-1 mb-2">
                                    <label>Rate Type </label> <br />
                                    <label class="radio-inline"><input type="radio" name="rate_type" value="live_rate" {{ $qoute_log->rate_type == 'live_rate' ? 'checked' : '' }} disabled>Live Rate</label>
                                    <label class="radio-inline"><input type="radio" name="rate_type" value="manual_rate" {{ $qoute_log->rate_type == 'manual_rate' ? 'checked' : '' }} disabled>Manual Rate</label>
                                </div>
                            </div>
                            
                            <div class="row ">
                                <div class="col-sm-5 col-sm-offset-1 mb-2">
                                    <label for="inputEmail3" id="referencename">Zoho Reference</label> <span style="color:red">*</span>
                                    <div class="input-group">
                                        <input type="text" name="ref_no" disabled value="{{ $qoute_log->ref_no }}"  class="form-control" placeholder='Enter Reference Number' >
                                        <span  id="link">
                                        </span>
                                        <span class="input-group-addon">
                                            <button id="sendReference" disabled type="button" class="btn-link"> Search </button>
                                        </span>
                                    </div>
                                    <div class="alert-danger" style="text-align:center" id="error_ref_no"></div>
                                </div>
                                
                                <div class="col-sm-5" style="margin-bottom:15px;">
                                    <label for="inputEmail3" class="">Quote Reference</label> <span style="color:red">*</span>
                                    <div class="input-group">
                                        <input type="text" name="quotation_no"  class="form-control" value="{{ $qoute_log->quotation_no }}" required readonly>
                                        <span class="input-group-addon"></span>
                                    </div>
                                    <div class="alert-danger" style="text-align:center" id="error_quotation_no"></div>
                                </div>
                        </div> 
                        
                        <div class="row">
                            <div class="col-sm-5 mb-2 col-sm-offset-1 mb-2">
                                <label for="inputEmail3" class="">Lead Passenger Name</label> <span style="color:red">*</span>
                                <div class="input-group">
                                    <input type="text" name="lead_passenger_name" class="form-control" value="{{ $qoute_log->lead_passenger_name }}" disabled>
                                    <span class="input-group-addon"></span>
                                </div>
                                <div class="alert-danger" style="text-align:center" id="error_lead_passenger_name"></div>
                            </div>
                
                            <div class="col-sm-5" style="margin-bottom:15px;">
                                <label class="">Brand Name</label> <span style="color:red">*</span>
                                <input type="text" name="brand_name" value="{{$qoute_log->getBrand->name}}" class="form-control"  disabled>
                                <div class="alert-danger" style="text-align:center" id="error_brand_name"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px;">
                                <label class="">Type Of Holidays</label> <span style="color:red">*</span>
                                <input type="text" name="type_of_holidays" value="{{ $qoute_log->getHolidayType->name }}" class="form-control"  disabled>
                                <div class="alert-danger" style="text-align:center" id="error_type_of_holidays"></div>
                            </div>
    
                            <div class="col-sm-5" style="margin-bottom:15px;">
                                <label class="">Sales Person</label> <span style="color:red">*</span>

                                <input type="text" name="sale_person" value="{{ $qoute_log->sale_person }}" class="form-control"  disabled>
                                <div class="alert-danger" style="text-align:center" id="error_sale_person"> </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px;">
                                <label class="">Booking Season</label> 
                                <span style="color:red">*</span>
                                <select class="form-control dropdown_value" name="season_id" disabled>
                                    <option value="">Select Season</option>
                                    @foreach ($seasons as $sess)
                                    <option value="{{ $sess->id }}" {{ $qoute_log->season_id == $sess->id ? 'selected' : ''}} >{{ $sess->name }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_season_id"> </div>
                            </div>

                            <div class="col-sm-1" style="margin-bottom: 35px; width:145px;">
                                <label for="inputEmail3" class="">Agency Booking</label> <span style="color:red"> *</span><br>
                                <input type="radio" name="agency_booking"  value="2" id="ab_yes" {{ $qoute_log->agency_booking == "2" ? 'checked' : ''}} disabled> <label for="ab_yes"> Yes</label>
                                <input type="radio" name="agency_booking"  value="1"  id="ab_no" {{ $qoute_log->agency_booking == "1" ? 'checked' : ''}}  disabled> <label for="ab_no"> No</label>
                                <div class="alert-danger" style="text-align:center" > </div>
                            </div>

                            <div class="row" style="{{ $qoute_log->agency_booking == 2 ? 'display:block' : 'display:none' }}" id="agency-detail">
                                <div class="col-sm-2" style="width:175px;">
                                    <label for="inputEmail3" class="">Agency Name</label> <span style="color:red"> *</span>
                                    <input type="text" name="agency_name" value="{{ $qoute_log->agency_name }}"  class="form-control">
                                    <div class="alert-danger" style="text-align:center" id="error_agency_name"> </div>
                                </div>

                                <div class="col-sm-2">
                                    <label for="inputEmail3" class="">Agency Contact No.</label> <span style="color:red"> *</span>
                                    <input type="text" name="agency_contact_no" value="{{ $qoute_log->agency_contact_no }}" class="form-control">
                                    <div class="alert-danger" style="text-align:center" id="error_agency_contact_no"> </div>
                                </div>
                            </div>

                        </div>


                        <div class="row">
                           <div class="col-sm-5 col-sm-offset-1 mb-2">
                                <label>Bedding Preferences</label> <span style="color:red">*</span>
                                <input type="text" name="bedding_preference" value="{{ $qoute_log->bedding_preference }}" class="form-control" disabled required>
                                <div class="alert-danger" style="text-align:center" id="error_bedding_preference"></div>
                            </div>
                            <div class="col-sm-5 mb-2">
                                <label> Dinning Preferences</label> <span style="color:red">*</span>
                                <input type="text" name="dinning_preferences" value="{{ $qoute_log->dinning_preferences }}" class="form-control" disabled>
                                <div class="alert-danger" style="text-align:center" id="error_dinning_preferences"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px;">
                                <label> Booking Currency</label> <span style="color:red">*</span>
                                <select name="currency" class="form-control currency-select2" disabled>
                                    <option value="">Select Currency</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->code }}" data-image="data:image/png;base64, {{$currency->flag}}" {{ $qoute_log->currency == $currency->code ? 'selected' : ''}} >  &nbsp; {{$currency->code}} - {{$currency->name}} </option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center" id="error_currency"></div>
                            </div>
                             <div class="col-sm-5" style="margin-bottom:15px">
                                <label class="">Pax No.</label> <span style="color:red">*</span>
                                  <select class="form-control dropdown_value select2" name="group_no" disabled>
                                    @for($i=1;$i<=30;$i++)
                                    <option value={{$i}} {{ $qoute_log->group_no == $i ? 'selected' : ''}} >{{$i}}</option>
                                    @endfor
                                  </select>
                                <div class="alert-danger" style="text-align:center" id="error_group_no"></div>
                            </div>
                        </div>
                        <div class="row" style="margin-left: 4px;">
                            <div class="col-sm-offset-1 mb-2" id="appendPaxName">
                                @if($qoute_log->group_no > 1)
                                    @foreach ($qoute_log->getPaxDetailLog as $paxKey => $pax )
                                    @php
                                         $count = $paxKey +1;
                                    @endphp
                                    <div class="mb-2">
                                        <div class="row" >
                                            <div class="col-md-3 mb-2">
                                                <label>Passenger #{{ $count }} Full Name</label> 
                                                <input disabled type="text" name="pax[{{$paxKey}}][full_name]" value="{{ $pax->full_name }}" class="form-control" placeholder="PASSENGER #2 FULL NAME" >
                                                <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label>Email Address</label> 
                                                <input disabled type="c" name="pax[{{$paxKey}}][email_address]" value="{{ $pax->email }}" class="form-control" placeholder="EMAIL ADDRESS" >
                                                <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label>Contact Number</label> 
                                                <input disabled type="c" name="pax[{{$paxKey}}][contact_number]" value="{{ $pax->contact }}" class="form-control" placeholder="CONTACT NUMBER" >
                                                <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 mb-2">
                                                <label>Date Of Birth</label> 
                                                <input disabled type="date" name="pax[{{$paxKey}}][date_of_birth]" value="{{ $pax->date_of_birth }}" class="form-control" placeholder="CONTACT NUMBER" >
                                                <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label>Bedding Preference</label> 
                                                <input disabled type="text" name="pax[{{$paxKey}}][bedding_preference]" value="{{ $pax->bedding_preference }}" class="form-control" placeholder="BEDDING PREFERENCES" >
                                                <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                            </div>
                                            
                                            <div class="col-md-3 mb-2">
                                                <label>Dinning Preference</label> 
                                                <input disabled type="text" name="pax[{{$paxKey}}][dinning_perference]" value="{{ $pax->dinning_preference }}" class="form-control" placeholder="DINNING PREFERENCES" >
                                                <div class="alert-danger errorpax" style="text-align:center" id="error_pax_name_'+validatecount+'"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <br><br>

                        <div class="parent" id="parent">
                            @foreach ($qoute_detail_logs as $key => $quote_detail)
                                
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
                                            <input type="text" name="date_of_service[]" autocomplete="off" value="{{ !empty($quote_detail->date_of_service) ? date('d/m/Y', strtotime($quote_detail->date_of_service)) : "" }}"  class="form-control datepicker" placeholder="Date of Service" disabled >
                                        </div>
                                        <div class="alert-danger date_of_service" style="text-align:center"></div>
                                    </div>

                       

                                    <div class="col-sm-2" style="margin-bottom:15px;">
                                        <label class="">Category</label> 
                                        <select class="form-control category-select2"  name="category[]" disabled>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ $quote_detail->category_id == $category->id  ? "selected" : "" }}> {{ $category->name }} </option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('category') }} </div>
                                    </div>
        
                                    <div class="col-sm-2" style="margin-bottom:15px">
                                        <label class="test">Supplier</label> 
                                        <select class="form-control supplier-select2 supplier-select2"  name="supplier[]" disabled>
                                            <option value="">Select Supplier</option>
                                            @if(!empty($quote_detail->getCategory->getSupplier))
                                                @foreach ($quote_detail->getCategory->getSupplier as $supplier)
                                                    <option value="{{ $supplier->id }}" {{ $quote_detail->supplier == $supplier->id  ? "selected" : "" }}> {{ $supplier->name }} </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label class="">Product</label> 
                                        <select class="form-control product-select2"  name="product[]" disabled>
                                            <option value="">Select Product</option>
                                            @if(!empty($quote_detail->getSupplier->products))
                                                @foreach ($quote_detail->getSupplier->products as $product)
                                                    <option value="{{ $product->id }}" {{ $quote_detail->product == $product->id  ? "selected" : "" }}> {{ $product->name }} </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('product') }} </div>
                                    </div>
        
                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Date</label>
                                        <div class="input-group">
                                            <input type="text" name="booking_date[]" value="{{ !empty($quote_detail->booking_date) ? date('d/m/Y', strtotime($quote_detail->booking_date)) : "" }}" class="form-control datepicker" autocomplete="off" placeholder="Booking Date" disabled>
                                        </div>
                                        <div class="alert-danger booking_date" style="text-align:center"> {{ $errors->first('booking_date') }} </div>
                                    </div>
        
                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Due Date <span style="color:red">*</span></label> 
                                        <div class="input-group">
                                            <input type="text" name="booking_due_date[]"  value="{{ !empty($quote_detail->booking_due_date) ? date('d/m/Y', strtotime($quote_detail->booking_due_date)) : "" }}" class="form-control datepicker" placeholder="Booking Date"  disabled>
                                        </div>
                                        <div class="alert-danger booking_due_date" style="text-align:center"></div>
                                    </div>
        
                    
                                </div>

                       
        
                                <div class="row">

                                    <div class="col-sm-2" style="margin-bottom: 35px;">
                                        <label for="inputEmail3" class="">Service Details</label> 
                                        <textarea name="service_details[]"   class="form-control" cols="30" rows="1" disabled>{{ $quote_detail->service_details }}</textarea>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Method</label>
                                        <div class="input-group">
                                            <select class="form-control booking-method-select2"  name="booking_method[]"   class="form-control" disabled>
                                                <option value="">Select Booking Method</option>
                                                @foreach ($booking_methods as $booking_method)
                                                    <option value="{{$booking_method->id}}" {{ $quote_detail->booking_method == $booking_method->id  ? "selected" : "" }}>{{$booking_method->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_method') }} </div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booked By </label>
                                        <div class="input-group">
                                            <select class="form-control booked-by-select2"  name="booked_by[]"   class="form-control" disabled>
                                                <option value="">Select Person</option>
                                                @foreach ($users as $user)
                                                    <option value="{{$user->id}}" {{ $quote_detail->booked_by == $user->id ? "selected" : "" }}>{{$user->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>
        
                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Reference</label>
                                        <div class="input-group">
                                            <input type="text" name="booking_refrence[]" value="{{ $quote_detail->booking_refrence }}" class="form-control" placeholder="Booking Refrence"  disabled>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> </div>
                                    </div>

                                    <div class="col-sm-2 " style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Booking Type</label> 
                                        <div class="input-group">
                                            <select class="form-control booked-by-select2" disabled name="booking_type[]" >
                                                <option value="">Select Booking Type</option>
                                                <option {{  ($quote_detail->booking_type == 'refundable')? 'selected' : '' }} value="refundable">Refundable</option>
                                                <option {{  ($quote_detail->booking_type == 'non_refundable')? 'selected' : '' }} value="non_refundable">Non-Refundable</option>
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_type') }} </div>
                                    </div>
        
                                    <div class="col-sm-2" style="margin-bottom: 35px;">
                                        <label for="inputEmail3" class="">Comments</label> 
                                        <textarea name="comments[]"   class="form-control" cols="30" rows="1" disabled>{{ $quote_detail->comments }}</textarea>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>
 
                                </div>

                                <div class="row">

                                    <div class="col-sm-2" style="margin-bottom:15px;">
                                        <label class="">Supplier Currency  <span class="text-danger">*</span></label> 
                                        <select class="form-control supplier-currency" required name="supplier_currency[]" required  disabled>
                                            <option value="">Select Currency</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->code }}" data-image="data:image/png;base64, {{$currency->flag}}" {{ $quote_detail->supplier_currency == $currency->code  ? "selected" : "" }}> &nbsp; {{$currency->code}} - {{$currency->name}}  </option>
                                            @endforeach
                                        </select>
                                        <div class="alert-danger" style="text-align:center"></div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Cost</label> <span style="color:red">*</span>
                                        <div class="input-group">
                                            <span class="input-group-addon symbol" >{{ $quote_detail->supplier_currency }}</span>
                                            <input type="number" data-code="{{$quote_detail->supplier_currency}}" name="cost[]" class="form-control cost" value="{{ $quote_detail->cost }}"  placeholder="Cost" min="0" required disabled>
                                        </div>
                                        <div class="alert-danger error-cost" style="text-align:center" ></div>
                                    </div>
                                    
                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Converted into Base Currency</label>
                                        <label class="currency"></label>  
                                        <input type="text" class="base-currency"  name="qoute_base_currency[]" value="{{ number_format($quote_detail->qoute_base_currency, 2, '.', '') }}" readonly disabled><br>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Added in Sage</label>
                                        <div class="input-group">
                                            <input type="hidden" name="added_in_sage[]" value="0" disabled>
                                            <input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value"  {{ $quote_detail->added_in_sage == '1' ? 'checked' : '' }} disabled >
                                        </div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px;">
                                        <label for="inputEmail3" class="">Supervisor</label>
                                        <div class="input-group">
                                            <select  name="supervisor[]" class="form-control supervisor-select2" disabled>
                                                <option value="">Select Supervisor</option>
                                                @foreach ($supervisors as $supervisor)
                                                    <option value="{{$supervisor->id}}" {{ $quote_detail->supervisor_id == $supervisor->id ? "selected" : "" }}>{{$supervisor->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="alert-danger" style="text-align:center"> </div>
                                    </div>

                                    <div class="col-sm-2" style="margin-bottom: 15px; padding-top: 3rem;">
                                        <a  target="_blank" href="{{ asset("quote/".$qoute_log->id."/".$quote_detail->qoute_invoice) }}" style="">  {{ $quote_detail->qoute_invoice }}</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
                                        <label class="currency" > {{ $qoute_log->currency }} </label>
                                        <input type="number" name="net_price" step="any" min="0" class="net_price hide-arrows" value="{{ number_format($qoute_log->net_price, 2, '.', '') }}" disabled>
                                    </label>
                                </div>
                                <div class="row">
                                    <label class="">
                                        <label class="currency" > {{ $qoute_log->currency }} </label>
                                        <input type="number" class="markup-amount" step="any" min="0" name="markup_amount" value="{{ number_format($qoute_log->markup_amount, 2, '.', '') }}" disabled>
                                    </label>
                                </div>
                                <div class="row">
                                    <label class="">
                                        <label class="currency" > {{ $qoute_log->currency }} </label>
                                        <input type="number" class="selling hide-arrows" min="0"  step="any" name="selling" value="{{ number_format($qoute_log->selling, 2, '.', '') }}" disabled>
                                    </label>
                                </div>
                                <div class="row">
                                    <label class="">
                                        <label class="currency" > {{ $qoute_log->currency }} </label>
                                        <input type="number" class="gross-profit hide-arrows" min="0" step="any" name="gross_profit" value="{{ number_format($qoute_log->gross_profit, 2, '.', '') }}" disabled>
                                        <span>%</span> 
                                    </label>
                                </div>
                    
                            </div>

                            
                            <div class="col-sm-2">
                                <br>
                                <div class="row">
                                    <label class="">
                                        <input type="number" class="markup-percent" name="markup_percent" min="0" value="{{$qoute_log->markup_percent}}" style="width:70px;" disabled>
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
                            <div class="col-sm-2 col-sm-offset-1" style="margin-bottom:15px;">
                                <label class="convert-currency">Total Selling in {{$qoute_log->convert_currency}}</label>
                                </label>
                                {{-- <select class="form-control select2" id="convert-currency" name="convert_currency">
                                    <option value="">Select Currency</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->code }}" {{ $qoute_log->convert_currency == $currency->code ? 'selected' : ''}}> {{ $currency->name }} ({{ $currency->symbol }}) </option>
                                    @endforeach
                                </select> --}}
                            </div>

                            <div class="col-sm-2" style="margin-bottom:15px;">
                                <label class="convert-currency">{{$qoute_log->convert_currency}}</label>
                                    <input type="number" name="show_convert_currency" min="0" value="{{ number_format($qoute_log->show_convert_currency, 2, '.', '') }}" step="any" class="show-convert-currency hide-arrows" value="0" disabled>
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
                                <label class="convert-currency">{{$qoute_log->convert_currency}}</label>
                                <input type="number" class="per-person hide-arrows" min="0" value="{{ number_format($qoute_log->per_person, 2, '.', '') }}" step="any" name="per_person" value="0" disabled>
                            </div>
                        </div>


                       

                        <div class="box-footer">
                            {{-- <button type="submit" class="btn btn-info pull-right">Recall</button> --}}
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

    $('.currency-select2, .supplier-currency').select2({
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

    // $(document).on('submit','#user_form',function(){

    //     event.preventDefault();
    //     $("#user_form :disabled").removeAttr('disabled');
    //     var formdata = $(this).serialize();
    //     console.log(formdata);

    //     // $('#error_ref_no, #error_brand_name, #error_type_of_holidays, #error_sale_person, #error_season_id, #error_agency_name, #error_agency_contact_no, #error_currency, #error_group_no, .error-cost, .date_of_service, .booking_date, .booking_due_date').html('');


     

    // });
  
</script>




</body>

</html>
@endsection