@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1> View All Holiday Types </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('setting.holidaytypes.create') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add"><span class="fa fa-plus">Add Holiday Type</span></a>
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
            <table id="example1" class="table table-bordered">
              <thead>
                <tr>
                  <th>Holiday Type</th>
                  <th>Brand</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($holiday_types as $holiday)
                  <tr>
                    <td>{{ $holiday->name }}</td>
                    <td>{{ $holiday->getBrand->name??NULL }}</td>
                    <td class="inline-flex">
                      <a href="{{ route('setting.holidaytypes.edit', encrypt($holiday->id)) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                      <form method="post" action="{{ route('setting.holidaytypes.destroy', encrypt($holiday->id)) }}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-xs ml-5" onclick="return confirm('Are you sure want to Delete {{ $holiday->name }}');">
                          <span class="fa fa-trash"></span>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <div class="pagination">
              {{ $holiday_types->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection