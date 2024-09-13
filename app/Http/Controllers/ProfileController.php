<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;

class ProfileController extends Controller
{
     //user profile
     public function profile()
     {
         return view('backend.profile.index');
     }


     //Password update
     public function password_update(Request $request)
     {
         $request->validate([
            'current_password' => 'required',
             'password' => 'required|string|min:8|confirmed',
             'password_confirmation' => 'required',
           ]);

           $user = Auth::user();

           if (!Hash::check($request->current_password, $user->password)) {
               return back()->with('password_error', 'Current password does not match!');
           }

           $user->password = Hash::make($request->password);
           $user->save();

           return redirect()->route('profile')->with('updated', 'Password is successfully Updated!');
     }


     public function profile_update(Request $request)
     {
        $data= $request->validate([
             'name'=>'required',
             'email'=>'required|email',

         ]);

         // dd($data);
         $user = Auth::user();
         $user->name = $request['name'];
         $user->email = $request['email'];
         $user->save();

         return redirect()->route('profile')->with('success', 'Profile Updated Successfully!');
     }
}
