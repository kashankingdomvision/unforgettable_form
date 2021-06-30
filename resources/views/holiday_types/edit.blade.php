@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
      <h1> Edit Holiday Type </h1>
      <ol class="breadcrumb">
        <li>
          <a href="{{ route('setting.holidaytypes.index') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add">View Holiday Types</a>
        </li>
      </ol>
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
          <form action="{{ route('setting.holidaytypes.update', encrypt($holidayType->id)) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                @csrf @method('put')
            <div class="box-body">
              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 " >
                      <label for="brandName" class=""> Holiday Type Name <span class="text-danger">*</span></label>
                      <input type="text" name="name" class="form-control" placeholder="Enter the holiday type name" value="{{ old('name')??$holidayType->name }}" id="brandName" />
                      <div class="alert-danger" style="text-align:center">{{ $errors->first('name') }}</div>
                  </div>
              </div>
          
              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 " >
                      <label for="brandEmail" class=""> Select Brand <span class="text-danger">*</span> </label>
                      <select class="form-control select2" name="brand_id">
                          <option value="" selected disabled>Select Brand </option>
                          @foreach ($brands as $brand)
                              <option value="{{$brand->id}}"  {{ ($holidayType->brand_id == $brand->id)? 'selected': ((old('brand_id') == $brand->id) ? 'selected' : '') }}> {{$brand->name}} </option>
                          @endforeach
                      </select>
                      <div class="alert-danger" style="text-align:center">{{ $errors->first('brand_id') }}</div>
                  </div>
              </div>
            </div>
            <div class="box-footer">
              <button type="submit" class="btn btn-info pull-right">Update</button> 
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
