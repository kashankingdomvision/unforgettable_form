@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1> Add New Booking Method</h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('setting.booking_methods.index') }}" class="btn btn-primary btn-xs"><span class="fa fa-plus">View All Booking Method</span></a>
      </li>
    </ol>
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
          <form method="POST" action="{{ route('setting.booking_methods.store') }}"> @csrf
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                <label for="inputEmail3" class="">Booking Method Name<span style="color:red">*</span></label>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      <input type="text" name="name" placeholder="Booking Method Name" class="form-control" required>
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('booking_method_name')}}</div>
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