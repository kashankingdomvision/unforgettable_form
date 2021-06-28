<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use Illuminate\Support\Str;
use App\Http\Requests\RoleRequest;

class RoleController extends Controller
{
    public $pagination = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['roles'] = Role::paginate($this->pagination);
        return view('roles.listing', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name), 
        ]);
        
        return redirect()->route('roles.index')->with('success_message', 'Role created successfully');
    }

    public function edit($id)
    {
        $data['role'] = Role::findOrFail(decrypt($id));
        return view('roles.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $role = Role::findOrFail(decrypt($id));
        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name), 
        ]);
        return redirect()->route('roles.index')->with('success_message', 'Role update successfully');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::destroy(decrypt($id));
        return redirect()->route('roles.index')->with('success_message', 'Role deleted successfully');
    }
}
