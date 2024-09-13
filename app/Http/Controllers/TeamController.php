<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Requests\Team\TeamRequest;
use App\Http\Requests\Team\TeamUpdateRequest;
use File;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::latest()->get();

        return view('backend.team.index',compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.team.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamRequest $request)
    {
        $data = $request->validated();

        if($request->file('image'))
        {
            $imageName = uniqid().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/team/'),$imageName);
            $data['image'] = $imageName;
        }

        Team::create($data);

        return redirect()->route('team.index')->with('success','Team member created sucessfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        return view('backend.team.edit',compact('team'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamUpdateRequest $request, Team $team)
    {
       $data =  $request->validated();

        if($request->file('image'))
        {
            $destination = public_path('uploads/images/team',$request->image);
            if(File::exists($destination))
            {
                File::delete($destination);
            }

            $imageName = uniqid().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/team/'),$imageName);
            $data['image'] = $imageName;
        }

        $team->update($data);

        return redirect()->route('team.index')->with('success','Team member updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $destination = public_path('uploads/images/team');

        if(File::exists($destination))
        {
            File::delete($destination);
        }

        $team->delete();

        return redirect()->route('team.index')->with('success','team member has been deleted successfully');
    }
}
