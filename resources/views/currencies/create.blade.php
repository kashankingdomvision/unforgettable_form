@extends('content_layout.default')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Add New Currency</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="col-sm-6 col-sm-offset-3">
                        @if (Session::has('success_message'))
                            <li>
                                <div class="alert alert-success">{{ Session::get('success_message') }}</div>
                            </li>
                        @endif
                    </div>
                    <form action="{{ route('setting.currencies.store') }}" method="POST" class="form-horizontal">
                        @csrf
                        <div class="box-body">
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3 mb-3" >
                                    <label for="inputEmail3" class="">Currency <span class="text-danger">*</span></span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <select class="form-control select2" name="currency">
                                            <option value="">Select Currency</option>
                                            @foreach ($all_currencies as $currency)
                                                <option value="{{$currency->code}}" data-image="data:image/png;base64, {{$currency->flag}}" {{ old('currency') == $currency->code ? 'selected' : '' }}> &nbsp; {{$currency->code}} - {{$currency->name}} {{ ($currency->isObsolete == 'true') ? '(obsolete)' : '' }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="alert-danger" style="text-align:center">{{ $errors->first('currency') }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <label for="inputEmail3" class="">Status </label>
                                    <div class="row">
                                      <div class="input-group">
                                        <div class="col-md-3 ">
                                            <input type="radio" id="active" name="status" value="1"  {{ $currency->status == 1 ? "checked" : "" }} >
                                            <label for="active">Active</label><br>
                                        </div>
                                        <div class="col-md-3 ">
                                            <input type="radio" id="inactive" name="status" value="0" {{ $currency->status == 0 ? "checked" : "" }}>
                                            <label for="inactive">Inactive</label><br>   
                                        </div>
                                      </div>
                                    </div>
                                    <div class="alert-danger" style="text-align:center">{{$errors->first('status')}}</div>
                                </div>
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
@endsection
