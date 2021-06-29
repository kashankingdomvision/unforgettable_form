<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Season;
use Carbon\Carbon;
use App\Http\Requests\SeasonRequest;

class SeasonController extends Controller
{
    public $pagination = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['seasons'] = Season::paginate($this->pagination);
        return view('seasons.listing', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seasons.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SeasonRequest $request)
    {
        Season::create($request->all());
        return redirect()->route('seasons.index')->with('success_message', 'Season created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['season'] = Season::findOrFail(decrypt($id));
        return view('seasons.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SeasonRequest $request, $id)
    {
        $season = Season::findOrFail(decrypt($id));
        $season->update($request->all());
        return redirect()->route('seasons.index')->with('success_message', 'Season updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Season::destroy(decrypt($id));
        return redirect()->route('seasons.index')->with('success_message', 'Season deleted successfully');
    }
}
