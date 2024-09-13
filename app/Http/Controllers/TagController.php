<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::latest()->get();

        return view('backend.tag.index',compact('tags'));
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
        $validator = $request->validate([
            'title'      => 'required|String|max:50',
            'slug'      => 'required|string|Max:75|unique:tags',
        ]);

        Tag::create([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
        ]);

        return redirect()->back()->with('success', 'Tag has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        $tag = Tag::where('id',$tag->id)->firstOrFail();
        return view('backend.tag.edit',compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $validator = $request->validate([
            'title'     => 'required',
            'slug' => ['required', Rule::unique('tags')->ignore($tag->id)],
        ]);

        $tag = Tag::where('id',$tag->id)->firstOrFail();


        $tag->title = $request->title;
        $tag->slug = $request->slug;
        $tag->save();
        return redirect()->route('tag.index')->with('success', 'Tag has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag = Tag::with('posts')->findOrFail($tag->id);

        //foreign keys lets add categories to pivot table
        $posts = $tag->posts;
        $tag->posts()->detach();
        //$poll->users()->detach();
          //foreign keys lets add categories to pivot table
        //   $tags = $request->tag;
        //   $post->tags()->attach($tags);
        $tag->delete();
        return redirect()->back()->with('success', 'Tag has been deleted successfully.');
    }
}
