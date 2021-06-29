@extends('content_layout.default')

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Supplier Details</h1>
      <ol class="breadcrumb">
        <li>
          <a href="{{ route('suppliers.index') }}" class="btn btn-primary btn-sm" style="color: white;">View All Supplier</a>
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
              <table class="table">
                <tbody>
                  <tr>
                    <th class="th">Supplier Name: </th>
                    <td>{{ $supplier->name }}</td>
                  </tr>
                  
                  <tr>
                    <th class="th">Supplier Email: </th>
                    <td>{{ $supplier->email }}</td>
                  </tr>
                  
                  <tr>
                    <th class="th"> Supplier Phone: </th>
                    <td>{{ $supplier->phone }}</td>
                  </tr>
                  
                  <tr>
                    <th class="th">Supplier Currency: </th>
                    <td>{{ (isset($supplier->getCurrency))? $supplier->getCurrency->name : NULL }}</td>
                  </tr>
                  
                  <tr>
                    <th class="th"> Supplier Categories: </th>
                    <td class="td">
                        @foreach ($supplier->getCategories as $cate)
                        <span class="badge badge-pill badge-primary mt-2 mb-2">{{ $cate['name'] }}</span>  
                        @endforeach
                    </td>
                  </tr>
                  <tr>
                    <th class="th"> Supplier Products: </th>
                    <td>
                        @foreach ($supplier->getProducts as $prod)
                        <span class="badge badge-pill badge-primary mt-2 mb-2">{{ $prod['name'] }}</span>  
                        @endforeach
                    </td>
                  </tr>
                  
                </tbody>
              </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection