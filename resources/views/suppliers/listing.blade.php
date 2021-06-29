@extends('content_layout.default')

  @section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1> View All Suppliers</h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-xs" ><span class="fa fa-plus">Add</span></a>
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
            <table  class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>#</th>
                <th>Supplier Name</th>
                <th>Supplier Email</th>
                <th>Supplier Phone</th>
                <th>Supplier Currency</th>
                <th>Action</th>
              </tr>
              </thead>
              <tbody>
              @foreach ($suppliers as $key => $supplier)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $supplier->name }}</td>
                  <td>{{ $supplier->email }}</td>
                  <td>{{ $supplier->phone }}</td>
                  <td>{{ $supplier->getCurrency->name??NULL }}</td>
                  <td class="inline-flex">
                    <a href="{{ route('suppliers.edit', encrypt($supplier->id)) }}" class="btn btn-primary btn-xs " data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                    <a href="{{ route('suppliers.show', encrypt($supplier->id)) }}" class="btn btn-primary btn-xs ml-5" data-title="Details" data-target="#details"><span class="fa fa-eye"></span></a>
                    <form method="post" action="{{ route('suppliers.destroy', encrypt($supplier->id)) }}">
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
              {{ $suppliers->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
 
@endsection