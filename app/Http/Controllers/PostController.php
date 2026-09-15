<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use File;


use Redirect;
use Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

            $posts = Post::with('categories')->latest()->get();

            //dd($posts->toArray());


        return view('backend.post.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $categories = Category::all();
        $categories = Category::where('parent_id',null)->orderby('title','asc')->get();
        $tags       = Tag::all();
        return view('backend.post.create',compact('categories','tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:200',
            'slug'              => 'required|unique:posts',
            'image'             => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'excerpt'           => 'required',
            'body'              => 'required',

            'meta_title'        => 'nullable',
            'meta_description'  => 'nullable',
            'meta_keyword'      => 'nullable',
            'featured'          => 'nullable',
            'published'         => 'nullable',
            'disable_comment'   => 'nullable',
            'views'             => 'nullable',
            'category'          => 'required'

        ]);


        $post = new Post;
        $post->title                = $request->title;
        $post->title                = $request->title;
        $post->slug                 = $request->slug;
        $post->excerpt              = $request->excerpt;
        $post->body                 = $request->body;
        $post->user_id              = Auth::user()->id;

        $post->meta_title           = $request->meta_title;
        $post->meta_description     = $request->meta_description;
        $post->meta_keyword         = $request->meta_keyword;
        $post->featured             = $request->featured == true ? '1': '0';
        $post->published            = $request->published;
        $post->disable_comment      = $request->disable_comment == true ? '1': '0';


        if($request->file('image'))
        {
			//create unique name of image
            $imageName = time().'.'.$request->image->extension();

			//move image to path you wish -- it auto generate folder
            $request->image->move(public_path('uploads/images/post/'), $imageName);
            $post->image = $imageName;
        }

        $post->save();



        //foreign keys lets add categories to pivot table
        $tags = $request->tag;
        $post->tags()->attach($tags);

        $categories = $request->category;
        $post->categories()->attach($categories);

        return redirect()->route('post.index')->with('success','Post has beeen added successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::where('parent_id',null)->orderby('title','asc')->get();

        $tags       = Tag::all();
        $post = Post::findOrFail($post->id);
        //$post = Post::with('tags')->findOrFail($post->id);
       // dd($post->toArray());
        return view('backend.post.edit',compact('post','categories','tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {


        $data = $request->validate([
            'title'             => 'required|string|max:200',
            'slug'              => ['required', Rule::unique('posts')->ignore($post->id)],
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
            'category'          => 'required'

        ]);

        $post = Post::where('id',$post->id)->firstOrFail();

        $post->title                = $request->title;
        $post->slug                 = $request->slug;
        $post->excerpt              = $request->excerpt;
        $post->body                 = $request->body;
        $post->user_id              = Auth::user()->id;
        $post->meta_title           = $request->meta_title;
        $post->meta_description     = $request->meta_description;
        $post->meta_keyword         = $request->meta_keyword;
        $post->featured             = $request->featured == true ? '1': '0';
        $post->published            = $request->published;
        $post->disable_comment      = $request->disable_comment == true ? '1': '0';

         //image
         if($request->file('image'))
         {
             $destination = 'uploads/images/post/'.$post->image;
             if(File::exists($destination))
             {
                 File::delete($destination);
             }


             //create unique name of image
             $imageName = time().'.'.$request->image->extension();

           	//move image to path you wish -- it auto generate folder
               $request->image->move(public_path('uploads/images/post/'), $imageName);
               $post->image = $imageName;

         }

         $post->save();

         //foreign keys lets add categories to pivot table
         $tags = $request->tag;
         $post->tags()->sync($tags);

         $categories = $request->category;
         $post->categories()->sync($categories);

        //  $tags = $request->tag;
        //  $post->tags()->sync($tags);
         return redirect()->route('post.index')->with('success','Post has been updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $tour = Post::findOrFail($post->id);
        $tour->delete();
        return back()->with('success', 'Post Succesfully moved to trash!');
    }

    public function trashView(Request $request)
    {
        $search = $request['search'] ?? "";

        if($search){
            $posts = Post::onlyTrashed()
            ->where('title', 'LIKE', "%{$search}%")
            ->paginate(6);
        }else{
            $posts = Post::onlyTrashed()->latest()->paginate(8);
        }
        return view('backend.post.trash',compact('posts','search'));
    }

    // restore data
    public function restore($id)
    {
        $data = Post::withTrashed()->find($id);
        if(!is_null($data)){
            $data->restore();
        }
        return redirect()->back()->with("success", "Post Restored Succesfully");
    }

    // force delete
    public function force_delete(Request $request,$id)
    {
        $post = Post::withTrashed()->find($id);
        if(!is_null($post)){

           //remove image
           $destination = 'uploads/images/post/'.$post->image;
               if(File::exists($destination))
               {
                   File::delete($destination);
               }

           $post->tags()->detach();
           $post->forceDelete();
        }
        return redirect()->back()->with("success", "Post Deleted Permanently!!");
    }



    public function ckeditor(Request $request)
    {

        if($request->hasFile('upload')) {

            $originName = $request->file('upload')->getClientOriginalName();

            $fileName = pathinfo($originName, PATHINFO_FILENAME);

            $extension = $request->file('upload')->getClientOriginalExtension();

            $fileName = $fileName.'_'.time().'.'.$extension;



            $request->file('upload')->move(public_path('media'), $fileName);



            $CKEditorFuncNum = $request->input('CKEditorFuncNum');

            $url = asset('public/media/'.$fileName);

            $msg = 'Image uploaded successfully';

            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";



            @header('Content-type: text/html; charset=utf-8');

            echo $response;

        }
    }
}