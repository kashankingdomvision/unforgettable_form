@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>View All Roles</h1>
    <ol class="breadcrumb">
        <li> <a href="{{ route('roles.create') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add"><span class="fa fa-plus">Add</span></a> </li>
    </ol>
  </section>>
  <section class="content">
    <div class="row">
      <div class="col-xs-6 col-md-offset-3">
        @if (Session::has('success_message'))
            <div class="alert alert-success" style="text-align: center;">{{ Session::get('success_message') }}
            </div>
        @endif
        @if (Session::has('error_message'))
            <div class="alert alert-danger" style="text-align: center;">{{ Session::get('error_message') }}
            </div>
        @endif
      </div>
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Role Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $key => $role)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-transform: capitalize;">{{ $role->name }}</td>
                        <td class="inline-flex">
                          <a href="{{ route('roles.edit', encrypt($role->id)) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                          <form method="post" action="{{ route('roles.destroy', encrypt($role->id)) }}">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger btn-xs ml-5" onclick="return confirm('Are you sure want to Delete {{ $role->name }}');">
                              <span class="fa fa-trash"></span>
                            </button>
                          </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
          </table>
          <div class="pagination">
            {{ $roles->links() }}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
  
    <!-- jQuery 2.2.3 -->

    {{-- {!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
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
        $(function() {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script> --}}
    </body>

    </html>
@endsection
