<?php

namespace App\Http\Controllers;

use App\Events\LinkPhone;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('user.dashboard');
    }
	

	
    public function setting()
    {
        $user = FacadesAuth::user();
        return view('user.setting', compact('user'));
    }
    public function editSetting(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:50',
        ]);
        if (isset($request->password)) {
            $request->validate([
                'password' => 'required|confirmed|min:6'
            ]);
        }
        $id = FacadesAuth::id();
        $user = User::find($id);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('profile', $filename);
            $user->image = $filename;
        }
        $user->name = $request->name;
        if (isset($request->password)) {
            $user->password = Hash::make($request->password);
        }
        $user->update();
        return redirect()->route('user.dashboard')->with('success', 'Settings Updated Successfully');
    }

    public function test()
    {
        // dd('test');
        event(new LinkPhone('SAAD ABDULLAH'));
    }
}
