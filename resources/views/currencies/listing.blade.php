@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>View All Currency</h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('setting.currencies.create') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add"><span class="fa fa-plus">Add</span></a>
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
                  <th>Currency Name</th>
                  <th>Currency Code</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($currencies as $key => $currency)
                  <tr>
                    <td>{{ $currency->name }}</td>
                    <td>{{ $currency->code }}</td>
                    <td>{{ $currency->status == 1 ? 'Active' : 'Inactive' }}</td>
                    <td class="inline-flex">
                      <a href="{{ route('setting.currencies.edit', encrypt($currency->id)) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                      <form method="post" action="{{ route('setting.currencies.destroy', encrypt($currency->id)) }}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-xs ml-5" onclick="return confirm('Are you sure want to Delete {{ $currency->code }}');">
                          <span class="fa fa-trash"></span>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <div class="pagination">
              {{ $currencies->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
  
@endsection