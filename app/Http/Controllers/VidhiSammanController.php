<?php

namespace App\Http\Controllers;

use App\Models\VidhiSamman;
use Illuminate\Http\Request;

class VidhiSammanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = VidhiSamman::latest()->get();
        return view('backend.vidhi-samman.index',compact('images'));
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
            'title' => 'nullable',
            'image' => 'required',
            'category' =>'required',
            'year' => 'required',
            'body' => 'nullable',
        ]);

        if($request->file('image'))
        {
			//create unique name of image
            $imageName = time().'.'.$request->image->getClientOriginalExtension();

			//move image to path you wish -- it auto generate folder
            $request->image->move(public_path('uploads/images/vidhi/'), $imageName);
            $data['image'] = $imageName ;
        }

        VidhiSamman::create($data);
        return redirect()->back()->withSuccess('Data has been added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(VidhiSamman $vidhiSamman)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Debug to see the model instance
        $image = VidhiSamman::whereId($id)->first();

        // Pass the model instance to the view
        return view('backend.vidhi-samman.edit', compact('image'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $image = VidhiSamman::whereId($id)->first();

        $data = $request->validate([
            'title' => 'nullable',
            'image' => 'sometimes',
            'category' =>'required',
            'year' => 'required',
            'body' => 'nullable',
        ]);

        if($request->file('image'))
        {

            $destination = public_path('uploads/images/vidhi/') . $image->image;

            if (!empty($image->image) && \File::exists($destination)) {
                \File::delete($destination);
            }


			//create unique name of image
            $imageName = time().'.'.$request->image->getClientOriginalExtension();

			//move image to path you wish -- it auto generate folder
            $request->image->move(public_path('uploads/images/vidhi/'), $imageName);
            $data['image'] = $imageName ;
        }

        $image->update($data);
        return redirect()->route('vidhi.index')->withSuccess('Data has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $image = VidhiSamman::whereId($id)->first();
         //remove image
         $destination = public_path('uploads/images/vidhi/') . $image->image;

         if (!empty($image->image) && \File::exists($destination)) {
             \File::delete($destination);
         }

         $image->delete();
         return back()->withSuccess('Data has been deleted successfully!');

    }
}