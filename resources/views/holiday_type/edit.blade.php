@extends('content_layout.default')

@section('content')

    <style>
        .mb-2 {
            margin-bottom: 2rem;
        }

    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> 
                Edit Holiday Type
            </h1>
            
            <ol class="breadcrumb">
              <li>
                <a href="{{ route('holidaytype.index') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add">View Holiday Types</a>
              </li>
            </ol>
        </section>

        <section class="content">
            <div class="row">

                <div class="col-md-12">
                    <div class="box box-info">

              <form action="{{ route('holidaytype.update', encrypt($holidayType->id)) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                @csrf
                
                <div class="col-sm-6 col-sm-offset-3">
                  @if (Session::has('success_message'))
                  <li>
                      <div class="alert alert-success">{{ Session::get('success_message') }}</div>
                  </li>
                  @endif
                </div>  
                <!-- <form class="form-horizontal"> -->
                <div class="box-body">
                  <div class="form-group">
                      <div class="col-sm-6 col-sm-offset-3 " >
                          <label for="brandName" class=""> Holiday Type Name <span class="text-danger">*</span></label>
                          <input type="text" name="name" class="form-control" placeholder="Enter the holiday type name" value="{{ old('name')??$holidayType->name }}" id="brandName" />
                          <div class="alert-danger" style="text-align:center">{{ $errors->first('name') }}</div>
                      </div>
                  </div>
              
                  <div class="form-group">
                      <div class="col-sm-6 col-sm-offset-3 " >
                          <label for="brandEmail" class=""> Select Brand <span class="text-danger">*</span> </label>
                          <select class="form-control select2" name="brand_id">
                              <option value="" selected disabled>Select Brand </option>
                              @foreach ($brands as $brand)
                                  <option value="{{$brand->id}}"  {{ ($holidayType->brand_id == $brand->id)? 'selected': ((old('brand_id') == $brand->id) ? 'selected' : '') }}> {{$brand->name}} </option>
                              @endforeach
                          </select>
                          <div class="alert-danger" style="text-align:center">{{ $errors->first('brand_id') }}</div>
                      </div>
                  </div>
                </div>
                  <!-- /.box-body -->
                  <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right">Update</button> 
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
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>

    </body>
    </html>
@endsection
