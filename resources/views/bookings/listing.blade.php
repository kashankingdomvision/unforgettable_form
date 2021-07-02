@extends('content_layout.default')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
      <h1>View All Bookings</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3> Total Records ({{$bookings->total()}})</h3>
              <div class="col-xs-4" style="padding-left:0;">  
                <div class="dropdown">
                  <select name="action" id="action" class="form-control" style="width:30%;">
                     <option booking="0">Action</option>
                     <option booking="delete">Delete</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="box-body" style="overflow-x: scroll;">
             <table id="example1" class="table table-bordered table-striped bookingsTable no-footer" role="grid">
                <thead>
                    <tr>
                        <th style="width:1%">
                            <input id="select_all" type='checkbox' name="" booking="" />
                        </th>
                        <th>Zoho Ref #</th>
                        <th>Quote Ref #</th>
                        <th>Lead Passenger</th>
                        <th>Brand</th>
                        <th>Type Of Holidays</th>
                        <th>Sales Person</th>
                        <th>Agency Booking</th>
                        <th>Booking Currency</th>
                        <th>Pax No.</th>
                        <th>Dinning Preferences</th>
                        <th>Bedding Preferences</th>
                        <th>Transfer Info Responsible Person</th>
                        <th>Transfer Organized Responsible Person</th>
                        <th>Itinerary Finalised Responsible Person</th>
                        <th>Travel Document Prepared Responsible Person</th>
                        <th>Document Sent Responsible Person</th>
                        <th>Asked For Transfer</th>
                        <th>Transfer Organised</th>
                        <th>Itinerary Finalised</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                    <tr>
                        <td>
                            <input class="checkbox multi_val" type='checkbox' name='multi_val[]' booking="<?php echo "$booking->id";?>" />
                        </td>
                        <td>{{$booking->ref_no}}</td>
                        <td>{{$booking->quote_ref}}</td>
                        <td>{{$booking->lead_passenger}}</td>
                        <td>{{$booking->getBrand->name??NULL}}</td>
                        <td>{{$booking->getHolidayType->name??NULL}}</td>
                        <td>{{$booking->sale_person}}</td>
                        <td>{{$booking->agency_booking == 1 ? 'No' : 'Yes'}}</td>
                        <td>{{ !empty($booking->getCurrency->code) && !empty($booking->getCurrency->name) ? $booking->getCurrency->code.' - '.$booking->getCurrency->name : NULL }}</td>
                        <td>{{$booking->pax_no}}</td>
                        <td>{{$booking->dinning_preference}}</td>
                        <td>{{$booking->bedding_preference}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="inline-flex">
                          <a href="{{ route('bookings.edit', encrypt($booking->id)) }}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
                          <form method="post" action="{{ route('bookings.destroy', encrypt($booking->id)) }}">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger btn-xs ml-5" onclick="return confirm('Are you sure want to Delete {{ $booking->name }}');">
                              <span class="fa fa-trash"></span>
                            </button>
                          </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
              <div style="float:right">
                {{ $bookings->links() }}
              </div>
              <div style="margin-top:30px">
                Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} entries
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>
 

{!! HTML::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
<!-- Bootstrap 3.3.6 -->
{!! HTML::script('bootstrap/js/bootstrap.min.js') !!}
<!-- DataTables -->
{!! HTML::script('plugins/daterangepicker/moment.min.js') !!}
{!! HTML::script('plugins/daterangepicker/daterangepicker.js') !!}

{!! HTML::script('plugins/bookingstables/jquery.bookingsTables.min.js') !!}
{!! HTML::script('plugins/bookingstables/bookingsTables.bootstrap.min.js') !!}
<!-- SlimScroll -->
{!! HTML::script('plugins/slimScroll/jquery.slimscroll.min.js') !!}
<!-- FastClick -->
{!! HTML::script('plugins/select2/select2.full.min.js') !!}
{!! HTML::script('plugins/fastclick/fastclick.js') !!}
<!-- AdminLTE App -->
{!! HTML::script('dist/js/app.min.js') !!}
<!-- AdminLTE for demo purposes -->
{!! HTML::script('dist/js/demo.js') !!}
<!-- page script -->
<script>
 $("#select_all").change(function(){  //"select all" change 
      var status = this.checked; // "select all" checked status
      $('.checkbox').each(function(){ //iterate all listed checkbox items
          this.checked = status; //change ".checkbox" checked status
      });
  });
 document.getElementById('action').onchange = function(){
   var id = this.booking;
   if(id != 0){
     $('#set_action').val(id);
     if($('.multi_val:checkbox:checked').length == 0){
        alert('Please select at least one checkbox')
        return false;
     }
     $('#del-multi-booking').submit();
   }
 };

</script>
@endsection