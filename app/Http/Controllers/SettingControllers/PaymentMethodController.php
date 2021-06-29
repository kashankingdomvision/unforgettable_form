<?php

namespace App\Http\Controllers\SettingControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PaymentMethod;
class PaymentMethodController extends Controller
{
    public $pagination = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['payment_mehtods'] = PaymentMethod::paginate($this->pagination);
        return view('payment_methods.listing',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('payment_methods.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        PaymentMethod::create($request->all());
        return redirect()->route('setting.payment_methods.index')->with('success_message', 'Payment method created successfully'); 
        
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['payment_method'] = PaymentMethod::findOrFail(decrypt($id));
        return view('payment_methods.edit',$data);
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
        $request->validate(['name' => 'required|string']);
        PaymentMethod::findOrFail(decrypt($id))->update($request->all());
        return redirect()->route('setting.payment_methods.index')->with('success_message', 'Payment method updated successfully'); 
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PaymentMethod::destroy(decrypt($id));
        return redirect()->route('setting.payment_methods.index')->with('success_message', 'Payment method deleted successfully'); 
        
    }
}
