<?php

namespace App\Http\Controllers;

use App\Models\Scategory;
use Illuminate\Http\Request;
use App\Models\Service;

use Illuminate\Validation\Rule;

class ScategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scategories = Scategory::where('parent_id',null)->orderby('title','asc')->get();

        return view('backend.scategory.index',compact('scategories'));
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
        $validate = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:scategories',
            'parent_is' => 'nullable|numeric',
        ]);

        Scategory::create([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'parent_id' => $request->input('parent_id')
        ]);

        return redirect()->back()->with('success','Service Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Scategory $scategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scategory $scategory)
    {
        $scategory = Scategory::whereId($scategory->id)->first();

        $categories = Scategory::where('parent_id', null)->where('id', '!=', $scategory->id)->orderby('title', 'asc')->get();

        return view('backend.scategory.edit',compact('scategory','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scategory $scategory)
    {
        $scategory = Scategory::whereId($scategory->id)->firstOrFail();

        $validate = $request->validate([
            'title' => 'required',
            'slug' => ['required', Rule::unique('categories')->ignore($scategory->id)],
            'parent_id' => 'nullable|numeric',
        ]);

        if($request->title != $scategory->title || $request->parent_id != $scategory->parent_id)
            {
                if(isset($request->parent_id))
                {
                    $checkDuplicate = Scategory::where('title', $request->title)->where('parent_id', $request->parent_id)->first();
                    if($checkDuplicate)
                    {
                        return redirect()->back()->with('error', 'Category already exist in this parent.');
                    }
                }
                else
                {
                    $checkDuplicate = Scategory::where('title', $request->title)->where('parent_id', null)->first();
                    if($checkDuplicate)
                    {
                        return redirect()->back()->with('error', 'Category already exist with this name.');
                    }
                }
            }

        $scategory->title = $request->title;
        $scategory->parent_id = $request->parent_id;
        $scategory->slug = $request->slug;
        $scategory->save();

        return redirect()->route('scategory.index')->with('success','Service Category updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scategory $scategory)
    {
        $scategory = Scategory::with('services')->findOrFail($scategory->id);

        //dd($scategory->toArray());

        if(count($scategory->subcategory))
        {
            $subcategories = $scategory->subcategory;

            foreach($subcategories as $cat)
            {
                $cat = Scategory::findOrFail($cat->id);
                $cat->parent_id = null;
                $cat->save();
            }
        }

        //foreign keys lets add categories to pivot table
        $services = $scategory->services;
        $scategory->services()->detach();
        $scategory->delete();
        return redirect()->back()->with('success', 'Service Category has been deleted successfully.');
    }
}
