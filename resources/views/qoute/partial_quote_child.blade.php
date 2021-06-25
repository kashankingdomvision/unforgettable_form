@foreach ($quotes as $key => $quote)
<tr>
  <td>
      @if($quote->quote_count > 1)
      <button class="btn btn-sm" onclick="getChild('{{ $quote->ref_no }}', '{{ $quote->id }}')">
        <span class="fa fa-plus"></span>
      </button>
      @endif
    </td>
  
    <td>{{ $quote->ref_no }}</td>
    <td>{{ $quote->quotation_no }}</td>
    <td>{{ $quote->season->name }}</td>
    <td>{{ (isset($quote->getHolidayType->name))? $quote->getHolidayType->name:NULL }}</td>
    <td>{{ (isset($quote->getBrand->name))? $quote->getBrand->name: NULL }}</td>
    <td>{{ $quote->sale_person }}</td>
  <td>{{ $quote->currency }}</td>
  <td>{{ $quote->group_no }}</td>
      <td>{!! $quote->booking_formated_status !!}</td>
      <td>{{ !empty($quote->qoute_to_booking_date) ? date('d/m/Y', strtotime($quote->qoute_to_booking_date)) : '' }}</td>

    <td width="10%">
      @if($quote->qoute_to_booking_status == 0)
        <a href="{{ URL::to('edit-quote/'.$quote->id)}}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>
        <a onclick="return confirm('Are you sure you want to convert this Quotation to Booking?');" href="{{ route('convert-quote-to-booking', $quote->id) }}" class="btn btn-success btn-xs" data-title="Delete" data-target="#delete"><span class="fa fa-check"></span></a>
      @endif
      
      @if($quote->qoute_to_booking_status == 1)
      <a target="_blank" href="{{ route('view-quote-detail', $quote->id) }}" class="btn btn-primary btn-xs" data-title="Delete" data-target="#delete"><span class="fa fa-eye"></span></a>
      @endif
    {{-- <a href="{{ URL::to('confirm-booking/'.$quote->id)}}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class=""></span>Booking</a> --}}
    
    <a onclick="return confirm('Are you sure want to Delete {{ $quote->ref_no }}');" href="{{ route('delete-quote', encrypt($quote->id)) }}" class="btn btn-danger btn-xs" data-title="Delete" data-target="#delete"><span class="fa fa-trash"></span></a>
    </td>
</tr>
@endforeach