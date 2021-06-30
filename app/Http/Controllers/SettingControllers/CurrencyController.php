<?php

namespace App\Http\Controllers\SettingControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Currency;
use App\AllCurrency;

class CurrencyController extends Controller
{
    public $pagination = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['currencies'] = Currency::paginate($this->pagination);
        return view('currencies.listing',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['all_currencies'] = AllCurrency::whereNotIn('code', Currency::get()->pluck('code')->toArray())->get();
        return view('currencies.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $nCurrency = AllCurrency::where('code', $request->currency)->first();
        Currency::create([
            'name'          => $nCurrency->name,
            'code'          => $nCurrency->code,
            'is_obsolete'   => $nCurrency->isObsolete,
            'flag'          => $nCurrency->flag,
            'status'        => ($request->status == '1')? 1 : 0, 
        ]);
       
        return redirect()->route('setting.currencies.index')->with('success_message', 'Currency updated successfully');  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $currency = Currency::findOrFail(decrypt($id));
        $data['all_currencies'] = AllCurrency::whereNotIn('code',Currency::where('code', '!=', $currency->code)->get()->pluck('code')->toArray())->get();
        $data['currency']       = $currency;
        
        return view('currencies.edit', $data);
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
        $nCurrency = AllCurrency::where('code', $request->currency)->first();
        Currency::findOrFail(decrypt($id))->update([
            'name'          => $nCurrency->name,
            'code'          => $nCurrency->code,
            'is_obsolete'   => $nCurrency->isObsolete,
            'flag'          => $nCurrency->flag,
            'status'        => ($request->status == "1")? 1 : 0, 
        ]);
        return redirect()->route('setting.currencies.index')->with('success_message', 'Currency updated successfully'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Currency::destroy(decrypt($id));
        return redirect()->route('setting.currencies.index')->with('success_message', 'Currency updated successfully'); 
        
    }
}
