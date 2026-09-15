<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = Video::latest()->get();
        return view('backend.video.index',compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'video' => 'required|string|max:250',
            'image' => 'nullable',
            'other' => 'nullable',
        ]);

        Video::create($data);
        return redirect()->back()->withSuccess('Video has been added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        return view('backend.video.edit',compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'video' => 'required|string|max:250',
            'image' => 'nullable',
            'other' => 'nullable',
        ]);

        $video->update($data);
        return redirect()->route('video.index')->withSuccess('Item has been successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        $video->delete();
        return back()->withSuccess('Item has been deleted successfully!');
    }
}