@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
      <h1> View All Booking Season </h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-xs-6 col-xs-offset-3">
          @if(Session::has('success_message'))
              <div style="text-align: center;" class="alert alert-success">{{Session::get('success_message')}}</div>
          @endif
          @if(Session::has('error_message'))
              <div style="text-align: center;" class="alert alert-danger">{{Session::get('error_message')}}</div>
          @endif
        </div>  
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>name</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($seasons as $season)
                    <tr>
                            <td><a href="{{ route('bookings.index', encrypt($season->id)) }}" class="btn btn-primary btn-xs" data-title="View" data-target="#view">{{ $season->name }}</a></td>
                            <td class="inline-flex">
                                <form method="post" action="{{ route('setting.airlines.destroy', encrypt($season->id)) }}">
                                  @csrf
                                  @method('delete')
                                  <button class="btn btn-danger btn-xs ml-5" onclick="return confirm('Are you sure want to Delete {{ $season->name }}');">
                                    <span class="fa fa-trash"></span>
                                  </button>
                                </form>
                              </td>
                        </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>

@endsection