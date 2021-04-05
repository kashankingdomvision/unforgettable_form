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
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1> Add Code</h1>
    </section>

    <section class="content">
        <div id="divLoading"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create Code</h3>
                    </div>
                    <div class="col-sm-6 col-sm-offset-3" style="text-align: center;">
                        @if (Session::has('success_message'))
                        <div class="alert alert-success">
                            {{ Session::get('success_message') }}</div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('creat-code') }}" class="form-horizontal" id='user_form' novalidate>
                        @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1">
                                <label for="inputEmail3" class="">Enter Reference Number</label><span style="color:red">
                                    * </span>
                                <div class="input-group">
                                    <input type="text" name="ref_no" value="{{old('ref_no')}}" class="form-control" placeholder='Enter Reference Number' >
                                    <span class="input-group-addon" id="link"><i class="fa fa-plane"></i></span>
                                </div>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('ref_no') }}</div>
                            </div>

                            <div class="col-sm-5" style="margin-bottom:15px">
                                <label class="">Brand Name</label><span style="color:red"> * </span>
                                <select class="form-control dropdown_value" name="brand_name" required="required">
                                    <option value="">Select Brand</option>
                                    @foreach ($get_user_branches->branches as $branche)
                                    <option value="{{ $branche->name }}" {{ old('brand_name') == $branche->name  ? "selected" : "" }}>{{ $branche->name }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('brand_name') }}
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px;">
                                <label class="">Type Of Holidays</label><span style="color:red"> * </span>
                                <select class="form-control dropdown_value" id="type_of_holidays" name="type_of_holidays" required="required">
                                    <option value="">Select Holiday</option>
                                    @foreach ($get_holiday_type->holiday_type as $holiday)
                                    <option value="{{ $holiday->name }}" {{ old('type_of_holidays') == $holiday->name  ? "selected" : "" }}>{{ $holiday->name }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('type_of_holidays') }}</div>
                            </div>
    
                            <div class="col-sm-5" style="margin-bottom:15px;">
                                <label class="">Sales Person</label><span style="color:red"> * </span>
                                <select class="form-control select2" id="sales_person" name="sale_person" required="required">
                                    <option value="">Select Person</option>
                                    @foreach ($get_user_branches->users as $user)
                                    <option value="{{ $user->email }}" {{ old('sale_person') == $user->email  ? "selected" : "" }}> {{ $user->email }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center"> {{ $errors->first('sale_person') }}</div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px">
                                <label class="">Select Category</label><span style="color:red"> * </span>
                                <select class="form-control select2 dropdown_value" id="category" name="category"
                                    required="required">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category') == $category->id  ? "selected" : "" }}> {{ $category->name }} </option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('category') }}
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <label class="">Select Product</label><span style="color:red"> * </span>
                                <select class="form-control select2 dropdown_value" id="product" name="product" required="required">
                                    <option value="">Select Product</option>
                                    @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product') == $product->id  ? "selected" : "" }}>  {{ $product->code }}-{{ $product->name }} </option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('product') }}
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-sm-5 col-sm-offset-1" style="margin-bottom:15px; padding-left:0px;">
                            <label class="">Booking Season</label><span style="color:red"> *
                            </span>

                            {{-- {{ old('season_id') == $sess->id || $sess->default_season == 1 ? "selected" : "" }} --}}
                            <select class="form-control dropdown_value" name="season_id" required="required">
                                <option value="">Select Season</option>
                                @foreach ($seasons as $sess)
                                <option value="{{ $sess->id }}"  
                                    @if(old('season_id') == $sess->id)
                                    selected
                                    @elseif($sess->default_season == 1 )
                                    selected
                                    @endif
                                    >{{ $sess->name }}</option>
                                @endforeach
                            </select>
                            <div class="alert-danger" style="text-align:center">{{ $errors->first('season_id') }}</div>
                        </div>

                        <div class="col-sm-5" style="margin-bottom: 35px;">
                            <label for="inputEmail3" class="">Agency Booking</label> <span style="color:red"> *</span><br>
                            {!! Form::radio('agency_booking', 2, null, ['id' => 'ab_yes', 'required' => 'true']) !!}&nbsp<label for="ab_yes">Yes</label>
                            {!! Form::radio('agency_booking', 1, null, ['id' => 'ab_no', 'required' => 'true']) !!}&nbsp<label for="ab_no">No</label>
                            <div class="alert-danger" style="text-align:center">{{ $errors->first('agency_booking') }}</div>
                        </div>

                        <div class="col-sm-5 col-sm-offset-1" style="padding-left:0px;">
                            <label class="">PAX NO</label><span style="color:red"> * </span>
                            <select class="form-control dropdown_value select2" name="pax_no" id="pax_no" required="required">
                                @for ($i = 1; $i <= 30; $i++) 
                                <option value={{ $i }} {{ old('pax_no') == $i ? "selected" : "" }}> {{ $i }}</option>
                                @endfor
                            </select>
                            <div class="alert-danger" style="text-align:center">{{ $errors->first('pax_no') }}</div>
                        </div>

                        <div class="col-sm-5" style="margin-bottom: 15px;">
                            <label for="inputEmail3" class="">Departure Date</label><span style="color:red"> * </span>
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('date_of_travel', null, ['autocomplete' => 'off', 'class' => 'form-control', 'id' => 'datepicker', 'placeholder' => 'Departure Date', 'required' => 'true', 'value' => "{{old('date_of_travel')}}"]) !!}
                            </div>
                            <div class="alert-danger" style="text-align:center"> {{ $errors->first('date_of_travel') }} </div>
                        </div>

                        <div class="col-sm-5 col-sm-offset-1" style="padding-left: 0px;">
                            <label for="inputEmail3" class="">Destination</label>
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('destination', null, ['autocomplete' => 'off', 'class' => 'form-control','id' => 'destination', 'placeholder' => 'Destination', 'value' => "{{old('destination')}}"]) !!}
                            </div>
                            <div class="alert-danger" style="text-align:center"> {{ $errors->first('destination') }}</div>
                        </div>

                        <div class="col-sm-5" style="margin-bottom:15px">
                            <label class="">Select Supplier</label><span style="color:red"> * </span>
                            <select class="form-control select2 dropdown_value" id="supplier" name="supplier" required="required">
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier') == $supplier->id  ? "selected" : "" }}> {{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <div class="alert-danger" style="text-align:center">{{ $errors->first('supplier') }}</div>
                        </div>

                        {{-- <div class="col-sm-5">
              <label for="inputEmail3" class="">Select To View Options</label>
              <div class="input-group">
                <span class="input-group-addon"></span>
                <select class="form-control det">
                  <option value=""> -- SELECT OPTION --</option>
                  <option value="flight-booked">Flight Booked</option>
                  <option value="transfer-info">Transfer Info</option>
                  <option value="transfers-organised">Transfers Organised</option>
                  <option value="itenerary-finalised">Itenerary Finalised</option>
                  <option value="travel-document-prepared">Travel Document Prepared</option>
                </select>
              </div>
              <div class="alert-danger" style="text-align:center">{{$errors->first('destination')}}
                    </div>
                </div> --}}
                <div class="col-sm-5"></div>
                <div class="col-sm-10 col-sm-offset-2" style="margin-top: 15px;">
                    <div class="col-sm-2">
                        <div class="input-group">
                            <a class="up-btn btn btn-primary" data-id="flight-booked">Flight Booked +</a>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <a class="up-btn btn btn-primary" data-id="transfer-info">Transfer Info +</a>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <a class="up-btn btn btn-primary" data-id="transfers-organised">Transfers Organised +</a>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <a class="up-btn btn btn-primary" data-id="itenerary-finalised">Itenerary Finalised +</a>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="input-group">
                            <a class="up-btn btn btn-primary" data-id="travel-document-prepared">Travel Document Prepared +</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 up-box" id="flight-booked" style="margin-bottom:10px;margin-top:30px" hidden>
                    <h2 class="col-sm-offset-1">Flight Booked</h2>
                    <div class="row box-cus">
                        <div class="col-sm-4">
                            <label for="inputEmail3" class="">Flight Booked</label><br>
                            {!! Form::radio('flight_booked', 'yes', null, ['id' => 'fb_yes']) !!}&nbsp<label for="fb_yes">Yes</label>
                            {!! Form::radio('flight_booked', 'no', true, ['id' => 'fb_no']) !!}&nbsp<label for="fb_no">No</label>
                            {!! Form::radio('flight_booked', 'NA', null, ['id' => 'fb_NA']) !!}&nbsp<label for="fb_NA">NA</label>
                            <div class="alert-danger" style="text-align:center">{{ $errors->first('flight_booked') }}</div>
                        </div>
                        <div class="col-sm-4 fb_depend">
                            <input class="responsible_person_counter" type="hidden" name="responsible_person_counter" value="0">
                            <label class="">Responsible Person</label>
                            <select class="form-control responsible_person" name="fb_person">
                                <option value="">Select Person</option>
                                @foreach ($persons as $person)
                                    @if (Auth::user()->id != $person->id)
                                        @if ($person->id != 1)
                                        <option value="{{ $person->id }}">{{ $person->name }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                            <div class="alert-danger" style="text-align:center">
                                {{ $errors->first('fb_person') }}
                            </div>
                        </div>

                        <div class="col-sm-4 fb_depend">
                            <label for="inputEmail3" class="">Last Date Of Flight Booking</label>
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('fb_last_date', null, ['autocomplete' => 'off', 'class' => 'form-control','id' => 'datepicker3', 'placeholder' => 'Last Date Of Flight Booking']) !!}
                            </div>
                            <div class="alert-danger" style="text-align:center">{{ $errors->first('fb_last_date') }}</div>
                        </div> 

                        <div class="col-sm-4" style="margin-bottom:15px">
                            <div class="fb_airline_name_id" style="display: none;">
                                <label class="">Select Airline </label><span style="color:red"> *</span>
                                <select class="form-control select2" name="fb_airline_name_id">
                                    <option value="">Select Airline</option>
                                    @foreach ($airline as $user)
                                    <option value="{{ $user->id }}"> {{ $user->name }} </option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('fb_airline_name_id') }}</div>
                            </div>
                        </div>

                        <div class="col-sm-4" style="margin-bottom:15px">
                            <div class="fb_payment_method_id" style="display: none;">
                                <label class="">Select Payment</label><span style="color:red"> * </span>
                                <select class="form-control select2" name="fb_payment_method_id">
                                    <option value="">Select Payment</option>
                                    @foreach ($payment as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('fb_payment_method_id') }}</div>
                            </div>
                        </div>

                        <div class="col-sm-4" style="margin-bottom:15px">
                            <div class="fb_booking_date" style="display: none;">
                                <label class="">Booking Date</label><span style="color:red"> * </span>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('fb_booking_date', null, ['autocomplete' => 'off', 'class' => 'form-control', 'id' => 'datepicker14', 'placeholder' => 'Booking Date']) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('fb_booking_date') }}</div>
                            </div>
                        </div>

                        <div class="col-sm-3" style="margin-bottom: 15px">
                            <div class="fb_airline_ref_no" style="display: none;">
                                <label class="">Airline Ref
                                    No</label><span style="color:red"> * </span>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('fb_airline_ref_no', null, ['autocomplete' => 'off', 'class' =>
                                    'form-control', 'id' => '', 'placeholder' => 'Airline Ref No']) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('fb_airline_ref_no') }}
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-sm-3">
                                         <label class="">Email finance to enter flight purchase details 
                                           <input type='checkbox' name="email_finance" value="1" style="margin:5px"/>
                                         </label>
                                         <div class="alert-danger" style="text-align:center">{{ $errors->first('email_finance') }}</div>
                                      </div> -->

                        <div class="col-sm-10 col-sm-offset-1">
                            <div class="flight_booking_details" style="display: none;">
                                <label for="inputEmail3" class="">Flight Booking Details</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::textarea('flight_booking_details', null, ['class' => 'form-control','placeholder' => 'Flight Booking Detail','style' => 'height: 60px;width: 450px',]) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('flight_booking_details') }}</div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-sm-12 up-box" id="transfer-info" hidden>
                    <h2 class="col-sm-offset-1">Transfer Info</h2>
                    <div class="row box-cus">
                        <!--  <div class="col-sm-5 col-sm-offset-1" >
                                         <label for="inputEmail3" class="">Transfer Info Received</label><br>
                                         {!! Form::radio('transfer_info_received', 'yes', null, ['id' => 'tir_yes']) !!}&nbsp<label for="tir_yes">Yes</label>
                                         {!! Form::radio('transfer_info_received', 'no', true, ['id' => 'tir_no']) !!}&nbsp<label for="tir_no">No</label>
                                         <div class="alert-danger" style="text-align:center">{{ $errors->first('transfer_info_received') }}</div>
                                     
                                         <div class="transfer_info_details" style="display: none;">
                                           <label for="inputEmail3" class="">Transfer Info Details</label>
                                           <div class="input-group">
                                              <span class="input-group-addon"></span>
                                              {!! Form::textarea('transfer_info_details', null, ['class' => 'form-control', 'placeholder' => 'Transfer Info Details', 'style' => 'height: 60px;']) !!}
                                           </div>
                                           <div class="alert-danger" style="text-align:center">{{ $errors->first('transfer_info_details') }}</div>
                                         </div>
                                     </div> -->
                        <div class="col-sm-7">

                            <div class="row">

                                <div class="col-sm-3">
                                    <label for="inputEmail3" class="">Asked For
                                        Transfer</label><br>
                                    {!! Form::radio('asked_for_transfer_details', 'yes', null, ['id' => 'td_yes'])
                                    !!}&nbsp<label for="td_yes">Yes</label>
                                    {!! Form::radio('asked_for_transfer_details', 'no', true, ['id' => 'td_no'])
                                    !!}&nbsp<label for="td_no">No</label>
                                    {!! Form::radio('asked_for_transfer_details', 'NA', null, ['id' => 'td_NA'])
                                    !!}&nbsp<label for="td_NA">NA</label>
                                    <div class="alert-danger" style="text-align:center">
                                        {{ $errors->first('asked_for_transfer_details') }}
                                    </div>
                                    {{-- new fields add here --}}
                                </div>

                                {{-- <div class="col-sm-4 aft_depend">
                                    <label class="">Responsible Person</label>
                                    <select class="form-control responsible_person_depend" name="aft_person">
                                        <option value="">Select Person</option>
                                        @foreach ($persons as $person)
                                            @if (Auth::user()->id != $person->id)
                                                @if ($person->id != 1)
                                                    <option value="{{ $person->id }}">{{ $person->name }}</option>
                                                @endif
                                            @endif
                                        @endfor
                                    </select>
                                    <div class="alert-danger" style="text-align:center">
                                        {{ $errors->first('aft_person') }}
                                    </div>
                                </div> --}}


                                <div class="col-sm-5 aft_depend">
                                    <label for="inputEmail3" class="">Last
                                        Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        {!! Form::text('aft_last_date', null, ['autocomplete' => 'off', 'class' => 'form-control','id' => 'datepicker10', 'placeholder' => 'Last Date','required' => 'true',]) !!}
                                    </div>
                                    <div class="alert-danger" style="text-align:center">
                                        {{ $errors->first('aft_last_date') }}
                                    </div>
                                </div>
                            </div>
                            {{-- end new fields add here --}}
                            <div class="transfer_details" style="margin-bottom:25px;display: none;">
                                <label for="inputEmail3" class="">Asked For Transfer Details</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::textarea('transfer_details', null, [ 'class' => 'form-control','placeholder' => 'Asked For Transfer Details','style' => 'height:60px',]) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('transfer_details') }}
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-5 col-sm-offset-1">
                            <label for="inputEmail3" class="">Form Sent On</label>
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('form_sent_on', null, ['autocomplete' => 'off', 'class' => 'form-control','id' => 'datepicker2', 'placeholder' => 'Form Sent On', 'required' => 'true']) !!}
                            </div>
                            <div class="alert-danger" style="text-align:center">
                                {{ $errors->first('form_sent_on') }}
                            </div><br>
                            <span id="form_received" style="color: #3c8dbc"></span>
                            <input id="form_received_on" type="hidden" name="form_received_on" value="">
                        </div>

                        <div class="row set_reminder" style="display: none;">
                            <div class="col-sm-12">
                                <label for="inputEmail3" class="">Set
                                    Reminder</label>
                            </div>
                            <div class="col-sm-4 fso_depend">
                                <label class="">Responsible
                                    Person</label>
                                <select class="form-control responsible_person_depend" name="fso_person">
                                    <option value="">Select Person
                                    </option>
                                    @foreach ($persons as $person)
                                    @if (Auth::user()->id != $person->id)
                                    @if ($person->id != 1)
                                    <option value="{{ $person->id }}">
                                        {{ $person->name }}
                                    </option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('fso_person') }}
                                </div>
                            </div>

                            <div class="col-sm-5 fso_depend">
                                <label for="inputEmail3" class="">Date</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('fso_last_date', null, ['autocomplete' => 'off', 'class' =>'form-control', 'id' => 'datepicker15', 'placeholder' => 'Date']) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('fso_last_date') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Transfer Organised --}}
                <div class="col-sm-12 up-box" id="transfers-organised" style="margin-bottom:10px" hidden>
                    <h2 class="col-sm-offset-1">Transfers Organised</h2>
                    <div class="row box-cus">
                        <div class="row">
                            <div class="col-sm-3">
                                <label for="inputEmail3" class="">Transfers
                                    Organised</label><br>
                                {!! Form::radio('transfer_organised', 'yes', null, ['id' => 'tro_yes']) !!}&nbsp<label
                                    for="tro_yes">Yes</label>
                                {!! Form::radio('transfer_organised', 'no', true, ['id' => 'tro_no']) !!}&nbsp<label
                                    for="tro_no">No</label>
                                {!! Form::radio('transfer_organised', 'NA', null, ['id' => 'tro_NA']) !!}&nbsp<label
                                    for="tro_NA">NA</label>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('transfer_organised') }}
                                </div>
                            </div>
                            <div class="col-sm-4 to_depend">
                                <label class="">Responsible
                                    Person</label>
                                <select class="form-control responsible_person_depend" name="to_person">
                                    <option value="">Select Person
                                    </option>
                                    @foreach ($persons as $person)
                                    @if (Auth::user()->id != $person->id)
                                    @if ($person->id != 1)
                                    <option value="{{ $person->id }}">
                                        {{ $person->name }}
                                    </option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('to_person') }}
                                </div>
                            </div>

                            <div class="col-sm-5 to_depend">
                                <label for="inputEmail3" class="">Last
                                    Date Of Transfer
                                    Organised</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('to_last_date', null, [
                                    'autocomplete' => 'off',
                                    'class' => 'form-control',
                                    'id' => 'datepicker6',
                                    'placeholder' => 'Last Date Of Transfer Organised',
                                    'required' => 'true',
                                    ]) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('to_last_date') }}
                                </div>
                            </div>
                            <div class="transfer_organised_details col-sm-12" style="display: none;">
                                <label for="inputEmail3" class="">Transfer Organised
                                    Details</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::textarea('transfer_organised_details', null, ['class' => 'form-control',
                                    'placeholder' => 'Transfer Organised Details', 'style' => 'height:60px']) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('transfer_organised_details') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Itinerary finalised --}}
                <div class="col-sm-12 up-box" id="itenerary-finalised" style="margin-bottom:10px;" hidden>
                    <h2 class="col-sm-offset-1">Itinerary Finalised</h2>
                    <div class="row box-cus">

                        <div class="row">
                            <div class="col-sm-3">
                                <label for="inputEmail3" class="">Itinerary
                                    Finalised</label><br>
                                {!! Form::radio('itinerary_finalised', 'yes', null, ['id' => 'itf_yes']) !!}&nbsp<label
                                    for="itf_yes">Yes</label>
                                {!! Form::radio('itinerary_finalised', 'no', true, ['id' => 'itf_no']) !!}&nbsp<label
                                    for="itf_no">No</label>
                                {!! Form::radio('itinerary_finalised', 'NA', null, ['id' => 'itf_NA']) !!}&nbsp<label
                                    for="itf_NA">NA</label>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('itinerary_finalised') }}
                                </div>

                            </div>
                            <div class="col-sm-4 itf_depend">
                                <label class="">Responsible
                                    Person</label>
                                <select class="form-control responsible_person_depend" name="itf_person">
                                    <option value="">Select Person
                                    </option>
                                    @foreach ($persons as $person)
                                    @if (Auth::user()->id != $person->id)
                                    @if ($person->id != 1)
                                    <option value="{{ $person->id }}">
                                        {{ $person->name }}
                                    </option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('itf_person') }}
                                </div>
                            </div>
                            <div class="col-sm-5 itf_depend">
                                <label for="inputEmail3" class="">Last
                                    Date Of Itinerary
                                    Finalised</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('itf_last_date', null, [
                                    'autocomplete' => 'off',
                                    'class' => 'form-control',
                                    'id' => 'datepicker9',
                                    'placeholder' => 'Last Date Of Itinerary Finalised',
                                    'required' => 'true',
                                    ]) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('itf_last_date') }}
                                </div>
                            </div>

                            <div class="itinerary_finalised_details col-sm-9" style="display: none;">
                                <label for="inputEmail3" class="">Itinerary Finalised
                                    Details</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::textarea('itinerary_finalised_details', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Itinerary Finalised Details',
                                    'style' => 'height:60px',
                                    ]) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('itinerary_finalised_details') }}
                                </div>
                            </div>

                            <div class="col-sm-5 itf_current_date" style="display: none;">
                                <label for="inputEmail3" class="">Itinerary Finalised
                                    Date</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('itf_current_date', null, [
                                    'autocomplete' => 'off',
                                    'class' => 'form-control',
                                    'id' => 'datepicker17',
                                    'placeholder' => 'Itinerary Finalised Date',
                                    'required' => 'true',
                                    ]) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('itf_current_date') }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Documents Prepare --}}
                <div class="col-sm-12 up-box" id="travel-document-prepared" style="margin-bottom:10px" hidden>
                    <h2 class="col-sm-offset-1">Travel Document Prepared</h2>
                    <div class="row box-cus">
                        <div class="row">
                            <div class="col-sm-3">
                                <label for="inputEmail3" class="">Travel
                                    Document Prepared</label><br>
                                {!! Form::radio('document_prepare', 'yes', null, ['id' => 'dp_yes']) !!}&nbsp<label
                                    for="dp_yes">Yes</label>
                                {!! Form::radio('document_prepare', 'no', true, ['id' => 'dp_no']) !!}&nbsp<label
                                    for="dp_no">No</label>
                                {!! Form::radio('document_prepare', 'NA', null, ['id' => 'dp_NA']) !!}&nbsp<label
                                    for="dp_NA">NA</label>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('document_prepare') }}
                                </div>
                            </div>
                            <div class="col-sm-4 dp_depend">
                                <label class="">Responsible
                                    Person</label>
                                <select class="form-control responsible_person_depend" name="dp_person">
                                    <option value="">Select Person
                                    </option>
                                    @foreach ($persons as $person)
                                    @if (Auth::user()->id != $person->id)
                                    @if ($person->id != 1)
                                    <option value="{{ $person->id }}">
                                        {{ $person->name }}
                                    </option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('dp_person') }}
                                </div>
                            </div>

                            <div class="col-sm-5 dp_depend">
                                <label for="inputEmail3" class="">Last
                                    Date Of Document
                                    Prepared</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('dp_last_date', null, [
                                    'autocomplete' => 'off',
                                    'class' => 'form-control',
                                    'id' => 'datepicker7',
                                    'placeholder' => 'Last Date Of Document Prepared',
                                    ]) !!}

                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('dp_last_date') }}
                                </div>
                            </div>

                            <div class="col-sm-5 tdp_current_date">
                                <div class="tdp_current_date" style="display: none;">
                                    <label for="inputEmail3" class="">Travel Document
                                        Prepared Date</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"></span>
                                        {!! Form::text('tdp_current_date', null, [
                                        'autocomplete' => 'off',
                                        'class' => 'form-control',
                                        'id' => 'datepicker18',
                                        'placeholder' => 'Travel Document Prepared Date',
                                        ]) !!}
                                    </div>
                                    <div class="alert-danger" style="text-align:center">
                                        {{ $errors->first('tdp_current_date') }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- end Documents Prepare --}}
                <div class="col-sm-10 col-sm-offset-1">
                    <h1 style="text-align: center;">Finance Detail</h1>

                    <div class="col-md-12 finance-holiday-amount text-center">
                        <label>Holiday Amount:</label>
                        <input id="holiday" type="text" name="holiday">
                    </div>

                    <div class="deposit-remaining">
                        <div class="col-sm-12">
                            <label>Deposit Received
                                <input type='checkbox' id="deposit" name="deposit_received" value="1"
                                    style="margin:5px" />
                            </label>
                            <div class="alert-danger" style="text-align:center">
                                {{ $errors->first('deposit_received') }}
                            </div>

                            <label>Remaining Amount Received
                                <input type='checkbox' id="remain" name="remaining_amount_received" value="1"
                                    style="margin:5px" />
                            </label>
                            <div class="alert-danger" style="text-align:center">
                                {{ $errors->first('remaining_amount_received') }}
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="finance_detail" value="" id="finance_detail">
                    <table id="example2" class="table table-bordered table-striped dataTable no-footer" role="grid">
                        <thead>
                            <tr>
                                <th>Agency Name</th>
                                <th>Agency Contact Name</th>
                                <th>Passenger Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Currency</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody style="text-transform: capitalize;" id="app_tab">
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-10 col-sm-offset-3" style="margin-top: 15px;">
                    <div class="col-sm-4">
                        <a data-id="travel-document-sent" class="btn btn-primary down-btn">Travel Document
                            +</a>
                    </div>
                    <div class="col-sm-4">
                        <a class="btn btn-primary down-btn" data-id="app-login-sent">App Login Sent +</a>
                    </div>
                </div>
                {{-- Document Sent --}}
                <div class="col-sm-12 down-box" id="travel-document-sent" style="margin-bottom:10px" hidden>
                    <h2 class="col-sm-offset-1">Travel Document Sent</h2>
                    <div class="row box-cus">

                        <div class="row">

                            <div class="col-sm-3">

                                <label for="inputEmail3" class="">Travel
                                    Document Sent</label><br>
                                {!! Form::radio('documents_sent', 'yes', null, ['id' => 'ds_yes']) !!}&nbsp<label
                                    for="ds_yes">Yes</label>
                                {!! Form::radio('documents_sent', 'no', true, ['id' => 'ds_no']) !!}&nbsp<label
                                    for="ds_no">No</label>
                                {!! Form::radio('documents_sent', 'NA', null, ['id' => 'ds_NA']) !!}&nbsp<label
                                    for="ds_NA">NA</label>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('documents_sent') }}
                                </div>
                            </div>
                            {{-- add field here --}}
                            <div class="col-sm-4 ds_depend">
                                <label class="">Responsible
                                    Person</label>
                                <select class="form-control responsible_person_depend" name="ds_person">
                                    <option value="">Select Person
                                    </option>
                                    @foreach ($persons as $person)
                                    @if (Auth::user()->id != $person->id)
                                    @if ($person->id != 1)
                                    <option value="{{ $person->id }}">
                                        {{ $person->name }}
                                    </option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('ds_person') }}
                                </div>
                            </div>

                            <div class="col-sm-5 ds_depend">
                                <label for="inputEmail3" class="">Last
                                    Date Of Document Sent</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('ds_last_date', null, [
                                    'autocomplete' => 'off',
                                    'class' => 'form-control',
                                    'id' => 'datepicker5',
                                    'placeholder' => 'Last Date Of Document Sent',
                                    'required' => 'true',
                                    ]) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('ds_last_date') }}
                                </div>
                            </div>
                        </div>

                        {{-- end field here --}}
                        <div class="documents_sent_details" style="display: none;">
                            <label for="inputEmail3" class="">Document
                                Details</label>
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::textarea('documents_sent_details', null, [
                                'class' => 'form-control',
                                'placeholder' => 'Document Details',
                                'style' => 'height:60px',
                                ]) !!}
                            </div>
                            <div class="alert-danger" style="text-align:center">
                                {{ $errors->first('documents_sent_details') }}
                            </div>
                        </div>

                        <div class="col-sm-5 tds_current_date">
                            <label for="inputEmail3" class="">Travel
                                Document Sent Date</label>
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('tds_current_date', null, [
                                'autocomplete' => 'off',
                                'class' => 'form-control',
                                'id' => 'datepicker19',
                                'placeholder' => 'Travel Document Sent Date',
                                'required' => 'true',
                                ]) !!}
                            </div>
                            <div class="alert-danger" style="text-align:center">
                                {{ $errors->first('tds_current_date') }}
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Electronic Copy Sent --}}
                <div class="col-sm-12 down-box" id="app-login-sent" style="margin-bottom: 10px" hidden>
                    <h2 class="col-sm-offset-1">App login Sent</h2>
                    <div class="row box-cus">
                        <label for="inputEmail3" class="">App login
                            Sent</label><br>
                        {!! Form::radio('electronic_copy_sent', 'yes', null, ['id' => 'ecs_yes']) !!}&nbsp<label
                            for="ecs_yes">Yes</label>
                        {!! Form::radio('electronic_copy_sent', 'no', true, ['id' => 'ecs_no']) !!}&nbsp<label
                            for="ecs_no">No</label>
                        <div class="alert-danger" style="text-align:center">
                            {{ $errors->first('electronic_copy_sent') }}
                        </div>
                        <span id="app_login_detail" style="color: #3c8dbc"></span>
                        <input id="app_login_date" type="hidden" name="app_login_date" value="">

                        {{--  --}}
                        <div class="row set_reminder_app">
                            <div class="col-sm-12">
                                <label for="inputEmail3" class="">Set
                                    Reminder</label>
                            </div>
                            <div class="col-sm-4 aps_depend">
                                <label class="">Responsible
                                    Person</label>
                                <select class="form-control responsible_person_depend" name="aps_person">
                                    <option value="">Select Person
                                    </option>
                                    @foreach ($persons as $person)
                                    @if (Auth::user()->id != $person->id)
                                    @if ($person->id != 1)
                                    <option value="{{ $person->id }}">
                                        {{ $person->name }}
                                    </option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('aps_person') }}
                                </div>
                            </div>

                            <div class="col-sm-5 aps_depend">
                                <label for="inputEmail3" class="">Date</label>
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('aps_last_date', null, ['autocomplete' => 'off', 'class' =>
                                    'form-control', 'id' => 'datepicker16', 'placeholder' => 'Date']) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">
                                    {{ $errors->first('aps_last_date') }}
                                </div>
                            </div>
                        </div>
                        {{--  --}}
                        <div class="electronic_copy_details" style="display: none;">
                            <label for="inputEmail3" class="">App Login Sent
                                Details</label>
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::textarea('electronic_copy_details', null, [
                                'class' => 'form-control',
                                'placeholder' => 'App Login Sent Details',
                                'style' => 'height:60px',
                                ]) !!}
                            </div>
                            <div class="alert-danger" style="text-align:center">
                                {{ $errors->first('electronic_copy_details') }}
                            </div>
                        </div>
                    </div>
                </div>


                {{-- end Electronic Copy Sent --}}

                {{-- end Transfer Organised --}}
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <!-- <button type="submit" class="btn btn-info pull-right">Sign in</button> -->
                {!! Form::submit('Submit', ['class' => 'btn btn-info pull-right']) !!}
            </div>

        </div>
            <!-- /.box-footer -->
            </form>


            <!-- <div class="col-sm-10 col-sm-offset-1" style="margin-top: 100px">
                              <h1 style="text-align: center;">Email Details</h1>
                              <table id="example3" class="table table-bordered table-striped dataTable no-footer" role="grid">
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
                                @foreach ($booking_email as $value)
                                <tr>
                                  <td>{{ $value->username }}</td>
                                  <td>{{ $value->hour }}</td>
                                  <td>{{ $value->is_read == '' ? '-' : $value->is_read }}</td>
                                  <td>{{ $value->is_read_date == '' ? '-' : $value->is_read_date }}</td>
                                  <td>{{ ucfirst(str_replace('_', ' ', $value->action)) }}</td>
                                  <td>{{ \Carbon\Carbon::parse(substr($value->created_at, 0, 10))->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                                </tbody>
                              </table>
                            </div> -->
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
    {{-- Copyright © 2017-2018 Almuftionline .Design & Developed by <a
    href="http://www.webfluorescent.com//">WebFluorescent </a> --}}
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
<script>
	var count = 0;
$('.responsible_person').change(function(event) {
	if($('.responsible_person_counter').val() == count) {
		$('.responsible_person_depend').val($(this).val());
	}
	$('.responsible_person_counter').val(2);
});
var date2 = new Date();
var date = new Date();
date.setDate(date.getDate() + 1);
$(function() {
	//Initialize Select2 Elements
	$('.select2').select2();
	//Date picker
	$('#datepicker').datepicker({
		autoclose: true,
		// startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker2').datepicker({
		autoclose: true,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker3').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker4').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker5').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker6').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker7').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker8').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker9').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker10').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker11').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker12').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker13').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker14').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker15').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	$('#datepicker16').datepicker({
		autoclose: true,
		startDate: date,
		format: 'dd/mm/yyyy'
	});
	/*$('#datepicker17').datepicker({
	  autoclose: true,
	  startDate: date2,
	  format: 'dd/mm/yyyy',
	  setDate:new Date()
	});*/
	$("#datepicker17").datepicker({
		autoclose: true,
		format: 'dd/mm/yyyy'
	}).datepicker("setDate", new Date());
	$("#datepicker18").datepicker({
		autoclose: true,
		format: 'dd/mm/yyyy'
	}).datepicker("setDate", new Date());
	$("#datepicker19").datepicker({
		autoclose: true,
		format: 'dd/mm/yyyy'
	}).datepicker("setDate", new Date());
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

        // load data table on load
        book_id = $('input[name="ref_no"]').val();
        if(book_id) {
            token = $('input[name=_token]').val();
            data  = {id: book_id};
            url   = '{{route('get-ref-detail')}}';
            console.log('{{route('get-ref-detail')}}');
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
                    if(data.item_rec4 != null){
                        $tab = '';
                        var i;
                        for (i = 0; i < data.item_rec4.record.length; ++i) {
                            $new = "<tr><td>"+data.item_rec4.record[i].agency_name+"</td><td>"+data.item_rec4.record[i].contact_first_name+" "+data.item_rec4.record[i].contact_last_name+"</td><td>"+data.item_rec4.record[i].passenger_first_name+" "+data.item_rec4.record[i].passenger_middle_name+" "+data.item_rec4.record[i].passenger_last_name+"</td><td>"+data.item_rec4.record[i].type+"</td><td>"+data.item_rec4.record[i].status+"</td><td>"+data.item_rec4.record[i].curruncy+"</td><td>"+data.item_rec4.record[i].total_amount * data.item_rec4.record[i].conversion+"</td><td>"+data.item_rec4.record[i].created_at+"</td></tr>";
                            $conversion = data.item_rec4.record[i].conversion;
                            $tab = $tab + $new;
                        }
                        if($tab != ''){
                        $('#app_tab').html('');
                        $('#app_tab').append($tab);
                        }
                    }
            
                    $("#divLoading").removeClass('show');
                }
            });
        }else{
            // $('select[name="brand_name"]').empty();
        }

    document.getElementById('ds_yes').addEventListener('click', function() {
        if ((document.getElementById('deposit').checked == false) || (
                document.getElementById(
                    'remain').checked == false)) {
            confirm(
                'Full Payment may not have been received--Please confirm you like to Send Documnet to client'
            );
        }
    }, false);
    var typingTimer; //timer identifier
    var doneTypingInterval = 2000; //time in ms, 5 second for example
    var $input = $('input[name="ref_no"]');

    //on keyup, start the countdown
    $input.on('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //on keydown, clear the countdown 
    $input.on('keydown', function() {
        clearTimeout(typingTimer);
    });

    //user is "finished typing," do something
    function doneTyping() {
        console.log('done-typing');
        //
        $('#link').html('<i class="fa fa-plane"></i>');
        $('#form_received').html('');
        $('#app_tab').html(
            '<tr class="odd"><td valign="top" colspan="8" class="dataTables_empty">No data available in table</td></tr>'
        );
        $('#app_login_date').val('0000-00-00');
        $('#form_received_on').val('0000-00-00');
        $('select[name="sale_person"]').val('').trigger('change');
        $('select[name="brand_name"]').val('');
        $('input:radio[name=agency_booking]').prop('checked', false);
        $('input[name="date_of_travel"]').datepicker("setDate", '');
        $('input[name="form_sent_on"]').datepicker("setDate", '');
        $('input:radio[name=electronic_copy_sent]').filter('[value=no]').prop('checked',
            true);
        $('#app_login_detail').html('');
        //
        $('input:radio[name=asked_for_transfer_details]').filter('[value=no]').prop(
            'checked', true);
        $('.transfer_details').hide(200);
        $('textarea[name=transfer_details]').prop('required', false);
        //
        book_id = $('input[name="ref_no"]').val();
        if (book_id) {
            token = $('input[name=_token]').val();
            data = {
                id: book_id
            };
            url = '{{ route('get-ref-detail') }}';
            console.log('{{ route('get-ref-detail') }}');
            $.ajax({
                url: url,
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
                    if (data.item_rec4 != null) {
                        $tab = '';
                        var i;
                        for (i = 0; i < data.item_rec4.record.length; ++i) {
                            $new = "<tr><td>" + data.item_rec4.record[i].agency_name + "</td><td>" + data
                                .item_rec4
                                .record[
                                    i]
                                .contact_first_name +
                                " " +
                                data
                                .item_rec4
                                .record[
                                    i]
                                .contact_last_name +
                                "</td><td>" +
                                data
                                .item_rec4
                                .record[
                                    i]
                                .passenger_first_name +
                                " " +
                                data
                                .item_rec4
                                .record[
                                    i]
                                .passenger_middle_name +
                                " " +
                                data
                                .item_rec4
                                .record[
                                    i]
                                .passenger_last_name +
                                "</td><td>" +
                                data
                                .item_rec4
                                .record[
                                    i]
                                .type +
                                "</td><td>" +
                                data
                                .item_rec4
                                .record[
                                    i
                                ]
                                .status +
                                "</td><td>" +
                                data
                                .item_rec4
                                .record[
                                    i]
                                .curruncy +
                                "</td><td>" +
                                data
                                .item_rec4
                                .record[
                                    i]
                                .total_amount *
                                data
                                .item_rec4
                                .record[
                                    i]
                                .conversion +
                                "</td><td>" +
                                data
                                .item_rec4
                                .record[
                                    i]
                                .created_at +
                                "</td></tr>";
                            $conversion =
                                data
                                .item_rec4
                                .record[
                                    i]
                                .conversion;
                            $tab = $tab +
                                $new;
                            $('#datepicker')
                                .val(data
                                    .item_rec4
                                    .record[
                                        i]
                                    .depart
                                );
                            $('#holiday')
                                .val(data
                                    .item_rec4
                                    .holiday_amount *
                                    $conversion
                                );
                            $('#type_of_holidays')
                                .val(data
                                    .item_rec4
                                    .record[
                                        i]
                                    .holiday_type
                                );
                            $('#sales_person')
                                .val(data
                                    .item_rec4
                                    .record[
                                        i]
                                    .sales_person
                                );
                            $('#select2-sales_person-container')
                                .attr('title',
                                    data
                                    .item_rec4
                                    .record[
                                        i]
                                    .sales_person
                                );
                            $('#select2-sales_person-container')
                                .html(data
                                    .item_rec4
                                    .record[
                                        i]
                                    .sales_person
                                );
                            $('#pax_no')
                                .val(data
                                    .item_rec4
                                    .record[
                                        i]
                                    .pax
                                );
                            $('#select2-pax_no-container')
                                .attr('title',
                                    data
                                    .item_rec4
                                    .record[
                                        i
                                    ]
                                    .pax
                                );
                            $('#select2-pax_no-container')
                                .html(data
                                    .item_rec4
                                    .record[
                                        i]
                                    .pax
                                );
                            data.item_rec4
                                .record[
                                    i]
                                .client_type ==
                                2 ? $(
                                    '#ab_yes')
                                .prop(
                                    'checked',
                                    true
                                ) :
                                $(
                                    '#ab_no')
                                .prop('checked',
                                    true
                                );
                            data.item_rec4
                                .record[
                                    i]
                                .paid ==
                                1 ? $(
                                    '#remain')
                                .prop('checked',
                                    true
                                ) :
                                '';
                            data.item_rec4
                                .record[
                                    i]
                                .payment_method ==
                                3 ? $(
                                    '#deposit')
                                .prop(
                                    'checked',
                                    true
                                ) :
                                '';

                        }
                        if ($tab != '') {
                            $('#app_tab')
                                .html(
                                    '');
                            $('#app_tab')
                                .append(
                                    $tab);
                        }
                    }
                    if (data.item_rec != null) {
                        $('select[name="brand_name"]')
                            .val(data
                                .item_rec
                                .branch_name
                            );
                        $('select[name="sale_person"]')
                            .val(data
                                .item_rec
                                .created_by
                            )
                            .trigger(
                                'change'
                            );
                        // $('select[name="brand_name"]').empty();
                        // $('select[name="brand_name"]').append('<option value="'+ data.item_rec.branch_name +'">'+ data.item_rec.branch_name +'</option>');
                        $('input:radio[name=agency_booking]')
                            .filter('[value=' +
                                data
                                .item_rec
                                .client_type +
                                ']')
                            .prop('checked',
                                true);
                        //
                        if (data.item_rec
                            .payment_for ==
                            3) {
                            if ("departure_date" in
                                data
                                .item_rec
                            ) {
                                var year =
                                    data
                                    .item_rec
                                    .departure_date
                                    .substr(0,
                                        4
                                    );
                                var month =
                                    data
                                    .item_rec
                                    .departure_date
                                    .substr(5,
                                        2
                                    );
                                var day =
                                    data
                                    .item_rec
                                    .departure_date
                                    .substr(8,
                                        2
                                    );
                                var convert_date =
                                    month +
                                    '/' +
                                    day +
                                    '/' +
                                    year;
                                $('input[name="date_of_travel"]')
                                    .datepicker(
                                        "setDate",
                                        convert_date
                                    );
                            }
                        }
                        //
                        //
                        $("#example2").on(
                                "draw.dt",
                                function() {
                                    $(this).find(
                                            ".dataTables_empty")
                                        .parents(
                                            'tbody'
                                        )
                                        .empty();
                                })
                            .DataTable();
                        var table = $(
                                '#example2')
                            .DataTable();
                        table.clear().draw();
                        var $sum = 0;
                        $.each(data.item_rec4
                            .record,
                            function(
                                key,
                                value
                            ) {
                                if (value
                                    .status ==
                                    "COMPLETED"
                                ) {
                                    $sum += parseFloat(
                                        value
                                        .amount_payable
                                    );
                                }
                                //
                                if (value
                                    .agency_name ==
                                    ''
                                ) {
                                    var agency_name =
                                        value
                                        .agency_name;
                                } else {
                                    var agency_name =
                                        '-';
                                }
                                var employee_data =
                                    '';
                                employee_data
                                    +=
                                    '<tr>';
                                employee_data
                                    +=
                                    '<td>' +
                                    agency_name +
                                    '</td>';
                                employee_data
                                    +=
                                    '<td>' +
                                    value
                                    .agency_contact_num +
                                    '</td>';
                                employee_data
                                    +=
                                    '<td>' +
                                    value
                                    .passenger_first_name +
                                    " " +
                                    value
                                    .passenger_last_name +
                                    '</td>';
                                employee_data
                                    +=
                                    '<td>' +
                                    value
                                    .type +
                                    '</td>';
                                employee_data
                                    +=
                                    '<td>' +
                                    value
                                    .status +
                                    '</td>';
                                employee_data
                                    +=
                                    '<td>' +
                                    value
                                    .curruncy +
                                    '</td>';
                                employee_data
                                    +=
                                    '<td>' +
                                    value
                                    .amount_payable +
                                    '</td>';
                                employee_data
                                    +=
                                    '<td>' +
                                    value
                                    .created_at +
                                    '</td>';
                                employee_data
                                    +=
                                    '<tr>';
                                $('#example2 tbody')
                                    .append(
                                        employee_data);
                                $('#finance_detail')
                                    .val(
                                        employee_data);
                            });
                        $("#holiday").val(data
                                .item_rec4
                                .holiday_amount
                            )
                            .trigger(
                                'change'
                            );
                        // $("#total").val($sum).trigger('change');
                        if ($sum == data
                            .item_rec4
                            .holiday_amount
                        ) {
                            // $('select[name="sale_person"]').val(data.item_rec.created_by).trigger('change');
                            $('#deposit')
                                .attr('checked',
                                    'checked'
                                )
                                .trigger(
                                    'change'
                                );
                            $('#remain')
                                .attr('checked',
                                    'checked'
                                )
                                .trigger(
                                    'change'
                                );
                        }
                    }


                    if (data.item_rec2 != null) {
                        if ("id" in data
                            .item_rec2) {
                            var id = data
                                .item_rec2
                                .id;
                            $('#link').html(
                                '<small><a href="https://unforgettabletravelcompany.com/ufg-form/user/' +
                                id +
                                '" target="_blank">View Reference Detail</a></small>'
                            );
                            $('input:radio[name=asked_for_transfer_details]')
                                .filter(
                                    '[value=yes]'
                                )
                                .prop('checked',
                                    true
                                );
                            $('.transfer_details')
                                .show(
                                    200);
                            $('textarea[name=transfer_details]')
                                .prop('required',
                                    true
                                );
                            //
                            var detail_rec_date =
                                data
                                .item_rec2
                                .detail_rec_date;
                            if (detail_rec_date ==
                                '0000-00-00'
                            ) {
                                $('#form_received')
                                    .html(
                                        '<strong><a href="https://unforgettabletravelcompany.com/ufg-form/user/' +
                                        id +
                                        '" target="_blank">Form Status (Pending)</a> </strong>'
                                    );
                                $('#form_received_on')
                                    .val(
                                        '0000-00-00');
                                $('.set_reminder')
                                    .show(
                                        200);
                                // $('select[name="fso_person"]').prop('required',true);
                                $('input[name="fso_last_date"]')
                                    .prop('required',
                                        true
                                    );
                            } else {
                                var a =
                                    detail_rec_date;
                                a = a.split(
                                    '-');
                                a = a[2] +
                                    '/' +
                                    a[
                                        1] +
                                    '/' +
                                    a[
                                        0];
                                $('#form_received')
                                    .html(
                                        '<strong><a href="https://unforgettabletravelcompany.com/ufg-form/user/' +
                                        id +
                                        '" target="_blank">Received On-' +
                                        a +
                                        '</a></strong>'
                                    );
                                $('#form_received_on')
                                    .val(
                                        detail_rec_date);
                                $('.set_reminder')
                                    .hide(
                                        200);
                                // $('select[name="fso_person"]').prop('required',false);
                                $('input[name="fso_last_date"]')
                                    .prop('required',
                                        false
                                    );
                                //
                                // $('input:radio[name=transfer_info_received]').filter('[value=yes]').prop('checked', true);
                                // $('.transfer_info_details').show(200);
                                // $('textarea[name=transfer_info_details]').prop('required',true);
                                //
                            }
                        }
                        if ("date" in data
                            .item_rec2) {
                            var year = data
                                .item_rec2
                                .date
                                .substr(0,
                                    4
                                );
                            var month = data
                                .item_rec2
                                .date
                                .substr(5,
                                    2
                                );
                            var day = data
                                .item_rec2
                                .date
                                .substr(8,
                                    2
                                );
                            var convert_date =
                                day +
                                '/' +
                                month +
                                '/' +
                                year;
                            // $('input[name="form_sent_on"]').val(convert_date);
                            $('input[name="form_sent_on"]')
                                .datepicker(
                                    "setDate",
                                    convert_date
                                );
                        }
                    }
                    if (data.item_rec3 != null) {
                        if (data.item_rec3
                            .lognum == null
                        ) {
                            $('#app_login_detail')
                                .html(
                                    '<strong>Status - App login not created </strong>'
                                );
                            $('#app_login_date')
                                .val(
                                    '0000-00-00');
                        } else {
                            $('input:radio[name=electronic_copy_sent]')
                                .filter(
                                    '[value=yes]')
                                .prop('checked',
                                    true
                                );
                            $('textarea[name=electronic_copy_details]')
                                .prop('required',
                                    true
                                );
                            $('.electronic_copy_details')
                                .show(
                                    200);
                            //
                            $('.set_reminder_app')
                                .show(
                                    200);
                            $('select[name=aps_person]')
                                .prop('required',
                                    true
                                );
                            $('input[name=aps_last_date]')
                                .prop('required',
                                    true
                                );
                            //
                            $('#app_login_detail')
                                .html('<strong>App login created on ' +
                                    data
                                    .item_rec3
                                    .created_at
                                    .substr(0,
                                        10
                                    ) +
                                    ' - User loggedin atleast once : yes</strong>'
                                )
                            $('#app_login_date')
                                .val(data
                                    .item_rec3
                                    .created_at
                                    .substr(0,
                                        10
                                    )
                                );
                        }
                    }
                    /*$.each(data.item_rec, function(key, value) {
                        $('select[name="brand_name"]').append('<option value="'+ value.id +'">'+ value.title +'</option>');
                    });*/
                    $("#divLoading").removeClass(
                        'show');
                }
            });
        } else {
            // $('select[name="brand_name"]').empty();
        }
    }
});
$('input:radio[name=flight_booked]').click(function() {
    if ($('input:radio[name=flight_booked]:checked').val() == 'yes') {

        $('.flight_booking_details').show(200);
        $('.fb_airline_ref_no').show(200);
        $('.fb_booking_date').show(200);
        $('.fb_airline_name_id').show(200);
        $('.fb_payment_method_id').show(200);



        $('textarea[name=flight_booking_details]').prop('required', true);

        $('input[name=fb_airline_ref_no]').prop('required', true);

        $('input[name=fb_booking_date]').prop('required', true);

        $('select[name="fb_airline_name_id"]').prop('required', true);

        $('select[name="fb_payment_method_id"]').prop('required', true);



    } else {
        $('.flight_booking_details').hide(200);
        $('.fb_airline_ref_no').hide(200);
        $('.fb_booking_date').hide(200);
        $('.fb_airline_name_id').hide(200);
        $('.fb_payment_method_id').hide(200);




        $('textarea[name=flight_booking_details]').prop('required', false);

        $('input[name=fb_airline_ref_no]').prop('required', false);

        $('input[name=fb_booking_date]').prop('required', false);

        $('select[name="fb_airline_name_id"]').prop('required', false);

        $('select[name="fb_payment_method_id"]').prop('required', false);



    }
    if ($('input:radio[name=flight_booked]:checked').val() == 'NA') {
        $('.fb_depend').hide(200);
    } else {
        $('.fb_depend').show(200);
    }
});
$('input:radio[name=asked_for_transfer_details]').click(function() {
    if ($('input:radio[name=asked_for_transfer_details]:checked').val() == 'yes') {
        $('.transfer_details').show(200);
        $('textarea[name=transfer_details]').prop('required', true);
        //
        $('input[name=aft_last_date]').prop('required', false);
        //
    } else {
        $('.transfer_details').hide(200);
        $('textarea[name=transfer_details]').prop('required', false);
        //
        $('input[name=aft_last_date]').prop('required', true);
        //
    }
    if ($('input:radio[name=asked_for_transfer_details]:checked').val() == 'NA') {
        $('.aft_depend').hide(200);
        $('select[name="aft_person"]').prop('required', false);
        $('input[name=aft_last_date]').prop('required', false);
    } else {
        $('.aft_depend').show(200);
    }
});
$('input:radio[name=transfer_info_received]').click(function() {
    if ($('input:radio[name=transfer_info_received]:checked').val() == 'yes') {
        $('.transfer_info_details').show(200);
        $('textarea[name=transfer_info_details]').prop('required', true);
    } else {
        $('.transfer_info_details').hide(200);
        $('textarea[name=transfer_info_details]').prop('required', false);
    }
});
$('input:radio[name=itinerary_finalised]').click(function() {
    if ($('input:radio[name=itinerary_finalised]:checked').val() == 'yes') {
        $('.itinerary_finalised_details').show(200);
        $('.itf_current_date').show(200);
        $('textarea[name=itinerary_finalised_details]').prop('required', true);
        $('input[name=itf_last_date]').prop('required', false);
    } else {
        $('.itinerary_finalised_details').hide(200);
        $('.itf_current_date').hide(200);
        $('textarea[name=itinerary_finalised_details]').prop('required', false);
        $('input[name=itf_last_date]').prop('required', true);
    }
    if ($('input:radio[name=itinerary_finalised]:checked').val() == 'NA') {
        $('.itf_depend').hide(200);
        $('input[name=itf_last_date]').prop('required', false);
    } else {
        $('.itf_depend').show(200);
    }
});
$('input:radio[name=documents_sent]').click(function() {
    if ($('input:radio[name=documents_sent]:checked').val() == 'yes') {
        $('.documents_sent_details').show(200);
        $('textarea[name=documents_sent_details]').prop('required', true);
        $('input[name=ds_last_date]').prop('required', false);
    } else {
        $('.documents_sent_details').hide(200);
        $('textarea[name=documents_sent_details]').prop('required', false);
        $('input[name=ds_last_date]').prop('required', true);
    }
    if ($('input:radio[name=documents_sent]:checked').val() == 'NA') {
        $('.ds_depend').hide(200);
        $('input[name=ds_last_date]').prop('required', false);
    } else {
        $('.ds_depend').show(200);
    }
});


$('input:radio[name=electronic_copy_sent]').click(function() {
    if ($('input:radio[name=electronic_copy_sent]:checked').val() == 'yes') {
        $('.electronic_copy_details').show(200);
        $('textarea[name=electronic_copy_details]').prop('required', true);
        //
        $('.set_reminder_app').show(200);
        $('select[name=aps_person]').prop('required', true);
        $('input[name=aps_last_date]').prop('required', true);
        //
    } else {
        $('.electronic_copy_details').hide(200);
        $('textarea[name=electronic_copy_details]').prop('required', false);
        //
        $('.set_reminder_app').hide(200);
        $('select[name=aps_person]').prop('required', false);
        $('input[name=aps_last_date]').prop('required', false);
        //
    }
});
$('input:radio[name=transfer_organised]').click(function() {
    if ($('input:radio[name=transfer_organised]:checked').val() == 'yes') {
        $('.transfer_organised_details').show(200);
        $('textarea[name=transfer_organised_details]').prop('required', true);
        $('input[name=to_last_date]').prop('required', false);
    } else {
        $('.transfer_organised_details').hide(200);
        $('textarea[name=transfer_organised_details]').prop('required', false);
        $('input[name=to_last_date]').prop('required', true);
    }
    if ($('input:radio[name=transfer_organised]:checked').val() == 'NA') {
        $('.to_depend').hide(200);
        $('input[name=to_last_date]').prop('required', false);
    } else {
        $('.to_depend').show(200);
    }
});
$('input:radio[name=document_prepare]').click(function() {
    if ($('input:radio[name=document_prepare]:checked').val() == 'yes') {

        $('.tdp_current_date').show(200);
        $('textarea[name=tdp_current_date]').prop('required', true);
    } else {
        $('.tdp_current_date').hide(200);
        $('textarea[name=tdp_current_date]').prop('required', false);
    }
    if ($('input:radio[name=document_prepare]:checked').val() == 'NA') {
        $('.dp_depend').hide(200);
    } else {
        $('.dp_depend').show(200);
    }
});
$(function() {
    $('#example2').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false
    });
});
$(function() {
    $('#example3').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false
    });
});
$('.up-btn').on('click', function() {
    $val = $(this).attr('data-id');
    $('.up-box').hide();
    $('#' + $val).toggle();
});
$('.down-btn').on('click', function() {
    $val = $(this).attr('data-id');
    $('.down-box').hide();
    $('#' + $val).toggle();
});


$('#category , #product').on('change', function() {
    $('#divLoading').addClass('show');
    product_id = $('#product').val();
    category_id = $('#category').val();
    $.ajax({
        type: 'POST',
        url: '{{ route('get-supplier') }}',
        data: {
            "_token": "{{ csrf_token() }}",
            'p_id': product_id,
            'c_id': category_id
        },
        success: function(response) {
            init =
                '<option value="">Select Supplier</option>';
            if (response.length == 0) {
                add =
                    '<option value="">No Record Found</option>';
                init = init + add;
            }
            var i;
            for (i = 0; i < response.length; i++) {
                add = "<option value='" + response[i]
                    .supplier + "'>" + response[i]
                    .name +
                    "</option>";
                init = init + add;
            }
            $('#supplier').html('');
            $('#supplier').html(init);
            $('#divLoading').removeClass('show');
        }
    })
});
</script>




</body>

</html>
@endsection