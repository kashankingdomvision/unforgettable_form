@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>View All Booking Method</h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('setting.booking_methods.create') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add"><span class="fa fa-plus">Add</span></a>
      </li>
    </ol>
  </section>
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
                <th>Booking Method</th>
                <th>Action</th>
              </tr>
              </thead>
              <tbody>
              @foreach ($booking_methods as  $booking_method)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td style="text-transform: capitalize;">{{ $booking_method->name }}</td>
                  <td class="inline-flex">
                    <a href="{{ route('setting.booking_methods.edit', encrypt($booking_method->id)) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                    <form method="post" action="{{ route('setting.booking_methods.destroy', encrypt($booking_method->id)) }}">
                      @csrf
                      @method('delete')
                      <button class="btn btn-danger btn-xs ml-5" onclick="return confirm('Are you sure want to Delete {{ $booking_method->name }}');">
                        <span class="fa fa-trash"></span>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
            <div class="pagination">
              {{ $booking_methods->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection