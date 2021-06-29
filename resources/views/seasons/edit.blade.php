@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>Edit New Season</h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('seasons.index') }}" class="btn btn-primary btn-xs" data-title="view" data-target="#view"><span class="fa fa-plus">View All Season</span></a>
      </li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <form method="POST" action="{{ route('seasons.update', encrypt($season->id)) }}" > 
            @csrf @method('put')
              <div class="col-sm-6 col-sm-offset-3">
                @if(Session::has('success_message'))
                <li> <div class="alert alert-success">{{Session::get('success_message')}}</div> </li>
                @endif
              </div>  
              <div class="box-body">
                <div class="form-group">
                  <div class="col-sm-4 col-sm-offset-3">
                    <label for="inputEmail3" class=""> Season Name <span style="color:red">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-cloud"></i></span>
                        <input type="text" id="seasons" name="name" class="form-control" value="{{ $season->name }}"  placeholder="YYYY-YYYY">
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('name')}}</div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-4 col-sm-offset-3" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">Start Date</label><span style="color:red"> * </span>
                    <div class="input-group">
                        <span class="input-group-addon"></span>
                        <input type="text" name="start_date" class="form-control datepicker"  value="{{\Carbon\Carbon::parse(str_replace('-', '/', $season->start_date))->format('d/m/Y')}}" placeholder="{{ date("d/m/Y") }}">
                    </div>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('start_date') }} </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-4 col-sm-offset-3" style="margin-bottom: 15px;">
                    <label for="inputEmail3" class="">End Date</label><span style="color:red"> * </span>
                    <div class="input-group">
                        <span class="input-group-addon"></span>
                        <input type="text" name="end_date" class="form-control datepicker"  value="{{\Carbon\Carbon::parse(str_replace('-', '/', $season->end_date))->format('d/m/Y')}}" placeholder="{{ date("d/m/Y") }}">
                    </div>
                    <div class="alert-danger" style="text-align:center"> {{ $errors->first('end_date') }} </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3">
                    <label for="inputEmail3" class="">Set Default Season </label>
                    <div class="row">
                        <div class="col-md-2">
                            <input type="radio" id="yes" name="default" value="1"  {{ $season->default == "1" ? "checked" : "" }} >
                            <label for="yes">Yes</label><br>
                        </div>
                        <div class="col-md-2">
                            <input type="radio" id="no" name="default" value="0" {{ $season->default == "0" ? "checked" : "" }}>
                            <label for="no">No</label><br>   
                        </div>
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('set_default_season')}}</div>
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