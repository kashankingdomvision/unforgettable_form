@extends('content_layout.default')

  @section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        View All Airline
        <!-- <small>advanced tables</small> -->
      </h1>
      <ol class="breadcrumb">
        <li>
          <a href="{{ route('setting.airlines.create') }}" class="btn btn-primary btn-xs"><span class="fa fa-plus">Add</span></a>
        </li>
      </ol>
    </section>

    <!-- Main content -->
  <section class="content">
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
            <table id="example1" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>#</th>
                <th>Airline Name</th>
                <th>Action</th>
              </tr>
              </thead>
              <tbody>
              @foreach ($airlines as $airline)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $airline->name }}</td>
                    <td class="inline-flex">
                      <a href="{{ route('setting.airlines.edit', encrypt($airline->id)) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                      <form method="post" action="{{ route('setting.airlines.destroy', encrypt($airline->id)) }}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-xs ml-5" onclick="return confirm('Are you sure want to Delete {{ $airline->name }}');">
                          <span class="fa fa-trash"></span>
                        </button>
                      </form>
                    </td>
                </tr>
              @endforeach
              </tbody>
            </table>
            <div class="pagination">
              {{ $airlines->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection