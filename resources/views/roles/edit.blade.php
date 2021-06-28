@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>Edit Role</h1>
    <ol class="breadcrumb">
      <li>
          <a href="{{ route('roles.index') }}" class="btn btn-primary btn-xs" ><span class="fa fa-eye">View All Roles</span></a>
      </li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <form method="POST" action="{{ route('roles.update',  encrypt($role->id)) }}"> @csrf
          @method('put')
            <div class="col-sm-6 col-sm-offset-3">
              @if(Session::has('success_message'))
              <li>
                  <div class="alert alert-success">{{Session::get('success_message')}}</div>
              </li>
              @endif
            </div>  
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                  <label for="inputEmail3" class="">Role Name <span style="color:red">*</span></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    {!! Form::text('name',$role->name,['class'=>'form-control','placeholder'=>'Role Name','required'=>'true']) !!}
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('name')}}</div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              {!! Form::submit('Update',['required' => 'required','class'=>'btn btn-info pull-right']) !!}
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection