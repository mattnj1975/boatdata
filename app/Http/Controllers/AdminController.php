<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use App\Models\Uploadlog;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
		
        $master_users = User::where('role_as', 2)->count('id');
        $users = User::where('role_as', 3)->count('id');
        $boats = Settings::count('id');
        $settings = Settings::pluck('mac')->toArray();

        if ($request->ajax()) {
            $query = Uploadlog::select('id', 'device_id', 'ip_address', 'uload_time' , 'created_at')
                    ->whereIn('id', function($query) {
                        $query->select(DB::raw('MAX(id)'))
                            ->from('uploadlog')
                            ->groupBy('device_id');
                    })
                    ->whereNotIn('device_id', $settings)
                    ->latest();
            // Original unfiltered total count
            $totalRecords = $query->count();

            
            // Count after applying filters
            $filteredRecords = $query->count();

            $length = $request->length ?: env("PER_PAGE_COUNT");
            $start = $request->start > $filteredRecords ? 0 : $request->start;

            $data = $query->skip($start)->take($length)->get();
            $draw = $request->get('draw');

            foreach ($data as $item) {
                
                $item->actions = '<div class="d-flex btn-group-lg" role="group" aria-label="Basic example">
                                    <div style="margin-right:10px"> <a href="javascript:void(0)" data-id="' . $item->id . '" data-toggle="tooltip" data-original-title="Edit" class="mr-3 btn btn-outline-success btn-sm addToSetting"> <i class="fa fa-check"></i> Add to Settings</a> </div>
                                    </div>';
            }

            return response()->json([
                'draw' => isset($draw) ? intval($draw) : 1,
                'recordsTotal' => $totalRecords, // Original unfiltered total count
                'recordsFiltered' => $filteredRecords, // Count after applying filters
                'data' => $data,
            ]);
        }


        return view('admin.dashboard', compact('master_users', 'users', 'boats'));
    }
    
    
    public function conn(Request $request)
    {
		
		$master_users = User::where('role_as', 2)->count('id');
        $users = User::where('role_as', 3)->count('id');
        $boats = Settings::count('id');
        $settings = Settings::pluck('mac')->toArray();
	
        if ($request->ajax()) {
            //$query = Uploadlog::select('id', 'device_id', 'ip_address', 'uload_time', 'upload_status', 'db_ok')
			//		->orderBy('uload_time', 'desc')
					
			$query = DB::table('uploadlog')
					->select('uploadlog.id', 'uploadlog.device_id', 'uploadlog.ip_address', 'uploadlog.uload_time', 'uploadlog.upload_status', 'uploadlog.db_ok')
					//->join('settings', 'settings.mac', '=', 'uploadlog.device_id')
					
					->orderBy('uploadlog.uload_time', 'desc')
                    ->latest();
            // Original unfiltered total count
            $totalRecords = $query->count();

            
            // Count after applying filters
            $filteredRecords = $query->count();

            $length = $request->length ?: env("PER_PAGE_COUNT");
            $start = $request->start > $filteredRecords ? 0 : $request->start;

            $data = $query->skip($start)->take($length)->get();
            $draw = $request->get('draw');

            //foreach ($data as $item) {
                
            //    $item->actions = '<div class="d-flex btn-group-lg" role="group" aria-label="Basic example">
            //                        <div style="margin-right:10px"> <a href="javascript:void(0)" data-id="' . $item->id . '" data-toggle="tooltip" data-original-title="Edit" class="mr-3 btn btn-outline-success btn-sm addToSetting"> <i class="fa fa-check"></i> Add to Settings</a> </div>
            //                        </div>';
            //}

            return response()->json([
                'draw' => isset($draw) ? intval($draw) : 1,
                'recordsTotal' => $totalRecords, // Original unfiltered total count
                'recordsFiltered' => $filteredRecords, // Count after applying filters
                'data' => $data,
            ]);
        }


        return view('admin.dashboard', compact('master_users', 'users', 'boats'));
    }
    
    
    
    public function addToSettings($id)
    {
        $Uploadlog = Uploadlog::find($id);
        $latestBoat = Settings::latest()->first('device_id');
        $Setting = new Settings();
        $Setting->mac = $Uploadlog->device_id;
        $Setting->device_id = $latestBoat->device_id + 1;
        $Setting->save();
        return response()->json(['success' => 'UploadLog added to settings successfully.']);
    }
    public function setting()
    {
        $user = FacadesAuth::user();
        return view('admin.setting', compact('user'));
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
        return redirect()->route('dashboard')->with('success', 'Settings Updated Successfully');
    }
}
