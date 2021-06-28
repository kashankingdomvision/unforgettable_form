@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Add New User</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('users.index') }}" class="btn btn-primary btn-xs" ><span class="fa fa-eye">View All Users</span></a>
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
                    <form method="post" class="form-horizontal" action="{{ route('users.store') }}"> @csrf
                        <div class="box-body"> {{-- box body --}}
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <label for="inputEmail3" class="">Username <span style="color:red">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'Username', 'required' => 'true']) !!}
                                    </div>
                                    <div class="alert-danger" style="text-align:center">{{ $errors->first('username') }}</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <label for="inputEmail3" class="">Email <span style="color:red">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'true']) !!}
                                    </div>
                                      <div class="alert-danger" style="text-align:center">{{ $errors->first('email') }}</div>
                                </div>
                            </div>

                            <div class="form-group">
                              <div class="col-sm-6 col-sm-offset-3">
                                <label for="inputEmail3" class="">User Type <span style="color:red">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <select class="form-control select2 changeRole" name="role">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" data-role="{{ $role->name }}">
                                                {{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('role') }}</div>
                              </div>
                            </div>

                            <div class="form-group">
                              <div class="col-sm-6 col-sm-offset-3">
                                <label for="inputPassword3" class="">Password <span style="color:red">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'required' => 'true']) !!}
                                </div>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('password') }}</div>
                              </div>
                            </div>

                            <div class="form-group userSupervisor" style="display: {{ $errors->first('supervisor') != null ? null : 'none' }};">
                              <div class="col-sm-6 col-sm-offset-3">
                                <label for="inputEmail3" class="">Supervisor</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <select class="form-control select2" name="supervisor" {{ $errors->first('supervisor') ? '' : 'disabled' }} id="selectSupervisor">
                                        <option value="">Select Supervisor</option>
                                        @foreach ($supervisors as $supervisor)
                                            <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('supervisor') }}</div>
                              </div>
                            </div>

                            <div class="form-group">
                              <div class="col-sm-6 col-sm-offset-3">
                                <label for="inputEmail3" class="">Default Currency</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <select class="form-control currencyImage" name="currency">
                                        <option selected value="">Select Currency</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}"
                                                {{ old('currency') == $currency->id ? 'selected' : null }}
                                                data-image="data:image/png;base64, {{ $currency->flag }}"> &nbsp;
                                                {{ $currency->code }} - {{ $currency->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('currency') }}</div>
                              </div>
                            </div>

                            <div class="form-group">
                              <div class="col-sm-6 col-sm-offset-3">
                                <label for="inputEmail3" class="">Default Brands</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <select class="form-control select2 getBrandtoHoliday" name="brand">
                                        <option selected value="">Select Brands</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                {{ old('brand') == $brand->id ? 'selected' : null }}>
                                                {{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('brand') }}</div>
                              </div>
                            </div>

                            <div class="form-group">
                              <div class="col-sm-6 col-sm-offset-3">
                                <label for="inputEmail3" class="">Holiday Type</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <select class="form-control select2 appendHolidayType" name="holiday_type">
                                        <option value="">Select Holiday Type</option>
                                    </select>
                                </div>
                                <div class="alert-danger" style="text-align:center">{{ $errors->first('holiday_type') }}</div>
                              </div>
                            </div>
                        </div>{{-- <!-- /.box-body --> --}}
                        <div class="box-footer">
                            {!! Form::submit('Submit', ['required' => 'required', 'class' => 'btn btn-info pull-right']) !!}
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
{{-- <!-- ./wrapper -->
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
{!! HTML::script('dist/js/demo.js') !!} --}}

{{-- <script type="text/javascript">

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

        $(document).on('change', 'select[name="role"]',function(){
             var role = $(this).find('option:selected').data('role'); 
            if(role == 'Sales Agent' || role == 2 ){
                $('#supervisor').show();
                $('#selectSupervisor').removeAttr('disabled');
            }else{
                $('#supervisor').hide();
                $('#selectSupervisor').attr('disabled', 'disabled');
                
            }
        
        });

      $(document).on('change', 'select[name="brand"]',function(){

        let brand_id = $(this).val();
        var options = '';

        console.log(brand_id);

        $.ajax({
          type: 'POST',
          url: '{{ route('get-holiday-type') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            'brand_id': brand_id
          },
          success: function(response) {

            options += '<option value="">Select Holiday Type</option>';
            $.each(response,function(key,value){
              options += '<option value="'+value.id+'">'+value.name+'</option>';
            });

            $('select[name="holiday_type"]').html(options);
            
          }
        });

      
      });

    });

</script> --}}
{{-- 
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {

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
                return opt.text;
            } else {
                var $opt = $(
                    '<span><img height="20" width="20" src="' + optimage + '" width="60px" /> ' + opt.text +
                    '</span>'
                );
                return $opt;
            }
        };

        $(document).on('change', 'select[name="role"]', function() {
            var role = $(this).find('option:selected').data('role');
            if (role == 'Sales Agent' || role == 2) {
                $('#supervisor').show();
                $('#selectSupervisor').removeAttr('disabled');
            } else {
                $('#supervisor').hide();
                $('#selectSupervisor').attr('disabled', 'disabled');

            }

        });

        $(document).on('change', '.userBrand', function() {

            let brand_id = $(this).val();
            var options = '';

            console.log(brand_id);

            $.ajax({
                type: 'POST',
                url: '{{ route('get-holiday-type') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'brand_id': brand_id
                },
                success: function(response) {

                    options += '<option value="">Select Holiday Type</option>';
                    $.each(response, function(key, value) {
                        options += '<option value="' + value.id + '">' + value
                            .name + '</option>';
                    });

                    $('select[name="holiday_type"]').html(options);

                }
            });


        });

    });
</script>
@endpush --}}
@endsection
