@extends('content_layout.default')

@section('content')

<style>
    .mb-2{
        margin-bottom: 2rem;
    }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Edit User
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- left column -->
        
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <!-- form start -->

            {!! Form::open(array('route' => array('update-user',$data->id),'method'=>'POST')) !!}
            <input type="hidden" name="id" value="<?=$data->id;?>">
            
            <div class="col-sm-6 col-sm-offset-3">
              @if(Session::has('success_message'))
              <li>
                  <div class="alert alert-success">{{Session::get('success_message')}}</div>
              </li>
              @endif
            </div>  
            <!-- <form class="form-horizontal"> -->
              <div class="box-body">

                <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 mb-2">
                  <label for="inputEmail3" class="">Username <span style="color:red">*</span></label>
                    <!-- <input type="email" class="form-control" id="inputEmail3" placeholder="Email"> -->
                    <div class="input-group">
                       <span class="input-group-addon"><i class="fa fa-user"></i></span>
                       <!-- <input name="username" type="email" class="form-control" placeholder="Username"> -->
                       {!! Form::text('username',$data->name,['class'=>'form-control','placeholder'=>'Username','required'=>'true']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('username')}}</div>
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 mb-2">
                  <label for="inputEmail3" class="">Email <span style="color:red">*</span></label>
                    <!-- <input type="email" class="form-control" id="inputEmail3" placeholder="Email"> -->
                    <div class="input-group">
                       <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                       <!-- <input name="email" type="email" class="form-control" placeholder="Email"> -->
                       {!! Form::email('email',$data->email,['class'=>'form-control','placeholder'=>'Email','required'=>'true']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('email')}}</div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 mb-2">
                  <label for="inputEmail3" class="">User Type {{$data->role}} <span style="color:red">*</span></label>
                    <!-- <input type="email" class="form-control" id="inputEmail3" placeholder="Email"> -->
                    <div class="input-group">
                       <span class="input-group-addon"><i class="fa fa-user"></i></span>
                       <!-- <input name="username" type="email" class="form-control" placeholder="Username"> -->
                       <select class="form-control" name="role">
                        @foreach($roles as $role)
                            <option {{ ($data->role_id == $role->id) ? 'selected' : ''}} value="{{$role->id}}" data-role="{{$role->name}}">{{$role->name}}</option>
                        @endforeach
                       </select>
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('username')}}</div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 mb-2">
                 
                  <label for="inputPassword3" class="">Password</label>
                    <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                    <div class="input-group">
                       <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                       <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                       {!! Form::password('password',['class'=>'form-control','placeholder'=>'Password']) !!}
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('password')}}</div>
                  </div>
                </div>

                <div class="form-group" id="supervisor">
                    <div class="col-sm-6 col-sm-offset-3 mb-2">
                    <label for="inputEmail3" class="">Supervisor</label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <select class="form-control" id="selectSupervisor" name="supervisor">
                            <option value="">Select Supervisor</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{$supervisor->id}}" {{ $supervisor->id == $data->supervisor_id ? 'selected' : '' }} >{{$supervisor->name}}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('supervisor')}}</div>
                    </div>
                </div>
                
                <div class="form-group">
                  <div class="col-sm-6 col-sm-offset-3 mb-2">
                  <label for="inputEmail3" class="">Default Currency</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      <select class="form-control currency-select2" name="currency">
                          <option value="">Select Currency</option>
                          @foreach($currencies as $currency)
                              <option value="{{$currency->id}}"  data-image="data:image/png;base64, {{$currency->flag}}" {{ ($data->currency_id == $currency->id )? 'selected' : ((old('currency') == $currency->id)? 'selected' : NULL) }}>  &nbsp; {{$currency->code}} - {{$currency->name}} </option>
                          @endforeach
                      </select>
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('currency')}}</div>
                  </div>
              </div>
              
              <div class="form-group" >
                <div class="col-sm-6 col-sm-offset-3 mb-2">
                  <label for="inputEmail3" class="">Default Brands</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <select class="form-control select2" name="brand">
                        <option value="">Select Brands</option>
                        @foreach($brands as $brand)
                          <option value="{{$brand->id}}" {{ ($data->brand_id == $brand->id)? 'selected' : ((old('brand') == $brand->id)? 'selected' : NULL) }} >{{ $brand->name }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('brand')}}</div>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                <label for="inputEmail3" class="">Holiday Type</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <select class="form-control select2" name="holiday_type">
                      <option  value="">Select Holiday Type</option>
                      @foreach($holiday_types as $holiday_type)
                        <option value="{{$holiday_type->id}}" {{ ($data->holiday_type_id == $holiday_type->id)? 'selected' : ((old('brand') == $holiday_type->id)? 'selected' : NULL) }} >{{ $holiday_type->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="alert-danger" style="text-align:center">{{$errors->first('holiday_type')}}</div>
                </div>
              </div>

              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <!-- <button type="submit" class="btn btn-info pull-right">Sign in</button> -->
                {!! Form::submit('Update',['required' => 'required','onclick'=>'submitForm(this)','class'=>'btn btn-info pull-right']) !!}
              </div>
              <!-- /.box-footer -->
            </form>
          </div>
          <!-- /.box -->
          <!-- general form elements disabled -->
          
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.3.7
    </div>
    <strong>Copyright © 2016-2017 <a href="http://www.visrox.com/">Visrox Inc</a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Recent Activity</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-birthday-cake bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                <p>Will be 23 on April 24th</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-user bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                <p>New phone +1(800)555-1234</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                <p>nora@example.com</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-file-code-o bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                <p>Execution time 5 seconds</p>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="label label-danger pull-right">70%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Update Resume
                <span class="label label-success pull-right">95%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Laravel Integration
                <span class="label label-warning pull-right">50%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Back End Framework
                <span class="label label-primary pull-right">68%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Some information about this general settings option
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Allow mail redirect
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Other sets of options are available
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Expose author name in posts
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Allow the user to show his name in blog posts
            </p>
          </div>
          <!-- /.form-group -->

          <h3 class="control-sidebar-heading">Chat Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Show me as online
              <input type="checkbox" class="pull-right" checked>
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Turn off notifications
              <input type="checkbox" class="pull-right">
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Delete chat history
              <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
            </label>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
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

  var role = $('select[name="role"]').find('option:selected').data('role'); 

  if(role == 'Sales Agent' || role == 2 ){
    $('#supervisor').show();                
    $('#selectSupervisor').removeAttr('disabled');
  }else{
    $('#supervisor').hide();                
    $('#selectSupervisor').attr('disabled', 'disabled');
  }

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

    var holiday_type_id  = "{{ isset($data->holiday_type_id) && !empty($data->holiday_type_id) ? $data->holiday_type_id : '' }}";

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
          options += '<option value="' + value.id + '"' + (value.id == holiday_type_id ? 'selected="selected"' : '') +'>' + value.name+ '</option>';
        });

        $('select[name="holiday_type"]').html(options);
        

      }
    });
  });

});
    
</script>

</body>
</html>
@endsection