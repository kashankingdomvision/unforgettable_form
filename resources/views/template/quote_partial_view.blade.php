@foreach ($template->getTemplateDetails as $key => $detail)   
<input type="hidden" name="quote[{{ $key }}][key]" value="{{ encrypt($detail->id) }}">                             
    <div class="qoute">
        <div class="row">
            <div class="col-md-12">
        @if($key > 0)
            <button type="button" class="btn  pull-right close"> x </button>
            {{-- <button type='button' class='remove btn btn-link pull-right'><i class='fa fa-times'  style='color:red' ></i></button> --}}
        @endif
            </div>
        </div>
        <div class="row" style="margin-top: 15px">
            <div class="col-sm-2">
                <label for="inputEmail3" class="">Date of Service</label> 
                <div class="input-group">
                    <input type="text" data-name="date_of_service" name="date_of_service[]" value="{{ !empty($detail->date_of_service) ? date('d/m/Y', strtotime($detail->date_of_service)) : NULL }}" class="form-control datepicker checkDates bookingDateOfService" autocomplete="off" placeholder="Date of Service"  >
                </div>
                {{-- <div class="alert-danger date_of_service" style="text-align:center"></div> --}}
            </div>

            <div class="col-sm-2">
                <label for="inputEmail3" class="">Service Details</label> 
                <textarea name="service_details[]"  class="form-control" cols="30" rows="1">{{ $detail->service_details }}</textarea>
                {{-- <div class="alert-danger" style="text-align:center">{{ $errors->first('service_details') }}</div> --}}
            </div>

            <div class="col-sm-2">
                <label class="">Select Category</label> 
                <select class="form-control category-select2"   name="category[]" >
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ ($detail->category_id == $category->id)? 'selected' :(old('category') == $category->id  ? "selected" : "") }}> {{ $category->name }} </option>
                    @endforeach
                </select>
                <div class="alert-danger" style="text-align:center"> {{ $errors->first('category') }} </div>
            </div>

            <div class="col-sm-2">
                <label class="test">Select Supplier</label> 
                <select class="form-control supplier-select2"  name="supplier[]" >
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
                    <input type="text" data-name="booking_date" name="booking_date[]" class="form-control datepicker bookingDate" placeholder="Booking Date" autocomplete="off" value="{{ !empty($detail->booking_date) ? date('d/m/Y', strtotime($detail->booking_date)) : NULL }}" >
                </div>
                <div class="alert-danger booking_date" value="" style="text-align:center"> {{ $errors->first('booking_date') }} </div>
            </div>

            <div class="col-sm-2">
                <label for="inputEmail3" class="">Booking Due Date <span style="color:red">*</span></label> 
                <div class="input-group">
                    <input type="text" data-name="booking_due_date"   name="booking_due_date[]"  value="{{ !empty($detail->booking_due_date) ? date('d/m/Y', strtotime($detail->booking_due_date)) : NULL }}""  class="form-control datepicker checkDates bookingDueDate" autocomplete="off" placeholder="Booking Due Date" >
                </div>
                <div class="alert-danger booking_due_date" style="text-align:center; width: 160px;"></div>
            </div>

        </div>
        <div class="row" style="margin-top: 15px">
            <div class="col-sm-2">
                <label for="inputEmail3" class="">Booking Method</label>
                <div class="input-group">
                    <select class="form-control booking-method-select2 "  name="booking_method[]"  >
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
                    <select class="form-control booked-by-select2"  name="booked_by[]">
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
                    <input type="text" name="booking_refrence[]" class="form-control" placeholder="Booking Reference" value="{{ $detail->booking_refrence }}" >
                </div>
                <div class="alert-danger" style="text-align:center"> </div>
            </div>

            <div class="col-sm-2 " style="margin-bottom: 15px;">
                <label for="inputEmail3" class="">Booking Type</label> 
                <div class="input-group">
                     <select class="form-control booking-type-select2" name="booking_type[]" >
                        <option value="">Select Booking Type</option>
                        <option {{ ($detail->booking_type == 'refundable')? 'selected': NULL }} value="refundable">Refundable</option>
                        <option {{ ($detail->booking_type == 'non_refundable')? 'selected': NULL }} value="non_refundable">Non-Refundable</option>
                    </select>
                </div>
                <div class="alert-danger" style="text-align:center"> {{ $errors->first('booking_type') }} </div>
            </div>

            <div class="col-sm-2">
                <label for="inputEmail3" class="">Comments</label> 
                <textarea name="comments[]"   class="form-control" cols="30" rows="1">{{ $detail->comments }}</textarea>
                <div class="alert-danger" style="text-align:center"></div>
            </div>

            <div class="col-sm-2">
                <label>Select Supplier Currency</label> 
                <select class="form-control supplier-currency"  name="supplier_currency[]" >
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
                    <input type="number" data-code=""  name="cost[]" class="form-control cost" min="0" value="{{ $detail->cost }}" placeholder="Cost" >
                </div>
                <div class="alert-danger error-cost" style="text-align:center" ></div>
            </div>
            <div class="col-sm-2">
                <label for="inputEmail3" class="">Currency Conversion</label>
                <label class="currency"></label>  
                <input type="text" class="base-currency" name="qoute_base_currency[]" readonly><br>
            </div>

            <div class="col-sm-2">
                <label for="inputEmail3" class="">Added in Sage</label>
                <div class="input-group">
                    <input type="checkbox" {{ ($detail->added_in_sage == '1')? 'checked': NULL }} class="addsaga" name="added_in_sage[]" value="{{ ($detail->added_in_sage == '1')? 1: 0 }}">
                    {{-- <input type="checkbox" onclick="this.previousSibling.value=1-this.previousSibling.value"> --}}
                </div>
                
            </div>

            <div class="col-sm-2">
                <label for="inputEmail3" class="">Supervisor</label>
                <div class="input-group">
                    <select class="form-control  supervisor-select2"  name="supervisor[]" class="form-control" >
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