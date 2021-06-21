<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Currency;
class CurrencyController extends Controller
{
    public $pagination;
    public function __CONSTRUCTS()
    {
        $this->pagination = 10;
    }
    
    public function getCurrencyArray($data)
    {
        return  [
            'name'   => $data->name,
            'code'   => $data->code,
            'symbol' => $data->symbol,
            'status' => $data->status
        ];
    }
    
    public function index()
    {
        $data['currencies'] = Currency::paginate($this->pagination);
        return view("currency.index", $data);
    }
    
    
    
    public function create()
    {
        return view('currency.create');
    }
    
    
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        Currency::create($this->getCurrencyArray($request));
        return redirect()->route('currency.index')->with('success_message', 'Currency Created Successfully'); 
        
    }
    
    public function destroy($id)
    {
        $currency = Currency::findOrFail(decrypt($id));
        $currency->delete();
        return redirect()->route('currency.index')->with('success_message', 'Currency Deleted successfully'); 
    }
}
