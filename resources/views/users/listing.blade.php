@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>View All User</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-xs" ><span class="fa fa-plus">Add</span></a>
        </li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
          @if (Session::has('success_message'))
              <div class="alert alert-success" style="text-align: center;">{{ Session::get('success_message') }}
              </div>
          @endif
          @if (Session::has('error_message'))
              <div class="alert alert-danger" style="text-align: center;">{{ Session::get('error_message') }}
              </div>
          @endif
      </div>
    </div>
    <div class="row"><!-- /.row -->
      <div class="col-md-12"><!-- /.col --> 
        <div class="box"><!-- /.box -->
          <div class="box-body"><!-- box-body -->
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>User Role</th>
                        <th>Email</th>
                        <th>Default Currency</th>
                        <th>Default Brand</th>
                        <th>Supervisor</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key => $value)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $value->name }}</td>
                            <td style="text-transform: capitalize;">{{ $value->getRole->name ?? null }}
                            </td>
                            <td>{{ $value->email }}</td>
                            <td>{{ !empty($value->getCurrency->code) && !empty($value->getCurrency->name) ? $value->getCurrency->code . ' - ' . $value->getCurrency->name : null }}
                            </td>
                            <td>{{ $value->getBrand->name ?? null }}</td>
                            <td>{{ $value->getSupervisor->name ?? null }}</td>
                            <td class="inline-flex">
                                <a href="{{ route('users.edit', encrypt($value->id)) }}" class="btn btn-primary btn-xs " data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                              <form method="post" action="{{ route('users.destroy', encrypt($value->id)) }}">
                                @csrf
                                @method('delete')
                                <button class="btn btn-danger btn-xs ml-5">
                                  <span class="fa fa-trash"></span>
                                </button>
                              </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">
              {{ $users->links() }}
            </div>
          </div><!-- /.box-body -->
        </div><!-- /.box -->
      </div><!-- /.col -->
    </div><!-- /.row -->
  </section>
  <!-- /.Main content end -->
</div>
    <!-- ./wrapper -->

@endsection
