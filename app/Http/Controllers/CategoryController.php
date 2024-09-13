<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Post;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('parent_id',null)->orderby('title','asc')->get();

        return view('backend.category.index',compact('categories'));
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
            'slug' => 'required|unique:categories',
            'parent_id' => 'nullable|numeric'
        ]);

        //dd($validate);
        Category::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'parent_id' =>$request->parent_id
        ]);

        return redirect()->back()->with('success','Category has been created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $category = Category::whereId($category->id)->first();

        $categories = Category::where('parent_id', null)->where('id', '!=', $category->id)->orderby('title', 'asc')->get();
        return view('backend.category.edit',compact('categories','category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $category = Category::whereId($category->id)->firstOrFail();

        $validator = $request->validate([
            'title'     => 'required',
            'slug' => ['required', Rule::unique('categories')->ignore($category->id)],
            'parent_id'=> 'nullable|numeric'
        ]);

        if($request->title != $category->title || $request->parent_id != $category->parent_id)
            {
                if(isset($request->parent_id))
                {
                    $checkDuplicate = Category::where('title', $request->title)->where('parent_id', $request->parent_id)->first();
                    if($checkDuplicate)
                    {
                        return redirect()->back()->with('error', 'Category already exist in this parent.');
                    }
                }
                else
                {
                    $checkDuplicate = Category::where('title', $request->title)->where('parent_id', null)->first();
                    if($checkDuplicate)
                    {
                        return redirect()->back()->with('error', 'Category already exist with this name.');
                    }
                }
            }


        $category->title = $request->title;
        $category->parent_id = $request->parent_id;
        $category->slug = $request->slug;
        $category->save();

        return redirect()->route('category.index')->with('success', 'Category has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category = Category::with('posts')->findOrFail($category->id);

       if(!$category->posts->count())
       {
        if(count($category->subcategory))
        {
            $subcategories = $category->subcategory;
            foreach($subcategories as $cat)
            {
                $cat = Category::findOrFail($cat->id);
                $cat->parent_id = null;
                $cat->save();
            }
        }
       }

        //foreign keys lets add categories to pivot table
        $posts = $category->posts;
        $category->posts()->detach();
        //$poll->users()->detach();
          //foreign keys lets add categories to pivot table
        //   $tags = $request->tag;
        //   $post->tags()->attach($tags);
        $category->delete();
        return redirect()->back()->with('success', 'Category has been deleted successfully.');
    }
}
