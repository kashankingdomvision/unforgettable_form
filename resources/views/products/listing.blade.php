@extends('content_layout.default')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1> View All Products </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-xs" data-title="Add" data-target="#Add"><span class="fa fa-plus">Add</span></a>
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
                  <th style="width: 5rem;">#</th>
                  <th style="width: 10rem;">Product Code</th>
                  <th style="width: 13rem;">Product Name</th>
                  <th>Product Description</th>
                  <th style="width: 10rem;">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($products as $key => $product)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->code }}</td>
                    <td style="text-transform: capitalize;">{{ $product->name }}</td>
                    <td id="descript_{{$product->id}}">{{ Str::limit($product->description, 100, '...')}} @if(Str::length($product->description) > 100)<button data-value="{{ $value->description }}" data-id="#descript_{{$value->id}}" class="readmore text-dark btn-link">read more</button> @endif</td>
                    <td class="inline-flex">
                      <a href="{{ route('products.edit', encrypt($product->id)) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                      <form method="post" action="{{ route('products.destroy', encrypt($product->id)) }}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-xs ml-5" onclick="return confirm('Are you sure want to Delete {{ $product->name }}');">
                          <span class="fa fa-trash"></span>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <div class="pagination">
              {{ $products->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection