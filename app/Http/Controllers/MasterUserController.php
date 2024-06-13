<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use DB;
use App\Models\Uploadlog;
use App\Models\AdminBoats;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class MasterUserController extends Controller
{
	
	    public function masterconn(Request $request)
    {
			
        if ($request->ajax()) {
            		
			//$query = AdminBoats::with('boat')
			//->where('user_id', Auth::id())
			//->latest();				
			
			$query = DB::table('uploadlog')
					->select('uploadlog.id', 'uploadlog.device_id', 'uploadlog.ip_address', 'uploadlog.uload_time', 'uploadlog.upload_status', 'uploadlog.db_ok')
					->orderBy('uploadlog.uload_time', 'desc')
                  ->latest();
					
			//$query = DB::table('users')
			//		->select("uploadlog.id", "uploadlog.device_id", "uploadlog.ip_address", "uploadlog.uload_time", "uploadlog.upload_status", "uploadlog.db_ok")
					
			//		->leftJoin("admin_boats", "users.id", "=", "admin_boats.user_id")
			//		->leftJoin("settings", "admin_boats.boat_id", "=", "settings.id")
			//		->leftJoin("uploadlog", "settings.mac", "=", "uploadlog.device_id")
					//->where("users.id", "=", 24)
					//->orderBy("uploadlog.uload_time","desc")
			//		->get();			
					
					
					
					
            // Original unfiltered total count
            $totalRecords = $query->count();
            
            // Count after applying filters
            $filteredRecords = $query->count();

            $length = $request->length ?: env("PER_PAGE_COUNT");
            $start = $request->start > $filteredRecords ? 0 : $request->start;

            $data = $query->skip($start)->take($length)->get();
            $draw = $request->get('draw');

            
            return response()->json([
                'draw' => isset($draw) ? intval($draw) : 1,
                'recordsTotal' => $totalRecords, // Original unfiltered total count
                'recordsFiltered' => $filteredRecords, // Count after applying filters
                'data' => $data,
            ]);
        }

		return view('master.dashboard');
    }
	
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
