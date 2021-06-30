@extends('content_layout.default')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1> View All Brands</h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('setting.brands.create') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add"><span class="fa fa-plus">Add Brand</span></a>
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
                  <th>Brand Name</th>
                  <th>Brand Email Address</th>
                  <th>Brand Address</th>
                  <th>Brand Phone Number</th>
                  <th>Brand Logo</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($brands as $key => $brand)
                  <tr>
                    <td>{{ $brand->name }}</td>
                    <td>{{ $brand->email }}</td>
                    <td>{{ $brand->address  }}</td>
                    <td>{{ $brand->phone  }}</td>
                    <td>@if($brand->logo)<img src="{{ $brand->logo  }}" width="30px" height="30px" alt="brand logo" /> @endif</td>
                    <td class="inline-flex">
                      <a href="{{ route('setting.brands.edit', encrypt($brand->id)) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                      <form method="post" action="{{ route('setting.brands.destroy', encrypt($brand->id)) }}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-xs ml-5" onclick="return confirm('Are you sure want to Delete {{ $brand->name }}');">
                          <span class="fa fa-trash"></span>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <div class="pagination">
              {{ $brands->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection