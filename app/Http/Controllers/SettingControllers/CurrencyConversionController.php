<?php

namespace App\Http\Controllers\SettingControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CurrencyConversion;
class CurrencyConversionController extends Controller
{
    public $pagination = 10;
    
    public function index(Request $request){

        $data['currency_conversions'] = CurrencyConversion::paginate($this->pagination);
        return view('currency_conversions.listing',$data);
    }

    public function edit(Request $request, $id){
        
        $data['currency_record'] = CurrencyConversion::findOrFail(decrypt($id));
        $data['currencies']      = Currency::where('status', 1)->orderBy('id', 'ASC')->get();
        return view('currency_conversions.edit',$data);
    }
    
    public function update(Request $request)
    {
        $this->validate($request, ['manual_rate' => 'required'], ['required' => 'Manual Rate is required']);
        CurrencyConversion::findOrFail(decrypt($id))->update([
                'manual_rate'   =>  $request->manual_rate
            ]);

        return redirect()->route('setting.currnecy_conversions.index')->with('success_message', 'Currency rate Updated Successfully');
    }
}
