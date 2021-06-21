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
    <td>{{ $quote->season->name }}</td>
    <td>{{ $quote->type_of_holidays }}</td>
    <td>{{ $quote->brand_name }}</td>
    <td>{{ $quote->sale_person }}</td>
  <td>{{ $quote->currency }}</td>
  <td>{{ $quote->group_no }}</td>
      <td>{!! $quote->booking_formated_status !!}</td>
      <td>{{ $quote->qoute_to_booking_date??NULL }}</td>
    <td width="10%">
    <a href="{{ URL::to('edit-quote/'.$quote->id)}}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class="fa fa-pencil"></span></a>

    <a onclick="return confirm('Are you sure you want to convert this Quotation to Booking?');" href="{{ route('convert-quote-to-booking', $quote->id) }}" class="btn btn-success btn-xs" data-title="Delete" data-target="#delete"><span class="fa fa-check"></span></a>
    {{-- <a href="{{ URL::to('confirm-booking/'.$quote->id)}}" class="btn btn-primary btn-xs" data-title="Edit" data-target="#edit"><span class=""></span>Booking</a> --}}
    
    <a onclick="return confirm('Are you sure want to Delete {{ $quote->ref_no }}');" href="{{ route('delete-quote', encrypt($quote->id)) }}" class="btn btn-danger btn-xs" data-title="Delete" data-target="#delete"><span class="fa fa-trash"></span></a>
    </td>
</tr>
@endforeach