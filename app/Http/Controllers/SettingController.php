<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = Setting::where('id', 1)->firstOrFail();
        return view('backend.settings.index', compact('setting'));
        // dd($setting->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'bname' => 'required|string|max:200',
            'email' => 'required|email|max:200',
            'phone' => 'nullable:max:20',
            'phone2' => 'nullable:max:20',
            'site_title' => 'nullable',
            'site_description' => 'nullable',
            'site_keywords' => 'nullable',
            'logo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'logo2' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'gtag' => 'nullable',
            'map' => 'nullable',
            'whatsapp' => 'nullable',
            'address' => 'nullable',
            'facebook' => 'nullable',
            'instagram' => 'nullable',
            'twitter' => 'nullable',
            'linkedin' => 'nullable',
            'youtube' => 'nullable',
            'captcha_site_key' => 'nullable',
            'captch_secret_key' => 'nullable',
            'captcha_contact_form' => 'nullable',
            'captcha_login_form' => 'nullable',
        ]);

        $setting = Setting::where('id', 1)->firstOrFail();

        $setting->bname                  = $request['bname'];
        $setting->email                  = $request['email'];
        $setting->phone                  = $request['phone'];
        $setting->phone2                 = $request['phone2'];
        $setting->site_title             = $request['site_title'];
        $setting->site_description       = $request['site_description'];
        $setting->site_keywords          = $request['site_keywords'];
        $setting->gtag                   = $request['gtag'];
        $setting->map                    = $request['map'];
        $setting->whatsapp               = $request['whatsapp'];
        $setting->address                = $request['address'];
        $setting->facebook               = $request['facebook'];
        $setting->instagram              = $request['instagram'];
        $setting->twitter                = $request['twitter'];
        $setting->linkedin               = $request['linkedin'];
        $setting->youtube                = $request['youtube'];
        $setting->linkedin               = $request['linkedin'];
        $setting->captcha_site_key       = $request['captcha_site_key'];
        $setting->captch_secret_key      = $request['captch_secret_key'];
        $setting->captcha_contact_form   = $request['captcha_contact_form'] ?? "0";
        $setting->captcha_login_form     = $request['captcha_login_form'] ?? "0";

        if ($request->file('logo')) {
            //create unique name of image
            $logoName = time() . '.' . $request->logo->getClientOriginalExtension();

            //move image to path you wish -- it auto generate folder
            $request->logo->move(public_path('uploads/images/logo/'), $logoName);
            $setting->logo = $logoName;
        }

        if ($request->file('logo2')) {
            //create unique name of image
            $logoName = time() . '.' . $request->logo2->extension();

            //move image to path you wish -- it auto generate folder
            $request->logo2->move(public_path('uploads/images/logo2/'), $logoName);
            $setting->logo2 = $logoName;
        }

        //dd($setting->toArray());

        $setting->save();
        return redirect()->route('setting')->with('success', 'Settings Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
