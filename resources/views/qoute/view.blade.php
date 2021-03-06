@extends('content_layout.default')

@section('content')

<style rel="styleSheet">
td.details-control {
    background: url('../resources/details_open.png') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('../resources/details_close.png') no-repeat center center;
}

</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>View Qoute</h1>
        <ol class="breadcrumb">
            <li>
              <a href="{{ route('creat-quote') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add"><span class="fa fa-plus">Add</span></a>
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
            <div class="box-body" style="overflow-x:auto;">
            <table id="example1" class="table table-bordered  table-striped" >
                <thead>
                  <tr>
                    {{-- <th>#</th> --}}
                    <th></th>
                    <th>Zoho Reference</th>
                    <th>Quote Reference</th>
                    <th>Season</th>
                    <th>Type Of Holidays</th>
                    <th>Brand Name</th>
                    <th>Sales Person</th>
                    <th>Booking Currency</th>
                    <th>Pax No.</th>
                    <th>Status</th>
                    <th>Booking Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                @foreach ($quotes as $key => $quote)
                    <tr style="{{ $quote->quote_count > 1 ? 'background-color: #f9f9f9;' : null}}">
                      <td>
                          @if($quote->quote_count > 1)
                          <button class="btn btn-sm addChild" id="show{{$quote->id}}" data-remove="#remove{{$quote->id}}" data-append="#appendChild{{$quote->id}}" data-ref="{{ $quote->ref_no }}" data-id="{{$quote->id}}">
                            <span class="fa fa-plus"></span>
                          </button>
                          
                          <button class="btn btn-sm removeChild" id="remove{{$quote->id}}" data-show="#show{{$quote->id}}" data-append="#appendChild{{$quote->id}}" data-ref="{{ $quote->ref_no }}" data-id="{{$quote->id}}" style="display:none;" >
                            <span class="fa fa-minus"></span>
                          </button>
                          @endif
                        </td>
                        <td>{{ $quote->ref_no }}</td>
                        <td>{{ $quote->quotation_no }}</td>
                        <td>{{ $quote->season->name }}</td>
                        <td>{{ (isset($quote->getHolidayType->name))? $quote->getHolidayType->name:NULL }}</td>
                        <td>{{ (isset($quote->getBrand->name))? $quote->getBrand->name: NULL }}</td>
                        <td>{{ $quote->sale_person }}</td>
                        <td>{{ !empty($quote->getCurrency->code) && !empty($quote->getCurrency->name) ? $quote->getCurrency->code.' - '.$quote->getCurrency->name : NULL }}</td>
                        <td>{{ $quote->group_no }}</td>
                        <td>{!! $quote->booking_formated_status !!}</td>
                        <td>{{ !empty($quote->qoute_to_booking_date) ? date('d/m/Y', strtotime($quote->qoute_to_booking_date)) : '' }}</td>
                        
                        <td width="10%" >
                          @if($quote->qoute_to_booking_status == 0)
                            <a href="{{ URL::to('edit-quote/'.$quote->id)}}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                            <a onclick="return confirm('Are you sure you want to convert this Quotation to Booking?');" href="{{ route('convert-quote-to-booking', $quote->id) }}" class="btn btn-success btn-xs" data-title="" data-target="#"><span class="fa fa-check"></span></a>
                          @endif

                          
                          @if($quote->qoute_to_booking_status == 1)
                          <a target="_blank" href="{{ route('view-quote-detail', $quote->id) }}" class="btn btn-primary btn-xs" data-title="Delete" data-target="#delete"><span class="fa fa-eye"></span></a>
                          @endif
                          
                          <a onclick="return confirm('Are you sure want to Delete {{ $quote->ref_no }}');" href="{{ route('delete-quote', encrypt($quote->id)) }}" class="btn btn-danger btn-xs" data-title="Delete" data-target="#delete"><span class="fa fa-trash"></span></a>

                        </td>
                        <tbody class="append" id="appendChild{{$quote->id}}" style="{{ $quote->quote_count > 1 ? 'background-color: #f9f9f9;' : null}}">
                          
                        </tbody>
                    </tr>
                @endforeach
                </tbody>
              </table>
            
            
            
            </div>
        </div>
        </div>
    </section>
</div>
<footer class="main-footer">
    <div class="pull-right hidden-xs">
      <!-- <b>Version</b> 2.3.7 -->
    </div>
    {{-- Copyright ?? 2017-2018 Almuftionline .Design & Developed by <a href="http://www.webfluorescent.com//">WebFluorescent </a> --}}
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
<!-- jQuery 2.2.3 -->

{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
<!-- Bootstrap 3.3.6 -->
{!! HTML::script('bootstrap/js/bootstrap.min.js') !!}
<!-- DataTables -->
{!! HTML::script('plugins/datatables/jquery.dataTables.min.js') !!}
{!! HTML::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
<!-- SlimScroll -->
{!! HTML::script('plugins/slimScroll/jquery.slimscroll.min.js') !!}
<!-- FastClick -->
{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}
<!-- page script -->
<script>

// function getChild(refNumber, id) {
//   console.log(id);
//   $('.appendChild').empty();
//   var url = '{{ route("get.child.reference", ":id") }}';
//       url = url.replace(':id', refNumber);
//       token = $('input[name=_token]').val();
//   $.ajax({
//       url:  url,
//       headers: {'X-CSRF-TOKEN': token},
//       data: {id: id},
//       type: 'get',
//       success: function(response) {
//         console.log(response);
//         $('.appendChild').append(response);
//       }
//   });
// }
  $(function () {
    
    $(document).on('click', '.removeChild', function () {
      var id = $(this).data('show');
      $(id).removeAttr("style");
      $($(this).data('append')).empty();
      $(this).attr("style", "display:none");
    });
    $(document).on('click', '.addChild', function () {
      $('.append').empty();
   
      
      var id = $(this).data('id');
      var refNumber = $(this).data('ref');
      var appendId  = $(this).data('append');
      console.log(appendId);
      var url = '{{ route("get.child.reference", ":id") }}';
      url = url.replace(':id', refNumber);
      
      var removeBtnId =$(this).data('remove');
      var showBtnId = $(this).data('show');
      $('.addChild').removeAttr("style");
      $('.removeChild').attr("style", "display:none");

      $(this).attr("style", "display:none")
      // $(appendId).empty();
      
      token = $('input[name=_token]').val();
      $.ajax({
          url:  url,
          headers: {'X-CSRF-TOKEN': token},
          data: {id: id},
          type: 'get',
          success: function(response) {
            $(appendId).html(response);
            $(removeBtnId).removeAttr("style");
          }
      });
    });
    
    $("#example1").DataTable({
      // createdRow: function(row) {
      //   $(row).find('td table')
      //     .DataTable({
      //       columns: columns,
      //       dom: 'td'
      //     })
      // }
      "order": [[ 2, "desc" ]]
    });
    
    
    
    $('#example1 tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
    
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>
</body>
</html>
@endsection