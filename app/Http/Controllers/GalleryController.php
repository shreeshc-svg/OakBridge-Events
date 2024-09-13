<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

use DB;
use File;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'src' => 'required',
        ]);

        foreach($request->file('src') as $image)
        {
            $name = time().'.'.$image->getClientOriginalName();

            $src = $image->move(public_path('uploads/images/our-gallery/'),$name);


            Gallery::create([
                'name' => $name,
                'src' => asset('uploads/images/our-gallery/'.$name)
            ]);
        }

        return redirect()->back()->with('success','Gallery has been updated successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gallery $gallery)
    {
        $galleries = Gallery::all();

        return view('backend.gallery.edit',compact('galleries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gallery $gallery)
    {
        $gallaries = Gallery::all();

        $images_ids=[];
        $oldIds=$request->old;

        if($gallaries->count() > 0)
        {
            foreach ($gallaries as $image) {

                if (!empty($oldIds) && !in_array($image->id, $oldIds)) {
                    $deletedImage = Gallery::find($image->id);
                    File::delete(public_path('uploads/images/our-gallery/' . $deletedImage->name));
                    $deletedImage ? $deletedImage->delete() : '';
                }elseif(empty($oldIds)){
                    $deletedImage = Gallery::find($image->id);
                    File::delete(public_path('uploads/images/our-gallery/' . $deletedImage->name));
                    $deletedImage ? $deletedImage->delete() : '';
                }
            }
        }


        if($request->hasfile('src'))
        {
            foreach($request->file('src') as $image)
            {
                $name = time().'.'.$image->getClientOriginalName();
                $src = $image->move(public_path('uploads/images/our-gallery/'), $name);

                Gallery::create([
                    'name'=>$name,
                    'src' => asset('public/uploads/images/our-gallery/'.$name)
                ]);
            }
        }




        return redirect()->back()->with('success','Gallery has been successfully Updated');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery)
    {
        //
    }
}
