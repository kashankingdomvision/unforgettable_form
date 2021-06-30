@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>View Manual Rate</h1>
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
                <th>From</th>
                <th>To</th>
                <th>Live Rate</th>
                <th>Munaul Rate</th>
                <th>Action</th>
              </tr>
              </thead>
              <tbody>
                @foreach ($currency_conversions as $key => $value)
                  <tr>
                    <td>{{ $value->from }}</td>
                    <td>{{ $value->to }}</td>
                    <td>{{ $value->value }}</td>
                    <td>{{ $value->manual_rate }}</td>
                    <td>
                      <a href="{{ route('update-manual-rate', $value->id ) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <div class="pagination">
              {{ $currency_conversions->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

@endsection