<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\User;
use App\Models\AdminBoats;
use App\Models\UserBoats;
use App\Models\BoatNotes;
use App\Models\BoatFile;
use Yajra\DataTables\DataTables;
use Auth;

class BoatController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = settings::latest()->select('id', 'boatname', 'mac', 'device_id', 'public', 'default_interval', 'lastseen', 'version');
			  
			
            // Original unfiltered total count
            $totalRecords = Settings::count();

            if ((!empty($request['filter_mac'])) &&  ($request->has('filter_mac'))) {
                $query->where('mac', 'like', '%' . $request->input('filter_mac') . '%');
            }

            // Count after applying filters
            $filteredRecords = $query->count();

            $length = $request->length ?: env("PER_PAGE_COUNT");
            $start = $request->start > $filteredRecords ? 0 : $request->start;

            $data = $query->skip($start)
			->take($length)
			->orderBy('device_id', 'desc')
			->get();
            $draw = $request->get('draw');

            foreach ($data as $item) {
                $admin = AdminBoats::where('boat_id', $item->id)->first();
                if (!empty($admin)) {
                    $adminUser = User::find($admin->user_id);
                    $item->assign_admin = '<span class="badge badge-pill badge-soft-primary font-size-14 m-1">' . ucwords($adminUser->name) . '</span>';
                } else {
                    $item->assign_admin = '<span class="badge badge-pill badge-soft-danger font-size-14 m-1 p-2">' . 'Not Assigned Yet' . '</span>';
                }
                if ($item->public == 1) {
                    $item->is_public = '<span class="badge badge-pill badge-soft-success font-size-14 p-2">YES</span>';
                } else {
                    $item->is_public = '<span class="badge badge-pill badge-soft-danger font-size-14 p-2">NO</span>';
                }

                $item->actions = '<div class="d-flex btn-group-lg" role="group" aria-label="Basic example">
                                       <div style="margin-right:10px"> <a href="' . route('boats.edit', $item->id) . '" data-toggle="tooltip" data-original-title="Edit" class="mr-3 btn btn-outline-primary btn-sm"> <i class="fa fa-edit"></i> Edit</a> </div>
                                       <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-danger btn-sm deleteBoat"> <i class="fa fa-trash"></i> Delete</a> </div>
                                       <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-success btn-sm assignBoat"> <i class="fa fa-check"></i> +Admin</a> </div>
                                       <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-success btn-sm assignUserBoat"> <i class="fa fa-check"></i> +Users</a> </div>
                                       <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-primary btn-sm addNoteInBoat"> <i class="fa fa-edit"></i> Add Note</a> </div>
                                       <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-primary btn-sm addFileInBoat"> <i class="fa fa-file"></i> Add File</a> </div>
                                       <div style="margin-right:10px"> <a href="' . route('view_boat', $item->id) . '" data-toggle="tooltip"  data-id="' . $item->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-primary btn-sm "> <i class="fa fa-eye"></i> View Notes and Files</a> </div>
                                      </div>';
            }

            return response()->json([
                'draw' => isset($draw) ? intval($draw) : 1,
                'recordsTotal' => $totalRecords, // Original unfiltered total count
                'recordsFiltered' => $filteredRecords, // Count after applying filters
                'data' => $data,
            ]);
        }
        return view('admin.boat.index');
    }
    public function edit($id)
    {
        $boat = Settings::find($id);
        return view('admin.boat.edit', compact('boat'));
    }
    public function editMasterBoat($id)
    {
        $boat = Settings::find($id);
        return view('master.boat.edit', compact('boat'));
    }
    public function updateBoat(Request $request)
    {
        try {
            $boat = Settings::find($request->boat_id);
            $boat->{$request->boat_key} = $request->boat_value;
            $boat->save();
            return response()->json(['success' => 'Boat updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function masterUpdateBoat(Request $request)
    {
        try {
            $boat = Settings::find($request->boat_id);
            $boat->{$request->boat_key} = $request->boat_value;
            $boat->save();
            return response()->json(['success' => 'Boat updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function masterBoats(Request $request)
    {
        if ($request->ajax()) {

            $query = AdminBoats::with('boat')->where('user_id', Auth::id())->latest();
            // Original unfiltered total count
            $totalRecords = AdminBoats::where('user_id', Auth::id())->count();

            if (!empty($request['filter_mac']) && $request->has('filter_mac')) {
                $filteredQuery = clone $query;
                $filteredQuery->whereHas('boat', function ($q) use ($request) {
                    $q->where('mac', 'like', '%' . $request->input('filter_mac') . '%');
                });
                $totalRecords = $filteredQuery->count();
                $query = $filteredQuery;
            }

            // Count after applying filters
            $filteredRecords = $query->count();

            $length = $request->length ?: env("PER_PAGE_COUNT");
            $start = $request->start > $filteredRecords ? 0 : $request->start;

            $data = $query->skip($start)->take($length)->get();
            $draw = $request->get('draw');

            foreach ($data as $item) {
                $item->boatname = $item->boat->boatname ?? '';
                $item->mac = $item->boat->mac ?? '';
                $item->default_interval = $item->boat->default_interval ?? '';
                $item->device_id = $item->boat->device_id ?? '';
                $item->lastseen = $item->boat->lastseen ?? '';
                $item->version = $item->boat->version ?? '';
                if ($item->boat->public == 1) {
                    $item->is_public = '<span class="badge badge-pill badge-soft-success font-size-14 p-2">YES</span>';
                } else {
                    $item->is_public = '<span class="badge badge-pill badge-soft-danger font-size-14 p-2">NO</span>';
                }
                $item->actions = '<div class="d-flex btn-group-lg" role="group" aria-label="Basic example">
                                       <div style="margin-right:10px"> <a href="' . route('master_boats.edit', $item->boat->id) . '" data-toggle="tooltip" data-original-title="Edit" class="mr-3 btn btn-outline-primary btn-sm"> <i class="fa fa-edit"></i> Edit</a> </div>
                                       <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->boat->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-success btn-sm assignBoat"> <i class="fa fa-check"></i>+Users</a> </div>
                                       <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->boat->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-primary btn-sm addNoteInBoat"> <i class="fa fa-edit"></i> Add Note</a> </div>
                                       <div style="margin-right:10px"> <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $item->boat->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-primary btn-sm addFileInBoat"> <i class="fa fa-file"></i> Add File</a> </div>
                                       <div style="margin-right:10px"> <a href="' . route('view_boat', $item->boat->id) . '" data-toggle="tooltip"  data-id="' . $item->boat->id . '" data-original-title="Delete" class="mr-3 btn btn-outline-primary btn-sm "> <i class="fa fa-eye"></i> View Notes and Files</a> </div>
                                      </div>';
            }

            return response()->json([
                'draw' => isset($draw) ? intval($draw) : 1,
                'recordsTotal' => $totalRecords, // Original unfiltered total count
                'recordsFiltered' => $filteredRecords, // Count after applying filters
                'data' => $data,
            ]);
        }
        return view('master.boat.index');
    }
    public function allAdmins()
    {
        $users = User::where('role_as', 2)->where('active', 1)->get(['id', 'name']);
        return response()->json($users);
    }
    public function allUsers()
    {
        if (Auth::user()->role_as == 1) {
            $users = User::where('role_as', 3)
                ->where('active', 1)
                ->latest()
                ->get(['id', 'name']);
        } else {
            $users = User::where('created_by', Auth::id())
                ->where('active', 1)
                ->where('role_as', 3)
                ->latest()
                ->get(['id', 'name']);
        }
        return response()->json($users);
    }
    public function getAssignedAdmin($boat_id)
    {
        $data = AdminBoats::where('boat_id', $boat_id)->first();
        return response()->json($data);
    }
    public function getAssignedUser($boat_id)
    {
        $data = UserBoats::where('boat_id', $boat_id)->where('assignee_user_id', Auth::id())->get();
        return response()->json($data);
    }
    public function assignBoat(Request $request)
    {

        AdminBoats::where('boat_id', $request->boat_id)->delete();

        $AdminBoats = new AdminBoats();
        $AdminBoats->user_id = $request->admin_id;
        $AdminBoats->boat_id = $request->boat_id;
        $AdminBoats->save();

        return response()->json(['success' => 'Boat assigned successfully.']);
    }
    public function assignBoatToUser(Request $request)
    {

        if ($request->users) {
            UserBoats::where('boat_id', $request->boat_id)->where('assignee_user_id', Auth::id())->delete();
            foreach ($request->users as $user_id) {
                $UserBoats = new UserBoats();
                $UserBoats->user_id = $user_id;
                $UserBoats->assignee_user_id = Auth::id();
                $UserBoats->boat_id = $request->boat_id;
                $UserBoats->save();
            }
        }

        return response()->json(['success' => 'Boat assigned successfully.']);
    }
    public function destroy($id)
    {
        AdminBoats::where('boat_id', $id)->delete();
        UserBoats::where('boat_id', $id)->delete();
        BoatNotes::where('boat_id', $id)->delete();
        BoatFile::where('boat_id', $id)->delete();

        Settings::find($id)->delete();
        return response()->json(['success' => 'Boat deleted successfully.']);
    }
    public function addBoatNote(Request $request)
    {
        $BoatNote = new BoatNotes();
        $BoatNote->boat_id = $request->boat_id;
        $BoatNote->user_id = Auth::id();
        $BoatNote->note = $request->note;
        $BoatNote->save();
        return response()->json(['success' => 'Note added successfully.']);
    }
    public function editBoatNote(Request $request)
    {
        $BoatNote = BoatNotes::find($request->note_id);
        $BoatNote->user_id = Auth::id();
        $BoatNote->note = $request->note;
        $BoatNote->save();
        return response()->json(['success' => 'Note updated successfully.']);
    }
    public function addBoatFile(Request $request)
    {
        $BoatNote = new BoatFile();
        $BoatNote->boat_id = $request->boat_id;
        $BoatNote->user_id = Auth::id();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $originalFilename = $file->getClientOriginalName();
            $file->move('boat_files', $filename);
            $BoatNote->file = 'boat_files/' . $filename;
            $BoatNote->file_name = $originalFilename;
        }
        $BoatNote->save();
        return response()->json(['success' => 'File added successfully.']);
    }
    public function editBoatFile(Request $request)
    {
        $BoatNote = BoatFile::find($request->file_id);
        $BoatNote->user_id = Auth::id();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $originalFilename = $file->getClientOriginalName();
            $file->move('boat_files', $filename);
            $BoatNote->file = 'boat_files/' . $filename;
            $BoatNote->file_name = $originalFilename;
        }
        $BoatNote->save();
        return response()->json(['success' => 'File updated successfully.']);
    }
    public function viewBoat($id)
    {
        $notes = BoatNotes::where('boat_id', $id)->latest()->get();
        $files = BoatFile::where('boat_id', $id)->latest()->get();
        $boat = Settings::find($id);
        return view('admin.boat.view', compact('boat', 'notes', 'files'));
    }
    public function editNote(Request $request)
    {
        $note = BoatNotes::find($request->id);
        return response()->json($note);
    }
    public function editFile(Request $request)
    {
        $file = BoatFile::find($request->id);
        return response()->json($file);
    }
    public function deleteNote($id)
    {
        BoatNotes::find($id)->delete();
        return response()->json(['success' => 'Note deleted successfully.']);
    }
    public function deleteFile($id)
    {
        $file = BoatFile::find($id);

        if ($file) {
            $filePath = $file->file;

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $file->delete();

            return response()->json(['success' => 'File deleted successfully.']);
        } else {
            // Return error response if file record not found
            return response()->json(['error' => 'File not found.'], 404);
        }
    }
}
