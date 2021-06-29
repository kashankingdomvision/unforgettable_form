@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>Add New Airline</h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('setting.airlines.index') }}" class="btn btn-primary btn-xs"><span class="fa fa-plus">View All Airlines</span></a>
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
          <form method="POST" action="{{ route('setting.airlines.store') }}"> @csrf
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                  <label for="inputEmail3" class="">Airline Name <span style="color:red">*</span></label>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      {!! Form::text('name',null,['class'=>'form-control','placeholder'=>'Airline Name','required'=>'true']) !!}
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('name')}}</div>
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