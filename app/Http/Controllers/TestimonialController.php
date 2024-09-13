<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use File;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonials = Testimonial::latest()->get();

        return view('backend.testimonial.index',compact('testimonials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.testimonial.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required',
            'slug'      => 'nullable',
            'image'     => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'location'  => 'required',
            'video'     => 'nullable',
            'featured'  => 'nullable',
            'body'      => 'nullable',
            'star'      => 'required'
        ]);


        $testimonial = new Testimonial;
        $testimonial->title = $request->title;
        $testimonial->slug = $request->slug;
        $testimonial->star = $request->star;
        $testimonial->video = $request->video;
        $testimonial->location = $request->location;
        $testimonial->body = $request->body;
        $testimonial->featured = $request->featured == true ? '1': '0';

        if($request->file('image'))
        {
			//create unique name of image
            $imageName = time().'.'.$request->image->extension();

			//move image to path you wish -- it auto generate folder
            $request->image->move(public_path('uploads/images/testimonial/'), $imageName);
            $testimonial->image = $imageName;
        }

        $testimonial->save();
        return redirect()->route('testimonial.index')->with('success','Testimonial has been added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimonial $testimonial)
    {
        $testimonial = Testimonial::findOrFail($testimonial->id);
        return view('backend.testimonial.edit',compact('testimonial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'title'     => 'required',
            'slug'      => 'nullable',
            'image'     => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'location'  => 'required',
            'video'     => 'nullable',
            'featured'  => 'nullable',
            'body'      => 'required',
            'star'      => 'required'
        ]);

        $testimonial = Testimonial::where('id',$testimonial->id)->firstOrFail();
        $testimonial->title        = $request->title;
        $testimonial->slug         = $request->slug;
        $testimonial->body         = $request->body;
        $testimonial->video        = $request->video;
        $testimonial->star         = $request->star;
        $testimonial->featured     = $request->featured == true ? '1': '0';

        if($request->file('image'))
        {
            $destination = 'uploads/images/testimonial/'.$testimonial->image;
             if(File::exists($destination))
             {
                 File::delete($destination);
             }
			//create unique name of image
            $imageName = time().'.'.$request->image->extension();

			//move image to path you wish -- it auto generate folder
            $request->image->move(public_path('uploads/images/testimonial/'), $imageName);
            $testimonial->image = $imageName;
        }

        $testimonial->save();
        return redirect()->route('testimonial.index')->with('success','Testimonial data has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        $testimonial = Testimonial::findOrFail($testimonial->id);
        $destination = 'uploads/images/testimonial/'.$testimonial->image;
         if(File::exists($destination))
         {
             File::delete($destination);
         }
         $testimonial->delete();
        return back()->with('success', 'Testimonial Deleted Succesfully!');
    }
}
