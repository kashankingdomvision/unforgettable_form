@extends('content_layout.default')

@section('content')
<style type="text/css">
    td.day{
      position:relative;  
    }
    td.day.disabled:read-only {
        color: gray;
    }
    
    td.day{
        color: #000;
        font-weight: 700;
    }
    
  
        .qoute{
            width: 100%;
            border: solid 1px #000;
            padding: 20px;
            margin: 0 auto 15px;
            float: none;
            border-radius: 10px;
        }
    
    
        .hide-arrows::-webkit-inner-spin-button, .hide-arrows::-webkit-outer-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
    
        .hide-arrows {
            -moz-appearance:textfield !important;
        }
    </style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Create Currency</h1>
        <ol class="breadcrumb">
            <li>
              <a href="{{ route('currency.index') }}" class="btn btn-primary btn-xs">View all Currencies</a>
            </li>
          </ol>
    </section>
    <section class="content">
        <div id="divLoading"></div>
        <div class="row">
            <div class="col-xs-6 col-md-offset-3">
               @if(Session::has('success_message'))
                   <div class="alert alert-success" style="text-align: center;">{{Session::get('success_message')}}</div>
               @endif
               @if(Session::has('error_message'))
                   <div class="alert alert-danger" style="text-align: center;">{{Session::get('error_message')}}</div>
               @endif 
            </div>
        <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 appendColumn">
                        <form method="POST" action="{{ route('currency.store') }}"> @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <section class="content">
                                        <div class="row">
                                          <!-- left column -->
                                          
                                          <!--/.col (left) -->
                                          <!-- right column -->
                                          <div class="col-md-12">
                                            <!-- Horizontal Form -->
                    
                                              <!-- form start -->
                                              
                                              {!! Form::open(array('route'=>'creat-user','class'=>'form-horizontal','id'=>'user_form')) !!}
                                              
                                              <div class="col-sm-6 col-sm-offset-3">
                                                @if(Session::has('success_message'))
                                                <li>
                                                    <div class="alert alert-success">{{Session::get('success_message')}}</div>
                                                </li>
                                                @endif
                                              </div>  
                                              <!-- <form class="form-horizontal"> -->
                                  
                                                  <div class="form-group">
                                                    <div class="col-sm-6 col-sm-offset-3">
                                                    <label for="inputEmail3" class="">Currency <span style="color:red">*</span></label>
                                                      <!-- <input type="email" class="form-control" id="inputEmail3" placeholder="Email"> -->
                                                      <div class="input-group">
                                                         <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                         <!-- <input name="username" type="email" class="form-control" placeholder="Username"> -->
                                                         {!! Form::text('username',null,['class'=>'form-control','placeholder'=>'Username','required'=>'true']) !!}
                                                      </div>
                                                      <div class="alert-danger" style="text-align:center">{{$errors->first('username')}}</div>
                                                    </div>
                                                  </div>
                                                  
                                              </form>
                                            </div>
                                            <!-- /.box -->
                                            <!-- general form elements disabled -->
                                            
                                          <!--/.col (right) -->
                                        </div>
                                        <!-- /.row -->
                                      </section>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- jQuery 2.2.3 -->

{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
<!-- Bootstrap 3.3.6 -->
{!! HTML::script('bootstrap/js/bootstrap.min.js') !!}
<!-- DataTables -->
{!! HTML::script('plugins/datatables/jquery.dataTables.min.js') !!}
{!! HTML::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
{!! HTML::script('plugins/datepicker/bootstrap-datepicker.js') !!}
<!-- SlimScroll -->
{!! HTML::script('plugins/slimScroll/jquery.slimscroll.min.js') !!}
<!-- FastClick -->
{!! HTML::script('plugins/select2/select2.full.min.js') !!}

{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}

</body>
</html>
@endsection