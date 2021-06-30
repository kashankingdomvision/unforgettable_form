@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>Edit Manual Rate</h1>
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
          <form method="POST" action="{{ route('setting.currecy_conversions.update', encrypt($currency_record->id)) }}">            
              <div class="box-body">
                  <div class="form-group" >
                      <div class="col-sm-6 col-sm-offset-3 mb-2">
                      <label for="inputEmail3" class="">From</label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-user"></i></span>
                          <select class="form-control currency-select2" name="from" disabled>
                              <option value="">Select Currency</option>
                              @foreach ($currencies as $currency)
                              <option value="{{ $currency->code }}"  {{ $currency->code == $currency_record->from ? 'selected' : ''}} data-image="data:image/png;base64, {{$currency->flag}}"> &nbsp; {{$currency->code}} - {{$currency->name}} </option>
                              @endforeach
                          </select>
                      </div>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('from')}}</div>
                      </div>
                  </div>
                  <div class="form-group" >
                      <div class="col-sm-6 col-sm-offset-3 mb-2">
                      <label for="inputEmail3" class="">To</label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-user"></i></span>
                          <select class="form-control currency-select2" name="to" disabled>
                              <option value="">Select Currency</option>
                              @foreach ($currencies as $currency)
                              <option value="{{ $currency->code }}"  {{ $currency->code == $currency_record->to ? 'selected' : ''}} data-image="data:image/png;base64, {{$currency->flag}}"> &nbsp; {{$currency->code}} - {{$currency->name}} </option>
                              @endforeach
                          </select>
                      </div>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('to')}}</div>
                      </div>
                  </div>

                  <div class="form-group">
                      <div class="col-sm-6 col-sm-offset-3 mb-2">
                      <label for="inputPassword3" class="">Manual Rate</label>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                          <input type="number" name="manual_rate" class="form-control hide-arrows" value="{{ $currency_record->manual_rate }}" placeholder="0.00" step="any">
                        </div>
                        <div class="alert-danger" style="text-align:center">{{$errors->first('manual_rate')}}</div>
                      </div>
                  </div>
              </div>
            <div class="box-footer">
              {!! Form::submit('Update',['required' => 'required','onclick'=>'submitForm(this)','class'=>'btn btn-info pull-right']) !!}
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
 
{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}

<!-- jQuery 2.2.3 -->
{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
<!-- Bootstrap 3.3.6 -->
{!! HTML::script('bootstrap/js/bootstrap.min.js') !!}
<!-- FastClick -->
{!! HTML::script('plugins/select2/select2.full.min.js') !!}

{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}

<script type="text/javascript">

$(document).ready(function(){

  $('.select2').select2();

  $('.currency-select2').select2({
    templateResult: formatState,
    templateSelection: formatState
  });

  function formatState(opt) {
    if (!opt.id) {
      return opt.text;
    }

    var optimage = $(opt.element).attr('data-image');

    if (!optimage) {
      return opt.text ;
    } else {
      var $opt = $(
        '<span><img height="20" width="20" src="' + optimage + '" width="60px" /> ' + opt.text + '</span>'
      );
      return $opt;
    }
  };

});
    
</script>

</body>
</html>
@endsection