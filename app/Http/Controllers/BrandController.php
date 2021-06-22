<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use Auth;
use Illuminate\Support\Str;
class BrandController extends Controller
{
    
    
    public function fileStore($request, $old = NULL)
    {
        if($old != NULL){
            if(is_file(public_path('brands/'.$old->logo))){
                unlink(public_path('brands/'.$old->logo));
            }
        }
        $image = $request->logo;
        $name =  $request->name.'-'.Str::random(3).'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/brands/');
        $image->move($destinationPath, $name);
        return $name;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['brands'] = Brand::get();
        return view('brands.view', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string'], ['required' => 'Brand name is required ']);
        $data = [ 
            'name'      => $request->name,
            'email'     => $request->email,
            'address'   => $request->address,
            'phone'     => $request->phone,
            'user_id'   => Auth::id(),
        ];
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->fileStore($request);
        }
        Brand::create($data);
        return redirect()->route('brand.index')->with('success_message', 'Brand created successfully'); 
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
        $data['brand'] = Brand::findOrFail(decrypt($id));
        return view('brands.edit', $data);
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
        $brand = Brand::findOrFail(decrypt($id));
        
        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'address'   => $request->address,
            'phone'     => $request->phone,
        ];
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->fileStore($request, $brand);
        }
        $brand->update($data);
        return redirect()->route('brand.index')->with('success_message', 'Brand updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::findOrFail(decrypt($id))->delete();
        return redirect()->route('brand.index')->with('success_message', 'Brand deleted successfully');
    }
}
