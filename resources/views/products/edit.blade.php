@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>Edit Product</h1>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <form method="POST" action="{{ route('products.update', encrypt($product->id)) }}" > @csrf
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
                <div class="col-sm-6 col-sm-offset-3 mb-2">
                  <label for="inputEmail3" class="">Product Code</label>
                  <span style="color:red">*</span>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      {!! Form::text('code',$product->code,['class'=>'form-control','placeholder'=>'Product Code','required'=>'true']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('code')}}</div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3 mb-2">
                  <label for="inputEmail3" class="">Product Name</label>
                  <span style="color:red">*</span>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    {!! Form::text('name',$product->name,['class'=>'form-control','placeholder'=>'Product Name','required'=>'true']) !!}
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('name')}}</div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3 mb-2">
                  <label for="inputEmail3" class="">Product Description</label>
                  <span style="color:red">*</span>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    {!! Form::textarea('description',$product->description,['class'=>'form-control','placeholder'=>'Product Name','required'=>'true','rows'=>'4']) !!}
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('name')}}</div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              {!! Form::submit('Update',['required' => 'required', 'class'=>'btn btn-info pull-right']) !!}
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection