@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
      <h1>Edit Currency</h1>
      <ol class="breadcrumb">
        <li>
          <a href="{{ route('setting.brands.index') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add">View All Brand</a>
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
          <form action="{{ route('setting.brands.update', encrypt($brand->id)) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
            @csrf @method('put')
            <div class="box-body">
              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 " >
                      <label for="brandName" class=""> Brand Name <span class="text-danger">*</span></label>
                      <input type="text" name="name" class="form-control" placeholder="Brand name" value="{{ old('name')??$brand->name }}" id="brandName" />
                      <div class="alert-danger" style="text-align:center">{{ $errors->first('name') }}</div>
                  </div>
              </div>
              
              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 " >
                      <label for="brandEmail" class=""> Email Address </label>
                      <input type="email" name="email" class="form-control" placeholder="Email address" value="{{ old('email')??$brand->email }}" id="brandEmail" />
                      <div class="alert-danger" style="text-align:center">{{ $errors->first('email') }}</div>
                  </div>
              </div>
          
              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 " >
                      <label for="brandAddress" class="">Address </label>
                      <input type="text" name="address" class="form-control" placeholder="Brand address" value="{{ old('address')??$brand->address }}" id="brandAddress" />
                      <div class="alert-danger" style="text-align:center">{{ $errors->first('address') }}</div>
                  </div>
              </div>
          
              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 " >
                      <label for="brandPhone" class=""> Phone Number </label>
                      <input type="Number" name="phone" class="form-control" placeholder="123456789" value="{{ old('phone')??$brand->phone }}" id="brandPhone" />
                      <div class="alert-danger" style="text-align:center">{{ $errors->first('phone') }}</div>
                  </div>
              </div>
          
              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 " >
                      <label for="brandLogo" class="">Logo  </label>
                      <input type="file" name="logo" class="form-control" placeholder="Select the logo file" value="" id="brandLogo" />
                      <div class="alert-danger" style="text-align:center">{{ $errors->first('logo') }}</div>
                  </div>
              </div>
              <hr />
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3 text-center" >
                  @if($brand->logo)<img src="{{ $brand->logo }}" width="150px" height="150px" /> @endif
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
