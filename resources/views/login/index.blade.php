@extends('content_layout.login')

@section('content')
<!-- <body class="hold-transition login-page"> -->
<body class="hold-transition">
<div class="login-box">
  <div class="login-logo" style="margin-left:-13%">
    <!-- <a href=""><b>Welcome To FL Traders</b></a> -->
    {!! HTML::image('img/logo.png') !!}
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <!-- <p class="login-box-msg">Sign in to start your session</p> -->

    {!! Form::open(array('route'=>'/login','id'=>'user_form')) !!}
    {{ csrf_field() }}
      @if(Session::has('success_message'))
      
          <div class="alert alert-success">{{Session::get('success_message')}}</div>
      
      @endif

      @if ($errors->has('email') || $errors->has('password'))
      
         <div class="alert alert-danger">{{Session::get('fail_message')}}</div>
      
      @endif
       <div class="alert-danger" style="text-align:center">{{$errors->first('email')}} {{ $errors->first('password') }}</div><br/>
     
      <div class="form-group has-feedback">
         {!! Form::email('email',null,['class'=>'form-control','placeholder'=>'Email','required'=>'true']) !!}
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>

      <div class="alert-danger" style="text-align:center">{{$errors->first('password' )}}</div><br/>
      <div class="form-group has-feedback">
        <!-- <input type="password" class="form-control" placeholder="Password"> -->
        {!! Form::password('password',['class'=>'form-control','placeholder'=>'Password']) !!}
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <!-- <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button> -->
          {!! Form::submit('Login',['required' => 'required','class'=>'btn btn-success btn-block']) !!}
        </div>
        <!-- /.col -->
      </div>
       {!! Form::close() !!}
    <!-- </form> -->
    <!-- /.social-auth-links -->

    <!-- <a href="#">I forgot my password</a><br>
    <a href="register.html" class="text-center">Register a new membership</a> -->

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
<!-- Bootstrap 3.3.6 -->
{!! HTML::script('bootstrap/js/bootstrap.min.js') !!}
<!-- iCheck -->
{!! HTML::script('plugins/iCheck/icheck.min.js') !!}
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>
