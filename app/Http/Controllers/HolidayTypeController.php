<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use App\HolidayType;

class HolidayTypeController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $holiday = HolidayType::get();
        $data =  [];
        foreach ($holiday as $key => $holi) {
            $x = [
                'name'      => $holi->name,
                'brand'     => $holi->getBrand->name??NULL,
                'created'   => $holi->created_at??now(),
                'id'        => $holi->id,
            ];
            array_push($data, $x);
        }
        
        $result['holiday_types'] = $data;
        
        
        return view('holiday_type.view', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['brands'] = Brand::get();
        return view('holiday_type.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string'], ['required' => 'Holiday type name is required ']);
        $request->validate(['brand_id' => 'required|string'], ['required' => 'Brand is required ']);
        HolidayType::create($request->all());
        return redirect()->route('holidaytype.index')->with('success_message', 'Holiday type created successfully'); 

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['holidayType'] = HolidayType::findOrFail(decrypt($id));
        $data['brands'] = Brand::get();
        return view('holiday_type.edit', $data);
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
        $request->validate(['name' => 'required|string'], ['required' => 'Holiday type name is required ']);
        $request->validate(['brand_id' => 'required|string'], ['required' => 'Brand is required ']);
        HolidayType::findOrFail(decrypt($id))->update($request->all());
        return redirect()->route('holidaytype.index')->with('success_message', 'Holiday type updated successfully'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        HolidayType::findOrFail(decrypt($id))->delete();
        return redirect()->route('holidaytype.index')->with('success_message', 'Holiday type deleted successfully'); 
    }
}
