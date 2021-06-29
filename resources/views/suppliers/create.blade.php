@extends('content_layout.default')

@section('content')

<div class="content-wrapper">
  <section class="content-header">
    <h1>Add New Supplier</h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('suppliers.index') }}" class="btn btn-primary btn-sm" style="color: white;">View All Supplier</a>
      </li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <form method="POST" action="{{ route("suppliers.store") }}" > 
          @csrf    
            <div class="col-sm-6 col-sm-offset-3">
              @if(Session::has('success_message'))
                <li> <div class="alert alert-success">{{Session::get('success_message')}}</div> </li>
              @endif
            </div>  
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                  <label for="inputEmail3" class="">Name</label>
                  <span style="color:red">*</span>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" name="username"  class="form-control" placeholder="Name" value="{{old('username')}}" required>
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('username')}}</div>
                </div>
              </div>
              
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                  <label for="inputEmail3" class="">Email</label>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                      <input type="email" name="email"  class="form-control" placeholder="Email" value="{{old('email')}}" >
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('email')}}</div>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                  <label for="inputEmail3" class="">Phone Number</label>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                      <input class="form-control" name="phone" placeholder="12345678" value="{{old('phone')}}">
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('phone')}}</div>
                </div>
              </div>

              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3">
                      <label for="inputEmail3" class="">Category</label>
                      <span style="color:red">*</span>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                          <select name="categories[]" class="form-control select2" multiple="multiple" required>
                              <option  value="">Select Category</option>
                              @foreach ($categories as $category)
                              <option value="{{$category->id}}" {{ in_array($category->id, old('categories') ?? []) ? 'selected' : '' }} >{{$category->name}}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('categories')}}</div>
                  </div>
              </div>

              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3">
                      <label for="inputEmail3" class="">Product</label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                          <select name="products[]" class="form-control  select2" multiple >
                              <option value="">Select Products</option>
                              @foreach ($products as $product)
                              <option value="{{$product->id}}" {{ in_array($product->id, old('products') ?? []) ? 'selected' : '' }} >{{$product->name}}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('products')}}</div>
                  </div>
              </div>

              <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3">
                      <label for="inputEmail3" class="">Currency</label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                          <select name="currency" class="form-control select2">
                              <option selected value="">Select Currency</option>
                              @foreach ($currencies as $currency)
                              <option value="{{$currency->id}}"  {{ (old("currency") == $currency->id ? "selected" : "") }} >{{ $currency->name }} ({{ $currency->symbol }})</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('currency')}}</div>
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