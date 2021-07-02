<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Season;
use Cache;
use App\User;
use App\Booking;
use App\PaymentMethod;
use App\Airline;
use App\Brand;
use App\HolidayType;
use App\Currency;
use App\Category;
use App\Supplier;
use App\BookingMethod;
;
class BookingController extends Controller
{
    public $pagination = 10;
    
    public function season_Index()
    {
        $data['seasons'] = Season::orderBy('seasons.created_at', 'desc')->groupBy('seasons.id', 'seasons.name')->get(['seasons.id', 'seasons.name']);
        return view('bookings.season_listing', $data);
    }
    
    private function curl_data($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return $output = curl_exec($ch);
    }
    
    public function delete_booking_season($id)
    {
        Season::destroy(decrypt($id));
        return Redirect::route('view-booking-season')->with('success_message', 'Deleted Successfully');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $season = Season::findOrFail(decrypt($id));
        $data['bookings'] = $season->getBooking()->paginate($this->pagination);
        return view('bookings.listing', $data);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $book  =Booking::findOrFail(decrypt($id));
        $data['booking']           = Booking::findOrFail(decrypt($id));
        $data['users']             = User::orderBy('name', 'asc')->get();
        $data['seasons']           = Season::get();
        $data['currencies']        = Currency::where('status', 1)->get();
        $data['categories']        = Category::get();
        $data['suppliers']         = Supplier::get();
        $data['booking_methods']   = BookingMethod::get();
        $data['payment_method']          = PaymentMethod::get();
        $data['brands']            = Brand::orderBy('id','ASC')->get();
        $data['booking_logs']      = [];
        $data['supervisors']       = User::whereHas('getRole', function($query){
                                            $query->where('slug', 'supervisor');
                                        });
        $data['holiday_types']     = HolidayType::get();

        return view('bookings.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $booking  = Booking::findOrFail(decrypt($id));
       dd($booking, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Booking::destroy(decrypt($id));
        return redirect()->route('categories.index')->with('success_message', 'Booking deleted successfully'); 
    }
}
