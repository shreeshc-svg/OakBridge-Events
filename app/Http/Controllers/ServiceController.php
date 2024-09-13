<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Scategory;

use Illuminate\Validation\Rule;
use File;


use Redirect;
use Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::latest()->get();

        return view('backend.service.index',compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $scategories = Scategory::where('parent_id',null)->orderby('title','asc')->get();
        // $scategories = Scategory::all();

        return view('backend.service.create',compact('scategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'  => 'required|string|max:200',
            'slug'   => 'required|unique:services',
            'image'  => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'excerpt' => 'nullable',
            'body'    => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'meta_keyword' => 'nullable',
            'featured'   => 'nullable',
            'published' => 'nullable',
            'disable_comment' => 'nullable',
            'views' => 'nullable',


        ]);

        $service = new Service;
        $service->title             = $request->title;
        $service->slug              = $request->slug;
        $service->excerpt              = $request->excerpt;
        $service->body                 = $request->body;
        $service->user_id              = Auth::user()->id;
        $service->meta_title           = $request->meta_title;
        $service->meta_description     = $request->meta_description;
        $service->meta_keyword         = $request->meta_keyword;
        $service->featured             = $request->featured == true ? '1': '0';
        $service->published            = $request->published;
        $service->disable_comment      = $request->disable_comment == true ? '1': '0';

        if($request->file('image'))
        {
            $imageName = time().'.'.$request->image->extension();


            $request->image->move(public_path('uploads/images/service/'),$imageName);
            $service->image = $imageName;
        }

        $service->save();


        $scategories = $request->scategory;
        $service->scategories()->attach($scategories);

        return redirect()->route('service.index')->with('success','Service has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $scategories = Scategory::where('parent_id',null)->orderby('title','asc')->get();
        $service = Service::findOrFail($service->id);
        return view('backend.service.edit',compact('service','scategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:200',
            'slug'              => ['required', Rule::unique('services')->ignore($service->id)],
            'image'             => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'excerpt'           => 'nullable',
            'body'              => 'required',

            'meta_title'        => 'nullable',
            'meta_description'  => 'nullable',
            'meta_keyword'      => 'nullable',
            'featured'          => 'nullable',
            'published'         => 'nullable',
            'disable_comment'   => 'nullable',
            'views'             => 'nullable',

        ]);

        $service = Service::where('id',$service->id)->firstOrFail();

        $service->title                = $request->title;
        $service->slug                 = $request->slug;
        $service->excerpt              = $request->excerpt;
        $service->body                 = $request->body;

        $service->user_id              = Auth::user()->id;
        $service->meta_title           = $request->meta_title;
        $service->meta_description     = $request->meta_description;
        $service->meta_keyword         = $request->meta_keyword;
        $service->featured             = $request->featured == true ? '1': '0';
        $service->published            = $request->published;
        $service->disable_comment      = $request->disable_comment == true ? '1': '0';



        if($request->file('image'))
        {
            $destination = 'uploads/images/service/'.$service->image;
            if(File::exists($destination))
            {
                File::delete($destination);
            }


            //create unique name of image
            $imageName = time().'.'.$request->image->extension();

              //move image to path you wish -- it auto generate folder
              $request->image->move(public_path('uploads/images/service/'), $imageName);
              $service->image = $imageName;

        }

        $service->save();

        //foreign keys lets add categories to pivot table

        $scategories = $request->scategory;
        $service->scategories()->sync($scategories);

       //  $tags = $request->tag;
       //  $post->tags()->sync($tags);
        return redirect()->route('service.index')->with('success','Service has beeen updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */

    //  public function destroy(Post $post)
    //  {
    //      $tour = Post::findOrFail($post->id);
    //      $tour->delete();
    //      return back()->with('success', 'Tour Succesfully moved to trash!');
    //  }

    public function destroy(Service $service)
    {
        $tour = Service::findOrFail($service->id);
        $tour->delete();
        return back()->with('success', 'Service Succesfully moved to trash!');
    }

    public function trashView(Request $request)
    {
        $search = $request['search'] ?? "";


            $services = Service::onlyTrashed()->latest()->get();

        return view('backend.service.trash',compact('services','search'));
    }

    // restore data
    public function restore($id)
    {
        $data = Service::withTrashed()->find($id);
        if(!is_null($data)){
            $data->restore();
        }
        return redirect()->back()->with("success", "Data Restored Succesfully");
    }

    public function force_delete(Request $request,$id)
    {
        $service = Service::withTrashed()->find($id);
        if(!is_null($service)){

           //remove image
           $destination = 'uploads/images/service/'.$service->image;
               if(File::exists($destination))
               {
                   File::delete($destination);
               }

           $service->scategories()->detach();
           $service->forceDelete();
        }
        return redirect()->back()->with("success", "Data Deleted Permanently!!");
    }
}
