@extends('content_layout.default')

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Add New Fatwa
      </h1>
      <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol> -->
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
            <div class="box-header with-border">
              <h3 class="box-title">Create Fatwa</h3>
            </div>
            
            <!-- /.box-header -->
            <!-- form start -->
            @if($data=='')  
              {!! Form::open(array('route'=>'add-fatwa','class'=>'form-horizontal','id'=>'user_form')) !!}
            @else
              {!! Form::open(array('route'=>'update-fatwa','class'=>'form-horizontal','id'=>'user_form')) !!}
              {!! Form::hidden('id',$data->id) !!}
            @endif
            <div class="col-sm-6 col-sm-offset-3">
              @if(Session::has('success_message'))
             
                  <div class="alert alert-success">{{Session::get('success_message')}}</div>
              
              @endif
             
            </div>  
            <!-- <form class="form-horizontal"> -->
              <div class="box-body">
                
                <!-- <div class="form-group"> -->
                  <div class="col-sm-5 col-sm-offset-1">
                  <label for="inputEmail3" class="">Asker Name</label>
                    <!-- <input type="email" class="form-control" id="inputEmail3" placeholder="Email"> -->
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       <!-- <input name="username" type="email" class="form-control" placeholder="Username"> -->
                       @if($data=='')
                       {!! Form::text('username',null,['class'=>'form-control','placeholder'=>'Asker Name','required'=>'true']) !!}
                       @else
                       {!! Form::text('username',$data->username,['class'=>'form-control','placeholder'=>'Asker Name','required'=>'true']) !!}
                       @endif
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('username')}}</div>
                  </div>
                <!-- </div> -->
                
                <!-- <div class="form-group"> -->
                  <div class="col-sm-5" style="margin-bottom:25px">
                  <label for="inputEmail3" class="">Asker Email</label>
                    <!-- <input type="email" class="form-control" id="inputEmail3" placeholder="Email"> -->
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       <!-- <input name="username" type="email" class="form-control" placeholder="Username"> -->
                       @if($data=='')
                       {!! Form::email('useremail',null,['class'=>'form-control','placeholder'=>'Asker Email']) !!}
                       @else
                       {!! Form::email('useremail',$data->useremail,['class'=>'form-control','placeholder'=>'Asker Email']) !!}
                       @endif
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('useremail')}}</div>
                  </div>
                <!-- </div> -->
                
                <!-- <div class="form-group"> -->
                  <div class="col-sm-5 col-sm-offset-1">
                  <label for="inputEmail3" class="">Asker Contact No</label>
                    <!-- <input type="email" class="form-control" id="inputEmail3" placeholder="Email"> -->
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       <!-- <input name="email" type="email" class="form-control" placeholder="Email"> -->
                       @if($data=='')
                       {!! Form::input('number','contact',null,['class'=>'form-control','placeholder'=>'Asker Contact No']) !!}
                       @else
                       {!! Form::input('number','contact',$data->contact,['class'=>'form-control','placeholder'=>'Asker Contact No']) !!}
                       @endif
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('contact')}}</div>
                  </div>
                <!-- </div> -->
                <!-- <div class="form-group"> -->
                  <div class="col-sm-5" style="margin-bottom:25px">
                  <label for="inputPassword3" class="">Asker Address</label>
                    <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                       @if($data=='')
                       {!! Form::text('address',null,['class'=>'form-control','placeholder'=>'Asker Address']) !!}
                       @else
                       {!! Form::text('address',$data->address,['class'=>'form-control','placeholder'=>'Asker Address']) !!}
                       @endif
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('address')}}</div>
                  </div>
                <!-- </div> -->

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5 col-sm-offset-1">
                  <label for="inputPassword3" class="">Asker Qualification</label>
                    <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                       @if($data=='')
                       {!! Form::text('education',null,['class'=>'form-control','placeholder'=>'Asker Qualification']) !!}
                       @else
                       {!! Form::text('education',$data->education,['class'=>'form-control','placeholder'=>'Asker Qualification']) !!}
                       @endif
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('education')}}</div>
                  </div>
                <!-- </div> -->

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5" style="margin-bottom:25px">
                  <label for="inputPassword3" class="">Asker Occupation</label>
                    <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                       @if($data=='')
                       {!! Form::text('profession',null,['class'=>'form-control','placeholder'=>'Asker Occupation']) !!}
                        @else
                       {!! Form::text('profession',$data->profession,['class'=>'form-control','placeholder'=>'Asker Occupation']) !!}
                       @endif
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('profession')}}</div>
                  </div>
                <!-- </div> -->

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5 col-sm-offset-1">
                  <label for="inputPassword3" class="">Fatwa Date</label>
                    <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                       @if($data=='')
                       {!! Form::input('date','replied_on',null,['class'=>'form-control','placeholder'=>'Fatwa Date','required'=>'true']) !!}
                       @else
                       {!! Form::input('date','replied_on',$data->replied_on,['class'=>'form-control','placeholder'=>'Fatwa Date','required'=>'true']) !!}
                       @endif
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('replied_on')}}</div>
                  </div>
                <!-- </div> -->

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5" style="margin-bottom:25px">
                  <label for="inputPassword3" class="">Fatwa Number</label>
                    <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                       @if($data=='')
                       {!! Form::text('fatwa_number',null,['class'=>'form-control','placeholder'=>'Fatwa Number','required'=>'true']) !!}
                       @else
                       {!! Form::text('fatwa_number',$data->fatwa_number,['class'=>'form-control','placeholder'=>'Fatwa Number','required'=>'true']) !!}
                       @endif
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('fatwa_number')}}</div>
                  </div>
                <!-- </div> -->

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5 col-sm-offset-1">
                  <label for="inputPassword3" class="">Fatwa Title</label>
                    <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                    <div class="input-group">
                       <span class="input-group-addon"></span>
                       <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                       @if($data=='')
                       {!! Form::text('subject',null,['class'=>'form-control','placeholder'=>'Fatwa Title','required'=>'true']) !!}
                       @else
                       {!! Form::text('subject',$data->subject,['class'=>'form-control','placeholder'=>'Fatwa Title','required'=>'true']) !!}
                       @endif
                    </div>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('subject')}}</div>
                  </div>
                <!-- </div> -->

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5" style="margin-bottom:25px">
                    <label class="">Mujeeb</label>
                       <select class="form-control dropdown_value" name="replier" required="required">
                         <option value="">Select Mujeeb</option>
                         @foreach($replier as $repli)
                         <option value="{{ $repli->id }} ">{{ $repli->name }}</option>
                         @endforeach
                       </select>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('replier')}}</div>
                  </div>
                <!-- </div> -->

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5 col-sm-offset-1">
                  <label for="inputPassword3" class="">Question</label>
                    <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                    <!-- <div class="input-group"> -->
                       <!-- <span class="input-group-addon"></span> -->
                       <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                       @if($data=='')
                       {!! Form::textarea('question', null,['class'=>'form-control','placeholder'=>'Question','style'=>'height:80px']) !!}
                       @else
                       {!! Form::textarea('question', $data->question,['class'=>'form-control','placeholder'=>'Question','style'=>'height:80px']) !!}
                       @endif

                    <!-- </div> -->
                    <div class="alert-danger" style="text-align:center">{{$errors->first('question')}}</div>
                  </div>
                <!-- </div> -->
                <div class="col-sm-5" style="margin-bottom:25px">
                <label for="inputPassword3" class="">Answer</label>
                  <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                  <!-- <div class="input-group"> -->
                     <!-- <span class="input-group-addon"></span> -->
                     <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                     @if($data=='')
                     {!! Form::textarea('answer', null,['class'=>'form-control','placeholder'=>'Answer','style'=>'height:80px']) !!}
                     @else
                     {!! Form::textarea('answer', $data->answer,['class'=>'form-control','placeholder'=>'Answer','style'=>'height:80px']) !!}
                     @endif
                  <div class="alert-danger" style="text-align:center">{{$errors->first('answer')}}</div>
                  <!-- </div> -->
                </div>

                <div class="col-sm-10 col-sm-offset-1">
                <label for="inputPassword3" class="">References</label>
                  <!-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password"> -->
                  <!-- <div class="input-group"> -->
                     <!-- <span class="input-group-addon"></span> -->
                     <!-- <input name="password" type="password" class="form-control" placeholder="Password"> -->
                     @if($data=='')
                     {!! Form::textarea('references', null,['class'=>'form-control','placeholder'=>'References','style'=>'height:80px']) !!}
                     @else
                     {!! Form::textarea('references', $data->references,['class'=>'form-control','placeholder'=>'References','style'=>'height:80px']) !!}
                     @endif
                    <div class="alert-danger" style="text-align:center">{{$errors->first('references')}}</div>
                  <!-- </div> -->
                </div>

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5 col-sm-offset-1">
                      <label class="">Book</label>
                         <select class="form-control dropdown_value" name="book" required="required">
                           <option value="">Select book</option>
                           @foreach($book as $bok)
                           <option value="{{ $bok->id }} ">{{ $bok->title }}</option>
                           @endforeach
                         </select>
                        <div class="alert-danger" style="text-align:center">{{$errors->first('book')}}</div>
                  </div>
                <!-- </div> -->

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5" style="margin-bottom:25px">
                    <label class="">Chapter</label>
                       <select class="form-control dropdown_value" name="chapter" required="required">
                         <option value="">Select Chapter</option>
                       </select>
                    <div class="alert-danger" style="text-align:center">{{$errors->first('chapter')}}</div>
                  </div>
                <!-- </div> -->

                <!-- <div class="form-group"> -->
                  <div class="col-sm-5 col-sm-offset-1">
                  <label class="">Mufti</label>
                     <select class="form-control select2" multiple="multiple" data-placeholder="Select Mufti" name="muftis_id[]" required="required" style="width: 100%;">
                       @foreach($mufti as $muft)
                       <option value="{{ $muft->id }} ">{{ $muft->name }}</option>
                       @endforeach
                     </select>
                     <div class="alert-danger" style="text-align:center">{{$errors->first('muftis_id')}}</div>
                  </div>
                <!-- </div> -->


                <!-- <div class="form-group"> -->
                  <div class="col-sm-5" style="margin-bottom:25px">
                    <label class="">Topic</label>
                       <select class="form-control select2" multiple="multiple" data-placeholder="Select category" name="categories_id[]" style="width: 100%;">
                         <option value="">Select category</option>
                          @foreach($categories as $categorie)
                          <option value="{{ $categorie->id }} ">{{ $categorie->title }}</option>
                          @endforeach 
                       </select>
                      <div class="alert-danger" style="text-align:center">{{$errors->first('categories_id')}}</div>
                  </div>
                <!-- </div> -->
                
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <!-- <button type="submit" class="btn btn-info pull-right">Sign in</button> -->
                {!! Form::submit('submit',['class'=>'btn btn-info pull-right']) !!}
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
      <!-- <b>Version</b> 2.3.7 -->
    </div>
    {{-- Copyright © 2017-2018 Almuftionline .Design & Developed by <a href="http://www.webfluorescent.com//">WebFluorescent </a> --}}
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

{!! HTML::script('plugins/select2/select2.full.min.js') !!}

<!-- FastClick -->
{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()
  })
</script>

<script type="text/javascript">
  function submitForm(btn) {
      // disable the button
      btn.disabled = true;
      // submit the form    
      btn.form.submit();
  }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('select[name="book"]').on('change', function() {
            book_id = $(this).val();
            if(book_id) {
              token = $('input[name=_token]').val();
              data  = {id: book_id};
              url   = '{{route('get-chapter')}}';
                $.ajax({
                    url: url,
                    headers: {'X-CSRF-TOKEN': token},
                    data : data,
                    type: 'POST',
                    dataType: "json",
                    success:function(data) {
                        $('select[name="chapter"]').empty();
                        $.each(data.item_rec, function(key, value) {
                            $('select[name="chapter"]').append('<option value="'+ value.id +'">'+ value.title +'</option>');
                        });
                    }
                });
            }else{
                $('select[name="chapter"]').empty();
            }
        });
    });
</script>

</body>
</html>
@endsection