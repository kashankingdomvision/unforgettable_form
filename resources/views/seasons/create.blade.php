@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>Add New Season</h1>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="col-sm-6 col-sm-offset-3">
            @if(Session::has('success_message'))
            <li>
                <div class="alert alert-success">{{Session::get('success_message')}}</div>
            </li>
            @endif
          </div>  
          <form method="POST" action="{{ route('seasons.store') }}">  @csrf  
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-4 col-sm-offset-3">
                  <label for="inputEmail3" class=""> Season Name <span style="color:red">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-cloud"></i></span>
                    <input type="text" name="name" id="seasons" class="form-control" maxlength="9" autocomplete="off" pattern="^-?\d{4}-\d{4}$" placeholder="Enter the season name into years (2021-2023)" value="{{ old('name') }}" required>
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('name')}}</div>
                </div>
              </div>

              <div class="col-sm-4 col-sm-offset-3" style="margin-bottom: 15px;">
                <label for="inputEmail3" class="">Start Date</label><span style="color:red"> * </span>
                <div class="input-group">
                    <span class="input-group-addon"></span>
                    <input type="text" name="start_date" autocomplete="off" class="form-control datepicker" value="{{ old('start_date') }}" placeholder="{{ date("d/m/Y") }}" required>
                </div>
                <div class="alert-danger" style="text-align:center"> {{ $errors->first('start_date') }} </div>
              </div>

              <div class="col-sm-4 col-sm-offset-3" style="margin-bottom: 15px;">
                <label for="inputEmail3" class="">End Date</label><span style="color:red"> * </span>
                <div class="input-group">
                    <span class="input-group-addon"></span>
                    <input type="text" name="end_date"  autocomplete="off" value="{{ old('end_date') }}" class="form-control datepicker" placeholder="{{ date("d/m/Y") }}" required>
                </div>
                <div class="alert-danger" style="text-align:center"> {{ $errors->first('end_date') }} </div>
              </div>

              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                  <label for="inputEmail3" class="">Set Default Season </label>
                  <div class="row">
                      <div class="col-md-2">
                          <input type="radio" id="yes" name="default" value="1">
                          <label for="yes"> Yes</label><br>
                      </div>
                      <div class="col-md-2">
                          <input type="radio" id="no" name="default" value="0" checked>
                          <label for="no"> No</label><br>   
                      </div>
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('default')}}</div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              {!! Form::submit('Submit',['required' => 'required','class'=>'btn btn-info pull-right']) !!}
            </div>
          </form>
          </div>
        </div>
      </div>
    </section>
  </div>


@endsection