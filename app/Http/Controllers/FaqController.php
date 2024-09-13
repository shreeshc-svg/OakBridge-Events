<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faqs = Faq::latest()->get();

        return view('backend.faq.index',compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.faq.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'  => 'required',
            'body'   => 'required',
        ]);

        $faq = new Faq;
        $faq->title  = $request->title;
        $faq->body   = $request->body;

        $faq->save();

        return redirect()->route('faq.index')->with('success','Faq Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq)
    {
        $faq = Faq::findOrFail($faq->id);

        return view('backend.faq.edit',compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'title'  => 'required',
            'body'   => 'required',
        ]);

        $faq = Faq::where('id',$faq->id)->firstOrFail();
        $faq->title         = $request->title;
        $faq->body          = $request->body;

        $faq->save();

        return redirect()->route('faq.index')->with('success','Faq Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq = Faq::findOrFail($faq->id);
        $faq->delete();
        return back()->with('success','Faq Deleted Successfully');
    }
}