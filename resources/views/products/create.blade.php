@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Add New Product
    </h1>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
            <div class="col-sm-6 col-sm-offset-3">
              @if(Session::has('success_message'))
                <li> <div class="alert alert-success">{{Session::get('success_message')}}</div> </li>
              @endif
            </div>   
          <form method="POST" action="{{ route('products.store') }}" >    @csrf
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                  <label for="inputEmail3" class="">Product Code<span style="color:red">*</span> </label>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-key"></i></span>
                      {!! Form::text('code',null,['class'=>'form-control','placeholder'=>'Code','required'=>'true']) !!}
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('code')}}</div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                  <label for="inputEmail3" class="">Product Name <span style="color:red">*</span> </label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    {!! Form::text('name',null,['class'=>'form-control','placeholder'=>'Name','required'=>'true']) !!}
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('name')}}</div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                  <label for="inputEmail3" class="">Product Description <span style="color:red">*</span></label>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-quote-left"></i></span>
                      {!! Form::textarea('description',null,['class'=>'form-control','placeholder'=>'Description','required'=>'true','rows'=>'4']) !!}
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('description')}}</div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              {!! Form::submit('submit',['required' => 'required','class'=>'btn btn-info pull-right']) !!}
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

</body>
</html>
@endsection