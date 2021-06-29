@extends('content_layout.default')

  @section('content')
  <div class="content-wrapper">
  <section class="content-header">
    <h1>View All Season</h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('seasons.create') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add"><span class="fa fa-plus">Add</span></a>
      </li>
    </ol>
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
                  <th>#</th>
                  <th>Season Name</th>
                  <th>Season Start Date</th>
                  <th>Season End Date</th>
                  <th>Action</th>
              </tr>
              </thead>
              <tbody>
              @foreach ($seasons as $season)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $season->name }} 
                      @if ($season->default == '1')
                          <span class="btn btn-primary badge ml-5">default</span>
                      @endif
                  </td>
                  <td>{{date('d/m/Y', strtotime($season->start_date))}}</td>
                  <td>{{date('d/m/Y', strtotime($season->end_date))}}</td>
                  <td class="inline-flex">
                    <a href="{{ route('seasons.edit', encrypt($season->id)) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                    <form method="post" action="{{ route('seasons.destroy', encrypt($season->id)) }}">
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