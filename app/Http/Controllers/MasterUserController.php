<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\AdminBoats;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class MasterUserController extends Controller
{
    public function dashboard()
    {
        $users = User::where('created_by', Auth::id())->where('role_as', 3)->count('id');
        $boats = AdminBoats::where('user_id', Auth::id())->count('id');
        return view('master.dashboard', compact('users', 'boats'));
    }
	
	
    public function setting()
    {
        $user = Auth::user();
        return view('master.setting', compact('user'));
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
        $id = Auth::id();
        $user = User::find($id);
        if($request->hasFile('image')){
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('profile', $filename);
            $user->image= $filename;
        }
        $user->name = $request->name;
        if (isset($request->password)) {
            $user->password = Hash::make($request->password);
        }
        $user->update();
        return redirect()->route('master.dashboard')->with('success', 'Settings Updated Successfully');
    }
}
